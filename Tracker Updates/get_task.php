<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "po_management";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Check if ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Get specific task by ID
    $tracker_id = (int)$_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM finance_tasks WHERE id = ?");
        $stmt->execute([$tracker_id]);
        $tracker = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$tracker) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Tracker update not found']);
            exit;
        }
        
        // Return tracker data as JSON
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $tracker]);
        
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database query failed']);
    }
} else {
    // Get all tasks
    try {
        $stmt = $pdo->query("SELECT * FROM finance_tasks ORDER BY request_date DESC, created_at DESC");
        $trackers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Return all trackers as JSON
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $trackers]);
        
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database query failed']);
    }
}
?>
