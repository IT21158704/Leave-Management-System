<?php

include('../../../config/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Employee') {
    header("Location: ../logout.php");
    exit();
}

$application_id = $_GET['id'];

$id = $_SESSION['user_id'];

// Fetch existing user data
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("Record not found");
}

// Fetch leave application data
$query = "SELECT * FROM leave_applications WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $application_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $application = $result->fetch_assoc();
} else {
    die("Record not found");
}

// Function to fetch user name by user ID
function fetchUserName($user_id, $conn)
{
    $query = "SELECT name FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        return $user['name'];
    }
    return 'Unknown';
}

// Fetch names for replacement, actingOfficer, and supervisingOfficer
$replacement_name = fetchUserName($application['replacement'], $conn);

$file_path = '';

// Prepare the SQL query to get the file path
$sql = "SELECT file_path FROM medicals WHERE application_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $application_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the file path
if ($row = $result->fetch_assoc()) {
    $file_path = $row['file_path'];
}

$conn->close();

?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo htmlspecialchars($_SESSION['role']); ?></title>
    <link rel="stylesheet" href="../../assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="../../assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../../assets/vendors/datatables.net-bs5/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="../../assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="../../assets/js/select.dataTables.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="shortcut icon" href="../../assets/images/favicon.svg" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body>
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
            <a class="navbar-brand brand-logo me-5" href="../../../public/index.php"><img src="../../assets/images/logo.svg" class="me-2"
                    alt="logo" /></a>
            <a class="navbar-brand brand-logo-mini" href="../../../public/index.php"><img src="../../assets/images/logo-mini.svg" alt="logo" /></a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                <span class="icon-menu"></span>
            </button>

            <ul class="navbar-nav navbar-nav-right">
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                <span class="icon-menu"></span>
            </button>
        </div>
    </nav>

    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" href="employee_dashboard.php">
                        <i class="icon-grid menu-icon"></i>
                        <span class="menu-title">Home</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="leave_application.php">
                        <i class="mdi mdi-note-plus-outline menu-icon"></i>
                        <span class="menu-title">Leave Application</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="leave_application_history.php">
                        <i class="mdi mdi-history menu-icon"></i>
                        <span class="menu-title">Leave History</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="leave_requests.php">
                        <i class="mdi mdi-bookmark-outline menu-icon"></i>
                        <span class="menu-title">Leave Requests</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="emergencyLeaves.php">
                        <i class="mdi mdi-alert-octagon-outline menu-icon"></i>
                        <span class="menu-title">Emergency Leave</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="userProfile.php">
                        <i class="icon-paper menu-icon"></i>
                        <span class="menu-title">All Records</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">
                        <i class="icon-head menu-icon"></i>
                        <span class="menu-title">Profile</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">
                        <i class="mdi mdi-logout menu-icon"></i>
                        <span class="menu-title">Logout</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="main-panel">
            <div class="content-wrapper">
                <?php if (!empty($error_message)): ?>
                    <div class="login-footer">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error_message; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($success_message)): ?>
                    <div class="login-footer">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success_message; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                <header>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3>Leave Application #<?php echo htmlspecialchars($application_id); ?></h3>
                        <!-- <a href="#" class="btn btn-primary">History</a> -->
                        <?php
                        if ($application['status'] == 'pending') {
                            echo '<label class="btn btn-warning">Pending</label>';
                        } elseif ($application['status'] == 'approved') {
                            echo '<label class="btn btn-success">Approved</label>';
                        } elseif ($application['status'] == 'rejected') {
                            echo '<label class="btn btn-danger">Rejected</label>';
                        }
                        ?>
                    </div>
                </header>

                <!-- Registration Form -->
                <form method="post" action="" class="needs-validation" novalidate>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" disabled required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="designation">Designation</label>
                            <input type="text" class="form-control" id="designation" name="designation" value="<?php echo htmlspecialchars($user['designation']); ?>" disabled required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="dept">Devision</label>
                            <input type="text" class="form-control" id="dept" name="dept" value="<?php echo htmlspecialchars($user['dept']); ?>" disabled required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="designation">Date</label>
                            <input type="date" id="date" class="form-control" name="date" value="<?php echo $currentDate; ?>" disabled>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="leaveDates">Number of days leave applied for</label>
                            <input type="number" id="leaveDates" class="form-control" value="<?php echo htmlspecialchars($application['leaveDates']); ?>" disabled>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="leaveReason">Reason</label>
                            <input type="text" id="leaveReason" class="form-control" value="<?php echo htmlspecialchars($application['leaveReason']); ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <!-- <label for="fullReason">Reasons for leave</label> -->
                        <textarea class="form-control" id="fullReason" name="fullReason" placeholder="<?php echo htmlspecialchars($application['fullReason']); ?>" disabled></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="firstAppointmentDate">Date of First Appoinment</label>
                            <input type="date" class="form-control" value="<?php echo htmlspecialchars($application['firstAppointmentDate']); ?>" disabled>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="commenceLeaveDate ">Date of Commencing Leave</label>
                            <input type="date" class="form-control" value="<?php echo htmlspecialchars($application['commenceLeaveDate']); ?>" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="resumeDate">Date of Resumption</label>
                            <input type="date" class="form-control" id="resumeDate" value="<?php echo htmlspecialchars($application['resumeDate']); ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="addressDuringLeave">Address During Leave</label>
                        <textarea class="form-control" placeholder="<?php echo htmlspecialchars($application['addressDuringLeave']); ?>" disabled></textarea>
                    </div>

                    <?php if ($file_path): ?>
                        <div class="form-group">
                            <label for="addressDuringLeave">Medical Report</label>
                            <a href="<?php echo htmlspecialchars($file_path); ?>" class="btn btn-secondary form-control" download>Download File</a>
                        </div>
                    <?php endif; ?>

                    <!-- Acting Officer -->
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="replacement">Name of Employee Who Will Act as you</label>
                            <input type="text" id="replacement" class="form-control"
                                value="<?php echo ($replacement_name === 'Unknown') ? 'Not assigned yet' : htmlspecialchars($replacement_name); ?>" disabled>
                        </div>
                    </div>



                    <?php if (!empty($application['rejectionReason'])): ?>
                        <div class="form-group">
                            <label for="rejectionReason">Rejection Reason</label>
                            <textarea class="form-control" placeholder="<?php echo htmlspecialchars($application['rejectionReason']); ?>" disabled></textarea>
                        </div>
                    <?php endif; ?>


                    <a href="leave_application_history.php" class="btn btn-secondary float-right ml-2">Back to list</a>
                </form>
            </div>
        </div>
        <!-- container-scroller -->
        <!-- plugins:js -->
        <script src="../../assets/vendors/js/vendor.bundle.base.js"></script>
        <!-- endinject -->
        <!-- Plugin js for this page -->
        <script src="../../assets/vendors/chart.js/chart.umd.js"></script>
        <script src="../../assets/vendors/datatables.net/jquery.dataTables.js"></script>
        <script src="../../assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js"></script>
        <script src="../../assets/js/dataTables.select.min.js"></script>
        <!-- End plugin js for this page -->
        <!-- inject:js -->
        <script src="../../assets/js/off-canvas.js"></script>
        <script src="../../assets/js/template.js"></script>
        <script src="../../assets/js/settings.js"></script>
        <script src="../../assets/js/todolist.js"></script>
        <!-- endinject -->
        <!-- Custom js for this page-->
        <script src="../../assets/js/jquery.cookie.js" type="text/javascript"></script>
        <script src="../../assets/js/dashboard.js"></script>
        <!-- End custom js for this page-->
</body>