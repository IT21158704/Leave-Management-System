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

        // If last_reset is not this year, proceed with resetting
        if ($last_reset == null || date('Y', strtotime($last_reset)) != $current_year) {
            // Fetch all users' leave balances to store them in the history
            $fetch_leaves_sql = "SELECT user_id, casual_leaves, rest_leaves, other_leaves FROM available_leaves";
            $leave_result = $conn->query($fetch_leaves_sql);

            if ($leave_result->num_rows > 0) {
                while ($leave_row = $leave_result->fetch_assoc()) {
                    $user_id = $leave_row['user_id'];
                    $casual_leaves = $leave_row['casual_leaves'];
                    $rest_leaves = $leave_row['rest_leaves'];
                    $other_leaves = $leave_row['other_leaves'];

                    // Insert the previous year's leave balances into the leave_history table
                    $insert_history_sql = "INSERT INTO leave_history (user_id, year, casual_leaves, rest_leaves, other_leaves) 
                                           VALUES ($user_id, $current_year - 1, $casual_leaves, $rest_leaves, $other_leaves)";
                    if (!$conn->query($insert_history_sql)) {
                        echo "Error inserting leave history for user ID $user_id: " . $conn->error;
                    }
                }
            }

            // Reset the leave balances to default values
            $reset_leaves_sql = "UPDATE available_leaves 
                                 SET casual_leaves = 21, 
                                     rest_leaves = 24, 
                                     other_leaves = 0, 
                                     last_reset = '$january_first'";

            if ($conn->query($reset_leaves_sql) === TRUE) {
            } else {
                echo "Error resetting leave balances: " . $conn->error;
            }
        }
    }
}

?>
