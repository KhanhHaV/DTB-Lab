<?php
    include("connect.inc");

    if(!$conn) {
        echo "<p>Oops! Something went wrong! :(</p>";
    } else {
        $fname = htmlspecialchars(trim($_POST["fname"]));
        $lname = htmlspecialchars(trim($_POST["lname"]));
        $phone = htmlspecialchars(trim($_POST["phone"]));
        $email = htmlspecialchars(trim($_POST["email"]));
        $address = htmlspecialchars(trim($_POST["address"]));
        $userImageContent = null;
        $userImageType = null;

        if(count($_FILES) > 0 && $_FILES["avt"] != null) {
            $userImageName = basename($_FILES["avt"]["name"]);
            $userImageType = pathinfo($userImageName, PATHINFO_EXTENSION);
    
            $allowTypes = ['jpg','png','jpeg','gif'];
    
            if(in_array($userImageType, $allowTypes)) {
                $userImage = $_FILES["avt"]["name"];
                $userImage_tmp = $_FILES["avt"]["tmp_name"];
                $folder = 'useravt/'. $userImage;
            }
        }
        $userId = $_GET["userId"];

        $errMsg = 0;

        if($fname == "" || $lname == "" || $email == "") {
            $errMsg += 1;
        }

        if($errMsg != 0) {
            echo "<p>Oops! Something went wrong! :(</p>";
        } else {
            if(isset($userImage_tmp)) {
                if (move_uploaded_file($userImage_tmp, $folder)) {
                    echo "<h3>  Image uploaded successfully!</h3>";
    
                    $query = "UPDATE users 
                              SET fname = '$fname', lname = '$lname', email = '$email', address = '$address', phone = '$phone', avatar = '$folder', avatar_type = '$userImageType'
                              WHERE user_id = $userId;";
                    
                    $result = sqlsrv_query($conn,$query);
                   
                    if ($result === false) {
                        die(print_r(sqlsrv_errors(), true)); // This will output detailed error information
                    }
                    session_start();
                    $query = "SELECT user_id, fname, lname, phone, email, address, type FROM users WHERE user_id = ".$_SESSION["user"]["user_id"]." ;";    
                    $result = sqlsrv_query($conn, $query);
                    if ($result === false) {
                        die(print_r(sqlsrv_errors(), true)); // This will output detailed error information
                    }
    
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        // Access column values by their names
                        echo $row['column_name'] . "<br>";
                    }
                   
                    header("Location: manager.php");
            } else {
                echo "<h3>  Failed to upload image!</h3>";}
    
            }
            else {
                
                $query = "UPDATE users 
                SET fname = '$fname', lname = '$lname', email = '$email', address = '$address', phone = '$phone'
                WHERE user_id = $userId;";
      
                $result = sqlsrv_query($conn,$query);
                
                if ($result === false) {
                    die(print_r(sqlsrv_errors(), true)); // This will output detailed error information
                }
              
                session_start();
                $query = "SELECT user_id, fname, lname, phone, email, address, type FROM users WHERE user_id = ".$_SESSION["user"]["user_id"]." ;";    
                $result = sqlsrv_query($conn, $query);
                if ($result === false) {
                    die(print_r(sqlsrv_errors(), true)); // This will output detailed error information
                }

                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    // Access column values by their names
                    echo $row['column_name'] . "<br>";
                }
                
                header("Location: manager.php");
            }
        }
           
    }
?>