<?php
include 'auth.php';
include 'db.php';

$success = '';
$error = '';

// Handle new complaint submission
if ($_POST) {
    $customerID = $_POST['customerID'];
    $subject = $_POST['subject'];
    $description = $_POST['description'];

    $sql = "INSERT INTO Complaint (CustomerID, Subject, Description, Status) VALUES (?, ?, ?, 'Open')";
    $params = array($customerID, $subject, $description);
    
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt) {
        $success = "Complaint added successfully!";
    } else {
        $error = "Error adding complaint: " . print_r(sqlsrv_errors(), true);
    }
}

// Get all complaints
$complaints = sqlsrv_query($conn, "SELECT * FROM Complaint");
$customers = sqlsrv_query($conn, "SELECT CustomerID, Name FROM Customer ORDER BY Name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaints - EBMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">EBMS - Complaints</a>
            <div class="navbar-nav ms-auto">
                <a href="index.php" class="btn btn-logout me-2">Back to Dashboard</a>
                <a href="logout.php" class="btn btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Add New Complaint</h2>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="customerID" class="form-label">Customer</label>
                <select class="form-select" id="customerID" name="customerID" required>
                    <option value="">Select Customer</option>
                    <?php while ($customer = sqlsrv_fetch_array($customers, SQLSRV_FETCH_ASSOC)): ?>
                        <option value="<?php echo $customer['CustomerID']; ?>"><?php echo $customer['Name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" class="form-control" id="subject" name="subject" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Complaint</button>
        </form>

        <h2 class="mt-5">Existing Complaints</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Complaint ID</th>
                    <th>Customer ID</th>
                    <th>Subject</th>
                    <th>Description</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                 <?php while ($complaint = sqlsrv_fetch_array($complaints, SQLSRV_FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo $complaint['ComplaintID']; ?></td>
                        <td><?php echo $complaint['CustomerID']; ?></td>
                        <td><?php echo $complaint['Subject']; ?></td>
                        <td><?php echo $complaint['Description']; ?></td>
                        <td><?php echo $complaint['Status']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
