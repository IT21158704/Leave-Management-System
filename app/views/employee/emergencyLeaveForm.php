<?php

include('../../../config/config.php');
include('../../../config/mailer.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Employee') {
    header("Location: ../logout.php");
    exit();
}

$id = $_SESSION['user_id'];

// Fetch existing data
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

// Fetch existing data
$query = "SELECT * FROM available_leaves WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $avLeaves = $result->fetch_assoc();
} else {
    die("Record not found");
}

$currentDate = date("Y-m-d");

// Fetch all employees for the Replacement dropdown
$employees_query = "SELECT id, name FROM users WHERE role = 'Employee'";
$employees_result = $conn->query($employees_query);

$officer_query = "SELECT id, name FROM users WHERE role = 'Officer Acting'";
$officer_result = $conn->query($officer_query);

$supervisor_query = "SELECT id, name FROM users WHERE role = 'Supervising Officer'";
$supervisor_result = $conn->query($supervisor_query);

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


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id'];
    $empOnLeave = $_POST['empOnLeave'];
    $reason = $_POST['reason'];
    $commenceLeaveDate = $_POST['commenceLeaveDate'];
    $resumeDate = $_POST['resumeDate'];
    $actingOfficer = !empty($_POST['actingOfficer']) ? $_POST['actingOfficer'] : NULL;
    $supervisingOfficer = $_POST['supervisingOfficer'];
    $submissionDate = date('Y-m-d');

    // Validate required fields
    if (empty($empOnLeave) || empty($reason) || empty($commenceLeaveDate) || empty($supervisingOfficer)) {
        die("Please fill in all required fields.");
    }

    // user_id	emp_on_leave	reason	commence_leave_date	resume_date	acting_officer	supervising_officer	submission_date	

    // Prepare the SQL statement
    $query = "INSERT INTO emergency_leave (user_id, emp_on_leave, reason, commence_leave_date, resume_date, acting_officer, supervising_officer, submission_date, status ) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";

    if ($stmt = $conn->prepare($query)) {

        $stmt->bind_param("iisssiis", $user_id, $empOnLeave, $reason, $commenceLeaveDate, $resumeDate, $actingOfficer, $supervisingOfficer, $submissionDate);

        if ($stmt->execute()) {

            if ($actingOfficer != null) {
                // Fetch existing data (email and name)
                $query = "SELECT email, name FROM users WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $actingOfficer);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc(); // Fetch the row as an associative array
                    $actingOfficerEmail = $row['email']; // Access the 'email' field
                    $actingOfficerName = $row['name']; // Access the 'name' field
                }

                $body = emergencyLeaveEmailBody($user['name'], fetchUserName($empOnLeave, $conn), $commenceLeaveDate, $resumeDate, $reason);
                sendMail($actingOfficerEmail, $actingOfficerName, 'Emergency leave request for ' . fetchUserName($empOnLeave, $conn), $body);
            }


            // Fetch existing data (email and name)
            $query = "SELECT email, name FROM users WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $supervisingOfficer);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc(); // Fetch the row as an associative array
                $supervisingOfficerEmail = $row['email']; // Access the 'email' field
                $supervisingOfficerName = $row['name']; // Access the 'name' field
            }

            $body = emergencyLeaveEmailBody($user['name'], fetchUserName($empOnLeave, $conn), $commenceLeaveDate, $resumeDate, $reason);
            sendMail($supervisingOfficerEmail, $supervisingOfficerName, 'Emergency leave request for ' . fetchUserName($empOnLeave, $conn), $body);

            // Fetch existing data (email and name)
            $query = "SELECT email, name FROM users WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $empOnLeave);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc(); // Fetch the row as an associative array
                $empOnLeaveEmail = $row['email']; // Access the 'email' field
                $empOnLeaveName = $row['name']; // Access the 'name' field
            }

            $body = emergencyLeaveEmailBody($user['name'], fetchUserName($empOnLeave, $conn), $commenceLeaveDate, $resumeDate, $reason);
            sendMail($empOnLeaveEmail, $empOnLeaveName, $user['name'] .' Placed emergency leave request for you ', $body);

            $success_message = "Leave application submitted successfully!";
            header("Location: leave_application_history.php");
            exit();
        } else {
            $error_message = $stmt->error;
        }

        $stmt->close();
    } else {
        $error_message = 'Connection failed';
    }
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
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" disabled required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="dept">Ministry/Dept.</label>
                            <input type="text" class="form-control" id="dept" name="dept" value="<?php echo htmlspecialchars($user['dept']); ?>" disabled required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="designation">Date</label>
                            <input type="date" id="date" class="form-control" name="date" value="<?php echo $currentDate; ?>" disabled>
                        </div>
                    </div>

                    <hr>
                    <p class="card-description text-secondary">Employee on Leave</p>
                    <div class="form-group">
                        <label for="empOnLeave">Name of employee who is absense</label>
                        <select class="form-control" id="empOnLeave" name="empOnLeave" required>
                            <option value="">Select the employee who is absense</option>
                            <?php
                            if ($employees_result->num_rows > 0) {
                                while ($employee = $employees_result->fetch_assoc()) {
                                    echo '<option value="' . htmlspecialchars($employee['id']) . '">' . htmlspecialchars($employee['name']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Please select the employee Who is absense.</div>
                    </div>

                    <div class="form-group">
                        <label for="reason">Reasons for leave</label>
                        <textarea class="form-control" id="reason" name="reason" rows="1" placeholder="Type reason here..." required></textarea>
                        <div class="invalid-feedback">Please enter the reasons for leave.</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="commenceLeaveDate ">Date of Commencing Leave</label>
                            <input type="date" class="form-control" id="commenceLeaveDate" name="commenceLeaveDate" required>
                            <div class="invalid-feedback">Please enter the date of commencing leave.</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="resumeDate">Date of Resumption</label>
                            <input type="date" class="form-control" id="resumeDate" name="resumeDate" required>
                            <div class="invalid-feedback">Please enter the date of resuming duties.</div>
                        </div>
                    </div>

                    <hr>
                    <p class="card-description text-secondary">Authorized Representative</p>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="actingOfficer">Officer Acting</label>
                            <select class="form-control" id="actingOfficer" name="actingOfficer">
                                <option value="">None</option> <!-- Empty value for optional field -->
                                <?php
                                if ($officer_result->num_rows > 0) {
                                    while ($officer = $officer_result->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($officer['id']) . '">' . htmlspecialchars($officer['name']) . '</option>';
                                    }
                                }
                                ?>
                            </select>

                            <div class="invalid-feedback">Please select an Officer Acting.</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="supervisingOfficer">Supervising Officer</label>
                            <select class="form-control" id="supervisingOfficer" name="supervisingOfficer" required>
                                <option value="none">None</option>
                                <?php
                                if ($supervisor_result->num_rows > 0) {
                                    while ($supervisor = $supervisor_result->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($supervisor['id']) . '">' . htmlspecialchars($supervisor['name']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Please select a Supervising Officer.</div>
                        </div>
                    </div>

                    <button type="reset" class="btn btn-secondary float-right ml-2">Reset</button>
                    <button type="submit" class="btn btn-primary float-right">Submit</button>
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

        <script>
            // Bootstrap form validation
            (function() {
                'use strict';
                window.addEventListener('load', function() {
                    var forms = document.getElementsByClassName('needs-validation');
                    var validation = Array.prototype.filter.call(forms, function(form) {
                        form.addEventListener('submit', function(event) {
                            if (form.checkValidity() === false) {
                                event.preventDefault();
                                event.stopPropagation();
                            }
                            form.classList.add('was-validated');
                        }, false);
                    });
                }, false);
            })();

            $(document).ready(function() {
                $('#replacement').select2({
                    placeholder: "Select a replacement",
                    allowClear: true
                });
                $('#Acting Officer').select2({
                    placeholder: "Select a Acting Officer",
                    allowClear: true
                });
                $('#Supervising Officer').select2({
                    placeholder: "Select a Supervising Officer",
                    allowClear: true
                });
            });
        </script>
</body>