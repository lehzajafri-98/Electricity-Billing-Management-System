<?php
$serverName = "AELIYAJAFFRI\\SQLEXPRESS"; // Replace with your server
$connectionInfo = array("Database" => "EBMS", "TrustServerCertificate" => true);
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}
?>