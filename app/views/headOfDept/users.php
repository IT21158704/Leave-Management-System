<?php

include('../../../config/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Head of Department') {
    header("Location: ../login.php");
    exit();
}

$nic = $_SESSION['nic'];
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
                    <a class="nav-link" href="head_of_department_dashboard.php">
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
                    <a class="nav-link" href="users.php">
                        <i class="mdi mdi-bookmark-outline menu-icon"></i>
                        <span class="menu-title">Users</span>
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
                        Employees
                    </h3>
                </header>

                <div class="mb-3">
                    <input class="form-control" id="searchInput" type="text" placeholder="Search...">
                </div>

                <?php
                // Fetch data from database
                $query = "SELECT * FROM users WHERE role = 'Employee'";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-striped table-hover table-bordered" id="userTable">';
                    echo '<thead class="thead-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Designation</th>
                        <th scope="col">Ministry / Dept</th>
                        <th scope="col">NIC</th>
                        <th scope="col">View</th>
                    </tr>
                  </thead>
                  <tbody>';

                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>
                        <td>' . htmlspecialchars($row['id']) . '</td>
                        <td>' . htmlspecialchars($row['name']) . '</td>
                        <td>' . htmlspecialchars($row['designation']) . '</td>
                        <td>' . htmlspecialchars($row['dept']) . '</td>
                        <td>' . htmlspecialchars($row['nic']) . '</td>
                        <td>
                            <a class="btn btn-primary btn-sm" href="userProfile.php?id=' . htmlspecialchars($row['id']) . '">View</a> 
                        </td>
                      </tr>';
                    }

                    echo '</tbody></table>';
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-warning" role="alert">No records found.</div>';
                }
                ?>

            </div>

            <script>
                document.getElementById('searchInput').addEventListener('keyup', function() {
                    var input = document.getElementById('searchInput').value.toLowerCase();
                    var table = document.getElementById('userTable');
                    var trs = table.getElementsByTagName('tr');

                    for (var i = 1; i < trs.length; i++) {
                        var tds = trs[i].getElementsByTagName('td');
                        var match = false;

                        for (var j = 0; j < tds.length; j++) {
                            if (tds[j].innerText.toLowerCase().indexOf(input) > -1) {
                                match = true;
                                break;
                            }
                        }

                        trs[i].style.display = match ? '' : 'none';
                    }
                });
            </script>
        </div>
        <!-- partial -->
    </div>
    <!-- main-panel ends -->
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