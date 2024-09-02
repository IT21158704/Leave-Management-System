<?php

include('../../../config/config.php');

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
    $row = $result->fetch_assoc();
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

$conn->close();

?>

<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> <!-- Select2 CSS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> <!-- Select2 JS -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 30px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
    </style>
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Employee Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="employee_dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="leave_application.php">Leave Application</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="leave_requests.php">Leave Requests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>


    <div class="container mt-4 mb-4">
        <header>
            <h1 class="mb-4">
                Application For Leave
                <!-- Welcome, <?php echo htmlspecialchars($username); ?>! -->
            </h1>
        </header>

        <!-- Registration Form -->
        <form method="post" action="" class="needs-validation" novalidate>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" disabled required>
                <div class="invalid-feedback">Please enter the name.</div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="designation">Designation</label>
                    <input type="text" class="form-control" id="designation" name="designation" value="<?php echo htmlspecialchars($row['designation']); ?>" disabled required>
                    <div class="invalid-feedback">Please enter the designation.</div>
                </div>
                <div class="form-group col-md-6">
                    <label for="dept">Ministry/Dept.</label>
                    <input type="text" class="form-control" id="dept" name="dept" value="<?php echo htmlspecialchars($row['dept']); ?>" disabled  required>
                    <div class="invalid-feedback">Please enter the ministry or department.</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="designation">Date</label>
                    <input type="date" id="date" class="form-control" name="date" value="<?php echo $currentDate; ?>" disabled>
                    <div class="invalid-feedback">Please enter the designation.</div>
                </div>
            </div>



            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="dept">Number of days leave applied for</label>
                    <input type="number" id="date" class="form-control" name="date">
                    <div class="invalid-feedback">Please enter the designation.</div>
                </div>
                <div class="form-group col-md-6">
                    <label for="dept">Reason</label>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="exampleRadios" id="radio1" value="option1" checked>
                                <label class="form-check-label" for="radio1">
                                    Option
                                </label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="exampleRadios" id="radio2" value="option2">
                                <label class="form-check-label" for="radio2">
                                    Option
                                </label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="exampleRadios" id="radio3" value="option3">
                                <label class="form-check-label" for="radio3">
                                    Option
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="invalid-feedback">Please enter the ministry or department.</div>
                </div>
            </div>

            <div class="form-group">
                <label for="dept">Available leaves for current year</label>
                <input type="test" id="date" class="form-control" name="date">
                <div class="invalid-feedback">Please enter the designation.</div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="designation">Date of First Appoinment</label>
                    <input type="date" class="form-control" id="designation" name="designation" required>
                    <div class="invalid-feedback">Please enter the designation.</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="designation">Date of commencing leave</label>
                    <input type="date" class="form-control" id="designation" name="designation" required>
                    <div class="invalid-feedback">Please enter the designation.</div>
                </div>
                <div class="form-group col-md-6">
                    <label for="dept">Date of resuming duties</label>
                    <input type="date" class="form-control" id="dept" name="dept" required>
                    <div class="invalid-feedback">Please enter the ministry or department.</div>
                </div>
            </div>

            <div class="form-group">
                <label for="dept">Reasons for leave</label>
                <textarea class="form-control" id="exampleTextarea" rows="1" placeholder="Type your message here..."></textarea>
                <div class="invalid-feedback">Please enter the designation.</div>
            </div>

            <div class="form-group">
                <label for="dept">Address when on leave</label>
                <textarea class="form-control" id="exampleTextarea" rows="2" placeholder="Type your message here..."></textarea>
                <div class="invalid-feedback">Please enter the designation.</div>
            </div>

            <div class="form-group">
                <label for="role">Replacement</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="">Select a replacement</option>
                        <?php
                        if ($employees_result->num_rows > 0) {
                            while ($employee = $employees_result->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($employee['id']) . '">' . htmlspecialchars($employee['name']) . '</option>';
                            }
                        }
                        ?>
                </select>
                <div class="invalid-feedback">Please select a role.</div>
            </div>


            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="designation">Officer Acting</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="none">None</option>
                        <?php
                        if ($officer_result->num_rows > 0) {
                            while ($officer = $officer_result->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($officer['id']) . '">' . htmlspecialchars($officer['name']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">Please enter the designation.</div>
                </div>
                <div class="form-group col-md-6">
                    <label for="dept">Supervising Officer</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="none">None</option>
                        <?php
                        if ($supervisor_result->num_rows > 0) {
                            while ($supervisor = $supervisor_result->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($supervisor['id']) . '">' . htmlspecialchars($supervisor['name']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">Please enter the ministry or department.</div>
                </div>
            </div>
            
            <button type="reset" class="btn btn-secondary float-right ml-2">Reset</button>
            <button type="submit" class="btn btn-primary float-right">Submit</button>
        </form>
    </div>

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
        });
        
    </script>
</body>