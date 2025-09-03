<?php
require 'db.php';

echo "<h2>Database Connection Test</h2>";

// Test database connection
if (isset($conn) && $conn !== null) {
    echo "<p style='color: green;'>✅ Database connection: SUCCESS</p>";
    
    // Test if table exists
    $result = $conn->query("SHOW TABLES LIKE 'billing_details'");
    if ($result && $result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table 'billing_details' exists</p>";
        
        // Count records
        $count_result = $conn->query("SELECT COUNT(*) as count FROM billing_details");
        if ($count_result) {
            $count = $count_result->fetch_assoc()['count'];
            echo "<p style='color: blue;'>📊 Total records in billing_details: $count</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Table 'billing_details' does not exist</p>";
        echo "<p>You may need to run the create_table.sql file to create the table.</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Database connection: FAILED</p>";
    echo "<p>Please check your database configuration in db.php</p>";
}

$conn->close();
?>
