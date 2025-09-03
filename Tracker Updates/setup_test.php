<?php
// Simple test file to check if everything is working
require_once 'config.php';

echo "<h1>Finance Tracker Setup Test</h1>";

// Test 1: Check if PHP is working
echo "<h2>Test 1: PHP is working</h2>";
echo "<p style='color: green;'>✓ PHP is working correctly</p>";

// Test 2: Check database connection
echo "<h2>Test 2: Database Connection</h2>";
try {
    $pdo = getDatabaseConnection();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p><strong>Please make sure:</strong></p>";
    echo "<ul>";
    echo "<li>XAMPP is running (Apache and MySQL)</li>";
    echo "<li>Database 'finance_tracker' exists</li>";
    echo "<li>Check config.php for correct database settings</li>";
    echo "</ul>";
    exit;
}

// Test 3: Check if table exists
echo "<h2>Test 3: Database Table</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'finance_tasks'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Table 'finance_tasks' exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Table 'finance_tasks' does not exist</p>";
        echo "<p>Please run the database setup script</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error checking table: " . $e->getMessage() . "</p>";
}

// Test 4: Check if we can insert and retrieve data
echo "<h2>Test 4: Data Operations</h2>";
try {
    // Try to insert a test record
    $stmt = $pdo->prepare("INSERT INTO finance_tasks (task_date, emp_dept, emp_id, action_req_by, action_req, action_owner, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute(['2024-01-01', 'Test', 'TEST001', 'Test User', 'Test Action', 'Test Owner', 'Incomplete']);
    
    // Get the inserted ID
    $testId = $pdo->lastInsertId();
    
    // Try to retrieve the record
    $stmt = $pdo->prepare("SELECT * FROM finance_tasks WHERE id = ?");
    $stmt->execute([$testId]);
    $result = $stmt->fetch();
    
    if ($result) {
        echo "<p style='color: green;'>✓ Data operations working correctly</p>";
        
        // Clean up test data
        $stmt = $pdo->prepare("DELETE FROM finance_tasks WHERE id = ?");
        $stmt->execute([$testId]);
        echo "<p style='color: blue;'>✓ Test data cleaned up</p>";
    } else {
        echo "<p style='color: red;'>✗ Data operations failed</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Data operations error: " . $e->getMessage() . "</p>";
}

// Test 5: Check file permissions
echo "<h2>Test 5: File Permissions</h2>";
$files = ['process.php', 'update_task.php', 'delete_task.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ $file exists</p>";
    } else {
        echo "<p style='color: red;'>✗ $file missing</p>";
    }
}

echo "<h2>Setup Instructions:</h2>";
echo "<ol>";
echo "<li>Make sure XAMPP is running (Apache and MySQL)</li>";
echo "<li>Create database by running: <code>CREATE DATABASE finance_tracker;</code></li>";
echo "<li>Import the database structure from <code>database_setup.sql</code></li>";
echo "<li>Access your application at: <code>http://localhost/your-folder-name/index.html</code></li>";
echo "</ol>";

echo "<h2>Quick Database Setup:</h2>";
echo "<p>If you haven't set up the database yet, you can:</p>";
echo "<ol>";
echo "<li>Open phpMyAdmin (http://localhost/phpmyadmin)</li>";
echo "<li>Create a new database called 'finance_tracker'</li>";
echo "<li>Import the <code>database_setup.sql</code> file</li>";
echo "</ol>";

echo "<p><strong>If all tests pass, your application should be ready to use!</strong></p>";
?> 