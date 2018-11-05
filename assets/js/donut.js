var data = donut_data;

//Sum the event hours
var totalHours =  d3.nest()
	.rollup(function(g) {
		return d3.format(".2f")(d3.sum(g, function (d) {
			return d.value;
		}));
	})
	.entries(data);

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
    .attr('transform', 'translate(150, 150)');

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

//Add label to the middle of the donut for total even hours
var label = g.append("text")
	.attr("class", "donut-label");

label.append("tspan")
	.attr("x", 0)
	.attr("dy", "-.2em")
	.text(totalHours + " hours");

label.append("tspan")
	.attr("x", 0)
	.attr("dy", "1.1em")
	.text("complete");

// building a legend
svg.append('g')
    .attr('class', 'legend')
    .selectAll('text')
    .data(slices)
    .enter()
    .append('text')
    .text(function (d) {
        return d.data.value + ' hours - ' + d.data.label;
    })
    .attr('fill', function (d) {
        return color(d.data.label);
    })
    .attr('y', function (d, i) {
        return 20 * (i + 1);
    })
    .attr('x', 300);
