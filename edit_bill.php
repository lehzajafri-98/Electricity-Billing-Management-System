<?php
include 'auth.php';
include 'db.php';

$success = '';
$error = '';
$billID = $_GET['id'] ?? 0;

// Get bill data
$sql = "SELECT b.*, c.Name as CustomerName FROM Bill b 
        JOIN Customer c ON b.CustomerID = c.CustomerID 
        WHERE b.BillID = ?";
$stmt = sqlsrv_query($conn, $sql, array($billID));
$bill = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$bill) {
    header("Location: index.php");
    exit;
}

if ($_POST) {
    $unitsConsumed = $_POST['unitsConsumed'];
    $ratePerUnit = $_POST['ratePerUnit'];
    $billDate = $_POST['billDate'];
    $paymentDate = $_POST['paymentDate'] ?: null;
    
    $sql = "UPDATE Bill SET 
            BillDate = ?, 
            UnitsConsumed = ?, 
            RatePerUnit = ?, 
            BillAmount = ?, 
            PaymentDate = ? 
            WHERE BillID = ?";
    
    $billAmount = $unitsConsumed * $ratePerUnit;
    $params = array($billDate, $unitsConsumed, $ratePerUnit, $billAmount, $paymentDate, $billID);
    
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt) {
        $success = "Bill updated successfully!";
        // Refresh bill data
        $sql = "SELECT b.*, c.Name as CustomerName FROM Bill b 
                JOIN Customer c ON b.CustomerID = c.CustomerID 
                WHERE b.BillID = ?";
        $stmt = sqlsrv_query($conn, $sql, array($billID));
        $bill = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    } else {
        $error = "Error updating bill: " . print_r(sqlsrv_errors(), true);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bill - EBMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-bolt"></i>
                EBMS - Edit Bill
            </a>
            <div class="navbar-nav ms-auto">
                <a href="index.php" class="btn btn-logout me-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Dashboard
                </a>
                <a href="logout.php" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container dashboard-container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="main-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit me-2"></i>
                            Edit Bill #<?php echo $bill['BillID']; ?>
                        </h3>
                    </div>
                    
                    <div class="p-4">
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo $success; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-user me-2"></i>Customer
                                        </label>
                                        <div class="form-control" style="background-color: #f8f9fa;">
                                            <?php echo $bill['CustomerName']; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="billDate" class="form-label">
                                            <i class="fas fa-calendar me-2"></i>Bill Date
                                        </label>
                                        <input type="date" class="form-control" id="billDate" name="billDate" 
                                               value="<?php echo $bill['BillDate']->format('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="unitsConsumed" class="form-label">
                                            <i class="fas fa-bolt me-2"></i>Units Consumed (kWh)
                                        </label>
                                        <input type="number" class="form-control" id="unitsConsumed" 
                                               name="unitsConsumed" min="1" step="1" 
                                               value="<?php echo $bill['UnitsConsumed']; ?>" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ratePerUnit" class="form-label">
                                            <i class="fas fa-dollar-sign me-2"></i>Rate Per Unit ($)
                                        </label>
                                        <input type="number" class="form-control" id="ratePerUnit" 
                                               name="ratePerUnit" min="0.01" step="0.01" 
                                               value="<?php echo $bill['RatePerUnit']; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-calculator me-2"></i>Total Amount
                                        </label>
                                        <div class="form-control" id="totalAmount" style="background-color: #f8f9fa;">
                                            $<?php echo number_format($bill['BillAmount'], 2); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="paymentDate" class="form-label">
                                            <i class="fas fa-credit-card me-2"></i>Payment Date (Optional)
                                        </label>
                                        <input type="date" class="form-control" id="paymentDate" name="paymentDate" 
                                               value="<?php echo $bill['PaymentDate'] ? $bill['PaymentDate']->format('Y-m-d') : ''; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Bill
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Calculate total amount automatically
        function calculateTotal() {
            const units = parseFloat(document.getElementById('unitsConsumed').value) || 0;
            const rate = parseFloat(document.getElementById('ratePerUnit').value) || 0;
            const total = units * rate;
            document.getElementById('totalAmount').textContent = '$' + total.toFixed(2);
        }
        
        document.getElementById('unitsConsumed').addEventListener('input', calculateTotal);
        document.getElementById('ratePerUnit').addEventListener('input', calculateTotal);
    </script>
</body>
</html>