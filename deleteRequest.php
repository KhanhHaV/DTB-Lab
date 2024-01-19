<?php
    include("connect.inc");

    if(!$conn) {
        echo "<p>sth went worng!:(</p>";
    } else {
        if(!isset($_GET["orderId"])) {
            header("Location: manager.php");
        } else {
            $orderId = $_GET["orderId"];
            $query = "DELETE FROM orders WHERE order_id = $orderId;";
            sqlsrv_query($conn, $query);
            $query = "DELETE FROM order_products WHERE order_id = $orderId";
            sqlsrv_query($conn, $query);
            header("Location: manager.php?page=2");
        }
    }
?>