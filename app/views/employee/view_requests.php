<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Leave Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .request-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .request-details {
            margin-top: 20px;
        }
        .request-details label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            color: #007bff;
        }
        .request-details p {
            margin: 5px 0;
            padding: 8px;
            background: #eef4ff;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .actions {
            margin-top: 20px;
            text-align: center;
        }
        button {
            padding: 10px 15px;
            margin: 5px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }
        .back-btn {
            background-color: #6c757d;
            color: white;
        }
        .accept-btn {
            background-color: #28a745;
            color: white;
        }
        .reject-btn {
            background-color: #dc3545;
            color: white;
        }
        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <div class="request-container">
        <h1>View Leave Request</h1>
        
        <div class="request-details">
            <label>Employee Name:</label>
            <p>John Doe</p>

            <label>Leave Type:</label>
            <p>Annual Leave</p>

            <label>Start Date:</label>
            <p>2024-09-05</p>

            <label>End Date:</label>
            <p>2024-09-10</p>

            <label>Reason:</label>
            <p>Family vacation planned for a long time. Need a break from work for mental relaxation.</p>

            <label>Status:</label>
            <p>Pending</p>
        </div>

        <div class="actions">
            <button class="back-btn" onclick="window.history.back();">Back</button>
            <button class="accept-btn">Accept</button>
            <button class="reject-btn">Reject</button>
        </div>
    </div>

</body>
</html>
