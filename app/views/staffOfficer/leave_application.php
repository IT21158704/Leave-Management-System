<?php

include('../../../config/config.php');
include('../../../config/mailer.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Staff Officer') {
    header("Location: ../logout.php");
    exit();
}

$id = $_SESSION['user_id'];

$emergency_id = null;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $emergency_id = $_GET['id'];
}

$med = 0;

if (isset($_GET['med']) && !empty($_GET['med'])) {
    $med = $_GET['med'];
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id'];
    $leaveDates = $_POST['leaveDates'];
    $leaveReason = $_POST['leaveReason'];
    $firstAppointmentDate = $_POST['firstAppointmentDate'];
    $commenceLeaveDate = $_POST['commenceLeaveDate'];
    $resumeDate = $_POST['resumeDate'];
    $addressDuringLeave = $_POST['addressDuringLeave'];
    $replacement = !empty($user['acting']) ? $user['acting'] : NULL;
    $fullReason = $_POST['fullReason'];
    $submissionDate = date('Y-m-d H:i:s');

    // Validate required fields
    if (empty($leaveDates) || empty($leaveReason) || empty($firstAppointmentDate) || empty($commenceLeaveDate) || empty($resumeDate) || empty($addressDuringLeave) || empty($fullReason)) {
        die("Please fill in all required fields.");
    }

    // Prepare the SQL statement
    $query = "INSERT INTO leave_applications (user_id, leaveDates, leaveReason, firstAppointmentDate, commenceLeaveDate, resumeDate, addressDuringLeave, replacement, submissionDate, fullReason, status) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("iisssssiss", $user_id, $leaveDates, $leaveReason, $firstAppointmentDate, $commenceLeaveDate, $resumeDate, $addressDuringLeave, $replacement, $submissionDate, $fullReason);

        if ($stmt->execute()) {
            // Get the last inserted ID (leave_application_id)
            $application_id = $conn->insert_id;

            // Now insert into the request_status table with the application_id
            $query = "INSERT INTO request_status (leave_application_id) VALUES (?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $application_id);

            if ($stmt->execute()) {

                if ($emergency_id != null) {
                    $query = "UPDATE emergency_leave SET status = 1 WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $emergency_id);
                    $stmt->execute();
                }
                if ($med == 1) {

                    if ($_FILES['medical']['error'] !== UPLOAD_ERR_OK) {
                        die('File upload error: ' . $_FILES['medical']['error']);
                    }


                    if ($med == 1 && isset($_FILES['medical']) && $_FILES['medical']['error'] == UPLOAD_ERR_OK) {
                        $filePath = uploadMedicalFile($_FILES['medical'], $application_id);

                        if ($filePath) {
                            // Insert the medical file path into the medicals table
                            $query = "INSERT INTO medicals (application_id, file_path) VALUES (?, ?)";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("is", $application_id, $filePath);
                            $stmt->execute();
                        } else {
                            die('Failed to move uploaded file.');
                        }
                    } else {
                        die('No file uploaded or upload error.');
                    }
                }

                if ($user['acting'] != null) {
                    // Fetch existing data (email and name)
                    $query = "SELECT email, name FROM users WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $user['acting']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc(); // Fetch the row as an associative array
                        $receiverEmail = $row['email']; // Access the 'email' field
                        $receiverName = $row['name']; // Access the 'name' field
                    }

                    $body = leaveRequestEmailBody($user['name'], $leaveReason, $commenceLeaveDate, $resumeDate, $fullReason);
                    sendMail($receiverEmail, $receiverName, 'Leave Request from ' . $user['name'], $body);
                } else {
                    // Fetch existing data (email and name)
                    // Assuming $user is already defined and contains the staff field
                    $staffIds = json_decode($user['staff'], true); // Decode the JSON array

                    if (!empty($staffIds)) {
                        // Prepare a string for the placeholders
                        $placeholders = implode(',', array_fill(0, count($staffIds), '?'));

                        // Fetch emails of all staff officers in the staff array
                        $query = "SELECT email, name FROM users WHERE id IN ($placeholders)";
                        $stmt = $conn->prepare($query);

                        // Dynamically bind parameters
                        $stmt->bind_param(str_repeat('i', count($staffIds)), ...$staffIds);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Prepare to send emails
                        while ($row = $result->fetch_assoc()) {
                            $receiverEmail = $row['email'];
                            $receiverName = $row['name'];

                            // Create the email body using your existing function
                            $body = leaveRequestEmailBody($user['name'], $leaveReason, $commenceLeaveDate, $resumeDate, $fullReason);

                            // Send the email using your existing sendMail function
                            sendMail($receiverEmail, $receiverName, 'Leave Request from ' . $user['name'], $body);

                            // echo "Message sent to $receiverName ($receiverEmail)<br>";
                        }
                    }
                }

                $success_message = "Leave application submitted successfully!";
                header("Location: leave_application_history.php");
                exit();
            } else {
                $error_message = $conn->error;
            }
        } else {
            $error_message = $stmt->error;
        }

        $stmt->close();
    } else {
        $error_message = 'Connection failed';
    }
}

