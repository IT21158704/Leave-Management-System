<?php

require('../../assets/vendors/fpdf/fpdf.php');
include('../../../config/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Employee') {
    header("Location: ../login.php");
    exit();
}


$id = $_SESSION['user_id'];

$sql = "SELECT casual_leaves, rest_leaves FROM available_leaves WHERE user_id = $id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $casual = $row["casual_leaves"];
    $rest = $row["rest_leaves"];
}

$short_leaves = 0;
$monthName = 'No Short Leaves';
$sql = "SELECT * FROM short_leaves WHERE user_id = $id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $short_leaves = $row["short_leaves"];
    $dateString = $row["modified_date"];
    $timestamp = strtotime($dateString);
    $monthName = date('F', $timestamp);
}


$sql = "SELECT 
            SUM(leaveDates) AS totalLeaveDays,
            SUM(CASE WHEN leaveReason = 'Casual' THEN 1 ELSE 0 END) AS casualLeaveCount,
            SUM(CASE WHEN leaveReason = 'Rest' THEN 1 ELSE 0 END) AS restLeaveCount,
            SUM(CASE WHEN leaveReason = 'Other' THEN 1 ELSE 0 END) AS otherLeaveCount
        FROM 
            leave_applications
        WHERE 
            user_id = ?
            AND status = 'approved'
            AND YEAR(submissionDate) = YEAR(CURRENT_DATE())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Store the values in variables
    $totalLeaveDays = $row['totalLeaveDays'];
    $casualLeaveCount = $row['casualLeaveCount'];
    $restLeaveCount = $row['restLeaveCount'];
    $otherLeaveCount = $row['otherLeaveCount'];
} else {
    $totalLeaveDays = 0;
    $casualLeaveCount = 0;
    $restLeaveCount = 0;
    $otherLeaveCount = 0;
}

$stmt->close();


$months = [
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December'
];

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
        <!-- partial -->
        <div class="main-panel">
            <div class="content-wrapper">
                <header>
                    <h3 class="mb-4">
                        <span><?php echo htmlspecialchars($user['name']); ?></span>
                        <br>
                        <span class="text-muted" style="opacity: 0.5; font-size: 20px;"><?php echo htmlspecialchars('NIC : ' . $user['nic']); ?></span>
                    </h3>
                </header>

                <div class="row">
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <!-- Flexbox container for title and button -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="card-title">Available Leaves in current year</p>
                                    <a class="btn btn-link btn-sm" href="previusLeaves.php">Leave History (Previous Years)</a>
                                </div>

                                <div class="d-flex flex-wrap">
                                    <div class="me-5 mt-3">
                                        <p class="text-muted">Casual</p>
                                        <h3 class="text-primary fs-30 font-weight-medium"><?php echo htmlspecialchars($casual); ?></h3>
                                    </div>
                                    <div class="me-5 mt-3">
                                        <p class="text-muted">Rest</p>
                                        <h3 class="text-primary fs-30 font-weight-medium"><?php echo htmlspecialchars($rest); ?></h3>
                                    </div>
                                    <div class="mt-3">
                                        <p class="text-muted">All</p>
                                        <h3 class="text-primary fs-30 font-weight-medium"><?php echo htmlspecialchars($rest + $casual); ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <!-- Flexbox container for title and button -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="card-title">Short Leaves in last month</p>
                                    <a class="btn btn-link btn-sm" href="previusShortLeaves.php">Short Leave History (Previous Months)</a>
                                </div>

                                <div class="d-flex flex-wrap">
                                    <div class="me-5 mt-3">
                                        <p class="text-muted"><?php echo htmlspecialchars($monthName); ?></p>
                                        <h3 class="text-primary fs-30 font-weight-medium"><?php echo htmlspecialchars($short_leaves); ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- partial -->
    </div>
    <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.11/jspdf.plugin.autotable.min.js"></script>

    


</body>