<?php require_once('../php/app.php'); ?>
<?php
$legID = isset($_GET['legID']) ? $_GET['legID'] :'N000002';
$leg = getLeg($legID);

$isHouse = $leg['chamber'] == 'house';
$title = $isHouse ? 'Rep.' : 'Sen.';
$name = legName($leg);
$terms = getLegTerms($leg['id']);
$terms = collapseLegTerms($terms);
?>
<html>
	<head>
<?php require_once('../html/head.php'); ?>
	</head>
	<body>
<?php require_once('../html/nav.php'); ?>
		<div class="container">
			<div class="row">
				<div class="col-sm-9">
<?php
?>
					<div class="row">
						<div class="col-md-12">
							<h1><?php echo $title; ?> <?php echo $name; ?> <small><?php echo legTag($leg); ?></small></h1>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2 text-center">
							<!-- TODO: caching of images -->
							<!-- <img src="/bioimg/<?php echo $leg['id']; ?>.jpg" class="img-responsive"> -->
							<img src="http://bioguide.congress.gov/bioguide/photo/<?php echo $leg['id'][0]; ?>/<?php echo $leg['id']; ?>.jpg" class="img-responsive">
						</div>
						<div class="col-md-5">
							<address>
								<?php echo $leg['office']; ?><br>
								Washington, DC <?php if ($isHouse): ?>20515<?php else: ?>20510<?php endif; ?><br>
								<br>
								<i class="fa fa-lg fa-fw fa-phone"></i> <a href="tel:<?php echo $leg['phone']; ?>"><?php echo $leg['phone']; ?></a><br>
								<i class="fa fa-lg fa-fw fa-fax"></i> <a href=""><?php echo $leg['fax']; ?></a><br>
								<!-- <i class="fa fa-lg fa-fw fa-envelope-o"></i> <a href="mailto:Rep.Nadler@opencongress.org">Rep.Nadler@opencongress.org</a><br> -->
								<i class="fa fa-lg fa-fw fa-home"></i> <a href="<?php echo $leg['website']; ?>"><?php echo $leg['website']; ?></a><br>
								<br>
								<i class="fa fa-lg fa-fw fa-twitter"></i> <a href="http://twitter.com/<?php echo $leg['twitter']; ?>">@<?php echo $leg['twitter']; ?></a><br>
								<i class="fa fa-lg fa-fw fa-facebook"></i> <a href="http://www.facebook.com/<?php echo $leg['facebook']; ?>/"><?php echo $leg['facebook']; ?></a><br>
								<i class="fa fa-lg fa-fw fa-youtube"></i> <a href="http://www.youtube.com/user/<?php echo $leg['youtube']; ?>"><?php echo $leg['youtube']; ?></a><br>
							</address>
						</div>
						<div class="col-md-5">
							<dl>
								<dt>Birthdate</dt>
								<dd><?php echo $leg['birthdate']; ?></dd>
								<dt>Electoral History</dt>
								<dd>
<?php
foreach ($terms as $term)
{
	$isHouse = $term['chamber'] == 'house';
	$p = $leg['party'] == 'O' ? $leg['partyName'] : $leg['party']; //TODO: lib this??
	$summary = $isHouse ? 'Rep. ' : 'Sen. '; //($term['rank'] == 'junior' ? 'Jr. Sen. ' : 'Sr. Sen. ');
	$summary .= $p.'-';
	$summary .= $term['state'].' ';
	if ($isHouse)
		$summary .= $term['district'].ordinalIndicator($term['district']);
	$start = date_create($term['start']);
	$end = date_create($term['end']);
	$end->sub(new DateInterval('P10D'));
	
	$titleStart = '"'.$term['start'].'"';
	$titleEnd = '"'.$term['end'].'"';
	$startY = $start->format('Y');
	$endY = $end->format('Y');
	echo "<span title=$titleStart>$startY</span>&mdash;<span title=$titleEnd>$endY</span> $summary<br>";
}
?>
								</dd>
								<dt>Reelection</dt>
								<dd>?</dd>
							</dl>
						</div>
					</div>
					<br>
					<ul class="nav nav-tabs">
						<li role="presentation" class="active"><a href="#summ" role="tab" data-toggle="tab">Summary</a></li>
						<!-- TODO: fx this -->
						<li role="presentation"><a href="#cur" role="tab" data-toggle="tab">2015-2016</a></li>
						<li role="presentation"><a href="#prev" role="tab" data-toggle="tab">2013-2014</a></li>
						<li role="presentation"><a href="#hist" role="tab" data-toggle="tab">History</a></li>
					</ul>
					<br>
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="summ">
							<h2 class="hidden">Summary</h2>
							<h3>Stats <small>beta</small></h3>
