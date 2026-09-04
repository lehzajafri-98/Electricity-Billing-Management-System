<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'auth.php';
include 'db.php';

// Handle update POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_plan'])) {
    $planId = intval($_POST['plan_id']);
    $ratePerUnit = floatval($_POST['rate_per_unit']);

    $updateSql = "UPDATE TariffPlan SET RatePerUnit = ? WHERE PlanID = ?";
    $params = [$ratePerUnit, $planId];

    $stmt = sqlsrv_query($conn, $updateSql, $params);

    if ($stmt === false) {
        die("Update error: " . print_r(sqlsrv_errors(), true));
    } else {
        header("Location: tariff_plans.php"); // Redirect to avoid form resubmission
        exit;
    }
}

// Fetch tariff plans
$sql = "
    SELECT tp.PlanID, eb.BoardName, tp.RatePerUnit
    FROM TariffPlan tp
    LEFT JOIN Elec_Board eb ON tp.BoardID = eb.BoardID
";

$tariffPlans = sqlsrv_query($conn, $sql);
if ($tariffPlans === false) {
    die("SQL error: " . print_r(sqlsrv_errors(), true));
}

// Determine if we are editing a particular plan (via GET param)
$editPlanId = isset($_GET['edit']) ? intval($_GET['edit']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Tariff Plans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">
<div class="container">
    <h2>Tariff Plans</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>Plan ID</th>
                <th>Board</th>
                <th>Rate Per Unit (₨)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (sqlsrv_has_rows($tariffPlans)): ?>
            <?php while ($row = sqlsrv_fetch_array($tariffPlans, SQLSRV_FETCH_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['PlanID']); ?></td>
                    <td><?= htmlspecialchars($row['BoardName'] ?? 'No Board'); ?></td>
                    <td>
                        <?php if ($editPlanId === $row['PlanID']): ?>
                            <form method="POST" action="tariff_plans.php" class="d-flex align-items-center" style="gap: 0.5rem;">
                                <input type="hidden" name="plan_id" value="<?= htmlspecialchars($row['PlanID']); ?>" />
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0" 
                                    name="rate_per_unit" 
                                    value="<?= htmlspecialchars($row['RatePerUnit']); ?>" 
                                    class="form-control form-control-sm" 
                                    required 
                                    style="max-width: 120px;"
                                />
                        <?php else: ?>
                            ₨<?= number_format($row['RatePerUnit'], 2); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($editPlanId === $row['PlanID']): ?>
                                <button type="submit" name="update_plan" class="btn btn-success btn-sm me-2">Save</button>
                                <a href="tariff_plans.php" class="btn btn-secondary btn-sm">Cancel</a>
                            </form>
                        <?php else: ?>
                            <a href="tariff_plans.php?edit=<?= urlencode($row['PlanID']); ?>" class="btn btn-warning btn-sm">Edit</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No tariff plans found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <a href="index.php" class="btn btn-secondary">Back</a>
</div>
</body>
</html>
