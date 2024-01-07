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
      
        if($userId == "" || $productId == "" || $color == "" || ($fea1 == "" && $fea2 == "" && $fea3 == "")) {
            $errMsg += 1;
        }
        

        if($errMsg != 0) {
            echo "<p>Oops! Something went wrong! :(</p>";
        } else {
            
            $query = "SELECT * FROM cart WHERE user_id = $userId AND product_id = $productId AND version = $;";
            $stmt = sqlsrv_query($conn,$query);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true)); // This will output detailed error information
            }

            $rowCount = 0;

            // Fetch each row and increment the counter
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $rowCount++;
            }

            if ($rowCount == 0) {
                $query = "INSERT INTO cart (user_id, product_id, color, version, quantity) VALUES ($userId, $productId, '$color', '";
                if ($fea1 != "") $query .= "$fea1 ";
                if ($fea2 != "") $query .= "$fea2 ";
                if ($fea3 != "") $query .= "$fea3 ";
                $query .= "', ";
                if (isset($_POST["quantity"]) && $_POST["quantity"] != "") {
                    $query .= $_POST["quantity"];
                } else {
                    $query .= '1';
                }
                $query .= ");";
                $result = sqlsrv_query($conn, $query);
            } else {
                if (isset($_POST["quantity"]) && $_POST["quantity"] != "") {
                    $quantity = $_POST["quantity"];
                    $query = "UPDATE cart SET quantity = quantity + $quantity WHERE user_id = $userId AND product_id = $productId;";
                } else {
                    $query = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = $userId AND product_id = $productId;";
                }
            }
            
               
                $result = sqlsrv_query($conn,$query);
                if ($result === false) {
                    die(print_r(sqlsrv_errors(), true)); // This will output detailed error information
                }

            }
            header("Location: productDesc.php?productId=$productId&status='success'");
        }
?>