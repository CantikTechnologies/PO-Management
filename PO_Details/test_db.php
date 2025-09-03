<?php
echo "<h2>PO Details Database Connection Test</h2>";

try {
    require_once 'db.php';
    
    if (isset($conn) && $conn !== null) {
        echo "<p style='color: green;'>✓ MySQLi connection successful!</p>";
        
        // Test database selection
        if ($conn->select_db('po_management')) {
            echo "<p style='color: green;'>✓ Database 'po_management' selected successfully!</p>";
            
            // Test table existence
            $result = $conn->query("SHOW TABLES LIKE 'po_details'");
            if ($result && $result->num_rows > 0) {
                echo "<p style='color: green;'>✓ Table 'po_details' exists!</p>";
                
                // Get table info
                $tableInfo = $conn->query("SELECT COUNT(*) as count FROM po_details");
                if ($tableInfo) {
                    $count = $tableInfo->fetch_assoc()['count'];
                    echo "<p style='color: green;'>✓ Table contains {$count} records</p>";
                }
            } else {
                echo "<p style='color: orange;'>⚠ Table 'po_details' does not exist. Run setup_database.php first.</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Failed to select database 'po_management'</p>";
        }
        
        $conn->close();
    } else {
        echo "<p style='color: red;'>✗ MySQLi connection failed</p>";
    }
    
    // Test PDO connection
    if (isset($pdo)) {
        echo "<p style='color: green;'>✓ PDO connection successful!</p>";
        
        try {
            $pdo->query("SELECT 1");
            echo "<p style='color: green;'>✓ PDO query test successful!</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>✗ PDO query test failed: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ PDO connection failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<br><a href='index.php'>← Back to PO Details Page</a>";
echo "<br><a href='setup_database.php'>← Setup Database</a>";
?>
