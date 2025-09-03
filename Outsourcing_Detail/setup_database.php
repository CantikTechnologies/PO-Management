<?php
require 'dp.php';

echo "<h2>Outsourcing Database Setup</h2>";

try {
  $check = $mysqli->query("SHOW TABLES LIKE 'outsourcing_detail'");
  if(!$check || $check->num_rows===0){
    echo "<p>Creating table  outsourcing_detail...</p>";
    $sql = "CREATE TABLE IF NOT EXISTS  outsourcing_detail (
      id INT AUTO_INCREMENT PRIMARY KEY,
      project_details TEXT,
      cost_center VARCHAR(100),
      ntt_po VARCHAR(100),
      vendor_name VARCHAR(150),
      cantik_po_no VARCHAR(100),
      cantik_po_date DATE,
      cantik_po_value DECIMAL(14,2),
      vendor_inv_frequency VARCHAR(50),
      vendor_inv_number VARCHAR(100),
      vendor_inv_date DATE,
      vendor_inv_value DECIMAL(14,2),
      tds_ded DECIMAL(14,2),
      net_payable DECIMAL(14,2),
      payment_status VARCHAR(100),
      payment_value DECIMAL(14,2),
      payment_date DATE,
      pending_payment DECIMAL(14,2),
      remarks TEXT,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    if(!$mysqli->query($sql)) throw new Exception('Create failed: '.$mysqli->error);
    echo "<p style='color:green'>✓ Table created</p>";

    // Insert a sample
    $mysqli->query("INSERT INTO  outsourcing_detail (project_details,cost_center,vendor_name,vendor_inv_number,vendor_inv_date,vendor_inv_value,tds_ded,net_payable,payment_status,payment_value,payment_date,pending_payment,remarks) VALUES ('Sample Project','CC-001','Sample Vendor','INV-001',CURDATE(),10000.00,200.00,11600.00,'Unpaid',0,NULL,11600.00,'Seed row')");
    echo "<p style='color:green'>✓ Sample row inserted</p>";
  } else {
    echo "<p style='color:green'>✓ Table  outsourcing_detail already exists</p>";
  }

  $res = $mysqli->query("DESCRIBE  outsourcing_detail");
  echo "<h3>Table Structure</h3><table border='1' cellspacing='0' cellpadding='6'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
  while($row=$res->fetch_assoc()){
    echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td><td>{$row['Extra']}</td></tr>";
  }
  echo "</table>";

  $cnt = $mysqli->query("SELECT COUNT(*) c FROM  outsourcing_detail")->fetch_assoc()['c'];
  echo "<p><strong>Total rows:</strong> ".$cnt."</p>";
  echo "<p><a href='index.php'>&larr; Back to Outsourcing Page</a></p>";

} catch(Exception $e){ echo "<p style='color:red'>Error: ".$e->getMessage()."</p>"; }
?>
