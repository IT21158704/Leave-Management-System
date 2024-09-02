<?php
session_start();
include('../../config/config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role'];
$username = $_SESSION['username'];

$dashboard = [
    'Employee' => './employee/employee_dashboard.php',
    'Supervising Officer' => './supervising_officer/supervising_officer_dashboard.php',
    'Head of Department' => './head_of_department/head_of_department_dashboard.php',
    'Officer Acting' => './officer_acting/officer_acting_dashboard.php',
    'Admin' => './admin/admin_dashboard.php'
];

// Output common content first
?>
<!DOCTYPE html>
<html lang="en">
<body>
    <header>
        <p>Hello, <?php echo htmlspecialchars($username); ?>!</p>
        <a href="logout.php">Logout</a>
    </header>

    <?php
    if (array_key_exists($user_role, $dashboard)) {
        include($dashboard[$user_role]);
    } else {
        echo "<p>Role not recognized</p>";
    }

    $conn->close();
    ?>
</body>
</html>
