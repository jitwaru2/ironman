
d3.json('http://localhost:8888/backend/api/getAll.php', function(err, data){


  //find amount of instances for specific tag
  //for reduces objects
  //for > foreach searches for tag instances and adds to tag[1]
  // resources = [tag name, instances], [tagname, instances]


  console.log(data[0].entity_overview);
  console.log(data);

  let tags = []
  let resources = []
  for(var i = 0 ; i < 12; i++){
    var entity = [data[i].entity_overview.tags.length, data[i].entity_details];
    tags.push(data[i].entity_overview.tags.length);
    //resources.push(data[i].entity_details);
    resources.push(entity);
  }
  //console.log(tags);
  //console.log(resources)

  var x = d3.scaleLinear()
      .domain([0, d3.max(resources[0])])
      .range([0, 420]);

  d3.select("body")
    .append("svg")
    .attr("class", "chart")

    d3.selectAll("div")
      .data(resources)
    .enter().append("div")
      .style("width", function(d) {
        var width = d[0];
        console.log(width);
        return x(width) })
      // .style("height", 50)
      .style("fill", "blue")
      .text(function(d) { if(!d[1]['shortTitle']){return d[0] + ' Classified'} return d[0] + ' ' + d[1]['shortTitle']; });

  });
