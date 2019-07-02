calendar_heatmap.create({
    target: '#calendar',
    data: exceedanceData,
    date_var: 'date',
    fill_var: 'value',
    color_scheme: calendar_heatmap.color_ramps.YlGnBu,
    stroke_color: 'whitesmoke',
    date_format: '%Y-%m-%d',
    missing_as_zero: true,
    title: 'Daily Measurements of Something Interesting',
    show_toggle: false,
});
