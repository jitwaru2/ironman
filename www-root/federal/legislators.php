<?php require_once('../php/app.php'); ?>
<?php
$state = isset($_GET['state']) ? $_GET['state'] : "NY";
$dist =  isset($_GET['dist']) ? $_GET['dist'] : 10;
?>
<html>
	<head>
<?php require_once('../html/head.php'); ?>
	</head>
	<body>
<?php require_once('../html/nav.php'); ?>
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<h1>Congresspeople</h1>
					<ul class="nav nav-tabs">
						<li role="presentation"class="active"><a href="#cur" role="tab" data-toggle="tab">Current</a></li>
						<li role="presentation"><a href="#fmr" role="tab" data-toggle="tab">Former</a></li>
					</ul>
					<br>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-9">
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="cur">
							<table id="curTable" class="table" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th></th>
										<th>Name</th>
										<th>Chamber</th>
										<th>Party</th>
										<th>State</th>
										<th>District</th>
									</tr>
								</thead>
								<tbody>
<?php
$curLegs = getLegsCur();
foreach ($curLegs as $leg)
{
?>
									<tr>
										<td><a href="/federal/legislator.php?legID=<?php echo $leg['id']; ?>"><img src="http://bioguide.congress.gov/bioguide/photo/<?php echo $leg['id'][0]; ?>/<?php echo $leg['id']; ?>.jpg" style="width: 50;"></a></td>
										<td><a href="/federal/legislator.php?legID=<?php echo $leg['id']; ?>"><?php echo legName($leg); ?></a>, <?php echo legTag($leg); ?>
										<br><?php echo $leg['role']; ?></td>
										<td><?php echo $leg['chamber']; ?></td>
										<td><?php echo $leg['partyName']; ?></td>
										<td><?php echo $leg['state']; ?></td>
										<td><?php echo $leg['district']; ?></td>
									</tr>
<?php
}
?>
								</tbody>
							</table>
							<script>
								$(document).ready(function()
								{
									$('#curTable').DataTable(
									{
										"ordering": false,
										"language": {"search": "Filter:"}
									});
								});
							</script>
						</div>
						<div role="tabpanel" class="tab-pane" id="fmr">
							<table id="fmrTable" class="table" cellspacing="0" width="100%">
							</table>
							<script>
								var dataSet =
								[
<?php
$fmrLegs = getLegsFmr();
foreach ($fmrLegs as $leg)
{
?>
									['<?php echo $leg['id']; ?>',"<?php echo $leg['firstName']; ?> <?php echo $leg['lastName']; ?>, <?php echo legTag($leg); ?>", '<?php echo $leg['chamber']; ?>', '<?php echo $leg['partyName']; ?>', '<?php echo $leg['state']; ?>', '<?php echo $leg['district']; ?>'],
<?php
}
?>
								];
								$(document).ready(function()
								{
									$('#fmrTable').DataTable(
									{
        								"data": dataSet,
										"ordering": false,
										"columns":
										[
											{ title: "ID" },
								            { title: "Name" },
								            { title: "Chamber" },
								            { title: "Party" },
								            { title: "State" },
								            { title: "District" }
										]
									});
								});
							</script>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					Side Content
				</div>
			</div>
		</div>
	</body>
</html>
