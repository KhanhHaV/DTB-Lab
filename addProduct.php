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
        $pname = htmlspecialchars(trim($_POST["pname"]));
        $pdesc = htmlspecialchars(trim($_POST["pdesc"]));
        $pprice = htmlspecialchars(trim($_POST["pprice"]));
        $discount = htmlspecialchars(trim($_POST["discount"]));
        $pstock = htmlspecialchars(trim($_POST["pstock"]));
        
        // process image file
        $pimageName = basename($_FILES["pimage"]["name"]);
        $pimageType = pathinfo($pimageName, PATHINFO_EXTENSION);

        $allowTypes = ['jpg','png','jpeg','gif'];

        if(in_array($pimageType, $allowTypes)) {
            $pimage = $_FILES["pimage"]["name"];
            $pimage_tmp = $_FILES["pimage"]["tmp_name"];
            $folder = 'imageproduct/' . $pimage;
        }

        $errMsg = 0;

        if($pname == "" || $pdesc == "" || $pprice == "" || $pprice <= 0 || $discount<0 ) {
            $errMsg += 1;
        }

        if($errMsg != 0) {
            echo "<p>Oops! Something went wrong! :(</p>";
        } else {
            // query to insert products information into products table
           
            if (move_uploaded_file($pimage_tmp, $folder)) {
                echo "<h3>  Image uploaded successfully!</h3>";

                $query .= "INSERT INTO products (pname, pdesc, pprice,pimage,pimagetype, pdate,discount,pstock) VALUES 
                ('$pname','$pdesc',$pprice,'$folder','$pimageType', GETDATE(),$discount, $pstock);";
                $result = sqlsrv_query($conn,$query);
            
                
            } else {
                echo "<h3>  Failed to upload image!</h3>";
            }
            header("Location: manager.php");
        }
    }
?>