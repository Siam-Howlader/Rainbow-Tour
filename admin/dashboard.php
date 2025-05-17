<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/rainbow-tour/utils/constants.php'; ?>
<?php include ROOT_PATH . 'template/header.php'; ?>
<?php include ROOT_PATH . 'db/connect-db.php'; ?>
<?php include ROOT_PATH . 'auth/connect-session.php'; ?>
<?php include ROOT_PATH . 'auth/manage-access.php'; ?>

<?php
	if (!$admin) {
		header("Location: " . BASE_URL . "tourist/index.php");
		exit();
	}
?>

<body class="bg-info">
	<?php include ROOT_PATH . 'template/navigation.php'; ?>
	
	<div class="container dashboard my-5">
		<h2 class="mb-4">Admin Dashboard</h2>
		<div class="row row-cols-1 row-cols-md-3 g-4">
			<div class="col">
				<a href="destinations.php" class="text-decoration-none">
					<div class="card card-hover shadow-sm">
						<div class="ratio ratio-1x1">
							<div class="card-body d-flex justify-content-center align-items-center">
								<h5 class="card-title text-center">Destinations</h5>
							</div>
						</div>
					</div>
				</a>
			</div>

			<div class="col">
				<a href="packages.php" class="text-decoration-none">
					<div class="card card-hover shadow-sm">
						<div class="ratio ratio-1x1">
							<div class="card-body d-flex justify-content-center align-items-center">
								<h5 class="card-title text-center">Packages</h5>
							</div>
						</div>
					</div>
				</a>
			</div>

			<div class="col">
				<a href="schedules.php" class="text-decoration-none">
					<div class="card card-hover shadow-sm">
						<div class="ratio ratio-1x1">
							<div class="card-body d-flex justify-content-center align-items-center">
								<h5 class="card-title text-center">Schedules</h5>
							</div>
						</div>
					</div>
				</a>
			</div>

			<div class="col">
				<a href="hotels.php" class="text-decoration-none">
					<div class="card card-hover shadow-sm">
						<div class="ratio ratio-1x1">
							<div class="card-body d-flex justify-content-center align-items-center">
								<h5 class="card-title text-center">Hotels</h5>
							</div>
						</div>
					</div>
				</a>
			</div>
		</div>
	</div>
</body>