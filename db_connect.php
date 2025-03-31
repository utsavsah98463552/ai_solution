<?php
// db_connect.php
$host = 'localhost';        // Database host
$dbname = 'ai_solutions';   // Database name from previous schema
$username = 'root';         // Default MySQL username (update as needed)
$password = 'root';             // Default MySQL password (update as needed)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>