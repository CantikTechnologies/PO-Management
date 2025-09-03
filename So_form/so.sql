CREATE DATABASE  po_management;
USE  po_management;

CREATE TABLE so_form (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project VARCHAR(255),
    cost_centre VARCHAR(255),
    customer_po_no VARCHAR(50),
    billed_po_no DECIMAL(12,2),
    remaining_balance_in_po DECIMAL(12,2),
    vendor_name VARCHAR(255),
    cantik_po_no VARCHAR(50),
    vendor_po_value DECIMAL(12,2),
    vendor_invoicing_till_date DECIMAL(12,2),
    remaining_balance_in_po DECIMAL(12,2),
    margin_till_date VARCHAR(20),
    target_gm VARCHAR(20),
    variance_in_gm VARCHAR(20)
);

