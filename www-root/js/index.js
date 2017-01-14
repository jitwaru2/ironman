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
var x = d3.scaleLinear()
    .range([window.innerHeight / 2, window.innerHeight / 2]);

var y = d3.scaleLinear()
    .range([window.innerHeight, 0]);


d3.select('body').append("svg")
  .attr("class", 'display')
//adds graph to svg
    .append("graph")
      .attr("class", "graph")
//labels for timespan
        .append("text")
          .attr("class", "title")
          .attr("dy", ".71em")
          .text(2015);

  //graph for representing all tags
  d3.select(display).style("color", function(err, data){
    let { type_id, name, date, time, tags, description } = data.entity_overview;



  });


// })
