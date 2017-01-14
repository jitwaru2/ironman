<?php require_once('../php/app.php'); ?>
<?php require_once('../html/leg_bio.php'); ?>
<?php require_once('../html/bills_table.php'); ?>
<?php
$legID = isset($_GET['legID']) ? $_GET['legID'] :'N000002';
$leg = getLeg($legID);

$isHouse = $leg['chamber'] == 'house';
$title = $isHouse ? 'Rep.' : 'Sen.';
$firstName = isset($leg['nickname']) ? $leg['nickname'] : $leg['first_name'];
?>
<html>
	<head>
<?php require_once('../html/head.php'); ?>
		</style>
	</head>
	<body>
<?php require_once('../html/nav.php'); ?>
		<div class="container">
			<div class="row">
				<div class="col-sm-9">
<?php
$terms = getLegTerms($leg['id']);
//$terms = collapseLegTerms($terms);
legFed($leg, $terms);
?>
					<div class="row">
						<div class="col-md-12">
							<h2>Committees</h2>
<?php
$comms = getCommsForLeg($leg['id']);
foreach ($comms as $comm)
{
	$name = $comm['name'];
	$title = isset($comm['title']) ? ', '.$comm['title'] : '';
?>
							<a href=""><?php echo $name; ?></a><?php echo $title; ?><br>
<?php
}
?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<h2>Sponsored Legislation</h2>
<?php
$bills = getBillsBySponsor($leg['id']);
billsTable($bills);
?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
						<h2>Votes</h2>
						<table class="table table-compact table-hover">
<?php
$votes = getVotesForLeg($leg['id']);
foreach ($votes as $vote)
{
	$voteicon = ['Y' => '<i class="fa fa-thumbs-up" style="color: green;"></i>', 'N' => '<i class="fa fa-thumbs-down" style="color: darkred;"></i>', 'X' => '', 'P' => '', '*' => ''];
?>
							<tr>
								<td class="text-nowrap"><?php echo date('Y-m-d', strtotime($vote['time'])); ?></td>
								<td class="text-nowrap"><?php echo $vote['vote_type']; ?></td>
								<td class="text-nowrap"><a href=""><?php echo $vote['ref_bill']; ?></a></td>
								<td><?php echo $vote['question']; ?></td>
								<td class="text-nowrap"><?php echo $voteicon[$vote['vote']]; ?></td>
								<td class="text-nowrap"><?php echo $vote['result']; ?></td>
							</tr>
<?php
}
?>
						</table>
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				Side Content
			</div>
		</div>
	</body>
</html>
