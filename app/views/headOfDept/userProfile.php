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

$id = $_GET['id'];

$sql = "SELECT casual_leaves, rest_leaves FROM available_leaves WHERE user_id = $id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $casual = $row["casual_leaves"];
    $rest = $row["rest_leaves"];
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
                        <span><?php echo htmlspecialchars($user['name']); ?></span>
                        <br>
                        <span class="text-muted" style="opacity: 0.5; font-size: 20px;"><?php echo htmlspecialchars('NIC : ' . $user['nic']); ?></span>
                    </h3>
                </header>

                <div class="row">
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-title">Available Leaves in current year</p>
                                <div class="d-flex flex-wrap">
                                    <div class="me-5 mt-3">
                                        <p class="text-muted">Casual</p>
                                        <h3 class="text-primary fs-30 font-weight-medium"><?php echo htmlspecialchars($casual); ?> </h3>
                                    </div>
                                    <div class="me-5 mt-3">
                                        <p class="text-muted">Rest</p>
                                        <h3 class="text-primary fs-30 font-weight-medium"><?php echo htmlspecialchars($rest); ?> </h3>
                                    </div>
                                    <div class="mt-3">
                                        <p class="text-muted">All</p>
                                        <h3 class="text-primary fs-30 font-weight-medium"><?php echo htmlspecialchars($rest + $casual); ?> </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="card-title">Approved leaves</p>
                                        <div class="d-flex">
                                            <div class="mb-3 mr-2">
                                                <button id="generateReport">Generate Report</button>

                                                <select class="form-control" id="monthPicker">
                                                    <option value="">Select Month</option>
                                                    <?php
                                                    foreach ($months as $number => $name) {
                                                        echo "<option value='$number'>$name</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <select class="form-control" id="yearPicker">
                                                    <option value="">Select Year</option>
                                                    <?php
                                                    $currentYear = date("Y");
                                                    for ($i = $currentYear; $i >= 2020; $i--) {
                                                        echo "<option value='$i'>$i</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="d-flex flex-wrap">
                                        <div class="col-md-12">

                                            <?php
                                            // Fetch data from database
                                            $query = "SELECT * FROM leave_applications WHERE user_id = $id AND status = 'approved'
                ORDER BY id DESC;";
                                            $result = $conn->query($query);

                                            if ($result->num_rows > 0) {
                                                echo '<div class="table-responsive">';
                                                echo '<table class="table table-striped table-hover table-bordered" id="userTable">';
                                                echo '<thead class="thead-dark">
    <tr>
        <th scope="col">Leave ID</th>
        <th scope="col">Date</th>
        <th scope="col">Number of days</th>
        <th scope="col">Reason / Dept</th>
        <th scope="col"></th>
    </tr>
  </thead>
  <tbody>';

                                                while ($row = $result->fetch_assoc()) {
                                                    echo '<tr>
        <td>' . htmlspecialchars($row['id']) . '</td>
        <td>' . htmlspecialchars($row['submissionDate']) . '</td>
        <td>' . htmlspecialchars($row['leaveDates']) . '</td>
        <td>' . htmlspecialchars($row['leaveReason']) . '</td>
        <td>
            <a class="btn btn-success btn-sm" href="filled_application.php?id=' . htmlspecialchars($row['id']) . '">View</a>
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

    <script>
        // Filter table based on selected year and month
        function filterTable() {
            var selectedYear = document.getElementById('yearPicker').value; // Selected year
            var selectedMonth = document.getElementById('monthPicker').value; // Selected month (1-12)
            var table = document.getElementById('userTable');
            var trs = table.getElementsByTagName('tr');

            for (var i = 1; i < trs.length; i++) {
                var tds = trs[i].getElementsByTagName('td');
                var dateCell = tds[1].innerText; // Assuming the date is in the second cell (index 1)

                // Extract year and month from the table's date field
                var tableDate = new Date(dateCell);
                var tableYear = tableDate.getFullYear();
                var tableMonth = tableDate.getMonth() + 1; // getMonth() returns 0-11, so add 1

                // Compare year and month
                if ((tableYear == selectedYear || selectedYear === "") &&
                    (tableMonth == selectedMonth || selectedMonth === "")) {
                    trs[i].style.display = '';
                } else {
                    trs[i].style.display = 'none';
                }
            }
        }

        // Generate PDF report from filtered table data
        async function generateReport() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Title
            doc.setFontSize(18);
            doc.text("Ministry of Fisheries", 14, 20);
            doc.setFontSize(12);
            doc.text(`Date: ${new Date().toLocaleDateString()}`, 14, 30);
            // doc.text(`User Name: ${document.getElementById('userName').innerText}`, 14, 35);
            // doc.text(`NIC No: ${document.getElementById('nicNo').innerText}`, 14, 40);

            // Create a table
            const table = document.getElementById('userTable');
            const trs = table.getElementsByTagName('tr');

            // Prepare data for the table
            const headers = Array.from(trs[0].getElementsByTagName('th')).map(th => th.innerText);
            const data = [];

            for (let i = 1; i < trs.length; i++) {
                if (trs[i].style.display !== 'none') { // Only include visible rows
                    const row = Array.from(trs[i].getElementsByTagName('td'))
                        .map(td => td.innerText);
                    data.push(row);
                }
            }

            // Use autoTable to create a styled table
            doc.autoTable({
                head: [headers],
                body: data,
                startY: 50, // Position the table below the heading
                theme: 'grid',
                headStyles: {
                    fillColor: [76, 175, 80],
                    textColor: [255, 255, 255],
                    fontSize: 12
                },
                margin: {
                    horizontal: 10
                },
                styles: {
                    fontSize: 10,
                    cellPadding: 5
                },
            });

            // Save the PDF
            doc.save('filtered_report.pdf');
        }


        document.getElementById('yearPicker').addEventListener('change', filterTable);
        document.getElementById('monthPicker').addEventListener('change', filterTable);
        document.getElementById('generateReport').addEventListener('click', generateReport);
    </script>



</body>