<!DOCTYPE html>
<html>
<head>
    <title>Test JavaScript Data</title>
</head>
<body>
    <h1>Test JavaScript Data Loading</h1>
    
    <div id="testResults"></div>
    
    <script>
    async function testDataLoading() {
        try {
            const response = await fetch('list.php');
            const result = await response.json();
            
            console.log('Full API Response:', result);
            
            if (result.success) {
                const entries = result.data;
                console.log('Entries array:', entries);
                
                if (entries.length > 0) {
                    const firstEntry = entries[0];
                    console.log('First entry:', firstEntry);
                    console.log('net_payble value:', firstEntry.net_payble);
                    console.log('net_payble type:', typeof firstEntry.net_payble);
                    
                    // Test the formatNumber function
                    function formatNumber(num) {
                        if (num === null || num === undefined) return '0';
                        return parseFloat(num).toLocaleString('en-IN', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                    
                    const testValue = firstEntry.net_payble;
                    const formattedValue = formatNumber(parseFloat(testValue) || 0);
                    
                    console.log('Test value:', testValue);
                    console.log('Parsed value:', parseFloat(testValue));
                    console.log('Formatted value:', formattedValue);
                    
                    // Display results
                    document.getElementById('testResults').innerHTML = `
                        <h3>Test Results:</h3>
                        <p><strong>First Entry ID:</strong> ${firstEntry.id}</p>
                        <p><strong>Vendor Inv Number:</strong> ${firstEntry.vendor_inv_number}</p>
                        <p><strong>Vendor Inv Value:</strong> ${firstEntry.vendor_inv_value}</p>
                        <p><strong>TDS Ded:</strong> ${firstEntry.tds_ded}</p>
                        <p><strong>Net Payble (raw):</strong> ${firstEntry.net_payble}</p>
                        <p><strong>Net Payble (type):</strong> ${typeof firstEntry.net_payble}</p>
                        <p><strong>Net Payble (parsed):</strong> ${parseFloat(firstEntry.net_payble)}</p>
                        <p><strong>Net Payble (formatted):</strong> ₹${formattedValue}</p>
                        <p><strong>Payment Value:</strong> ${firstEntry.payment_value}</p>
                        <p><strong>Pending Payment:</strong> ${firstEntry.pending_payment}</p>
                        
                        <h4>All Fields in First Entry:</h4>
                        <ul>
                            ${Object.keys(firstEntry).map(key => `<li><strong>${key}:</strong> ${firstEntry[key]} (${typeof firstEntry[key]})</li>`).join('')}
                        </ul>
                    `;
                } else {
                    document.getElementById('testResults').innerHTML = '<p>No entries found</p>';
                }
            } else {
                console.error('API Error:', result.error);
                document.getElementById('testResults').innerHTML = `<p>API Error: ${result.error}</p>`;
            }
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('testResults').innerHTML = `<p>Error: ${error.message}</p>`;
        }
    }
    
    // Run the test when page loads
    testDataLoading();
    </script>
    
    <hr>
    <p><a href="index.php">← Back to Outsourcing Page</a></p>
    <p><a href="debug_data.php">Debug Data</a></p>
</body>
</html>
