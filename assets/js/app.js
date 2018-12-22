var map, featureList;

$(window).resize(function() {
    sizeLayerControl();
});

$(document).on("click", ".feature-row", function(e) {
    $(document).off("mouseout", ".feature-row", clearHighlight);
    sidebarClick(parseInt($(this).attr("id"), 10));
});

if ( !("ontouchstart" in window) ) {
    $(document).on("mouseover", ".feature-row", function(e) {
        highlight.clearLayers().addLayer(L.circleMarker([$(this).attr("lat"), $(this).attr("lng")], highlightStyle));
    });
}

$(document).on("mouseout", ".feature-row", clearHighlight);

$("#about-btn").click(function() {
    $("#aboutModal").modal("show");
    $(".navbar-collapse.in").collapse("hide");
    return false;
});

$("#full-extent-btn").click(function() {
    //map.fitBounds(boroughs.getBounds());
    $(".navbar-collapse.in").collapse("hide");
    return false;
});

$("#legend-btn").click(function() {
    $("#legendModal").modal("show");
    $(".navbar-collapse.in").collapse("hide");
    return false;
});

$("#login-btn").click(function() {
    $("#loginModal").modal("show");
    $(".navbar-collapse.in").collapse("hide");
    return false;
});

$("#list-btn").click(function() {
    animateSidebar();
    return false;
});

$("#nav-btn").click(function() {
    $(".navbar-collapse").collapse("toggle");
    return false;
});

$("#sidebar-toggle-btn").click(function() {
    animateSidebar();
    return false;
});

$("#sidebar-hide-btn").click(function() {
    animateSidebar();
    return false;
});

function animateSidebar() {
    $("#sidebar").animate({
        width: "toggle"
    }, 350, function() {
        map.invalidateSize();
    });
}

function sizeLayerControl() {
    $(".leaflet-control-layers").css("max-height", $("#map").height() - 50);
}

function clearHighlight() {
    highlight.clearLayers();
}

function sidebarClick(id) {
    var layer = markerClusters.getLayer(id);
    map.setView([layer.getLatLng().lat, layer.getLatLng().lng], 17);
    layer.fire("click");
    /* Hide sidebar and go to the map on small screens */
    if (document.body.clientWidth <= 767) {
        $("#sidebar").hide();
        map.invalidateSize();
    }
}


var stationLayer = L.featureGroup();
var stationList = [];

function loadStations() {
    var bounds = map.getBounds();
    var defaultStationIcon = this.createStandardStationIcon();

    $.ajax({
        url: 'http://luft.ct/api/station',
        data: {
            north: bounds.getNorth(),
            east: bounds.getEast(),
            south: bounds.getSouth(),
            west: bounds.getWest(),
            remember_stations: true,
            provider_identifier: 'uba_de',
        },
        success: function (result) {
            var i;

            for (i = 0; i < result.length; ++i) {
                var station = result[i];
                var stationCode = station.station_code;

                if (!(stationCode in stationList)) {
                    stationList[stationCode] = station;

                    var marker = L.marker([station.latitude, station.longitude], { icon: defaultStationIcon }).addTo(stationLayer);

                    marker.station = station;

                    marker.on('click', showStationModal);

                    $("#feature-list tbody").append('<tr class="feature-row" id="' + L.stamp(marker) + '" lat="' + marker.getLatLng().lat + '" lng="' +marker.getLatLng().lng + '"><td class="feature-name">' + station.station_code + '</td></tr>');
                }
            }
        },
    });
}

function createStandardStationIcon() {
    var redMarker = L.ExtraMarkers.icon({
        icon: 'fa-thermometer',
        markerColor: 'blue-dark',
        shape: 'circle',
        prefix: 'fa'
    });

    return redMarker;
}

function showStationModal(e) {
    const $marker = e.target;

    $.get('http://luft.ct/api/' + $marker.station.station_code, {}, function(dataList) {
        let content = '<table class="table table-striped table-bordered table-condensed">';

        for (let i = 0; i < dataList.length; ++i) {
            const data = dataList[i];

            content += '<tr><td>' + data.pollutant.short_name_html +'</td><td>' + data.data.value + ' ' + data.pollutant.unit_html + '</td></tr>';
        }
        content += '</table>';

        $('#feature-title').html($marker.station.station_code);
        $('#feature-info').html(content);
        $('#featureModal').modal("show");
        highlight.clearLayers().addLayer(L.circleMarker($marker.getLatLng(), highlightStyle));
    });
}

function syncSidebar() {
    /* Empty sidebar features */
    $("#feature-list tbody").empty();

    stationLayer.eachLayer(function (layer) {
        if (map.hasLayer(stationLayer)) {
            if (map.getBounds().contains(layer.getLatLng())) {
                $("#feature-list tbody").append('<tr class="feature-row" id="' + L.stamp(layer) + '" lat="' + layer.getLatLng().lat + '" lng="' + layer.getLatLng().lng + '"><td style="vertical-align: middle;"><img width="16" height="18" src="assets/img/theater.png"></td><td class="feature-name">' + layer.station.station_code + '</td><td style="vertical-align: middle;"><i class="fa fa-chevron-right pull-right"></i></td></tr>');
            }
        }
    });

    /* Update list.js featureList */
    featureList = new List("features", {
        valueNames: ["feature-name"]
    });
    featureList.sort("feature-name", {
        order: "asc"
    });
}

