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
        echo "<p>Oops! Something went wrong! :(</p>";
    } else {
        $editingProduct = $_POST["editting-product"];
        if(isset($_POST["delete"])) {
            $query = "DELETE FROM products WHERE product_id = $editingProduct;";
            $result = mysqli_query($conn,$query);
            header("Location: manager.php?page=1");
        } else {
            $pname = "";
            $pdesc = "";
            $pprice = "";
            $pimage = "";
            if(isset($_POST["pname"])) $pname = htmlspecialchars(trim($_POST["pname"]));
            if(isset($_POST["pdesc"])) $pdesc = htmlspecialchars(trim($_POST["pdesc"]));
            if(isset($_POST["pprice"])) $pprice = htmlspecialchars(trim($_POST["pprice"]));

            // process image file
            if(isset($_FILES["pimage"])) 
            $pimageName = basename($_FILES["pimage"]["name"]);
            if(isset($_FILES["pimage"])) 
            $pimageType = pathinfo($pimageName, PATHINFO_EXTENSION);
    
            $allowTypes = ['jpg','png','jpeg','gif'];
    
            if(
                isset($_FILES["pimage"]) && 
                in_array($pimageType, $allowTypes)) {
                $pimage = $_FILES["pimage"]["tmp_name"];
                $pimageContent = addslashes(file_get_contents($pimage));
            }
    
            $errMsg = 0;
    
            if($pname == "" && $pdesc == "" && $pprice == "" && $pimage == "") {
                $errMsg += 1;
            }
    
            if($errMsg != 0) {
                echo "<p>Oops! Something went wrong! :(</p>";
            } else {

                // query to update product information
                $query = "UPDATE products SET ";
                if($pname != "") {
                    $query .= "pname = '$pname', ";
                }
                if($pdesc != "") {
                    $query .= "pdesc = '$pdesc', ";
                }
                if($pprice != "") {
                    $query .= "pprice = '$pprice', ";
                }
                if($pimage != "") {
                    $query .= "pimage = '$pimageContent',pimagetype = '$pimageType', ";
                }
                $query = trim($query, ", ");
                $query .= " WHERE product_id = $editingProduct;";
                $result = mysqli_multi_query($conn,$query);
                header("Location: manager.php?page=1");
            }
        }
    }
?>