<<<<<<< HEAD

d3.json('http://localhost:8888/api/getAll.php', function(err, data){
  // var data = d3.range(1000).map(d3.randomBates(10));

  console.log(data[0].entity_overview);
  console.log(data);

  let tags = []
  let resources = []
  for(var i = 0 ; i < 20; i++){
    tags.push(data[i].entity_overview.tags.length);
    resources.push(data[i].entity_details);
  }
  // data.forEach(function(obj){
  //   tags.push(obj.entity_overview.tags.length);

  // });
  console.log(tags);
  var tagCount = tags.keys.length;
  console.log(tagCount)


  var formatCount = d3.format(",.0f");

  var svg = d3.select("body").append('svg'),
      margin = {top: 10, right: 30, bottom: 30, left: 30},
      width = +svg.attr("width"), //- margin.left - margin.right,
      height = +svg.attr("height"), //- margin.top - margin.bottom,
      g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");

  var x = d3.scaleLinear()
      .rangeRound([0, width]);

  var bins = d3.histogram()
      .domain(x.domain())
      .thresholds(x.ticks(20))
      (tags);

  var y = d3.scaleLinear()
      .domain([0, d3.max(bins, function(d) { return d.length; })])
      .range([height, 0]);

  var bar = g.selectAll(".bar")
    .data(bins)
    .enter().append("g")
      .attr("class", "bar")
      // .attr("transform", function(d) { return "translate(" + x(d.x0) + "," + y(d.length) + ")"; });

  bar.append("rect")
      .attr("x", 1)
      .attr("width", 20)
      .attr("height", function(d){ return height - y(d.length) });

  resources.forEach(function(obj){
    bar.append("text")
        .attr("dy", ".75em")
        .attr("y", 6)
        .attr("x", (x(bins[0].x1) - x(bins[0].x0)) / 2)
        .attr("text-anchor", "middle")
        .text(function(d) { return obj.subjectMain; });
  });

  g.append("g")
      .attr("class", "axis axis--x")
      .attr("transform", "translate(0," + height + ")")
      .call(d3.axisBottom(x));


});
=======
window.ironman = {};

window.ironman.init = function () {
	$('.datepicker').pickadate();

	$('.filter-search').click(function () {
		// pull data from filter pane input elements
		var jsonRequestData = {};
		appendIfNonNull(jsonRequestData, "date_start", getDateStart());
		appendIfNonNull(jsonRequestData, "date_end", getDateEnd());
		appendIfNonNull(jsonRequestData, "tags", getTags());
		appendIfNonNull(jsonRequestData, "names", getNames());

		// turn into request object
		var urlData = $.param(jsonRequestData);

		// call d3
		window.alert(urlData);
	});

	function appendIfNonNull (obj, key, val) {
		if(val != null){
			obj[key] = val;
		}
	}

	function getDateStart () {
		var rawVal = $('.filter-input-date-start').val();
		var formattedVal = moment(rawVal, "DD MMMM, YYYY").format('YYYY-MM-DD');
		return formattedVal;
	}

	function getDateEnd () {
		var rawVal = $('.filter-input-date-end').val();

		if(!rawVal){
			return null;
		}

		var formattedVal = moment(rawVal, "DD MMMM, YYYY").format('YYYY-MM-DD');
		return formattedVal;
	}

	function getTags () {
		var rawVal = $('.filter-input-tags').materialtags('items');
		return rawVal;
	}

	function getNames () {
		var rawVal = $('.filter-input-names').materialtags('items');
		return rawVal;
	}
};

window.ironman.init();
>>>>>>> 132648d961000347f4b6bbbf9eafc15cd8d45400
