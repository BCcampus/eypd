(function ($) {
    $(document).ready(function () {

        var data = [{"label": "Cat 1", "value": 2},
            {"label": "Cat 2", "value": 5},
            {"label": "Cat 3", "value": 2},
            {"label": "Cat 4", "value": 5},
            {"label": "Cat 5", "value": 2},
            {"label": "Cat 6", "value": 1},
            {"label": "Cat 7", "value": 2},
            {"label": "Cat 8", "value": 5},
            {"label": "Cat 9", "value": 3}
        ];

        var pie = d3.pie()
            .value(function (d) {
                return d.value
            });

        var slices = pie(data);

        var arc = d3.arc()
            .innerRadius(50)
            .outerRadius(90);

// helper that returns a color based on an ID
        var color = d3.scaleOrdinal(d3.schemeCategory10);

        var svg = d3.select('.donut')
            .append('svg')
            .attr("class", "donut");
        var g = svg.append('g')
            .attr('transform', 'translate(200, 75)');

        var arcGraph = g.selectAll('path.slice')
            .data(slices)
            .enter();
        arcGraph.append('path')
            .attr('class', 'slice')
            .attr('d', arc)
            .attr('fill', function (d) {
                return color(d.data.label);
            });

        arcGraph.append("text")
            .attr("transform", function (d) {
                return "translate(" + arc.centroid(d) + ")";
            })

            .attr("dy", "0.35em")
            .text(function (d) {
                return d.data.value
            });
// building a legend
        svg.append('g')
            .attr('class', 'legend')
            .selectAll('text')
            .data(slices)
            .enter()
            .append('text')
            .text(function (d) {
                return '• ' + d.data.label;
            })
            .attr('fill', function (d) {
                return color(d.data.label);
            })
            .attr('y', function (d, i) {
                return 20 * (i + 1);
            });
    });
})(jQuery);