<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>EBMS Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet"
  />
  <style>
    body {
      background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
      background-image: url('https://images.unsplash.com/photo-1473341304170-971dccb5ac1e');
      background-size: cover;
      background-position: center;
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .overlay {
      background-color: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(10px);
      min-height: 100vh;
      padding: 2rem;
    }

    .top-navbar {
      background: rgba(255, 255, 255, 0.95);
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .stats-card {
      border-radius: 20px;
      background: rgba(255, 255, 255, 0.9);
      padding: 1.5rem;
      box-shadow: 0 10px 20px rgba(0,0,0,0.05);
      backdrop-filter: blur(10px);
      height: 100%;
    }

    .stats-title {
      font-size: 1rem;
      color: #6b7280;
    }

    .stats-number {
      font-size: 2rem;
      font-weight: bold;
      color: #1e3a8a;
    }

    .main-card {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 16px;
      padding: 2rem;
      margin-top: 2rem;
      backdrop-filter: blur(10px);
    }

    .badge-paid {
      background: #10b981;
      color: white;
      border-radius: 20px;
      padding: 5px 15px;
    }

    .badge-unpaid {
      background: #ef4444;
      color: white;
      border-radius: 20px;
      padding: 5px 15px;
    }

    .btn-action {
      font-size: 0.85rem;
      border-radius: 20px;
      margin-right: 5px;
      padding: 6px 12px;
    }

    .btn-edit {
      background: #f59e0b;
      color: white;
    }

    .btn-delete {
      background: #dc2626;
      color: white;
    }

    .datetime-info {
      font-size: 0.9rem;
      text-align: right;
    }
  </style>
</head>
<body>
  <div class="overlay">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg top-navbar mb-4">
      <div class="container-fluid">
        <a class="navbar-brand fw-bold text-primary" href="#"
          ><i class="fas fa-bolt me-2 text-warning"></i>EBMS Dashboard</a
        >
        <a href="logout.php" class="btn btn-danger">Logout</a>
      </div>
    </nav>

    <!-- Welcome -->
    <div class="row mb-4">
      <div class="col-md-8">
        <h2 class="fw-bold text-primary">Welcome back, Admin!</h2>
        <p class="text-muted">Monitor and manage electricity bills efficiently</p>
      </div>
      <div class="col-md-4 text-end datetime-info">
        <div><i class="fas fa-calendar-alt me-1"></i> <?= date('F j, Y'); ?></div>
        <div><i class="fas fa-clock me-1"></i> <span id="timeDisplay"></span></div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="stats-card">
          <div class="stats-title">Total Bills</div>
          <div class="stats-number">
            <?php
              $count = sqlsrv_query($conn, "SELECT COUNT(*) as total FROM Bill");
              $total = sqlsrv_fetch_array($count, SQLSRV_FETCH_ASSOC);
              echo $total['total'];
            ?>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stats-card">
          <div class="stats-title">Paid Bills</div>
          <div class="stats-number">
            <?php
              $count = sqlsrv_query($conn, "SELECT COUNT(*) as paid FROM Bill WHERE PaymentDate IS NOT NULL");
              $paid = sqlsrv_fetch_array($count, SQLSRV_FETCH_ASSOC);
              echo $paid['paid'];
            ?>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stats-card">
          <div class="stats-title">Unpaid Bills</div>
          <div class="stats-number">
            <?php
              $count = sqlsrv_query($conn, "SELECT COUNT(*) as unpaid FROM Bill WHERE PaymentDate IS NULL");
              $unpaid = sqlsrv_fetch_array($count, SQLSRV_FETCH_ASSOC);
              echo $unpaid['unpaid'];
            ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Bills Table -->
    <div class="main-card">
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4><i class="fas fa-list me-2 text-primary"></i>All Electricity Bills</h4>
        <div>
          <a href="complaints.php" class="btn btn-primary">View Complaints</a>
          <a href="tariff_plans.php" class="btn btn-primary">View Tariff Plans</a>
          <a href="add_bill.php" class="btn btn-primary"
            ><i class="fas fa-plus me-1"></i>Add Bill</a
          >
        </div>
      </div>
      <div class="table-responsive">
        <table class="table align-middle table-hover">
          <thead class="table-light">
            <tr>
              <th>Bill ID</th>
              <th>Customer</th>
              <th>Date</th>
              <th>Units</th>
              <th>Rate (₨/kWh)</th>
              <th>Amount (₨)</th>
              <th>Payment</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $sql = "SELECT b.BillID, c.Name, b.BillDate, b.UnitsConsumed, b.RatePerUnit, b.BillAmount, b.PaymentDate
                      FROM Bill b
                      JOIN Customer c ON b.CustomerID = c.CustomerID";
              $stmt = sqlsrv_query($conn, $sql);
              while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                  $paymentStatus = $row['PaymentDate'] 
                      ? "<span class='badge badge-paid'>" . $row['PaymentDate']->format('Y-m-d') . "</span>"
                      : "<span class='badge badge-unpaid'>Unpaid</span>";
                  echo "<tr>
                          <td>{$row['BillID']}</td>
                          <td>{$row['Name']}</td>
                          <td>{$row['BillDate']->format('M j, Y')}</td>
                          <td>{$row['UnitsConsumed']} kWh</td>
                          <td>₨" . number_format($row['RatePerUnit'], 2) . "</td>
                          <td><strong>₨" . number_format($row['BillAmount'], 2) . "</strong></td>
                          <td>$paymentStatus</td>
                          <td>
                            <a href='edit_bill.php?id={$row['BillID']}' class='btn btn-edit btn-action'>Edit</a>
                            <a href='delete_bill.php?id={$row['BillID']}' class='btn btn-delete btn-action' onclick=\"return confirm('Are you sure?')\">Delete</a>
                          </td>
                        </tr>";
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
<!-- Customers with High Bills -->
<div class="main-card mt-4">
  <h4><i class="fas fa-money-bill-wave me-2 text-primary"></i>Customers with High Bills (&gt; ₨2000)</h4>
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Customer Name</th>
          <th>Bill Amount (₨)</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $highBillQuery = "
            SELECT c.Name, b.BillAmount
            FROM Bill b
            JOIN Customer c ON b.CustomerID = c.CustomerID
            WHERE b.BillAmount > 2000";
          $highResult = sqlsrv_query($conn, $highBillQuery);

          if ($highResult === false) {
              echo "<tr><td colspan='2'>Error fetching data.</td></tr>";
          } else {
              while ($row = sqlsrv_fetch_array($highResult, SQLSRV_FETCH_ASSOC)) {
                  echo "<tr>";
                  echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
                  echo "<td>₨" . number_format($row['BillAmount'], 2) . "</td>";
                  echo "</tr>";
              }
          }
        ?>
      </tbody>
    </table>
  </div>
</div>

    <!-- Additional Queries Section -->
    <div class="main-card mt-4">
      <h4><i class="fas fa-chart-bar me-2 text-primary"></i>Additional Statistics</h4>
      <div class="row">
        <div class="col-md-6">
          <h5>Billed Customers in January 2025</h5>
          <ul>
            <?php
              $query = "SELECT c.Name AS CustomerName, b.BillDate, b.BillAmount
                        FROM Bill b
                        JOIN Customer c ON b.CustomerID = c.CustomerID
                        WHERE MONTH(b.BillDate) = 1 AND YEAR(b.BillDate) = 2025";
              $result = sqlsrv_query($conn, $query);
              while ($customer = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                  echo "<li>{$customer['CustomerName']} - ₨" . number_format($customer['BillAmount'], 2) . " on " . $customer['BillDate']->format('M j, Y') . "</li>";
              }
            ?>
          </ul>
        </div>
        <div class="col-md-6">
          <h5>Total Revenue Collected</h5>
          <p>
            <?php
              $revenueQuery = "SELECT SUM(BillAmount) AS TotalCollectedRevenue FROM Bill WHERE PaymentDate IS NOT NULL";
              $revenueResult = sqlsrv_query($conn, $revenueQuery);
              $revenue = sqlsrv_fetch_array($revenueResult, SQLSRV_FETCH_ASSOC);
              echo "₨" . number_format($revenue['TotalCollectedRevenue'], 2);
            ?>
          </p>
        </div>
      </div>
    </div>

    <!-- Average Units Consumed by Region -->
    <div class="main-card mt-4">
      <h4><i class="fas fa-chart-bar me-2 text-primary"></i>Average Units Consumed by Region</h4>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Region</th>
              <th>Average Units Consumed</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $sqlAvg = "
                SELECT 
                    eb.Region,
                    AVG(b.UnitsConsumed) AS AvgUnits
                FROM Bill b
                JOIN Customer c ON b.CustomerID = c.CustomerID
                JOIN Customer_ElecBoard ceb ON c.CustomerID = ceb.CustomerID
                JOIN Elec_Board eb ON ceb.BoardID = eb.BoardID
                GROUP BY eb.Region
                ORDER BY eb.Region;
              ";
              $stmtAvg = sqlsrv_query($conn, $sqlAvg);
              if ($stmtAvg === false) {
                  echo "<tr><td colspan='2'>Error fetching data.</td></tr>";
              } else {
                  while ($rowAvg = sqlsrv_fetch_array($stmtAvg, SQLSRV_FETCH_ASSOC)) {
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($rowAvg['Region']) . "</td>";
                      echo "<td>" . number_format($rowAvg['AvgUnits'], 2) . "</td>";
                      echo "</tr>";
                  }
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    function updateTime() {
      const now = new Date();
      const timeString = now.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        second: '2-digit',
        hour12: true,
      });
      document.getElementById('timeDisplay').textContent = timeString;
    }
    updateTime();
    setInterval(updateTime, 1000);
  </script>
</body>
</html>
