// luft.jetzt · Atmosphäre — schlanker JS-Layer für Start/Ergebnis/Karte.
// Geolocation + Autocomplete auf den bestehenden Endpunkten, ohne Bootstrap/Algolia.
// Leaflet wird nur bei Bedarf (Kartenseite) dynamisch nachgeladen.

const AQI = { 1: '#1fb3a6', 2: '#74c247', 3: '#f2c33c', 4: '#e8683c', 5: '#a83d6b' };

const PREFETCH_CITIES = '/search/prefetch-cities';
const PREFETCH_STATIONS = '/search/prefetch-stations';
const GEOCODE = '/search/query';

function initGeolocation() {
    const btn = document.querySelector('[data-at-geolocate]');
    if (!btn) return;
    const error = document.querySelector('[data-at-geo-error]');

    btn.addEventListener('click', () => {
        if (!navigator.geolocation) {
            if (error) { error.textContent = 'Standort wird von diesem Browser nicht unterstützt.'; error.hidden = false; }
            return;
        }
        btn.classList.add('is-loading');
        btn.disabled = true;
        if (error) error.hidden = true;

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                window.location = '/display?latitude=' + encodeURIComponent(pos.coords.latitude) + '&longitude=' + encodeURIComponent(pos.coords.longitude);
            },
            (err) => {
                btn.classList.remove('is-loading');
                btn.disabled = false;
                if (error) {
                    error.textContent = err && err.code === err.PERMISSION_DENIED
                        ? 'Standortzugriff abgelehnt. Bitte gib deinen Ort ein.'
                        : 'Dein Standort konnte nicht ermittelt werden. Bitte gib deinen Ort ein.';
                    error.hidden = false;
                }
            },
            { enableHighAccuracy: false, timeout: 15000, maximumAge: 300000 }
        );
    });
}

function initSearch() {
    const input = document.querySelector('[data-at-search]');
    const panel = document.querySelector('[data-at-results]');
    if (!input || !panel) return;

    let cities = [], stations = [];
    let debounce, geoCtrl, geoSeq = 0, places = [], options = [], active = -1;

    Promise.all([
        fetch(PREFETCH_CITIES).then((r) => r.ok ? r.json() : []),
        fetch(PREFETCH_STATIONS).then((r) => r.ok ? r.json() : []),
    ]).then(([c, s]) => { cities = c || []; stations = s || []; }).catch(() => {});

    const filter = (list, q, key) => {
        const n = q.toLowerCase(), out = [];
        for (const item of list) {
            const v = item.value;
            const hay = (key === 'station' ? (v.title + ' ' + v.stationCode) : v.name).toLowerCase();
            if (hay.includes(n)) { out.push(v); if (out.length >= 5) break; }
        }
        return out;
    };

    const close = () => { panel.hidden = true; panel.innerHTML = ''; options = []; active = -1; };

    const render = (q) => {
        const cM = filter(cities, q, 'city');
        const sM = filter(stations, q, 'station');
        panel.innerHTML = '';
        options = [];

        const group = (label, items, build) => {
            if (!items.length) return;
            const h = document.createElement('div'); h.className = 'at-results__group'; h.textContent = label;
            panel.appendChild(h);
            items.forEach((it) => {
                const el = document.createElement('a'); el.className = 'at-results__item'; el.href = '#';
                build(el, it);
                el.addEventListener('mousedown', (e) => { e.preventDefault(); go(it); });
                panel.appendChild(el);
                options.push({ data: it, el });
            });
        };

        group('Städte', cM, (el, c) => { el.textContent = c.name; el.dataset.type = 'nav'; el.dataset.url = c.url; });
        group('Messstationen', sM, (el, s) => {
            el.innerHTML = '<span>' + s.title + '</span><small>' + s.stationCode + '</small>';
            el.dataset.type = 'nav'; el.dataset.url = s.url;
        });
        group('Orte', places, (el, p) => {
            const label = [p.name, p.zipCode, p.city].filter(Boolean).join(' · ');
            el.textContent = label || (p.city || 'Ort');
            el.dataset.type = 'coord'; el.dataset.lat = p.latitude; el.dataset.lon = p.longitude;
        });

        panel.hidden = options.length === 0;
    };

    const go = (item) => {
        if (item.url) { window.location = item.url; return; }
        if (item.latitude != null) { window.location = '/display?latitude=' + encodeURIComponent(item.latitude) + '&longitude=' + encodeURIComponent(item.longitude); }
    };

    const searchPlaces = (q) => {
        if (geoCtrl) geoCtrl.abort();
        geoCtrl = new AbortController();
        const seq = ++geoSeq;
        fetch(GEOCODE + '?query=' + encodeURIComponent(q), { signal: geoCtrl.signal })
            .then((r) => r.ok ? r.json() : [])
            .then((data) => {
                if (seq !== geoSeq) return;
                places = (Array.isArray(data) ? data : []).map((d) => d.value).filter(Boolean).slice(0, 5);
                render(input.value.trim());
            })
            .catch(() => {});
    };

    input.addEventListener('input', () => {
        const q = input.value.trim();
        window.clearTimeout(debounce);
        if (q.length < 2) { places = []; close(); return; }
        render(q);
        if (q.length >= 3) debounce = window.setTimeout(() => searchPlaces(q), 260);
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown' && options.length) { e.preventDefault(); active = (active + 1) % options.length; highlight(); }
        else if (e.key === 'ArrowUp' && options.length) { e.preventDefault(); active = (active - 1 + options.length) % options.length; highlight(); }
        else if (e.key === 'Enter' && options.length) { e.preventDefault(); go(options[active >= 0 ? active : 0].data); }
        else if (e.key === 'Escape') close();
    });

    const highlight = () => options.forEach((o, i) => o.el.classList.toggle('is-active', i === active));

    document.addEventListener('click', (e) => { if (!panel.contains(e.target) && e.target !== input) close(); });
}

