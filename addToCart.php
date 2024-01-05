<?php
    include("connect.inc");
    if(!$conn) {
        echo "<p>Oops! Something went wrong! :(</p>";
    } else {
        if(!$_SESSION["user"] || $_SESSION["user"] == null) {
            header("Location: index.php");
        }
        $userId = htmlspecialchars(trim($_GET["userId"]));
        $productId = htmlspecialchars(trim($_GET["productId"]));
        $color = htmlspecialchars(trim($_POST["color"]));
        $fea1 = "";
        $fea2 = "";
        $fea3 = "";
        if(isset($_POST["fea1"])) {
            $fea1 = $_POST["fea1"];
        }
        if(isset($_POST["fea2"])) {
            $fea2 = $_POST["fea2"];
        }
        if(isset($_POST["fea3"])) {
            $fea3 = $_POST["fea3"];
        }
        
        $errMsg = 0;

        if($userId == "" || $productId == "" || $color == "" || ($fea1 == "" && $fea2 == ""  && $fea3 == "")) {
            $errMsg += 1;
        }

        if($errMsg != 0) {
            echo "<p>Oops! Something went wrong! :(</p>";
        } else {
            sqlsrv_query($conn, $query);
            $query = "SELECT * FROM cart WHERE user_id = $userId AND product_id = $productId;";
            $result = sqlsrv_query($conn, $query);
            if ($result === false) {
                die(print_r(sqlsrv_errors(), true)); // This will output detailed error information
            }
            $row = sqlsrv_fetch_array($result);
            if( !$row || count($row) == 0) {
                $query = "INSERT INTO cart (user_id, product_id, color, version, quantity) VALUES ($userId, $productId, '$color', '";
                if($fea1 != "") $query .= "$fea1 ";
                if($fea2 != "") $query .= "$fea2 ";
                if($fea3 != "") $query .= "$fea3 ";
                $query .= "', ";
                if(isset($_POST["quantity"]) && $_POST["quantity"] != "") {
                    $query .= $_POST["quantity"];
                } else {
                    $query .= '1';
                }
                $query .= ");";
                $result = sqlsrv_query($conn,$query);
            } else {
                $query = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = $userId AND product_id = $productId;";
                $result = sqlsrv_query($conn,$query);
            }
            header("Location: productDesc.php?productId=$productId&status='success'");
        }
    }
?>