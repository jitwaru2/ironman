  //subject-over-time

    <table class="bill">
      <tr>
        <th colspan="3" class="description">{{entity_details.subjectMain}}</th>
      </tr>
      <tr>
        <td>{{entity_details.introduced}}</td>
        <th>{{entity_details.shortTitle}}</th>
      </tr>
    </table>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.18.0/vis.min.js"> </script>
<link href="../../../dist/vis-timeline-graph2d.min.css" rel="stylesheet" type="text/css" />

let container = document.append('<div id="timeline"> </div>');

let items = new vis.DataSet([]);

for(var i = 0; i < 40; i++){
  items.push(data[i].entity_details);
}
  var options = {
    // specify a template for the items
    template: template
  };

var timeline = new vis.Timeline(container, items, options);


//ends my code

<!DOCTYPE HTML>
<html>
<head>
  <title>Timeline | Templates</title>

  <!-- load handlebars for templating, and create a template -->
  <script src="http://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.5/handlebars.min.js"></script>
  <script id="item-template" type="text/x-handlebars-template">
    <table class="bill">
      <tr>
        <th colspan="3" class="description">{{entity_details.subjectMain}}</th>
      </tr>
      <tr>
        <td>{{entity_details.introduced}}</td>
        <th>{{entity_details.shortTitle}}</th>
      </tr>
    </table>
  </script>

  <script src="../../../dist/vis.js"></script>
  <link href="../../../dist/vis-timeline-graph2d.min.css" rel="stylesheet" type="text/css" />

  <style type="text/css">
    body, html {
      font-family: sans-serif;
      font-size: 10pt;
    }

    .vis.timeline .item {
      border-color: #acacac;
      background-color: #efefef;
      box-shadow: 5px 5px 10px rgba(128,128,128, 0.3);
    }

    table .description {
      font-style: italic;
    }

    #visualization {
      position: relative;
      overflow: hidden;
    }
  </style>

  <script src="../../googleAnalytics.js"></script>
</head>
<body>
<h4 class="col s3 center-align">
  This example illustrates the subject of a bill and the time it was introduced.
</h4>

<div id="visualization">
  <div class="logo"><img src="#"></div>
</div>

<script type="text/javascript">
  // create a handlebars template
  var source   = document.getElementById('item-template').innerHTML;
  var template = Handlebars.compile(document.getElementById('item-template').innerHTML);

  // DOM element where the Timeline will be attached
  var container = document.getElementById('visualization');
  ajax({
    method: 'GET',
    url:'http://localhost:8888/api/getAll.php',
    success: function(data){
      // Create a DataSet (allows two way data-binding)
      let items = new vis.DataSet([]);

      for(var i = 0; i < 40; i++){
        items.push(data[i].entity_details);
      }
      // Configuration for the Timeline
      var options = {
        template: template
      };
      // Create a Timeline
      var timeline = new vis.Timeline(container, items, options);

    }
  })

</script>
</body>
</html>
