<?php require_once('../php/app.php'); ?>
<?php
$billID = $_GET['billID'];
$bill = getBill($billID);
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
					<h1><?php echo billNum($bill, true); ?></h1>
					<?php echo billStatus($bill, true); ?>
					<ul>
<?php
$hists = getBillHistory($billID);
foreach ($hists as $hist)
{
	$status = $hist['status'];
	if (strlen($status) > 0)
	{
		$time = $hist['time'];
		echo "<li>$time: $status</li>";
	}
}
$intro = $bill['introduced'];
echo "<li>$intro: INTRODUCED</li>";
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
