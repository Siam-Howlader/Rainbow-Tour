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

<?php 
  	if (isset($_POST['add_schedule'])) {
		$transport_type      = $conn->real_escape_string($_POST['transport_type']);
		$company             = $conn->real_escape_string($_POST['company']);
		$capacity            = (int) $_POST['capacity'];
		$departure_location  = $conn->real_escape_string($_POST['departure_location']);
		$departure_time      = $conn->real_escape_string($_POST['departure_time']);
		$driver_contact      = $conn->real_escape_string($_POST['driver_contact']);
		
		$conn->query("INSERT INTO transportations (transport_type, company, capacity, departure_location, departure_time, driver_contact) 
						VALUES ('$transport_type', '$company', $capacity, '$departure_location', '$departure_time', '$driver_contact')");
		$transportation_id = $conn->insert_id;

		$package_id  = (int) $_POST['package_id'];
		$start_date  = $conn->real_escape_string($_POST['start_date']);
		$end_date    = $conn->real_escape_string($_POST['end_date']);
		$staff_id    = (int) $_POST['staff_id'];

		$status = ''; 
		$conn->query("INSERT INTO schedules (start_date, end_date, status, package_id, staff_id, transportation_id) 
						VALUES ('$start_date', '$end_date', '$status', $package_id, $staff_id, $transportation_id)");
		echo "<script>location.href = 'schedules.php';</script>";
		exit;
  	}

  	if (isset($_POST['update_schedule'])) {
		$schedule_id = (int) $_POST['schedule_id'];
		$start_date  = $conn->real_escape_string($_POST['start_date']);
		$end_date    = $conn->real_escape_string($_POST['end_date']);
		$staff_id    = (int) $_POST['staff_id'];
		$package_id  = (int) $_POST['package_id'];

		$transport_type      = $conn->real_escape_string($_POST['transport_type']);
		$company             = $conn->real_escape_string($_POST['company']);
		$capacity            = (int) $_POST['capacity'];
		$departure_location  = $conn->real_escape_string($_POST['departure_location']);
		$departure_time      = $conn->real_escape_string($_POST['departure_time']);
		$driver_contact      = $conn->real_escape_string($_POST['driver_contact']);

		$conn->query("UPDATE transportations SET 
						transport_type='$transport_type', company='$company', capacity=$capacity, 
						departure_location='$departure_location', departure_time='$departure_time', 
						driver_contact='$driver_contact' WHERE id=$schedule_id");

		$conn->query("UPDATE schedules SET 
						start_date='$start_date', end_date='$end_date', staff_id=$staff_id, package_id=$package_id
						WHERE id=$schedule_id");

		echo "<script>location.href = 'schedules.php';</script>";
		exit;
  	}

  	if (isset($_GET['delete_schedule'])) {
		$schedule_id = (int) $_GET['delete_schedule'];

		$schedulesResult = $conn->query("SELECT id, transportation_id FROM schedules WHERE id = $schedule_id");

        while ($schedule = $schedulesResult->fetch_assoc()) {
            $schedule_id = $schedule['id'];
            $transportation_id = $schedule['transportation_id'];
    
            $conn->query("DELETE FROM hotel_bookings WHERE schedule_id = $schedule_id");

            $bookingsResult = $conn->query("SELECT id FROM bookings WHERE schedule_id = $schedule_id");
            while ($booking = $bookingsResult->fetch_assoc()) {
                $booking_id = $booking['id'];
                $conn->query("DELETE FROM payments WHERE booking_id = $booking_id");
            }

            $conn->query("DELETE FROM bookings WHERE schedule_id = $schedule_id");
            $conn->query("DELETE FROM schedules WHERE id = $schedule_id");
            $conn->query("DELETE FROM transportations WHERE id = $transportation_id");
        }
		echo "<script>location.href = 'schedules.php';</script>";
		exit;
  	}
?>

<body class="bg-info">
	<?php include ROOT_PATH . 'template/navigation.php'; ?>

	<div class="container admin-container my-5">
		<div class="d-flex mb-4">
			<h2>Admin Panel</h2>
			<div class="d-flex ms-auto justify-content-center">
				<a href="dashboard.php" class="btn-dashboard d-flex align-items-center">
					<i class="fas fa-qrcode me-2"></i>
					<span>Dashboard</span>
				</a>
			</div>
		</div>

		<div class="d-flex justify-content-between align-items-center mb-3">
			<h4 class="mb-0">Schedules</h4>
			<button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal-schedules">
				<i class="fas fa-plus"> Add Schedule</i>
			</button>
		</div>

		<?php
			$packagesResult = $conn->query("SELECT * FROM tour_packages");
			while ($package = $packagesResult->fetch_assoc()):?>
				<div class="card mb-4">
					<div class="card-header">
						<h5 class="mb-0">
							<?= htmlspecialchars($package['name']) ?>
						</h5>
					</div>

					<div class="card-body">
						<?php
							$schedulesResult = $conn->query("SELECT * FROM schedules WHERE package_id = {$package['id']} ORDER BY start_date");
							$i = 1;

							if ($schedulesResult && $schedulesResult->num_rows > 0) {?>
								<table class="table table-bordered table-striped mb-0">
									<thead class="table-dark">
										<tr>
											<th>SL.</th>
											<th>Start Date</th>
											<th>End Date</th>
											<th>Status</th>
											<th>Transportation</th>
											<th>Staff</th>
											<th>Actions</th>
										</tr>
									</thead>

									<tbody>
										<?php
											$schedulesResult = $conn->query("SELECT * FROM schedules WHERE package_id = {$package['id']} ORDER BY start_date");
											$i = 1;

											if ($schedulesResult && $schedulesResult->num_rows > 0) {
												while ($schedule = $schedulesResult->fetch_assoc()):
													$currentDate = new DateTime();
													$startDate   = new DateTime($schedule['start_date']);
													$endDate     = new DateTime($schedule['end_date']);
													if ($startDate > $currentDate) {
														$status = 'Upcoming';
													} elseif ($endDate < $currentDate) {
														$status = 'Completed';
													} else {
														$status = 'Ongoing';
													}
						
													$transport = $conn->query("SELECT * FROM transportations WHERE id = {$schedule['transportation_id']}")->fetch_assoc();
													$staff = $conn->query("SELECT name FROM staffs WHERE id = {$schedule['staff_id']}")->fetch_assoc();?>
													
													<tr>
														<td>
															<?= $i++ ?>
														</td>
														<td>
															<?= htmlspecialchars($schedule['start_date']) ?>
														</td>
														<td>
															<?= htmlspecialchars($schedule['end_date']) ?>
														</td>
														<td>
															<?= $status ?>
														</td>
														<td>
															<?= htmlspecialchars($transport['transport_type']) ?> (
															<?= htmlspecialchars($transport['company']) ?>)
														</td>
														<td>
															<?= htmlspecialchars($staff['name']) ?>
														</td>
														<td>
															<button class="btn btn-sm btn-warning" data-bs-toggle="modal"
																data-bs-target="#editScheduleModal<?= $schedule['id'] ?>">
																<i class="fas fa-pencil-alt"> Edit</i>
															</button>
															<a href="?delete_schedule=<?= $schedule['id'] ?>" class="btn btn-sm btn-danger"
																onclick="return confirm('Delete this schedule?')">
																<i class="fas fa-trash-alt"> Delete</i>
															</a>
														</td>
													</tr>
						
													<div class="modal fade" id="editScheduleModal<?= $schedule['id'] ?>" tabindex="-1">
														<div class="modal-dialog modal-lg modal-dialog-centered">
															<div class="modal-content">
																<form method="POST">
																	<input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">
																	<div class="modal-header">
																		<h5 class="modal-title">Edit Schedule</h5>
																		<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
																	</div>
																	<div class="modal-body row">
																		<div class="col-md-6">
																			<div class="mb-3">
																				<label>Package</label>
																				<select name="package_id" class="form-select" required>
																					<?php
																					$pkgOptions = $conn->query("SELECT id, name FROM tour_packages ORDER BY name");
																					while ($pkg = $pkgOptions->fetch_assoc()):
																					?>
																					<option value="<?= $pkg['id'] ?>"
																						<?=$schedule['package_id']==$pkg['id'] ? 'selected' : '' ?>>
																						<?= htmlspecialchars($pkg['name']) ?>
																					</option>
																					<?php endwhile; ?>
																				</select>
																			</div>
																			<div class="mb-3">
																				<label>Start Date</label>
																				<input type="date" name="start_date"
																					value="<?= $schedule['start_date'] ?>" class="form-control"
																					required>
																			</div>
																			<div class="mb-3">
																				<label>End Date</label>
																				<input type="date" name="end_date" value="<?= $schedule['end_date'] ?>"
																					class="form-control" required>
																			</div>
																		</div>
																		<div class="col-md-6">
																			<div class="mb-3">
																				<label>Staff</label>
																				<select name="staff_id" class="form-select" required>
																					<?php
																					$staffOptions = $conn->query("SELECT * FROM staffs");
																					while ($staffOption = $staffOptions->fetch_assoc()):
																					?>
																					<option value="<?= $staffOption['id'] ?>"
																						<?=$schedule['staff_id']==$staffOption['id'] ? 'selected' : ''
																						?>>
																						<?= htmlspecialchars($staffOption['name']) ?>
																					</option>
																					<?php endwhile; ?>
																				</select>
																			</div>
						
																			<h6>Transportation</h6>
																			<div class="mb-3">
																				<label>Transport Type</label>
																				<input type="text" name="transport_type"
																					value="<?= $transport['transport_type'] ?>" class="form-control"
																					required>
																			</div>
																			<div class="mb-3">
																				<label>Company</label>
																				<input type="text" name="company" value="<?= $transport['company'] ?>"
																					class="form-control">
																			</div>
																			<div class="mb-3">
																				<label>Capacity</label>
																				<input type="number" name="capacity"
																					value="<?= $transport['capacity'] ?>" class="form-control">
																			</div>
																			<div class="mb-3">
																				<label>Departure Location</label>
																				<input type="text" name="departure_location"
																					value="<?= $transport['departure_location'] ?>"
																					class="form-control">
																			</div>
																			<div class="mb-3">
																				<label>Departure Time</label>
																				<input type="datetime-local" name="departure_time"
																					value="<?= $transport['departure_time'] ?>" class="form-control">
																			</div>
																			<div class="mb-3">
																				<label>Driver Contact</label>
																				<input type="text" name="driver_contact"
																					value="<?= $transport['driver_contact'] ?>" class="form-control">
																			</div>
																		</div>
																	</div>
																	<div class="modal-footer">
																		<button name="update_schedule" class="btn btn-primary">Update</button>
																		<button type="button" class="btn btn-secondary"
																			data-bs-dismiss="modal">Cancel</button>
																	</div>
																</form>
															</div>
														</div>
													</div>
												<?php endwhile;
											} else {
												
											}
										?>
									</tbody>
								</table>
							<?php } else {
								echo '<div class="alert alert-warning">No schedules available for this package.</div>';
							}
						?>	
					</div>
				</div>
			<?php endwhile; 
		?>

		<div class="modal fade" id="addModal-schedules" tabindex="-1">
			<div class="modal-dialog modal-lg modal-dialog-centered">
				<div class="modal-content">
					<form method="POST">
						<div class="modal-header">
							<h5 class="modal-title">Add Schedule</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
						</div>
						<div class="modal-body row">
							<div class="col-md-6">
								<div class="mb-3">
									<label>Package</label>
									<select name="package_id" class="form-select" required>
										<?php
										$pkgOptions = $conn->query("SELECT id, name FROM tour_packages ORDER BY name");
										while ($pkg = $pkgOptions->fetch_assoc()):
										?>
										<option value="<?= $pkg['id'] ?>">
											<?= htmlspecialchars($pkg['name']) ?>
										</option>
										<?php endwhile; ?>
									</select>
								</div>
								<div class="mb-3">
									<label>Start Date</label>
									<input type="date" name="start_date" class="form-control" required>
								</div>
								<div class="mb-3">
									<label>End Date</label>
									<input type="date" name="end_date" class="form-control" required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label>Staff</label>
									<select name="staff_id" class="form-select" required>
										<?php
										$staffOptions = $conn->query("SELECT * FROM staffs");
										while ($staffOption = $staffOptions->fetch_assoc()):
										?>
										<option value="<?= $staffOption['id'] ?>">
											<?= htmlspecialchars($staffOption['name']) ?>
										</option>
										<?php endwhile; ?>
									</select>
								</div>
								<h6>Transportation</h6>
								<div class="mb-3">
									<label>Transport Type</label>
									<input type="text" name="transport_type" class="form-control" required>
								</div>
								<div class="mb-3">
									<label>Company</label>
									<input type="text" name="company" class="form-control">
								</div>
								<div class="mb-3">
									<label>Capacity</label>
									<input type="number" name="capacity" class="form-control">
								</div>
								<div class="mb-3">
									<label>Departure Location</label>
									<input type="text" name="departure_location" class="form-control">
								</div>
								<div class="mb-3">
									<label>Departure Time</label>
									<input type="datetime-local" name="departure_time" class="form-control">
								</div>
								<div class="mb-3">
									<label>Driver Contact</label>
									<input type="text" name="driver_contact" class="form-control">
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button name="add_schedule" class="btn btn-success">Add Schedule</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>