var my_data = [
    { day: "2015-01-01", count: 100 },
    { day: "2015-01-02", count: 101 },
    { day: "2015-01-03", count: 102 },
    { day: "2015-01-04", count: 103 },
    { day: "2015-01-05", count: 104 },
    { day: "2015-01-06", count: 105 },
    { day: "2015-01-07", count: 106 },
    { day: "2015-01-08", count: 107 },
    { day: "2015-01-09", count: 108 },
    { day: "2015-01-10", count: 109 },
    { day: "2015-01-11", count: 110 },
    { day: "2015-01-12", count: 111 },
    { day: "2015-01-13", count: 112 },
    { day: "2015-01-14", count: 113 },
    { day: "2015-01-15", count: 114 },
];

calendar_heatmap.create({
    target: "#calendar",
    data: my_data,
    date_var: "day",
    fill_var: "count"
});
