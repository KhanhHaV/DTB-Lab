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
        $fname = htmlspecialchars(trim($_POST["1stname"]));
        $lname = htmlspecialchars(trim($_POST["lastname"]));
        $phone = htmlspecialchars(trim($_POST["phone"]));
        $email = htmlspecialchars(trim($_POST["email"]));
        $password = htmlspecialchars(trim($_POST["password"]));
        $repassword = htmlspecialchars(trim($_POST["repassword"]));

        $errors = [];
        $errMsg = "";

        // validate the user inputs
        // ==========================================================================
        
        if(strlen($fname) > 25  || strlen($fname) < 2|| !ctype_alpha($fname)) {
            array_push($errors,'err_fname');
        }
        if(strlen($lname) > 25 || strlen($lname) < 2 || !ctype_alpha($lname)) {
            array_push($errors,'err_lname');
        }
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
        $query = "SELECT email FROM users WHERE email = '$email' AND type = 0;";

        $stmt = sqlsrv_query($conn, $query);

        if ($stmt === false) {
            // Handle query execution errors
            die(print_r(sqlsrv_errors(), true));
        } else {
            // Query executed successfully, proceed with result handling (if applicable)
            // ...
        }



        if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            if ($row["email"] == $email) {
                array_push($errors, 'err_email_exists');
            }
        }

        if(!is_numeric($phone) || strlen($phone)>10  || strlen($phone) < 2) {
            array_push($errors,'err_phone');
        }

        if(!isset($_POST["password"]) || strlen($password) < 3) {
            array_push($errors,'err_pwd');
        }

        if($password != $repassword) {
            array_push($errors,'err_no_match_pwd');
        }

        // ==========================================================================

        // if there's any error, redirect user to register.php
        if(count($errors) != 0) {
            $errString = "";
            for($i = 0; $i < count($errors); $i ++) {
                $errCode = $errors[$i];
                $errString .= "$errCode=1";
                if($i < count($errors) - 1) {
                    $errString .= "&";
                }
            }
            header("Location: register.php?$errString");
        } 

        // if there's no error, insert the data to the user table in the database 
        else {
            
            $query = "INSERT INTO dbo.users (fname,lname,phone,email,type,password) 
                        VALUES ('$fname','$lname','$phone','$email',0,'$password');";

            $stmt1 = sqlsrv_query($conn, $query);            
            

           if ($stmt === false) {
                // Handle query execution errors
                die(print_r(sqlsrv_errors(), true));
            } else {
                // Query executed successfully, proceed with result handling (if applicable)
                // ...
            }
            
            // Free the statement after use
            //sqlsrv_free_stmt($stmt);
            header("Location: index.php");
        }
    }
?>