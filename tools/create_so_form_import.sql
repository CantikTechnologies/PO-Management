-- Create an insertable table to hold SO Form data imported from CSV
CREATE TABLE IF NOT EXISTS so_form_import (
  id INT AUTO_INCREMENT PRIMARY KEY,
  project_name VARCHAR(255) NULL,
  cost_center VARCHAR(255) NULL,
  customer_po_no VARCHAR(64) NULL,
  customer_po_value DECIMAL(15,2) DEFAULT 0,
  billed_till_date DECIMAL(15,2) DEFAULT 0,
  remaining_balance_po DECIMAL(15,2) DEFAULT 0,
  vendor_name VARCHAR(255) NULL,
  vendor_po_no VARCHAR(128) NULL,
  vendor_po_value DECIMAL(15,2) DEFAULT 0,
  vendor_invoicing_till_date DECIMAL(15,2) DEFAULT 0,
  remaining_vendor_balance DECIMAL(15,2) DEFAULT 0,
  sale_margin_till_date DECIMAL(7,2) DEFAULT 0,
  target_gm DECIMAL(7,2) DEFAULT 0,
  variance_in_gm DECIMAL(7,2) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_project_name (project_name),
  INDEX idx_cost_center (cost_center),
  INDEX idx_po_no (customer_po_no)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

