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
    case 'Subject Officer':
        header("Location:  app/views/subjectOfficer/subject_officer_dashboard.php");
        break;
    case 'Admin':
        header("Location: app/views/admin/admin_dashboard.php");
        break;
    case 'Staff Officer':
        header("Location: ../app/views/staffOfficer/staff_officer_dashboard.php");
        break;
    case 'Super Admin':
        header("Location: ./superAdmin/super_admin_dashboard.php");
        break;
    default:
        // If the role is not recognized, log the user out
        header("Location: logout.php");
        break;
}

exit();
