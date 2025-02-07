<?php
include 'dbconnection.php';

// Fetch Sales Overview Report
$salesOverviewSql = "SELECT COUNT(OrderID) AS TotalOrders, 
                            SUM(TotalAmount) AS TotalSales, 
                            DATE(OrderDate) AS Date 
                     FROM `order` 
                     WHERE OrderStatus = 'Delivered' 
                     GROUP BY DATE(OrderDate)";
$salesOverviewResult = $conn->query($salesOverviewSql);

// Fetch Current Stock Levels
$currentStockSql = "SELECT Name, StockQuantity FROM `product` WHERE IsActive = 'Active'";
$currentStockResult = $conn->query($currentStockSql);

// Fetch Pending Orders
$pendingOrdersSql = "SELECT O.OrderID, O.CID, C.C_Name, O.OrderDate, O.TotalAmount 
                     FROM `order` O 
                     JOIN `customers` C ON O.CID = C.CID 
                     WHERE O.OrderStatus = 'Pending'";
$pendingOrdersResult = $conn->query($pendingOrdersSql);

// Fetch Failed Payments Report
$failedPaymentsSql = "SELECT P.OrderID, C.C_Name, P.PaymentDate, P.PaymentStatus 
                      FROM `payment` P 
                      JOIN `order` O ON P.OrderID = O.OrderID 
                      JOIN `customers` C ON O.CID = C.CID 
                      WHERE P.PaymentStatus = 'Failed'";
$failedPaymentsResult = $conn->query($failedPaymentsSql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports 1 to 5</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px; /* Smaller font size */
        }
        .report-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr); /* 2 reports per row */
            gap: 10px; /* Smaller gap between reports */
            margin-bottom: 10px;
        }
        .report-box {
            border: 1px solid #ccc;
            padding: 5px; /* Reduced padding */
            border-radius: 4px; /* Smaller border radius */
            box-shadow: 0 0 5px rgba(0,0,0,0.1); /* Lighter shadow */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px; /* Smaller table font size */
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 4px; /* Smaller cell padding */
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        canvas {
            width: 100% !important;
            height: 150px !important; /* Smaller chart height */
        }
        h2 {
            font-size: 14px; /* Smaller heading size */
        }
        @media (max-width: 768px) {
            .report-container {
                grid-template-columns: 1fr; /* Stacks reports on smaller screens */
            }
        }
    </style>
</head>
<body>
    <h1 style="font-size: 16px;">Business Reports</h1> <!-- Smaller title -->

    <div class="report-container">
        <!-- Sales Overview Report -->
        <div class="report-box">
            <h2>Sales Overview Report</h2>
            <canvas id="salesChart"></canvas>
        </div>

        <!-- Current Stock Levels -->
        <div class="report-box">
            <h2>Current Stock Levels</h2>
            <table>
                <tr><th>Product Name</th><th>Stock Quantity</th></tr>
                <?php while($row = $currentStockResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['StockQuantity']); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <div class="report-container">
        <!-- Pending Orders -->
        <div class="report-box">
            <h2>Pending Orders</h2>
            <table>
                <tr><th>Order ID</th><th>Customer ID</th><th>Customer Name</th><th>Order Date</th><th>Total Amount</th></tr>
                <?php while($row = $pendingOrdersResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['OrderID']); ?></td>
                        <td><?php echo htmlspecialchars($row['CID']); ?></td>
                        <td><?php echo htmlspecialchars($row['C_Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['OrderDate']); ?></td>
                        <td><?php echo htmlspecialchars($row['TotalAmount']); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        <!-- Failed Payments Report -->
        <div class="report-box">
            <h2>Failed Payments Report</h2>
            <table>
                <tr><th>Order ID</th><th>Customer Name</th><th>Payment Date</th><th>Payment Status</th></tr>
                <?php while($row = $failedPaymentsResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['OrderID']); ?></td>
                        <td><?php echo htmlspecialchars($row['C_Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['PaymentDate']); ?></td>
                        <td><?php echo htmlspecialchars($row['PaymentStatus']); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <script>
        const salesData = {
            labels: [],
            datasets: [{
                label: 'Total Sales',
                data: [],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        };

        <?php while($row = $salesOverviewResult->fetch_assoc()) { ?>
            salesData.labels.push('<?php echo htmlspecialchars($row['Date']); ?>');
            salesData.datasets[0].data.push(<?php echo $row['TotalSales']; ?>);
        <?php } ?>

        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: salesData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
