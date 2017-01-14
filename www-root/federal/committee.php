<?php require_once('../php/app.php'); ?>
<?php
$commID = $_GET['commID'];
$comm = getComm($commID);
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
					<h1>Committee</h1>
<?php
$super = $comm;
$subs = array();
if (isset($comm['parent']))
{
	$super = getComm($comm['parent']);
	$subs = array($comm);
}
else
{
	$subs = getSubcomms($commID);
}

$href = '"/federal/committee.php?commID='.$super['id'].'"';
$name = $super['name'];
if ($super == $comm)
	echo "<span>$name</span>";
else
	echo "<a href=$href>$name</a>";
?>
					<ul>
<?php
foreach ($subs as $sub)
{
	$href = '"/federal/committee.php?commID='.$sub['id'].'"';
	$name = $sub['name'];
	if ($super == $comm)
		echo "<li><a href=$href>$name</a></li>";
	else
		echo "<li>$name</li>";
}
?>
					</ul>
					<p><?php echo $comm['description']; ?></p>
					<p><a href="/federal/committees.php"><i class="fa fa-angle-left"></i> All Committees</a></p>
					<h2>Members</h2>
					<ul>
<?php
$mems = getCommMembers($commID);
foreach ($mems as $mem)
{
	$href = '"/federal/legislator.php?legID='.$mem['id'].'"';
	//TODO: full/nickname in db?
	$name = legName($mem);
	$tag = legTag($mem);
	$rank = isset($mem['title']) ? '('.$mem['title'].')' : '';
	echo "<li><a href=$href>$name</a>, $tag $rank</span>";
}
?>
					</ul>
				</div>
				<div class="col-sm-3">
					Side Content
				</div>
			</div>
		</div>
	</body>
</html>