<?php
$votesMissed = getScore(114, $legID, 'missedVotes');
$votesMissedClose = getScore(114, $legID, 'missedVotesClose');
$billsIntroduced = getScore(114, $legID, 'billsIntroduced');
$billsEnacted = getScore(114, $legID, 'billsEnacted');
?>
							<p><?php echo $votesMissed; ?> missed votes (<?php echo $votesMissedClose; ?> were close)</p>
							<p><?php echo $billsIntroduced; ?> bills introduced and <?php echo $billsEnacted; ?> bills enacted (<?php echo ($billsEnacted / $billsIntroduced) * 100; ?>%)</p>
							<h3>Committee Leadership</h3>
<?php
$comms = getCommsForLeg($legID);
$allSupers = array();
$allSubs = array();
$rankSupers = array();
$rankSubs = array();
foreach ($comms as $comm)
{
	if (isset($comm['parent']))
	{
		$allSubs[] = $comm;
		if (isset($comm['title']))
		{
			$rankSupers[] = getCommForLeg($legID, $comm['parent']); //NOTE: needs to be identical to existing supercomm objects
			$rankSubs[] = $comm;
		}
	}
	else
	{
		$allSupers[] = $comm;
		if (isset($comm['title']))
			$rankSupers[] = $comm;
	}
}
$rankSupers = array_unique($rankSupers, SORT_REGULAR); //NOTE: SORT_REGULAR because it's an array of arrays

function committeeList($supers, $subs)
{
?>
							<ul>
<?php
	foreach ($supers as $super)
	{
		$name = $super['name'];
		$title = isset($super['title']) ? ', '.$super['title'] : '';
?>
								<li>
									<span><a href="/federal/committee.php?commID=<?php echo $super['id']; ?>"><?php echo $name; ?></a><?php echo $title; ?></span>
									<ul>
<?php
		foreach ($subs as $sub)
		{
			if ($sub['parent'] != $super['id'])
				continue;
			$name = $sub['name'];
			$title = isset($sub['title']) ? ', '.$sub['title'] : '';
?>
										<li><a href="/federal/committee.php?commID=<?php echo $sub['id']; ?>"><?php echo $name; ?></a><?php echo $title; ?></li>
<?php
		}
?>
									</ul>
								</li>
<?php
	}
?>
							</ul>
<?php
}
committeeList($rankSupers, $rankSubs);
?>
							<h3>Active Sponsored Legislation</h3>
							<table class="table table-compact table-hover">
								<thead>
									<tr>
										<th>Number</th>
										<th>Title</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
<?php
$bills = getBillsBySponsor($legID);
foreach ($bills as $bill)
{
	//TODO: sql?
	if ($bill['congress'] != 114) continue;
	if ($bill['status'] == 'Draft') continue;
?>
									<tr>
										<td class="text-nowrap"><a href="/federal/bill.php?billID=<?php echo $bill['id']; ?>"><?php echo billNum($bill); ?></a></td>
										<td><?php 
										if (count($bill['shortTitle']))
											echo '<strong>'.$bill['shortTitle'].'</strong><br><small>'.$bill['officialTitle'].'</small>'; 
										else
											echo '<small>'.$bill['officialTitle'].'</small>';
										?></td>
										<td class="text-nowrap"><?php echo billStatus($bill); ?></td>
									</tr>
<?php
}
?>
								</tbody>
							</table>
							<h3>Important Votes</h3>
						</div>
						<div role="tabpanel" class="tab-pane" id="cur">
							<h2 class="hidden">Current Session</h2>
							<h3>Committees</h3>
<?php committeeList($allSupers, $allSubs); ?>
							<h3>Sponsored Legislation</h3>
							<table class="table table-compact table-hover">
								<thead>
									<tr>
										<th>Number</th>
										<th>Title</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
<?php
foreach ($bills as $bill)
{
	//TODO: sql?
	if ($bill['congress'] != 114) continue;
?>
									<tr>
										<td class="text-nowrap"><a href="/federal/bill.php?billID=<?php echo $bill['id']; ?>"><?php echo billNum($bill); ?></a></td>
										<td><?php 
										if (count($bill['shortTitle']))
											echo '<strong>'.$bill['shortTitle'].'</strong><br><small>'.$bill['officialTitle'].'</small>'; 
										else
											echo '<small>'.$bill['officialTitle'].'</small>';
										?></td>
										<td class="text-nowrap"><?php echo billStatus($bill); ?></td>
									</tr>
<?php
}
?>
								</tbody>
							</table>
							<h3>Votes</h3>
							<table class="table table-compact table-hover">
