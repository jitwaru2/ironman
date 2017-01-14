<?php require_once('./php/app.php'); ?>
<?php
$state = isset($_GET['state']) ? $_GET['state'] : "NY";
$dist =  isset($_GET['dist']) ? $_GET['dist'] : 10;
$meta = getStateMeta($state);
?>
<html>
	<head>
<?php require_once('./html/head.php'); ?>
	</head>
	<body>
<?php require_once('./html/nav.php'); ?>
		<div class="container">
			<div class="row">
				<div class="col-sm-9">
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="cur">
							<h1>United States Congress</h1>
							<h2>Congress</h2>
							<h3>Representative</h3>
<?php
$rep = getRepCur($state, $dist);
$terms = getLegTerms($rep['id']);
$terms = collapseLegTerms($terms);
//legFed($rep, $terms);
var_export($rep);
?>
							<p><a href="">See Senators <i class="fa fa-angle-right"></i></a>&emsp;<a href=""><small class="text-muted">Why should I contact a representative instead of a senator?</small></a></p>
							<h2><?php echo $meta['legname']; ?></h2>
							<h3><?php echo $meta['upperrep']; ?></h3>
							<h3><?php echo $meta['lowerrep']; ?></h3>
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
