<?php
    include("connect.inc");

    if(!$conn) {
        echo "<p>Oops! Something went wrong! :(</p>";
    } else {

        // prevent accessing directly 
        if(count($_GET) == 0 && count($_POST) == 0) {
            header("Location: home.php");
        } else {
            if(
                isset($_POST["fname"]) && isset($_POST["lname"]) && isset($_POST["phone"]) && isset($_POST["email"]) && isset($_POST["street"])  && isset($_POST["town"]) && isset($_POST["state"])
                && isset($_POST["prefcontact"]) && isset($_POST["postcode"]) && isset($_GET["productId"]) && isset($_GET["quantity"]) && (isset($_GET["fea1"]) || isset($_GET["fea2"]) || isset($_GET["fea3"])) && isset($_GET["color"])
                && isset($_POST["card-type"]) && isset($_POST["nameoncard"]) && isset($_POST["cardnumber"]) && isset($_POST["expiry"]) && isset($_POST["cvv"])
             ) {
                $fname = htmlspecialchars(trim($_POST["fname"]));
                $lname = htmlspecialchars(trim($_POST["lname"]));
                $phone = htmlspecialchars(trim($_POST["phone"]));
                $email = htmlspecialchars(trim($_POST["email"]));
                $street = htmlspecialchars(trim($_POST["street"]));
                $town = htmlspecialchars(trim($_POST["town"]));
                $state = htmlspecialchars(trim($_POST["state"]));
                $prefContact = htmlspecialchars(trim($_POST["prefcontact"]));
                $postCode = htmlspecialchars(trim($_POST["postcode"]));
                $product = htmlspecialchars(trim($_GET["productId"]));
                $quantity = htmlspecialchars(trim($_GET["quantity"]));
                $fea1 = "";
                $fea2 = "";
                $fea3 = "";
                if(isset($_GET["fea1"])) {
                    $fea1 = $_GET["fea1"];
                }
                if(isset($_GET["fea2"])) {
                    $fea2 = $_GET["fea2"];
                }
                if(isset($_GET["fea3"])) {
                    $fea3 = $_GET["fea3"];
                }
                $color = htmlspecialchars(trim($_GET["color"]));
                $cardType = htmlspecialchars(trim($_POST["card-type"]));
                $nameOnCard = htmlspecialchars(trim($_POST["nameoncard"]));
                $cardNumber = htmlspecialchars(trim($_POST["cardnumber"]));
                $expiry = htmlspecialchars(trim($_POST["expiry"]));
                $cvv = htmlspecialchars(trim($_POST["cvv"]));
                if(isset($_POST["addition"])) {
                    $addition = $_POST["addition"];
                }
                $errors = [];
                $errMsg = "";
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
                $result = sqlsrv_query($conn, $query);
                if($row = sqlsrv_fetch_array($result,sqlsrv_fetch_assoc)) {
                    if($row["email"] == $email) {
                        array_push($errors,'err_email_exists');
                    }
                }
    
        
                
                if(strlen($street) > 40 || strlen($street) < 2) {
                    array_push($errors,'err_street');
                }
        
                
                if(strlen($town) > 40 || strlen($town) < 2) {
                    array_push($errors,'err_town');
                }
        
                if(!in_array($state,['vic','nsw','qld','nt','wa','sa','tas','act'])) {
                    echo $state;
                    array_push($errors,'err_state');
                }
        
                if(!is_numeric($postCode) || strlen($postCode) != 4) {
                    array_push($errors,'err_postcode');
                }
        
                if(!is_numeric($phone) || strlen($phone)>10  || strlen($phone) < 2) {
                    array_push($errors,'err_phone');
                }
        
                if(!in_array($prefContact,['email','post','phone'])) {
                    echo $prefContact;
                    array_push($errors, 'err_prefcontact');
                }
        
        
                if(!in_array($cardType,['visa','master','express'])) {
                    $errMsg .= "<p>Card type must be VISA, MasterCard, or American Express</p>";
                    array_push($errors,'card_type');
                }

    
                if(strlen($nameOnCard) < 2) {
                    array_push($errors,'err_nameoncard');
                } else {
                    $checkNameOnCard = true;
                    for($i = 0; $i < strlen($nameOnCard); $i++) {
                        if(!ctype_alpha($nameOnCard[$i]) && $nameOnCard[$i] != " ") {
                            $checkNameOnCard = false;
                            break;
                        }
                    }
                    if(!$checkNameOnCard) array_push($errors,'err_nameoncard');
                }
        
                switch($cardType) {
                    case 'visa':
                        if(!is_numeric($cardNumber) || strlen($cardNumber) != 16 || $cardNumber[0] != '4') {
                            $errMsg .= "<p>Visa card number must has 16 digits and starts with 4</p>";
                            array_push($errors,'visa_number');
                        }
                        break;
                    
                    case 'master':
                        if(!is_numeric($cardNumber) || strlen($cardNumber) != 16 || $cardNumber[0] != '5' || !in_array($cardNumber[1],['1','2','3','4','5'])) {
                            $errMsg .= "<p>Mastercard number must has 16 digits and starts with 51 to 55</p>";
                            array_push($errors,'master_number');
                        }
                        break;
                    case 'express':
                        if(!is_numeric($cardNumber) || strlen($cardNumber) != 15 || $cardNumber[0] != '3' || !in_array($cardNumber[1],['4','5','6','7'])) {
                            $errMsg .= "<p>American Express card must has 15 digits and starts with 34 to 37</p>";
                            array_push($errors,'express_number');
                        }
                        break;
                }
                if($expiry[2] != '-' || !is_numeric($expiry[0])|| !is_numeric($expiry[1])|| !is_numeric($expiry[3])|| !is_numeric($expiry[4])) {
                    $errMsg .= "<p>Expiry must be in the format of mm-yy</p>";
                    array_push($errors,'expiry');
                } else {
                    if($expiry[0] != '0' && $expiry[0] != '1') {
                        array_push($errors,'expiry_month');
                    }
                    if($expiry[0] == '0' && $expiry[1] == '0') {
                        $errMsg .= "<p>Invalid expiry month</p>";
                        array_push($errors,'expiry_month');
                    } elseif($expiry[0] == '1' && !in_array($expiry[1], ['0','1','2'])) {
                        $errMsg .= "<p>Invalid expiry month</p>";
                        array_push($errors,'expiry_month');
                    }
                }
        
                if(!is_numeric($cvv)  || strlen($cvv) < 2) {
                    $errMsg .= "<p>CVV must be digits</p>";
                    array_push($errors,'cvv');
                }
        
                $dataString = "product=$product&quantity=$quantity";
                if($fea1 != "") $dataString .= "&fea1=$fea1";
                if($fea2 != "") $dataString .= "&fea2=$fea2";
                if($fea3 != "") $dataString .= "&fea3=$fea3";
                $dataString .= "&color=$color&fname=$fname&lname=$lname&email=$email&street=$street&town=$town&state=$state&postcode=$postCode&prefcontact=$prefContact&phone=$phone";
        
                if(count($errors) != 0) {
                    $errString = "";
                    for($i = 0; $i < count($errors); $i ++) {
                        $errCode = $errors[$i];
                        $errString .= "$errCode=1";
                        if($i < count($errors) - 1) {
                            $errString .= "&";
                        }
                    }
                    header("Location: fix_order.php?$dataString&$errString");
                } else {
                    $lastId = 0;
                    $query = "SELECT user_id, email FROM users WHERE email = '$email' AND type = 2;";
                    $result = sqlsrv_query($conn, $query);
                    if($row = sqlsrv_fetch_array($result,sqlsrv_fetch_assoc)) {
                        if($row["email"] == $email) {
                            $lastId = $row["user_id"];
                        }
                    } else {
                        $query = "INSERT INTO users (fname,lname,phone,email,address,type,password) VALUES ('$fname','$lname','$phone','$email', '$address',2,'password');";
                        $result = sqlsrv_query($conn, $query);
                        if ($result === false) {
                            die(print_r(sqlsrv_errors(), true));
                        } else {
                            // Process the result set, fetch rows, etc.
                        }
                        
                    }
                    }
                    $query = "SELECT * FROM products WHERE product_id = $product;";
                    $result = sqlsrv_query($conn,$query);
                    $totalCost = 0;
                    while($row = sqlsrv_fetch_array($result)) {
                        $totalCost += $row["pprice"] * $quantity;
                    }

                    // query to create table order if not already exists
                    $query = "INSERT INTO orders (user_id,pref_contact, card_type, nameoncard, card_number, expiry, cvv, order_cost, order_time,order_items,town,street,state,post_code) VALUES 
                    ($userId,'$prefContact','$cardType','$nameOnCard','$cardNumber','$expiry','$cvv',$total_cprice, GETDATE(), $total_item,'$town','$street','$state','$postCode');";
                    $result = sqlsrv_query($conn,$query) ;

                    if ($result === false) {
                    die(print_r(sqlsrv_errors(), true)); // Handle query execution error
                    }

                    $query = "SELECT TOP 1 order_id as order_id FROM orders WHERE user_id = $userId  ORDER BY order_time DESC;";
                    $result = sqlsrv_query($conn, $query);
                    $lastId = 0; 
                    if ($result !== false) {
                    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
                    if ($row)  $lastId = $row['order_id']; 
                    }

                    $query = "SELECT * FROM cart WHERE user_id = $userId;";
                    $result = sqlsrv_query($conn,$query);
                    while($row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {

                    $productId = $row["product_id"];
                    $color = $row["color"];
                    $version = $row["version"];
                    $quantity = $row["quantity"];
                    $query = "INSERT INTO order_products (order_id, product_id, color, version, quantity) VALUES ($lastId, $productId, '$color', '$version', $quantity);";
                    $result = sqlsrv_query($conn,$query);
                    }


                    sqlsrv_query($conn, $query);
                    header("Location: receipt.php?orderId=$lastId");
                    }
                    } else {
                header("Location: fix_order.php?userId=$userId&insufficient=1");
               }
        }
    }
?>