<?php
$votes = getVotesForLeg($legID);
foreach ($votes as $vote)
{
	$voteicon = ['Y' => '<i class="fa fa-thumbs-up" style="color: green;"></i>', 'N' => '<i class="fa fa-thumbs-down" style="color: darkred;"></i>', 'X' => '', 'P' => '', '*' => ''];
?>
								<tr>
									<td class="text-nowrap"><?php echo date('Y-m-d', strtotime($vote['time'])); ?></td>
									<td class="text-nowrap"><?php echo $vote['voteType']; ?></td>
									<td class="text-nowrap"><?php echo $vote['ref_bill']; ?></a></td>
									<td><?php echo $vote['question']; ?></td>
									<td class="text-nowrap"><?php echo $voteicon[$vote['vote']]; ?></td>
									<td class="text-nowrap"><?php echo $vote['result']; ?></td>
								</tr>
<?php
}
?>
							</table>
						</div>
						<!-- TODO: DRY with cur -->
						<div role="tabpanel" class="tab-pane" id="prev">
							<h2 class="hidden">Previous Session</h2>
<!-- 							<h3>Committees</h3>
<?php committeeList($allSupers, $allSubs); ?> -->
							<h3>Sponsored Legislation</h3>
							<table class="table table-compact table-hover">
								<thead>
									<tr>
										<th>Number</th>
										<th>Title</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
<?php
foreach ($bills as $bill)
{
	//TODO: sql?
	if ($bill['congress'] != 113) continue;
?>
									<tr>
										<td class="text-nowrap"><a href="/federal/bill.php?billID=<?php echo $bill['id']; ?>"><?php echo billNum($bill); ?></a></td>
										<td><?php 
										if (count($bill['shortTitle']))
											echo '<strong>'.$bill['shortTitle'].'</strong><br><small>'.$bill['officialTitle'].'</small>'; 
										else
											echo '<small>'.$bill['officialTitle'].'</small>';
										?></td>
										<td class="text-nowrap"><?php echo billStatus($bill); ?></td>
									</tr>
<?php
}
?>
								</tbody>
							</table>
							<h3>Votes</h3>
							<table class="table table-compact table-hover">
<?php
$votes = getVotesForLeg($legID);
foreach ($votes as $vote)
{
	$voteicon = ['Y' => '<i class="fa fa-thumbs-up" style="color: green;"></i>', 'N' => '<i class="fa fa-thumbs-down" style="color: darkred;"></i>', 'X' => '', 'P' => '', '*' => ''];
?>
								<tr>
									<td class="text-nowrap"><?php echo date('Y-m-d', strtotime($vote['time'])); ?></td>
									<td class="text-nowrap"><?php echo $vote['voteType']; ?></td>
									<td class="text-nowrap"><?php echo $vote['ref_bill']; ?></a></td>
									<td><?php echo $vote['question']; ?></td>
									<td class="text-nowrap"><?php echo $voteicon[$vote['vote']]; ?></td>
									<td class="text-nowrap"><?php echo $vote['result']; ?></td>
								</tr>
<?php
}
?>
							</table>
						</div>
						<div role="tabpanel" class="tab-pane" id="hist">
							<h2 class="hidden">History</h2>
							<h3>Sponsored Legislation</h3>
							<table id="histSpon" class="table table-compact table-hover">
								<thead>
									<tr>
										<th>Number</th>
										<th>Title</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
<?php
foreach ($bills as $bill)
{
?>
									<tr>
										<td class="text-nowrap"><a href="/federal/bill.php?billID=<?php echo $bill['id']; ?>"><?php echo billNum($bill, true); ?></a></td>
										<td><?php 
										if (count($bill['shortTitle']))
											echo '<strong>'.$bill['shortTitle'].'</strong><br><small>'.$bill['officialTitle'].'</small>'; 
										else
											echo '<small>'.$bill['officialTitle'].'</small>';
										?></td>
										<td class="text-nowrap"><?php echo billStatus($bill, true); ?></td>
									</tr>
<?php
}
?>
								</tbody>
							</table>
							<script>
								$(document).ready(function()
								{
									$('#histSpon').DataTable(
									{
										"ordering": false,
										"language": {"search": "Filter:"}
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
