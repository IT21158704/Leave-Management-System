<?php

include('../../../config/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Super Admin') {
    header("Location: ../login.php");
    exit();
}


$department_name = '';
$role_name = '';
$edit_id = 0;
$edit_state_department = false;
$edit_state_role = false;

// Handling Department Add/Update
if (isset($_POST['save_department'])) {
    $department_name = $_POST['name'];
    $edit_id = $_POST['id'];  // Fetch the department id from the form

    // If edit_id is not empty, it's an update operation
    if (!empty($edit_id)) {
        $stmt = $conn->prepare("UPDATE department SET name=? WHERE id=?");
        $stmt->bind_param("si", $department_name, $edit_id);
        echo "Department updated successfully!";
    } else {
        $stmt = $conn->prepare("INSERT INTO department (name) VALUES (?)");
        $stmt->bind_param("s", $department_name);
        echo "Department added successfully!";
    }
    $stmt->execute();
    $stmt->close();
    header('location: customize.php');
}


// Handling Role Add/Update
if (isset($_POST['save_role'])) {
    $role_name = $_POST['name'];
    $edit_id = $_POST['id'];  // Fetch the role id from the form

    // If edit_id is not empty, it's an update operation
    if (!empty($edit_id)) {
        $stmt = $conn->prepare("UPDATE role SET name=? WHERE id=?");
        $stmt->bind_param("si", $role_name, $edit_id);
        echo "Role updated successfully!";
    } else {
        $stmt = $conn->prepare("INSERT INTO role (name) VALUES (?)");
        $stmt->bind_param("s", $role_name);
        echo "Role added successfully!";
    }
    $stmt->execute();
    $stmt->close();
    header('location: customize.php');
}


// Fetch for Department Edit
if (isset($_GET['edit_department'])) {
    $edit_id = $_GET['edit_department'];
    $edit_state_department = true;
    $result = $conn->query("SELECT * FROM department WHERE id=$edit_id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $department_name = $row['name'];
    }
}

// Fetch for Role Edit
if (isset($_GET['edit_role'])) {
    $edit_id = $_GET['edit_role'];
    $edit_state_role = true;
    $result = $conn->query("SELECT * FROM role WHERE id=$edit_id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $role_name = $row['name'];
    }
}

// Handling Department Delete
if (isset($_GET['delete_department'])) {
    $delete_id = $_GET['delete_department'];
    $conn->query("DELETE FROM department WHERE id=$delete_id");
    header('location: customize.php');
}

// Handling Role Delete
if (isset($_GET['delete_role'])) {
    $delete_id = $_GET['delete_role'];
    $conn->query("DELETE FROM role WHERE id=$delete_id");
    header('location: customize.php');
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
        <!-- partial -->
        <div class="main-panel">
            <div class="content-wrapper">

                <header>
                    <h3 class="mb-4">
                        Manage Devisions
                    </h3>
                </header>
                <form method="post" action="" class="needs-validation">
                    <div class="form-row align-items-center">
                        <div class="col-md-4">
                            <input class="form-control" type="hidden" name="id" value="<?php echo $edit_id; ?>"> <!-- Department ID -->
                            <input class="form-control" type="text" name="name" value="<?php echo $department_name; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <?php if ($edit_state_department == false): ?>
                                <button type="submit" name="save_department" class="btn btn-secondary">Add Devision</button>
                            <?php else: ?>
                                <button type="submit" name="save_department" class="btn btn-secondary">Update Devision</button>
                                <a href="customize.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>



                <!-- <h3>All Departments</h3> -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM department");
                            while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td>
                                        <a href="customize.php?edit_department=<?php echo $row['id']; ?>" class="btn btn-link btn-sm">Edit</a>
                                        <a href="customize.php?delete_department=<?php echo $row['id']; ?>" class="btn btn-link btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="../../assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="../../assets/vendors/chart.js/chart.umd.js"></script>
    <script src="../../assets/vendors/datatables.net/jquery.dataTables.js"></script>
    <script src="../../assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js"></script>
    <script src="../../assets/js/dataTables.select.min.js"></script>
    <script src="../../assets/js/off-canvas.js"></script>
    <script src="../../assets/js/template.js"></script>
    <script src="../../assets/js/settings.js"></script>
    <script src="../../assets/js/todolist.js"></script>
    <script src="../../assets/js/jquery.cookie.js" type="text/javascript"></script>
    <script src="../../assets/js/dashboard.js"></script>
</body>