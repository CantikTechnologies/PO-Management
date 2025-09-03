<?php
// Simple test file to check dashboard functionality
echo "<h1>Dashboard Test Page</h1>";

// Test database connection
echo "<h2>Testing Database Connections</h2>";

try {
    $conn = new mysqli('127.0.0.1', 'root', '', 'po_management');
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>❌ Database connection failed: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Database connection successful!</p>";
        
        // Check if database exists
        if ($conn->select_db('po_management')) {
            echo "<p style='color: green;'>✅ Database 'po_management' selected successfully!</p>";
            
            // Check tables
            $tables = ['billing_details', 'outsourcing_detail', 'po_details'];
            foreach ($tables as $table) {
                $result = $conn->query("SHOW TABLES LIKE '$table'");
                if ($result && $result->num_rows > 0) {
                    echo "<p style='color: green;'>✅ Table '$table' exists</p>";
                    
                    // Get record count
                    $countResult = $conn->query("SELECT COUNT(*) as count FROM $table");
                    if ($countResult) {
                        $count = $countResult->fetch_assoc()['count'];
                        echo "<p style='color: blue;'>📊 Table '$table' has $count records</p>";
                    }
                } else {
                    echo "<p style='color: orange;'>⚠️ Table '$table' does not exist</p>";
                }
            }
        } else {
            echo "<p style='color: red;'>❌ Failed to select database 'po_management'</p>";
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// Test file includes
echo "<h2>Testing File Includes</h2>";

$files = [
    '../shared/nav.php' => 'Navigation PHP',
    '../shared/nav.css' => 'Navigation CSS',
    'style.css' => 'Dashboard CSS',
    'cantik_logo.png' => 'Logo Image'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $description file exists: $file</p>";
    } else {
        echo "<p style='color: red;'>❌ $description file missing: $file</p>";
    }
}

// Test session
echo "<h2>Testing Session</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p style='color: green;'>✅ Session is active</p>";
    if (isset($_SESSION['username'])) {
        echo "<p style='color: green;'>✅ User logged in: " . htmlspecialchars($_SESSION['username']) . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ No user session found</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Session is not active</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.php'>Go to Dashboard</a></p>";
echo "<p><a href='../PO_Details/setup_database.php'>Setup PO Details Database</a></p>";
echo "<p><a href='../Billing_Paydetails/setup_database.php'>Setup Billing Database</a></p>";
echo "<p><a href='../Outsourcing_Detail/setup_database.php'>Setup Outsourcing Database</a></p>";
?>
