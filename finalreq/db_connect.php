<?php
// Define database connection parameters
$servername = "localhost"; // Database server (usually localhost for local development)
$username = "root"; // Database username (default for local MySQL)
$password = ""; // Database password (empty by default for local MySQL)

try {
    // Create a new PDO (PHP Data Objects) connection to MySQL
    $conn = new PDO("mysql:host=$servername;dbname=dbclothes", $username, $password);

    // Set the PDO error mode to Exception for better error handling
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    
    echo "Connection failed: " . $e->getMessage();
}
?>