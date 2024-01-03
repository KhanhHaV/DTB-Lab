<?php
    $serverName = "AFKAJHFJKAHDFJA\SQLEXPRESS01"; 
    $connectionOptions = array(
        "Database" => "master", 
    );

    $conn = sqlsrv_connect($serverName, $connectionOptions);

    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true)); // Handle connection errors
    } else {
        echo "Connected to SQL Server successfully";
    }
?>

<?php
    if(!$conn) {
        echo "<p>sth went worng!:(</p>";
    } else {
        if(!isset($_GET["orderId"])) {
            header("Location: manager.php");
        } else {
            $orderId = $_GET["orderId"];
            $query = "DELETE FROM orders WHERE order_id = $orderId;";
            $query .= "DELETE FROM order_products WHERE order_id = $orderId";
            mysqli_multi_query($conn, $query);
            header("Location: manager.php?page=2");
        }
    }
?>