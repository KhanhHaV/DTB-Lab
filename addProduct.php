<?php
    include("connect.inc");
    if(!$conn) {
        echo "<p>Oops! Something went wrong! :(</p>";
    } else {
        
        $pname = htmlspecialchars(trim($_POST["pname"]));
        $pdesc = htmlspecialchars(trim($_POST["pdesc"]));
        $pprice = htmlspecialchars(trim($_POST["pprice"]));
        
        if (isset($_POST["discount"]) and $_POST["discount"] !="" ){
            $discount = htmlspecialchars(trim($_POST["discount"]));
        } else {
            $discount = 0;
        }
       

        $pstock = htmlspecialchars(trim($_POST["pstock"]));
        
        if (isset($_POST["add-pcat"]) and $_POST["add-pcat"] !="" ){
            $addPcat = $_POST["add-pcat"];
        } else {
            $addPcat = 0;
        }
        // process image file
        $pimageName = basename($_FILES["pimage"]["name"]);
        $pimageType = pathinfo($pimageName, PATHINFO_EXTENSION);

        $allowTypes = ['jpg','png','jpeg','gif'];

        if(in_array($pimageType, $allowTypes)) {
            $pimage = $_FILES["pimage"]["name"];
            $pimage_tmp = $_FILES["pimage"]["tmp_name"];
            $folder = 'imageproduct/' . $pimage;
        }
       


        if($pname == "" || $pdesc == "" || $pprice == "" || $pprice <= 0  ) {
            echo "<p>Oops! Something went wrong! :(</p>";
        
        } else {
           
            // query to insert products information into products table
           
            if (move_uploaded_file($pimage_tmp, $folder)) {
                echo "<h3>  Image uploaded successfully!</h3>";
              
                $query = "INSERT INTO products (pname, pdesc, pprice,pimage,pimagetype, pdate,discount,pstock,cat_id) VALUES 
                ('$pname','$pdesc',$pprice,'$folder','$pimageType', GETDATE(),$discount, $pstock,$addPcat);";
                $result = sqlsrv_query($conn,$query);
            
        
            } else {
                echo "<h3>  Failed to upload image!</h3>";
                
            }
            header("Location: manager.php");
        }
    }
?>