/* Overlay Layers */
var highlight = L.geoJson(null);
var highlightStyle = {
    stroke: false,
    fillColor: "#00FFFF",
    fillOpacity: 0.7,
    radius: 10
};

/* Single marker cluster layer to hold all clusters */
var markerClusters = new L.MarkerClusterGroup({
    spiderfyOnMaxZoom: true,
    showCoverageOnHover: false,
    zoomToBoundsOnClick: true,
    disableClusteringAtZoom: 16
});

map = L.map('map', {
    zoom: 15,
    layers: [markerClusters, highlight, stationLayer],
    zoomControl: false,
    attributionControl: false,
    maxZoom: 18,
});

L.tileLayer('https://tiles.caldera.cc/wikimedia-intl/{z}/{x}/{y}.png', {
    attribution: 'Wikimedia maps beta | Map data &copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
}).addTo(map);

map.on('load', function(loadEvent) {
    loadStations();
});

map.setView([53.56414, 9.967882]);

/* Layer control listeners that allow for a single markerClusters layer */
map.on("overlayadd", function(e) {
    /*if (e.layer === theaterLayer) {
        markerClusters.addLayer(theaters);
        syncSidebar();
    }*/
    /*if (e.layer === museumLayer) {
        markerClusters.addLayer(museums);
        syncSidebar();
    }*/
});

map.on("overlayremove", function(e) {
    /*if (e.layer === theaterLayer) {
        markerClusters.removeLayer(theaters);
        syncSidebar();
    }*/
    /*if (e.layer === museumLayer) {
        markerClusters.removeLayer(museums);
        syncSidebar();
    }*/
});

/* Filter sidebar feature list to only show features in current map bounds */
map.on('moveend', function (e) {
    loadStations();
    syncSidebar();
});

map.on('zoomend', function (e) {
    loadStations();
    syncSidebar();
});

/* Clear feature highlight when map is clicked */
map.on("click", function(e) {
    highlight.clearLayers();
});

/* Attribution control */
function updateAttribution(e) {
    $.each(map._layers, function(index, layer) {
        if (layer.getAttribution) {
            $("#attribution").html((layer.getAttribution()));
        }
    });
}
map.on("layeradd", updateAttribution);
map.on("layerremove", updateAttribution);

var attributionControl = L.control({
    position: "bottomright"
});
attributionControl.onAdd = function (map) {
    var div = L.DomUtil.create("div", "leaflet-control-attribution");
    div.innerHTML = "<span class='hidden-xs'>Developed by <a href='http://bryanmcbride.com'>bryanmcbride.com</a> | </span><a href='#' onclick='$(\"#attributionModal\").modal(\"show\"); return false;'>Attribution</a>";
    return div;
};
map.addControl(attributionControl);

var zoomControl = L.control.zoom({
    position: "bottomright"
}).addTo(map);

/* GPS enabled geolocation control set to follow the user's location */
var locateControl = L.control.locate({
    position: "bottomright",
    drawCircle: true,
    follow: true,
    setView: true,
    keepCurrentZoomLevel: true,
    markerStyle: {
        weight: 1,
        opacity: 0.8,
        fillOpacity: 0.8
    },
    circleStyle: {
        weight: 1,
        clickable: false
    },
    icon: "fa fa-location-arrow",
    metric: false,
    strings: {
        title: "My location",
        popup: "You are within {distance} {unit} from this point",
        outsideMapBoundsMsg: "You seem located outside the boundaries of the map"
    },
    locateOptions: {
        maxZoom: 18,
        watch: true,
        enableHighAccuracy: true,
        maximumAge: 10000,
        timeout: 10000
    }
}).addTo(map);

/* Larger screens get expanded layer control and visible sidebar */
if (document.body.clientWidth <= 767) {
    var isCollapsed = true;
} else {
    var isCollapsed = false;
}

var groupedOverlays = {
    "Points of Interest": {
        'Messstationen': stationLayer,
    },
    "Reference": {
    }
};

var layerControl = L.control.groupedLayers(groupedOverlays, {
    collapsed: isCollapsed
}).addTo(map);

/* Highlight search box text on click */
$("#searchbox").click(function () {
    $(this).select();
});

/* Prevent hitting enter from refreshing the page */
$("#searchbox").keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$("#featureModal").on("hidden.bs.modal", function (e) {
    $(document).on("mouseout", ".feature-row", clearHighlight);
});

// Leaflet patch to make layer control scrollable on touch browsers
var container = $(".leaflet-control-layers")[0];
if (!L.Browser.touch) {
    L.DomEvent
        .disableClickPropagation(container)
        .disableScrollPropagation(container);
} else {
    L.DomEvent.disableClickPropagation(container);
}
