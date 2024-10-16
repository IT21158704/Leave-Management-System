<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leave_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the current date and year
$current_date = date('Y-m-d');
$current_year = date('Y');
$january_first = $current_year . '-01-01';

// Check if today is January 1st
if ($current_date == $january_first) {
    // Query to check if leaves were already reset for the current year
    $check_sql = "SELECT last_reset FROM available_leaves LIMIT 1";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_reset = $row['last_reset'];

        // If last_reset is not this year, update leave balances
        if ($last_reset == null || date('Y', strtotime($last_reset)) != $current_year) {
            // SQL to update casual_leaves and rest_leaves for each user
            $sql = "UPDATE available_leaves 
                    SET casual_leaves = casual_leaves + 24, 
                        rest_leaves = rest_leaves + 21, 
                        last_reset = '$january_first'";

            if ($conn->query($sql) === TRUE) {
                echo "Leave balances updated for the new year.";
            } else {
                echo "Error updating leave balances: " . $conn->error;
            }
        }
    }
}
