




// $(document).ready(function(){
  console.log("D3 script loaded")

  //  NAME
  //  START DATE
  //  END DATE
  //  TAGS
  //  TYPE: BILL, AMENDMENT

  // birthyear: year1: start year2: end
  const sample = {
          "entity_overview": {
            "type_id": 4,
            "name": "On Passage: H R 5303 Water Resources Development Act",
            "datetime": "2016-09-28 18:20:00",
            "tags": [
              {
                "name": "environment"
              }
            ],
            "description": null
          }
        }

        //count amount of entries for year, create to graph
        //break down entries per tag, create segmented graph

//timeline docs, doesn't need full functionality
// http://bl.ocks.org/mbostock/4062085

let margin = {top: 20, right: 40, bottom: 30, left: 40},
    width = 960 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom,
    barWidth = Math.floor(width / 19) - 1;

const x = d3.scaleLinear()
    .range([barWidth / 2, width - barWidth / 2]);

const y = d3.scaleLinear()
    .range([height, 0]);




// An SVG element with a bottom-right origin.
var svg = d3.select("body").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

// const yAxis = d3.svg.axis()
//     .scale(y)
//     .orient("right")
//     .tickSize(-width)
//     .tickFormat(function(d) { return Math.round(d / 1e6) + "M"; });

d3.select('body').append("svg")
  .attr("class", 'display')
//adds graph to svg
    .append("g")
      .attr("class", "g")
//labels for timespan
        .append("text")
          .attr("class", "title")
          .attr("dy", ".71em")
          .text(2015);

let url = "http://localhost:8888/api/getAll.php"
d3.json(url, (err, data) => {
  data.forEach(function(d){
    parseInt(d.datetime);
  })
  let tagCount = data[tags].keys.length + 1
  //gets value range for datetime
  let start = d3.min(data, function(d) { return d.datetime; });
  let end = d3.max(data, function(d) { return d.datetime; });

  //domain based of range
  // x.domain([year1 - age1, year1]);
  // y.domain([0, d3.max(data, function(d) { return d.people; })]);

  // Produce a map from year and tag to [agro, transpo].
  data = d3.nest()
      .key(function(d) { return d.datetime; })
      .key(function(d) { return d.tags; })
      .rollup(function(v) { return v.map(function(d) { return d.tags; }); })
      .map(data);

  // Set axis to show the population values.
  svg.append("graph")
      .attr("class", "y axis")
      .call(yAxis)
    .selectAll("graph")
    .filter(function(value) { return !value; })
      .classed("zero", true);

  // Add labeled rects for each birthyear (so that no enter or exit is required).
  let tag = tags.selectAll(".tag")
      .data(d3.range(start, end + 1, 5))
    .enter().append("g")
      .attr("class", "tag")
      // .attr("transform", function(tag) { return "translate(" + x(tag) + ",0)"; });

  tag.selectAll("rect")
      .data(function(tag) { return data[datetime][tags] || [0, 'unknown']; })
    .enter().append("rect")
      .attr("x", -barWidth / 2)
      .attr("width", barWidth)
      .attr("y", y)
      .attr("height", function(value) { return height - y(value); });

   // Add labels to show birthyear.
  // birthyear.append("text")
  //     .attr("y", height - 4)
  //     .text(function(birthyear) { return birthyear; });

  // Add labels to show referendum count (separate; not animated).
  // Experimental changes
  svg.selectAll(".tag")
      .data(d3.range(0, tagCount + 1, 5))
    .enter().append("text")
      .attr("class", "tag")
      .attr("x", function() { return x(year - tagCount); })
      .attr("y", height + 4)
      .attr("dy", ".71em")
      .text(function() { return tagCount; });

  // Allow the arrow keys to change the displayed year.
  window.focus();
  d3.select(window).on("keydown", function() {
    switch (d3.event.keyCode) {
      case 37: year = Math.max(year0, year - 10); break;
      case 39: year = Math.min(year1, year + 10); break;
    }
    update();
});

function update() {
    if (!(datetime in data)) return;
    title.text(datetime);

    tag.transition()
        .duration(750)
        .attr("transform", "translate(" + (x(end) - x(year)) + ",0)");

    tags.selectAll("rect")
        .data(function(tag) { return data[datetime][tags] || [0, 'unknown']; })
      .transition()
        .duration(750)
        .attr("y", y)
        .attr("height", function(value) { return height - y(value); });
  }

});


//   // (data) => {
//   //graph for representing all tags
//   d3.select(display).style("color", function(err, data){
//     let { type_id, name, date, time, tags, description } = data.entity_overview;
//     console.log(name);
//   });
// // }


// })
