var data = donut_data;

var pie = d3.pie()
    .value(function (d) {
        return d.value
    });

var slices = pie(data);

var arc = d3.arc()
    .innerRadius(55)
    .outerRadius(140);

// helper that returns a color based on an ID
var color = d3.scaleOrdinal(d3.schemeCategory10);

var svg = d3.select('.donut')
    .append('svg')
    .attr("class", "donut");
var g = svg.append('g')
    .attr('transform', 'translate(400, 150)');

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

// values in donut
/*    .text(function (d) {
        return d.data.value
    });
*/

// building a legend
svg.append('g')
    .attr('class', 'legend')
    .selectAll('text')
    .data(slices)
    .enter()
    .append('text')
    .text(function (d) {
        return d.data.value + ' - ' + d.data.label;
    })
    .attr('fill', function (d) {
        return color(d.data.label);
    })
    .attr('y', function (d, i) {
        return 30 * (i + 1);
    });