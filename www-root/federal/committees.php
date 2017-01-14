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
					<h1>Committees</h1>
					<h2>Senate</h2>
					<ul>
<?php
function commList($comms)
{
	foreach ($comms as $comm):
		$count = count(getCommMembers($comm['id']));
		if ($count > 0):
?>
						<li>
							<span><a href="/federal/committee.php?commID=<?php echo $comm['id']; ?>"><?php echo $comm['name']; ?></a></span>
							<ul>
<?php
		endif;
		$subs = getSubcomms($comm['id']);
		foreach ($subs as $sub):
			$count = count(getCommMembers($sub['id']));
			if ($count > 0):
?>
								<li><a href="/federal/committee.php?commID=<?php echo $sub['id']; ?>"><?php echo $sub['name']; ?></a></span>
<?php
			endif;
		endforeach;
?>
							</ul>
						</li>
<?php
	endforeach;
}
$comms = getSupercomms('senate');
commList($comms);
?>
					</ul>
					<h2>House</h2>
					<ul>

<?php
$comms = getSupercomms('house');
commList($comms);
?>
					</ul>
					<h2>Joint</h2>
					<ul>

<?php
$comms = getSupercomms('joint');
commList($comms);
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
