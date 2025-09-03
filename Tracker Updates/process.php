<?php
// Include database configuration
require_once 'config.php';

// Set content type to JSON for AJAX requests
header('Content-Type: application/json');

try {
    // Get database connection
    $pdo = getDatabaseConnection();
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate and sanitize input data
        $actionReqBy = $_POST['actionReqBy'] ?? '';
        $requestDate = $_POST['requestDate'] ?? '';
        $costCenter = $_POST['costCenter'] ?? '';
        $actionReq = $_POST['actionReq'] ?? '';
        $actionOwner = $_POST['actionOwner'] ?? '';
        $status = $_POST['status'] ?? '';
        $completionDate = $_POST['completionDate'] ?? null;
        $remark = $_POST['remark'] ?? '';

        // Validate required fields
        if (empty($actionReqBy) || empty($requestDate) || empty($costCenter) || 
            empty($actionReq) || empty($actionOwner) || empty($status)) {
            throw new Exception('All required fields must be filled');
        }

        // Prepare SQL statement
        $sql = "INSERT INTO finance_tasks (action_req_by, request_date, cost_center, 
                action_req, action_owner, status, completion_date, remark, created_at) 
                VALUES (:action_req_by, :request_date, :cost_center, :action_req, 
                :action_owner, :status, :completion_date, :remark, NOW())";

        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':action_req_by', $actionReqBy);
        $stmt->bindParam(':request_date', $requestDate);
        $stmt->bindParam(':cost_center', $costCenter);
        $stmt->bindParam(':action_req', $actionReq);
        $stmt->bindParam(':action_owner', $actionOwner);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':completion_date', $completionDate);
        $stmt->bindParam(':remark', $remark);

        // Execute the statement
        $stmt->execute();

        echo json_encode([
            'success' => true, 
            'message' => 'Task submitted successfully!'
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

// Handle AJAX request to get tasks
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_tasks') {
    try {
        $limitParam = $_GET['limit'] ?? '10';

        if ($limitParam === 'all') {
            $sql = "SELECT * FROM finance_tasks ORDER BY created_at DESC";
            $stmt = $pdo->prepare($sql);
        } else {
            $limit = (int)$limitParam;
            if ($limit <= 0) { $limit = 10; }
            $sql = "SELECT * FROM finance_tasks ORDER BY created_at DESC LIMIT :limit";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true, 
            'tasks' => $tasks
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Error fetching tasks: ' . $e->getMessage()
        ]);
    }
}
?>