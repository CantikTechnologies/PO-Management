 <?php
// db.php
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$dbname = 'po_management_new';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    // Avoid emitting HTML; let callers JSON-encode an error
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success'=>false,'error'=>'Database connection failed']);
    exit;
}
$mysqli->set_charset('utf8mb4');
// echo "connection success";
?>