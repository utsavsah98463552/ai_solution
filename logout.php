<?php
session_start();

// Console logs for backend
$logs = ["PHP: Logout initiated for {$_SESSION['admin_username']}"];
session_unset();
session_destroy();
$logs[] = "PHP: Session destroyed, logout successful";

// Inject console logs into the response
echo "<script>";
foreach ($logs as $log) {
    echo "console.log('" . addslashes($log) . "');";
}
echo "</script>";

// Redirect to login page
header('Location: login.php');
exit();
?>