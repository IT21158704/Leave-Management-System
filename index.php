<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the session is not set, redirect to the login page
    header("Location: app/views/login.php");
    exit();
}

// Check the user's role and redirect to the appropriate dashboard
switch ($_SESSION['role']) {
    case 'Employee':
        header("Location: app/views/employee/employee_dashboard.php");
        break;
    case 'Supervising Officer':
        header("Location:  app/views/supervisingOfficer/supervising_officer_dashboard.php");
        break;
    case 'Head of Department':
        header("Location:  app/views/headOfDept/head_of_department_dashboard.php");
        break;
    // case 'Officer Acting':
    //     header("Location:  app/views/officerActing/officer_acting_dashboard.php");
    //     break;
    case 'Admin':
        header("Location: app/views/admin/admin_dashboard.php");
        break;
    default:
        // If the role is not recognized, log the user out
        header("Location: logout.php");
        break;
}

exit();

?>
