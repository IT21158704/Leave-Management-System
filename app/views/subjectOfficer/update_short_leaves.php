<?php
include('../../../config/config.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['short_leaves'])) {
    $id = intval($_POST['id']);
    $short_leaves = intval($_POST['short_leaves']);

    // Step 1: Fetch current values of casual_leaves and rest_leaves
    $stmt = $conn->prepare("SELECT casual_leaves, rest_leaves FROM available_leaves WHERE user_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $current_row = $result->fetch_assoc();
        $current_casual_leaves = $current_row['casual_leaves'];
        $current_rest_leaves = $current_row['rest_leaves'];

        // Initialize variables to hold the updated values
        $updated_casual_leaves = $current_casual_leaves;
        $updated_rest_leaves = $current_rest_leaves;

        // Step 2: Calculate leaves to reduce
        $effective_short_leaves = max(0, $short_leaves - 2); // Skip the first 2 short leaves
        $newShort_leaves = $effective_short_leaves * 0.5; // Reduce remaining by 50%

        // Check if reduction is necessary
        if ($newShort_leaves > 0) {
            if ($current_casual_leaves >= $newShort_leaves) {
                // If casual leaves are enough, reduce from casual leaves
                $updated_casual_leaves -= $newShort_leaves;
            } else {
                // Reduce all casual leaves and the rest from rest leaves
                $remaining_short_leaves = $newShort_leaves - $current_casual_leaves; // Remaining short leaves after using casual leaves
                $updated_casual_leaves = 0; // Casual leaves are now 0

                if ($current_rest_leaves >= $remaining_short_leaves) {
                    // If there are enough rest leaves, reduce from rest leaves
                    $updated_rest_leaves -= $remaining_short_leaves;
                } else {
                    // Not enough leaves available
                    echo json_encode(['success' => false, 'error' => 'Not enough leaves available.']);
                    exit();
                }
            }
        }

        // Step 3: Insert the current value of short_leaves into short_leave_history
        $history_stmt = $conn->prepare("INSERT INTO short_leave_history (user_id, short_leaves) VALUES (?, ?)");
        $history_stmt->bind_param("ii", $id, $short_leaves);

        if ($history_stmt->execute()) {
            // Step 4: Update the available_leaves table with the new values
            $update_stmt = $conn->prepare("UPDATE available_leaves SET casual_leaves = ?, rest_leaves = ?, last_reset = NOW() WHERE user_id = ?");
            $update_stmt->bind_param("ddi", $updated_casual_leaves, $updated_rest_leaves, $id);

            if ($update_stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => $update_stmt->error]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => $history_stmt->error]);
        }
        $history_stmt->close(); // Close history_stmt
    } else {
        echo json_encode(['success' => false, 'error' => 'Record not found']);
        exit();
    }
    $stmt->close();
    $conn->close();
}
?>
