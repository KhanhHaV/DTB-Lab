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
        $email = htmlspecialchars(trim($_POST["email"]));
        $password = htmlspecialchars(trim($_POST["password"]));

        
        $errors = [];
        $errMsg = "";
        if($email != "admin") {
            if(strlen($email) < 3) {
                array_push($errors,'err_email');
            } else {
                $check = false;
                for($i = 1; $i < strlen($email) - 1; $i++) {
                    if($email[$i] == '@') {
                        $check = true;
                        break;
                    }
                }
                if(!$check) {
                    array_push($errors,'err_email');
                }
            }
        }

        if(!isset($_POST["password"]) || strlen($password) < 3) {
            array_push($errors,'err_pwd');
        }

        
        if(count($errors) != 0) {
            $errString = "";
            for($i = 0; $i < count($errors); $i ++) {
                $errCode = $errors[$i];
                $errString .= "$errCode=1";
                if($i < count($errors) - 1) {
                    $errString .= "&";
                }
            }
            header("Location: index.php?$errString");
        } else 
        {
           
            $query = "SELECT user_id, email, type, password FROM users WHERE email = '$email' AND password = '$password' ";
            //$params = array($email, $password, ($email == "admin") ? 1 : 0);


            $stmt = sqlsrv_query($conn, $query);
            if ($stmt === false) {
             die(print_r(sqlsrv_errors(), true));
            }

            $userCre = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

            
            // check if the result return a valid user
            if($userCre && $userCre["email"] == $email && ($userCre["type"] == 0 || $userCre["type"] == 1) && $userCre["password"] == $password) {
                $query = "SELECT user_id, fname, lname, phone, email, address,
                            type FROM users WHERE user_id = ".$userCre["user_id"].";";
                
                
                $stmt = sqlsrv_query($conn, $query);
                if ($stmt === false) {
                 die(print_r(sqlsrv_errors(), true));
                }
    
                $userCre = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    
                //  save user information to session so the page can "remember" that user was logged in
                session_start();

                $_SESSION["user"] = $userCre;
                if(array_key_exists("user",$_SESSION) && $_SESSION["user"] != null) {
                    header("Location: home.php");
                }
            } else {
                header("Location: index.php?err_wrong=1");
            }
            sqlsrv_free_stmt($stmt);
        }
    }
?>