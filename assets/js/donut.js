var data = donut_data;

//Sum the event hours
var totalHours =  d3.nest()
	.rollup(function(g) {
		return d3.format(".2f")(d3.sum(g, function (d) {
			return parseFloat(d.value.replace(/,/g, ''));
		}));
	})
	.entries(data);

var pie = d3.pie()
	.value(function (d) {
		return parseFloat(d.value.replace(/,/g, ''));
	});

var slices = pie(data);

var arc = d3.arc()
	.innerRadius(55)
	.outerRadius(140);

// helper that returns a color based on an ID
var color = d3.scaleOrdinal(d3.schemeCategory10);

var donutSVG = d3.select('.donut svg');

var g = donutSVG.append('g')
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

(function ($) {
	$(document).ready(function () {
		$.each(data,function(i, val){
			var li = $('<div/>').attr('class','legend-item').appendTo('.donut-legend');
			$('<span/>').attr('class','square').css('background-color',color(val.label)).appendTo(li);
			var percentHours = parseFloat(val.value.replace(/,/g, ''))/totalHours*100;
			var label = $('<p/>').appendTo(li).text(percentHours.toFixed(1) + '% - '+val.label + ' (' + val.value +' hours)');;

		});
	});
})(jQuery);
