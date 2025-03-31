<?php
// submit_inquiry.php
session_start(); // Start session for storing messages
require_once '../db_connect.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $company = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_STRING);
    $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
    $job_title = filter_input(INPUT_POST, 'jobTitle', FILTER_SANITIZE_STRING);
    $job_details = filter_input(INPUT_POST, 'jobDetails', FILTER_SANITIZE_STRING);
    $inquiry_date = date('Y-m-d'); // Current date

    // Array to collect console logs
    $logs = ["PHP: Inquiry submission started for $name ($email)"];

    // Basic validation
    if (empty($name) || empty($email) || empty($phone) || empty($company) || empty($country) || empty($job_title) || empty($job_details)) {
        $logs[] = "PHP: Validation failed - missing required fields";
        $_SESSION['message'] = "All fields are required.";
        $_SESSION['message_type'] = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $logs[] = "PHP: Validation failed - invalid email format for $email";
        $_SESSION['message'] = "Invalid email format.";
        $_SESSION['message_type'] = "danger";
    } else {
        try {
            $logs[] = "PHP: Preparing to insert inquiry into database";
            // Prepare and execute the insert query
            $stmt = $pdo->prepare("
                INSERT INTO inquiries (name, company, email, country, job_title, inquiry_date, job_details, created_at)
                VALUES (:name, :company, :email, :country, :job_title, :inquiry_date, :job_details, NOW())
            ");
            $stmt->execute([
                ':name' => $name,
                ':company' => $company,
                ':email' => $email,
                ':country' => $country,
                ':job_title' => $job_title,
                ':inquiry_date' => $inquiry_date,
                ':job_details' => $job_details
            ]);

            $logs[] = "PHP: Inquiry submitted successfully for $name";
            $_SESSION['message'] = "Inquiry submitted successfully!";
            $_SESSION['message_type'] = "success";
        } catch (PDOException $e) {
            $logs[] = "PHP: Database error - " . $e->getMessage();
            $_SESSION['message'] = "Error submitting inquiry: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
        }
    }

    // Inject console logs into the redirect page
    echo "<script>";
    foreach ($logs as $log) {
        echo "console.log('" . addslashes($log) . "');";
    }
    echo "</script>";

    // Redirect back to contact.php
    header("Location: ../contact.php");
    exit();
}

// If not a POST request, redirect to contact page
header("Location: ../contact.php");
exit();
?>