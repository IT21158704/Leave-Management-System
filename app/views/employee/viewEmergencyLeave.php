<?php

include('../../../config/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Employee') {
    header("Location: ../logout.php");
    exit();
}

$id = $_GET['id'];

$emergStatus = isset($_GET['status']) ? $_GET['status'] : 'notnull';

// Fetch existing data
$query = "SELECT * FROM emergency_leave WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $emergency_leave = $result->fetch_assoc();
} else {
    die("Record not found");
}

$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $emergency_leave['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $submitter = $result->fetch_assoc();
} else {
    die("Record not found");
}

$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $emergency_leave['emp_on_leave']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $emponleave = $result->fetch_assoc();
} else {
    die("Record not found");
}

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


$acting_name = fetchUserName($emponleave['acting'], $conn);

$conn->close();

?>

<head>
    <!-- disabled meta tags -->
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
                <header>
                    <h3 class="mb-4">
                        Emergency Leave Request
                    </h3>
                </header>

                <!-- Registration Form -->
                <form method="post" action="" class="needs-validation" novalidate>
                    <hr>
                    <p class="card-description text-secondary">Leave Request Submitter</p>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" value="<?php echo htmlspecialchars($submitter['name']); ?>" disabled>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="dept">Ministry/Dept.</label>
                            <input type="text" class="form-control" id="dept" value="<?php echo htmlspecialchars($submitter['dept']); ?>" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="designation">Date</label>
                            <input type="date" id="date" class="form-control" name="date" value="<?php echo htmlspecialchars($emergency_leave['submission_date']); ?>" disabled>
                        </div>
                    </div>

                    <hr>
                    <p class="card-description text-secondary">Employee on Leave</p>
                    <div class="form-group">
                        <label for="empOnLeave">Name of employee who is absense</label>
                        <input type="text" class="form-control" id="empOnLeave" name="empOnLeave" value="<?php echo htmlspecialchars($emponleave['name']); ?>" disabled>
                        <!-- $_SESSION['user_id'] -->
                    </div>

                    <div class="form-group">
                        <label for="reason">Reasons for leave</label>
                        <textarea class="form-control" id="reason" name="reason" rows="1" disabled><?php echo htmlspecialchars($emergency_leave['reason']); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="commenceLeaveDate ">Date of Commencing Leave</label>
                            <input type="date" class="form-control" id="commenceLeaveDate" value="<?php echo htmlspecialchars($emergency_leave['submission_date']); ?>" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="resumeDate">Date of Resumption</label>
                            <input type="date" class="form-control" id="resumeDate" value="<?php echo htmlspecialchars($emergency_leave['resume_date']); ?>" disabled>
                        </div>
                    </div>

                    <hr>
                    <p class="card-description text-secondary">Authorized Representative</p>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="actingOfficer">Officer Acting</label>
                            <input type="text" class="form-control" id="actingOfficer" value="<?php echo htmlspecialchars($acting_name); ?>" disabled>
                        </div>
                    </div>

                    <?php
                    if ($emergStatus == 'null') {
                        echo '<a class="btn btn-secondary float-right" href="emergencySubmissions.php">Back</a>';
                    } else {
                        // Get the current date
                        $currentDate = new DateTime();
                        $submissionDate = new DateTime($emergency_leave['submission_date']);

                        // Calculate the difference between the current date and the submission date
                        $interval = $submissionDate->diff($currentDate)->days;

                        // Check if the submission date is 2 days behind the current date
                        if ($interval >= 2) {
                            $med_value = 1;
                        } else {
                            $med_value = 0;
                        }

                        if ($emergency_leave['status'] == 0) {
                            echo '<a class="btn btn-primary float-right" href="leave_application.php?id=' . htmlspecialchars($emergency_leave['id']) . '&med=' . $med_value . '">Submit Leave Application</a>';
                        } else {
                            echo '<a class="btn btn-secondary float-right" href="emergencyLeaves.php">Back</a>';
                        }
                    }
                    ?>

                </form>
            </div>
            <!-- page-body-wrapper ends -->
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