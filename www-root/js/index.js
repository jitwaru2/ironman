// $(document).ready(function(){
  console.log("D3 script loaded")

  //  NAME
  //  START DATE
  //  END DATE
  //  TAGS
  //  TYPE: BILL, AMENDMENT
  const data = {
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

// http://bl.ocks.org/mbostock/4062085

var x = d3.scaleLinear()
    .range([window.innerHeight / 2, window.innerHeight / 2]);

var y = d3.scaleLinear()
    .range([window.innerHeight, 0]);


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

d3.json(url, (err, data) => {
  data.forEach(function(d){
    parseInt(d.datetime);
  })

  //gets value range for datetime
  let start = d3.min(data, function(d) { return d.datetime; });
  let end = d3.max(data, function(d) { return d.year; });

  //domain based of range
  // x.domain([year1 - age1, year1]);
  // y.domain([0, d3.max(data, function(d) { return d.people; })]);

  // Produce a map from year and birthyear to [male, female].
  data = d3.nest()
      .key(function(d) { return d.datetime; })
      .key(function(d) { return d.end - d.start; })
      .rollup(function(v) { return v.map(function(d) { return d.people; }); })
      .map(data);

  // Set axis to show the population values.
  // svg.append("graph")
  //     .attr("class", "y axis")
  //     .call(yAxis)
  //   .selectAll("graph")
  //   //wtf is this?
  //   .filter(function(value) { return !value; })
  //     .classed("zero", true);

})

  // (data) => {
  //graph for representing all tags
  d3.select(display).style("color", function(err, data){
    let { type_id, name, date, time, tags, description } = data.entity_overview;
    console.log(name);
  });
// }


// })
