<?php require_once('../php/app.php'); ?>
<html>
	<head>
<?php require_once('../html/head.php'); ?>
	</head>
	<body>
<?php require_once('../html/nav.php'); ?>
		<div class="container">
			<div class="row">
				<div class="col-sm-9">
					<h1>Bills</h1>
					<ul class="nav nav-tabs">
						<li role="presentation" class="active"><a href="#summ" role="tab" data-toggle="tab">Summary</a></li>
						<!-- TODO: fx this -->
						<li role="presentation"><a href="#laws" role="tab" data-toggle="tab">Enacted Laws</a></li>
						<li role="presentation"><a href="#bills" role="tab" data-toggle="tab">Bills</a></li>
						<li role="presentation"><a href="#res" role="tab" data-toggle="tab">Resolutions</a></li>
						<li role="presentation"><a href="#res" role="tab" data-toggle="tab">History</a></li>
					</ul>
					<h2>Lawmaking</h2>
<?php
function statusList($cnum)
{
	global $dbfed;
	$cOrd = $cnum.ordinalIndicator($cnum);
	echo "<h2>$cOrd Congress</h2>";
	//TODO: api this
	$draft = getLawCount($cnum, 'Draft');
	$ready = getLawCount($cnum, 'Ready');
	$active = getLawCount($cnum, 'Active');
	$stalled = getLawCount($cnum, 'Stalled');
	$filib = getLawCount($cnum, 'Stalled:Filibustered');
	$stalledTotal = $stalled + $filib;
	$failed = getLawCount($cnum, 'Failed');
	$passed = getLawCount($cnum, 'Passed');
	$vetosoft = getLawCount($cnum, 'Veto:Soft');
	$vetohard = getLawCount($cnum, 'Veto:Hard');
	$vetopocket = getLawCount($cnum, 'Veto:Pocket');
	$vetoTotal = $vetosoft + $vetohard + $vetopocket;
	$signed = getLawCount($cnum, 'Enacted:Signed');
	$forced = getLawCount($cnum, 'Enacted:Override');
	$inaction = getLawCount($cnum, 'Enacted:Inaction');
	$enacted = $signed + $forced + $inaction;
	echo '<ul>';
	echo "<li>$draft bills introduced and referred to committee</li>";
	echo "<li>$ready bills ready for debate</li>";
	echo "<li>$active bills active in Congress</li>";
	echo "<li>$stalledTotal bills stalled (including $filib filibustered)</li>";
	echo "<li>$failed bills failed to pass</li>";
	echo "<li>$passed bills passed Congress and are awaiting Presidential review</li>";
	echo "<li>$vetoTotal bills vetoed (including $vetohard that failed to override, and $vetopocket pocket vetoes)</li>";
	echo "<li>$enacted bills enacted into law (including $forced vetoes overridden by Congress)</li>";
	echo '</ul>';
}
?>
					<div class="row">
						<div class="col-sm-6">
							<?php statusList(114) ?>
						</div>
						<div class="col-sm-6">
							<?php statusList(113) ?>
							<p>All bills not enacted into law expire at the end of this session and must be reintroduced in the next</p>
						</div>
					</div>
					<h2>Other Activities</h2>
				</div>
				<div class="col-sm-3">
					Side Content
				</div>
			</div>
		</div>
	</body>
</html>
