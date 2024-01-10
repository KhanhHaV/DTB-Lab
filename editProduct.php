<?php
    include("connect.inc");

    if(!$conn) {
        echo "<p>Oops! Something went wrong! :(</p>";
    } else {
        $editingProduct = $_POST["editting-product"];
        if(isset($_POST["delete"])) {
            $query = "DELETE FROM products WHERE product_id = $editingProduct;";
            $result = sqlsrv_query($conn,$query);
            header("Location: manager.php?page=1");
        } else {
            $pname = "";
            $pdesc = "";
            $pprice = "";
            $pimage = "";
            $pstock = "";
            $discount = "";
            $addPcat =""; 

            if(isset($_POST["pname"])) $pname = htmlspecialchars(trim($_POST["pname"]));
            if(isset($_POST["pdesc"])) $pdesc = htmlspecialchars(trim($_POST["pdesc"]));
            if(isset($_POST["pprice"])) $pprice = htmlspecialchars(trim($_POST["pprice"]));
            if(isset($_POST["discount"])) $discount = htmlspecialchars(trim($_POST["discount"]));
            if(isset($_POST["pstock"])) $pstock = htmlspecialchars(trim($_POST["pstock"]));
            $addPcat = $_POST["add-pcat"];
           
            
            // process image file
            if(isset($_POST["pimage"])) {
                $pimageName = basename($_FILES["pimage"]["name"]);
                $pimageType = pathinfo($pimageName, PATHINFO_EXTENSION);
                $allowTypes = ['jpg','png','jpeg','gif'];
                if( in_array($pimageType, $allowTypes)) {
                    $pimage = $_FILES["pimage"]["name"];
                    $pimage_tmp = $_FILES["pimage"]["tmp_name"];
                    $folder = 'imageproduct/' . $pimage;
                    
                if (move_uploaded_file($pimage_tmp, $folder)) {
                    echo "<h3>  Image uploaded successfully!</h3>";}
                }
            }
            
           
    
            $errMsg = 0;
          
            if($pname == "" && $pdesc == "" && $pprice == "" && $folder == "" && $pstock == ""&& $discount == "" && $addPcat =="") {
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
                if($pstock != "") {
                    $query .= "pstock = '$pstock', ";
                }
                if($discount != "") {
                    $query .= "discount = '$discount', ";
                }
                if($folder != "") {
                    $query .= "pimage = '$folder', ";
                }
                if($addPcat != "") {
                    $query .= "cat_id= '$addPcat', ";
                }
                $query = trim($query, ", ");
                $query .= " WHERE product_id = $editingProduct;";
                $result = sqlsrv_query($conn,$query);
                header("Location: manager.php?page=1");
            }
        }
    }
?>