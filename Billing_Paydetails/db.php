<?php
// Shared DB connector for Billing module
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'po_management';

$conn = @new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    die('DB connection failed: ' . $conn->connect_error);
}
?>
<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'po_management_new';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Don't die, just log the error silently
    error_log('PDO Connection failed: ' . $e->getMessage());
}

// For backward compatibility with existing code
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    // Don't die, just log the error silently
    error_log('MySQLi Connection failed: ' . $conn->connect_error);
    // Set $conn to null so we can check for it
    $conn = null;
} else {
    $conn->set_charset('utf8mb4');
}
?>