<?php
include 'auth.php';
include 'db.php';

$success = '';
$error = '';

if ($_POST) {
    $customerID = $_POST['customerID'];
    $unitsConsumed = $_POST['unitsConsumed'];
    $ratePerUnit = $_POST['ratePerUnit'];
    $billDate = $_POST['billDate'];
    
    $sql = "INSERT INTO Bill (CustomerID, BillDate, UnitsConsumed, RatePerUnit, BillAmount) 
            VALUES (?, ?, ?, ?, ?)";
    $billAmount = $unitsConsumed * $ratePerUnit;
    $params = array($customerID, $billDate, $unitsConsumed, $ratePerUnit, $billAmount);
    
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt) {
        $success = "Bill added successfully!";
    } else {
        $error = "Error adding bill: " . print_r(sqlsrv_errors(), true);
    }
}

// Get all customers for dropdown
$customers = sqlsrv_query($conn, "SELECT CustomerID, Name FROM Customer ORDER BY Name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Bill - EBMS</title>
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
                EBMS - Add Bill
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
                            <i class="fas fa-plus me-2"></i>
                            Add New Electricity Bill
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
                                        <label for="customerID" class="form-label">
                                            <i class="fas fa-user me-2"></i>Customer
                                        </label>
                                        <select class="form-select" id="customerID" name="customerID" required>
                                            <option value="">Select Customer</option>
                                            <?php
                                            while ($customer = sqlsrv_fetch_array($customers, SQLSRV_FETCH_ASSOC)) {
                                                echo "<option value='{$customer['CustomerID']}'>{$customer['Name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="billDate" class="form-label">
                                            <i class="fas fa-calendar me-2"></i>Bill Date
                                        </label>
                                        <input type="date" class="form-control" id="billDate" name="billDate" 
                                               value="<?php echo date('Y-m-d'); ?>" required>
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
                                               name="unitsConsumed" min="1" step="1" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ratePerUnit" class="form-label">
                                            <i class="fas fa-money-bill-wave me-2"></i>Rate Per Unit (Rs)
                                        </label>
                                        <input type="number" class="form-control" id="ratePerUnit" 
                                               name="ratePerUnit" min="0.01" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calculator me-2"></i>Total Amount
                                </label>
                                <div class="form-control" id="totalAmount" style="background-color: #f8f9fa;">
                                    Rs 0.00
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Add Bill
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
            document.getElementById('totalAmount').textContent = 'Rs ' + total.toFixed(2);
        }
        
        document.getElementById('unitsConsumed').addEventListener('input', calculateTotal);
        document.getElementById('ratePerUnit').addEventListener('input', calculateTotal);
    </script>
</body>
</html>