async function initMaps() {
    const containers = document.querySelectorAll('[data-at-map]');
    if (!containers.length) return;

    const { default: L } = await import('leaflet');

    containers.forEach((el) => {
        let stations = [], me = null;
        try { stations = JSON.parse(el.dataset.atStations || '[]'); } catch (e) { /* noop */ }
        try { me = el.dataset.atMe ? JSON.parse(el.dataset.atMe) : null; } catch (e) { /* noop */ }
        if (!stations.length && !me) return;

        const map = L.map(el, { scrollWheelZoom: false, attributionControl: true });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap', maxZoom: 19,
        }).addTo(map);

        const pts = [];
        stations.forEach((s) => {
            if (typeof s.lat !== 'number' || typeof s.lon !== 'number') return;
            const marker = L.circleMarker([s.lat, s.lon], {
                radius: 8, color: '#fff', weight: 2, opacity: 0.9,
                fillColor: AQI[s.level] || 'rgba(255,255,255,.6)', fillOpacity: 1,
            }).addTo(map);
            if (s.name) marker.bindPopup(s.name + (s.code ? ' · ' + s.code : ''));
            if (s.url) marker.on('click', () => { window.location = s.url; });
            pts.push([s.lat, s.lon]);
        });
        if (me && typeof me.lat === 'number') {
            L.circleMarker([me.lat, me.lon], { radius: 7, color: '#fff', weight: 3, fillColor: '#4a86c5', fillOpacity: 1 })
                .addTo(map).bindPopup('Dein Standort');
            pts.push([me.lat, me.lon]);
        }
        if (pts.length === 1) map.setView(pts[0], 12);
        else if (pts.length) map.fitBounds(pts, { padding: [28, 28], maxZoom: 13 });
    });
}

function initTimeAgo() {
    const fmt = (ts) => {
        const diff = Math.floor(Date.now() / 1000 - ts);
        if (diff < 0) return 'gerade eben';
        if (diff < 90) return 'gerade eben';
        if (diff < 3600) return 'vor ' + Math.floor(diff / 60) + ' Min';
        if (diff < 86400) return 'vor ' + Math.round(diff / 3600) + ' Std';
        return 'vor ' + Math.round(diff / 86400) + ' Tg';
    };
    document.querySelectorAll('[data-time-ago-timestamp]').forEach((el) => {
        const ts = parseInt(el.dataset.timeAgoTimestamp, 10);
        if (ts) { el.textContent = fmt(ts); el.title = el.dataset.timeAgoTitle || el.title; }
    });
}

function initTimeOfDay() {
    const h = new Date().getHours();
    let bucket = 'day';
    if (h >= 21 || h < 5) bucket = 'night';
    else if (h < 8) bucket = 'dawn';
    else if (h >= 18) bucket = 'dusk';
    document.body.classList.add('at-time-' + bucket);
}

function init() { initTimeOfDay(); initGeolocation(); initSearch(); initMaps(); initTimeAgo(); }
if (document.readyState !== 'loading') init();
else document.addEventListener('DOMContentLoaded', init);
