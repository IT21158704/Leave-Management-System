<?php
include('../../../config/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Super Admin') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];

// Fetch all employees for the acting role dropdown
$employees_query = "SELECT id, name, nic FROM users WHERE role = 'Employee'";
$employees_result = $conn->query($employees_query);

// Fetch all departments for the department dropdown
$departments_query = "SELECT id, name FROM department";
$departments_result = $conn->query($departments_query);

$roles_query = "SELECT id, name FROM role";
$roles_result = $conn->query($roles_query);


// Fetch existing user data
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentReplacement = $row['acting'];
    $selectedStaffOfficers = json_decode($row['staff'], true) ?? [];
} else {
    die("Record not found");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $designation = $_POST['designation'];
    $dept = $_POST['dept'];
    $nic = $_POST['nic'];
    $email = $_POST['email'];
    $role = 'Admin';
    $acting = $_POST['replacement'];
    $staffOfficers = $_POST['staff_officers']; // Array of selected staff officers

    // Check if the acting role (replacement) is set to NULL (i.e., "None" selected)
    if ($acting == "NULL") {
        $acting = null; // Set to null for SQL
    }

    // Convert the selected staff officers array to a JSON string for storing in the database
    $staffOfficersJson = json_encode($staffOfficers);

    // Update the user record in the database
    $updateQuery = "UPDATE users SET name = ?, designation = ?, dept = ?, nic = ?, email = ?, role = ?, acting = ?, staff = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssssisi", $name, $designation, $dept, $nic, $email, $role, $acting, $staffOfficersJson, $id);

    if ($stmt->execute()) {
        header("Location: view_users.php"); // Redirect to the user list page
        exit();
    } else {
        echo '<div class="alert alert-danger" role="alert">Update failed: ' . $stmt->error . '</div>';
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

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
    <<!-- partial:partials/_navbar.html -->
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
                        <a class="nav-link" href="super_admin_dashboard.php">
                            <i class="icon-grid menu-icon"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_users.php">
                            <i class="mdi mdi-account-outline menu-icon"></i>
                            <span class="menu-title">Admins</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="customize.php">
                            <i class="mdi mdi-cog-outline menu-icon"></i>
                            <span class="menu-title">Customize Sys.</span>
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
                            Update user #<?php echo htmlspecialchars($row['name']); ?>
                        </h3>
                    </header>

                    <!-- Update Form -->
                    <form method="post" class="needs-validation" novalidate>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                            <div class="invalid-feedback">Please enter the name.</div>
                        </div>
                        <div class="form-group">
                            <label for="designation">Designation</label>
                            <input type="text" class="form-control" id="designation" name="designation" value="<?php echo htmlspecialchars($row['designation']); ?>" required>
                            <div class="invalid-feedback">Please enter the designation.</div>
                        </div>
                        <div class="form-group">
                            <label for="dept">Devision</label>
                            <select class="form-control" id="dept" name="dept" required>
                                <option value="">Select a Devision</option>
                                <?php
                                if ($departments_result->num_rows > 0) {
                                    while ($department = $departments_result->fetch_assoc()) {
                                        $selected = ($row['dept'] == $department['name']) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($department['name']) . '" ' . $selected . '>' . htmlspecialchars($department['name']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Please enter the devision.</div>
                        </div>

                        <div class="form-group">
                            <label for="nic">NIC</label>
                            <input type="text" class="form-control" id="nic" name="nic" value="<?php echo htmlspecialchars($row['nic']); ?>" required>
                            <div class="invalid-feedback">Please enter a nic.</div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                            <div class="invalid-feedback">Please enter a Email.</div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="view_users.php" class="btn btn-secondary">Back to list</a>
                        <a href="password_reset.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-success">Reset Password</a>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
        <script>
            document.getElementById('dept').addEventListener('change', function() {
                var dept = this.value;
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'fetch_staff_officers.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (this.status === 200) {
                        var staffOfficers = JSON.parse(this.responseText);
                        var staffSelect = document.getElementById('staff_officers');
                        staffSelect.innerHTML = ''; // Clear previous options

                        staffOfficers.forEach(function(staff) {
                            var option = document.createElement('option');
                            option.value = staff.id;
                            option.text = staff.name + ' (' + staff.nic + ')';
                            // Check if the staff is already selected
                            if (<?php echo json_encode($selectedStaffOfficers); ?>.includes(staff.id.toString())) {
                                option.selected = true;
                            }
                            staffSelect.appendChild(option);
                        });
                    }
                };
                xhr.send('dept=' + dept);
            });

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
        </script>
</body>

</html>