function uploadMedicalFile($file)
{
    // Define the target directory and file path
    $targetDir = "../../uploads/medical_files/";
    $fileName = basename($file["name"]);
    $targetFilePath = $targetDir . uniqid() . "_" . $fileName;

    // Create the uploads directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        return $targetFilePath; // Return the file path on success
    } else {
        return false; // Return false on failure
    }
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

if ($user['acting'] == null) {
    $acting_officer_name = "No Acting User";
} else {
    $acting_officer_name = fetchUserName($user['acting'], $conn);
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
                    <a class="nav-link" href="staff_officer_dashboard.php">
                        <i class="icon-grid menu-icon"></i>
                        <span class="menu-title">Home</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="leave_requests.php">
                        <i class="mdi mdi-bookmark-outline menu-icon"></i>
                        <span class="menu-title">Leave Requests</span>
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
                        Application For Leave
                        <!-- Welcome, <?php echo htmlspecialchars($username); ?>! -->
                    </h3>
                </header>

                <!-- Registration Form -->
                <form method="post" action="" class="needs-validation" novalidate enctype="multipart/form-data">
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
                            <label for="dept">Department</label>
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
                            <input type="number" id="leaveDates" class="form-control" name="leaveDates" required>
                            <div class="invalid-feedback">Please enter the number of days leave applied for.</div>
                        </div>

                        <!-- <small id="passwordHelpBlock" class="form-text text-muted"> Your  </small> -->
                        <div class="form-group col-md-6">
                            <label for="availableLeaves">Available leaves for current year</label>
                            <input type="text" id="availableLeaves" class="form-control" name="availableLeaves" value="Casual - <?php echo htmlspecialchars($avLeaves['casual_leaves']); ?>    |   Rest - <?php echo htmlspecialchars($avLeaves['rest_leaves']); ?>" disabled>
                            <div class="invalid-feedback">Please enter the designation.</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="leaveReason">Reason</label>
                        <div class="form-row">
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="leaveReason" id="radio1" value="casual" required>
                                    <label class="form-check-label" for="radio1">
                                        Casual
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="leaveReason" id="radio2" value="rest" required>
                                    <label class="form-check-label" for="radio2">
                                        Rest
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="leaveReason" id="radio3" value="other" required>
                                    <label class="form-check-label" for="radio3">
                                        Other
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="invalid-feedback">Please select the reason.</div>
                    </div>

                    <div class="form-group">
                        <!-- <label for="fullReason">Reasons for leave</label> -->
                        <textarea class="form-control" id="fullReason" name="fullReason" rows="1" placeholder="Type your reason here..." required></textarea>
                        <div class="invalid-feedback">Please enter the reasons for leave.</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="firstAppointmentDate">Date of First Appoinment</label>
                            <input type="date" class="form-control" id="firstAppointmentDate" name="firstAppointmentDate" required>
                            <div class="invalid-feedback">Please enter the date of first appoinment.</div>
                        </div>
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

                    <div class="form-group">
                        <label for="addressDuringLeave">Address During Leave</label>
                        <textarea class="form-control" id="addressDuringLeave" name="addressDuringLeave" rows="2" placeholder="Type your address During Leave here..." required></textarea>
                        <div class="invalid-feedback">Please enter the address During Leave.</div>
                    </div>

                    <?php if ($med == 1): ?>
                        <div class="form-group">
                            <label for="addressDuringLeave">Attach Medical</label>
                            <input type='file' class="form-control" id="medical" name="medical" required />
                            <div class="invalid-feedback">Please attach a medical.</div>
                        </div>
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="actingOfficer">Employee Who Will Act as you</label>
                            <input type="text" id="actingOfficer" class="form-control" value="<?php echo htmlspecialchars($acting_officer_name); ?>" disabled>
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