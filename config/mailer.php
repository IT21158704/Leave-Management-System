<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendMail($toEmail, $toName, $subject, $body, $altBody = '') {
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                   //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'testmyweatherapp@gmail.com';           //SMTP username
        $mail->Password   = 'zouwmvmxmlrvjxum';                     //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to

        //Recipients
        $mail->setFrom('testmyweatherapp@gmail.com', 'Ministry of Fisheries');
        $mail->addAddress($toEmail, $toName);  //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody ?: 'This is the plain text for non-HTML clients';

        //Send email
        $mail->send();
        return true; // Email sent successfully
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function leaveConfirmationBody($userName, $leaveType, $startDate, $endDate, $status) {
    return '
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .email-container {
                max-width: 600px;
                margin: 20px auto;
                background-color: #ffffff;
                padding: 20px;
                border: 1px solid #e0e0e0;
                box-shadow: 0 2px 3px rgba(0,0,0,0.1);
            }
            .email-header {
                background-color: #0073e6;
                color: #ffffff;
                padding: 10px;
                text-align: center;
                font-size: 24px;
            }
            .email-body {
                padding: 20px;
                font-size: 16px;
                line-height: 1.6;
            }
            .email-body h2 {
                color: #0073e6;
            }
            .email-footer {
                margin-top: 20px;
                font-size: 14px;
                color: #888888;
                text-align: center;
            }
            .button {
                background-color: #0073e6;
                color: white;
                padding: 10px 20px;
                text-decoration: none;
                font-size: 16px;
                display: inline-block;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="email-header">
                Leave Management System
            </div>
            <div class="email-body">
                <h2>Hello ' . htmlspecialchars($userName) . ',</h2>
                <p>Your leave request has been <strong>' . htmlspecialchars($status) . '</strong>.</p>
                <p><strong>Leave Type:</strong> ' . htmlspecialchars($leaveType) . '</p>
                <p><strong>From:</strong> ' . htmlspecialchars($startDate) . '</p>
                <p><strong>To:</strong> ' . htmlspecialchars($endDate) . '</p>
            </div>
            <div class="email-footer">
                <p>Leave Management System | Ministry of Fisheries</p>
            </div>
        </div>
    </body>
    </html>
    ';
}

function leaveRequestEmailBody($userName, $leaveType, $startDate, $endDate, $reason) {
    return '
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .email-container {
                max-width: 600px;
                margin: 20px auto;
                background-color: #ffffff;
                padding: 20px;
                border: 1px solid #e0e0e0;
                box-shadow: 0 2px 3px rgba(0,0,0,0.1);
            }
            .email-header {
                background-color: #0073e6;
                color: #ffffff;
                padding: 10px;
                text-align: center;
                font-size: 24px;
            }
            .email-body {
                padding: 20px;
                font-size: 16px;
                line-height: 1.6;
            }
            .email-body h2 {
                color: #0073e6;
            }
            .email-footer {
                margin-top: 20px;
                font-size: 14px;
                color: #888888;
                text-align: center;
            }
            .button {
                background-color: #0073e6;
                color: white;
                padding: 10px 20px;
                text-decoration: none;
                font-size: 16px;
                display: inline-block;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="email-header">
                Leave Management System
            </div>
            <div class="email-body">
                <h2>Hello,</h2>
                <p><strong>' . htmlspecialchars($userName) . '</strong> has requested leave.</p>
                <p><strong>Leave Type:</strong> ' . htmlspecialchars($leaveType) . '</p>
                <p><strong>From:</strong> ' . htmlspecialchars($startDate) . '</p>
                <p><strong>To:</strong> ' . htmlspecialchars($endDate) . '</p>
                <p><strong>Reason for Leave:</strong> ' . htmlspecialchars($reason) . '</p>
            </div>
            <div class="email-footer">
                <p>Leave Management System | Ministry of Fisheries</p>
            </div>
        </div>
    </body>
    </html>
    ';
}

// $body = leaveConfirmationBody('Nadun Dilshan', 'Casual Leave', '2024-09-12', '2024-09-15', 'Approved');
// sendMail('nadun.dilshan.733@gmail.com', 'Nadun Dilshan', 'Leave Request Status', $body);

// $body = leaveRequestEmailBody('Nadun Dilshan', 'Annual Leave', '2024-09-12', '2024-09-20', 'Family emergency');
// sendMail('nadun.dilshan.733@gmail.com', 'Manager Name', 'Leave Request from Nadun Dilshan', $body);

function newUserEmailBody($userName, $nic, $password = null) {
    $passwordSection = $password ? '<p><strong>Temporary Password:</strong> ' . htmlspecialchars($password) . '</p>' : '';
    
    return '
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .email-container {
                max-width: 600px;
                margin: 20px auto;
                background-color: #ffffff;
                padding: 20px;
                border: 1px solid #e0e0e0;
                box-shadow: 0 2px 3px rgba(0,0,0,0.1);
            }
            .email-header {
                background-color: #0073e6;
                color: #ffffff;
                padding: 10px;
                text-align: center;
                font-size: 24px;
            }
            .email-body {
                padding: 20px;
                font-size: 16px;
                line-height: 1.6;
            }
            .email-body h2 {
                color: #0073e6;
            }
            .email-footer {
                margin-top: 20px;
                font-size: 14px;
                color: #888888;
                text-align: center;
            }
            .button {
                background-color: #0073e6;
                color: white;
                padding: 10px 20px;
                text-decoration: none;
                font-size: 16px;
                display: inline-block;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="email-header">
                Welcome to Leave Management System
            </div>
            <div class="email-body">
                <h2>Hello ' . htmlspecialchars($userName) . ',</h2>
                <p>Your account has been successfully created in the Leave Management System.</p>
                <p><strong>NIC:</strong> ' . htmlspecialchars($nic) . '</p>
                ' . $passwordSection . '
                <p>You can now log in to your account and manage your leaves.</p>
            </div>
            <div class="email-footer">
                <p>Leave Management System | Ministry of Fisheries</p>
            </div>
        </div>
    </body>
    </html>
    ';
}

function emergencyLeaveEmailBody($submitterName, $empOnLeave, $commenceLeaveDate, $resumeDate, $reason) {
    return '
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .email-container {
                max-width: 600px;
                margin: 20px auto;
                background-color: #ffffff;
                padding: 20px;
                border: 1px solid #e0e0e0;
                box-shadow: 0 2px 3px rgba(0,0,0,0.1);
            }
            .email-header {
                background-color: #0073e6;
                color: #ffffff;
                padding: 10px;
                text-align: center;
                font-size: 24px;
            }
            .email-body {
                padding: 20px;
                font-size: 16px;
                line-height: 1.6;
            }
            .email-body h2 {
                color: #0073e6;
            }
            .email-footer {
                margin-top: 20px;
                font-size: 14px;
                color: #888888;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="email-header">
                Emergency Leave Request
            </div>
            <div class="email-body">
                <h2>Hello,</h2>
                <p><strong>' . htmlspecialchars($submitterName) . '</strong> has submitted an emergency leave request on behalf of <strong>' . htmlspecialchars($empOnLeave) . '</strong>.</p>
                <p><strong>Commence Date:</strong> ' . htmlspecialchars($commenceLeaveDate) . '</p>
                <p><strong>Resume Date:</strong> ' . htmlspecialchars($resumeDate) . '</p>
                <p><strong>Reason for Leave:</strong> ' . htmlspecialchars($reason) . '</p>
            </div>
            <div class="email-footer">
                <p>Leave Management System | Ministry of Fisheries</p>
            </div>
        </div>
    </body>
    </html>
    ';
}

// $body = newUserEmailBody('Nadun Dilshan', 'nadun.dilshan.733@gmail.com', 'temporaryPassword123');
// sendMail('nadun.dilshan.733@gmail.com', 'Nadun Dilshan', 'Welcome to Leave Management System', $body);

?>