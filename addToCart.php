<?php
     include("connect.inc");

    if(!$conn) {
        echo "<p>Oops! Something went wrong! :(</p>";
    } else {
        session_start();
        if(!isset($_SESSION["user"]) || $_SESSION["user"] == null) {
            header("Location: index.php");
        }
        $userId = htmlspecialchars(trim($_SESSION["user"]["user_id"]));
        $productId = htmlspecialchars(trim($_GET["productId"]));
        $color = htmlspecialchars(trim($_POST["color"]));
        $fea = "";
        
        if(isset($_POST["fea1"])) {
            $fea = $_POST["fea1"];
        }
        else if(isset($_POST["fea2"])) {
            $fea = $_POST["fea2"];
        }
        else if(isset($_POST["fea3"])) {
            $fea = $_POST["fea3"];
        }
        

        $errMsg = 0;

        if($userId == "" || $productId == "" || $color == "" || $fea == "") {
            $errMsg += 1;
        }

        if($errMsg != 0) {
            echo "<p>Oops! Something went wrong! :(</p>";
        } else {
            sqlsrv_query($conn, $query);
            $query = "SELECT * FROM cart WHERE user_id = $userId AND product_id = $productId AND version ='$fea' AND color ='$color' ;";
            $result = sqlsrv_query($conn, $query);
            $row = sqlsrv_fetch_array($result);

            if( !$row || count($row) == 0) {
                $query = "INSERT INTO cart (user_id, product_id, color, version, quantity) VALUES ($userId, $productId, '$color','$fea',";
                
                if(isset($_POST["quantity"]) && $_POST["quantity"] != "") {
                    $query .= $_POST["quantity"];
                } else {
                    $query .= '1';
                }
                $query .= ");";
                $result = sqlsrv_query($conn,$query);
            } else {
                 if (isset($_POST["quantity"]) && $_POST["quantity"] != "") {
                     $quantity = $_POST["quantity"];
                     $query = "UPDATE cart SET quantity = quantity + $quantity WHERE user_id = $userId AND product_id = $productId AND version ='$fea' AND color ='$color';";
                     $result = sqlsrv_query($conn,$query);
                 } else {
                     $query = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = $userId AND product_id = $productId AND version ='$fea' AND color ='$color';";
                     $result = sqlsrv_query($conn,$query);
                 }
             }
            header("Location: productDesc.php?productId=$productId&status='success'");
        }
    }
?>