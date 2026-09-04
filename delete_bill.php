<?php
include 'auth.php';
include 'db.php';

$billID = $_GET['id'] ?? 0;

if ($billID) {
    // Get bill details for confirmation
    $sql = "SELECT b.BillID, c.Name as CustomerName, b.BillAmount, b.BillDate 
            FROM Bill b 
            JOIN Customer c ON b.CustomerID = c.CustomerID 
            WHERE b.BillID = ?";
    $stmt = sqlsrv_query($conn, $sql, array($billID));
    $bill = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    
    if (!$bill) {
        header("Location: dashboard.php?error=Bill not found");
        exit;
    }
    
    // Delete the bill
    $deleteSql = "DELETE FROM Bill WHERE BillID = ?";
    $deleteStmt = sqlsrv_query($conn, $deleteSql, array($billID));
    
    if ($deleteStmt) {
        header("Location: dashboard.php?success=Bill deleted successfully");
    } else {
        header("Location: dashboard.php?error=Error deleting bill");
    }
} else {
    header("Location: dashboard.php?error=Invalid bill ID");
}

exit;
?>