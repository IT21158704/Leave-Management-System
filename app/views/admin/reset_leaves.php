<?php
// Get the current date
$current_date = date('Y-m-d');
// $current_date = date('2024-01-01');

// Check if today is January 1st
if ($current_date == date('Y') . '-01-01') {
    // SQL to update casual_leaves and rest_leaves for each user
    // The logic here is: new casual_leaves = remaining casual_leaves + default 24
    // The same applies to rest_leaves

    $sql = "UPDATE available_leaves 
            SET casual_leaves = casual_leaves + 24, 
                rest_leaves = rest_leaves + 21";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        echo "Leave balances have been reset successfully.";
    } else {
        echo "Error resetting leave balances: " . $conn->error;
    }
}
