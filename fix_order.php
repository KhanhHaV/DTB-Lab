<?php
    include("head.inc");
    include("header.inc");
    include("footer.inc");
    // if(!$_SESSION["user"] || $_SESSION["user"] == null) {
    //     header("Location: index.php");
    // }
    require_once("settings.php");
    $conn = @mysqli_connect($host, $user, $password, $sql_db);
    if(!$conn) {
        echo "<p>Sth went wrong!:(</P>";
    } else {
        if(count($_GET) == 0 && count($_POST) == 0) {
            header("Location: home.php");
        } else {
            function cartItem($isHistory,$userId, $productId, $name, $price, $color, $version, $quantity, $image, $imageType) {
                echo "
                <div class='cart-item'>
                    <img src='data:image/$imageType;charset=utf8;base64,$image' alt=''/>
                    <div class='cart-item-info'>
                        <h3>$name</h3>
                        <p class='cart-item-price'><strong>Price: </strong><span class='price'>$$price</span></p>
                        <p class='cart-item-price'><strong>Version: </strong><span>$version</span></p>
                        <p class='cart-item-price'><strong>Color: </strong><span>$color</span></p>
                        <div class='cart-item-quantity'>";
                            echo "<strong>x$quantity</strong>";
                        echo "</div>
                    </div>
                    <img class='item-bg' src='data:image/$imageType;charset=utf8;base64,$image' alt=''/>
                </div>
                ";
            }
            function cartTab($conn, $userId) {
                if(!$conn) {
                    echo "<p>Sth went wrong!:(</P>";
                } else {
                    if($userId != null) {
                        $query = "SELECT * FROM cart JOIN products ON cart.product_id = products.product_id WHERE user_id = $userId;";
                        $result = mysqli_query($conn, $query);
                        $cart_array = [];
                        $itemCount = 0;
                        while($cart = mysqli_fetch_assoc($result)) {
                            array_push($cart_array, $cart);
                            $itemCount += $cart["quantity"];
                        }
                    echo "
                    
                    <div class='tab' id='cart-tab'>
                        <h2>$itemCount Item";
                        if($itemCount > 1) echo "s";
                        echo ":</h2>
                        <div id='cart-list'>
                            <ul>";
                            $total = 0;
                            $i = 0;
        
                            while($i < count($cart_array)) {
                                echo "
                                <li>";
                                cartItem(true,$_SESSION["user"]["user_id"],$cart_array[$i]["product_id"], $cart_array[$i]["pname"],$cart_array[$i]["pprice"],$cart_array[$i]["color"],$cart_array[$i]["version"],$cart_array[$i]["quantity"],base64_encode($cart_array[$i]["pimage"]),$cart_array[$i]["pimagetype"]);
                                $total += $cart_array[$i]["pprice"];
                                if(strpos($cart_array[$i]["version"],'fea1') !== false) {
                                    $total += 50;
                                }
                                if(strpos($cart_array[$i]["version"],'fea2') !== false) {
                                    $total += 100;
                                }
                                if(strpos($cart_array[$i]["version"],'fea3') !== false) {
                                    $total += 200;
                                }
                                $total *= $cart_array[$i]["quantity"];
                                echo "</li>
                                ";
                                $i++;
                            }
                            echo "</ul>
                            <hr/>
                            <h3>Total: <span class='price'>$$total</span></h3>
                        </div>
                    </div>
                    ";
                    } else {
                        if(isset($_GET["product"]) && isset($_GET["quantity"]) && isset($_GET["color"]) && (isset($_GET["fea1"]) || isset($_GET["fea2"]) || isset($_GET["fea3"]))) {
                            $product = htmlspecialchars(trim($_GET["product"]));
                            $quantity = htmlspecialchars(trim($_GET["quantity"]));
                            $color = htmlspecialchars(trim($_GET["color"]));
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
                            $query = "SELECT * FROM products WHERE product_id = $product";
                            $result = mysqli_query($conn, $query);
                            $cart_array = [];
                            $itemCount = 0;
                            $cart = mysqli_fetch_assoc($result);
                            array_push($cart_array, $cart);
                            $itemCount += $quantity;
                            echo "
                            
                            <div class='tab' id='cart-tab'>
                                <h2>$itemCount Item";
                                if($itemCount > 1) echo "s";
                                echo ":</h2>
                                <div id='cart-list'>
                                    <ul>";
                                    $total = 0;
                                    $i = 0;
                
                                    while($i < count($cart_array)) {
                                        echo "
                                        <li>";
                                        cartItem(true,null,$cart_array[$i]["product_id"], $cart_array[$i]["pname"],$cart_array[$i]["pprice"],$color,$fea1." ".$fea2." ".$fea3,$quantity,base64_encode($cart_array[$i]["pimage"]),$cart_array[$i]["pimagetype"]);
                                        if($fea1 != "") {
                                            $total += 50;
                                        }
                                        if($fea2 != "") {
                                            $total += 100;
                                        }
                                        if($fea3 != "") {
                                            $total += 200;
                                        }
                                        $total += $cart_array[$i]["pprice"];
                                        $total *= $quantity;
                                        echo "</li>
                                        ";
                                        $i++;
                                    }
                                    echo "</ul>
                                    <hr/>
                                    <h3>Total: <span class='price'>$$total</span></h3>
                                </div>
                            </div>
                            ";
                        } else {
                            echo "where?";
                            // header("Location: enquiry.php?insufficient=12");
                        }
    
                    }
                }
            }
    
            function userInfoTab($conn, $userId) {
                if(!$conn) {
                    echo "<p>Sth went wrong!:(</P>";
                } else {
                        $fname = htmlspecialchars(trim($_GET["fname"]));   
                        $lname = htmlspecialchars(trim($_GET["lname"]));
                        $phone = htmlspecialchars(trim($_GET["phone"]));
                        $email = htmlspecialchars(trim($_GET["email"]));
                        $street = htmlspecialchars(trim($_GET["street"]));
                        $town = htmlspecialchars(trim($_GET["town"]));
                        $state = htmlspecialchars(trim($_GET["state"]));
                        $prefContact = htmlspecialchars(trim($_GET["prefcontact"]));
                        $postCode = htmlspecialchars(trim($_GET["postcode"]));
                        $addition = "";
                        if(isset($_GET["addition"])) {
                            $addition = htmlspecialchars(trim($_GET["addition"]));
                        }
                        if(isset($_GET["product"]) && isset($_GET["quantity"]) && isset($_GET["color"]) && (isset($_GET["fea1"]) || isset($_GET["fea2"]) || isset($_GET["fea3"]))) {
                            $product = htmlspecialchars(trim($_GET["product"]));
                            $quantity = htmlspecialchars(trim($_GET["quantity"]));
                            $color = htmlspecialchars(trim($_GET["color"]));
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
                        } else {
                            // header("Location: enquiry.php?insufficient=13");
                        }
                    echo "
                    <div class='tab'>
                        <div id='payment-form'>
                            <form method='POST' action='";
                            if($userId || $userId != "") {
                                echo "checkout.php?userId=$userId";
                            } else {
                                echo "process_order.php?productId=$product&quantity=$quantity";
                                if($fea1 != "") echo"&fea1=$fea1";
                                if($fea2 != "") echo"&fea2=$fea2";
                                if($fea3 != "") echo"&fea3=$fea3";
                                echo "&color=$color";
                            }
                            echo "' novalidate>
                            <h2>Your Information:</h2>
                            <div id='input-fields' class='payment-inputs'>
                                    <div class='group'>
                                        <input type='text' name='fname' id='fname' ";
                                        if(isset($_GET["fname"])) echo "value='$fname' ";
                                        echo "required/>
                                        <span class='highlight'></span>
                                        <span class='bar'></span>
                                        <label for='fname'><strong>First Name: </strong></label>
                                    </div>
                                    ";
                                    if(isset($_GET["err_fname"])) 
                                        echo "
                                        <p class='error'>
                                            First name must contain only text and maximum length of 25 characters
                                        </p>";
                                    echo "
                                    <div class='group'>
                                        <input type='text' name='lname' id='lname'";
                                        if(isset($_GET["lname"])) echo "value='$lname' ";
                                        echo "required/>
                                        <span class='highlight'></span>
                                        <span class='bar'></span>
                                        <label for='lname'><strong>Last Name: </strong></label>
                                    </div>
                                    ";
                                    if(isset($_GET["err_lname"])) echo "<p class='error'>Last name must contain only text and maximum length of 25 characters</p>";
                                    echo "
                                    <div class='group'>
                                        <input type='email' name='email' id='email' ";
                                        if(isset($_GET["email"])) echo "value='$email' ";
                                        echo " required/>
                                        <span class='highlight'></span>
                                        <span class='bar'></span>
                                        <label for='email'><strong>Email: </strong></label>
                                    </div>
                                    ";
                                    if(isset($_GET["err_email"])) echo "<p class='error'>Wrong email syntax (e.g. example@example.com)</p>";
                                    echo "
                                    ";
                                    if(isset($_GET["err_email_exists"])) echo "<p class='error'>An account with this email exists, please login</p>";
                                    echo "
                                    ";
                                    if(isset($_GET["err_email_exists_loggedin"])) echo "<p class='error'>An account with this email exists, please use a different email</p>";
                                    echo "
                                    <fieldset>
                                        <legend><strong>Address</strong></legend>
                                        <div class='group'>
                                            <input type='text' id='street' name='street' ";
                                            if(isset($_GET["street"])) echo "value='$street' ";
                                            echo "required='required' />
                                            <span class='highlight'></span>
                                            <span class='bar'></span>
                                            <label for='street'><strong>Street Address: </strong></label>
                                        </div>
                                        ";
                                        if(isset($_GET["err_street"])) echo "<p class='error'>Street address must have maximum length of 40 characters</p>";
                                        echo "
                                        <div class='group'>
                                            <input type='text' id='town' name='town' ";
                                            if(isset($_GET["town"])) echo "value='$town' ";
                                            echo "required='required' />
                                            <span class='highlight'></span>
                                            <span class='bar'></span>
                                            <label for='town'><strong>Suburb/Town: </strong></label>
                                        </div>
                                        ";
                                        if(isset($_GET["err_town"])) echo "<p class='error'>Town address must have maximum length of 40 characters</p>";
                                        echo "
                                        <div class='other-group select'>
                                            <label for='state'><strong>State: </strong></label>
                                            <select id='state' name='state' required='required'>
                                                <option value='' hidden>Select</option>
                                                <option value='vic'";
                                                if($state == 'vic') echo "selected";
                                                echo ">VIC</option>
                                                <option value='nsw'";
                                                if($state == 'nsw') echo "selected";
                                                echo ">NSW</option>
                                                <option value='qld'";
                                                if($state == 'qld') echo "selected";
                                                echo ">QLD</option>
                                                <option value='nt'";
                                                if($state == 'nt') echo "selected";
                                                echo ">NT</option>
                                                <option value='wa'";
                                                if($state == 'wa') echo "selected";
                                                echo ">WA</option>
                                                <option value='sa'";
                                                if($state == 'sa') echo "selected";
                                                echo ">SA</option>
                                                <option value='tas'";
                                                if($state == 'tas') echo "selected";
                                                echo ">TAS</option>
                                                <option value='act'";
                                                if($state == 'act') echo "selected";
                                                echo ">ACT</option>
                                            </select>
                                        </div>
                                        ";
                                        if(isset($_GET["err_state"])) echo "<p class='error'>State must in the presented list</p>";
                                        echo "
                                    </fieldset>
                                    <div class='group'>
                                        <input type='text' name='phone' id='phonenumber' ";
                                        if(isset($_GET["phone"])) echo "value='$phone' ";
                                        echo "  required/>
                                        <span class='highlight'></span>
                                        <span class='bar'></span>
                                        <label for='phonenumber'><strong>Phone Number: </strong></label>
                                    </div>
                                    ";
                                    if(isset($_GET["err_phone"])) echo "<p class='error'>Phone number must be digits and has maximum length of 10</p>";
                                    echo "
                                    <div class='other-group radio-group'>
                                        <p id='prefcontact'><strong>Preffered Contact: </strong></p>
                                        <label class='option-group'>Email
                                            <input type='radio' id='prefemail' name='prefcontact' value='email' ";
                                            if(isset($_GET["prefcontact"])) {
                                                if($prefContact == 'email') {
                                                    echo "checked='checked'";
                                                }
                                            } else {
                                                echo "checked='checked'";
                                            } 
                                            echo "/>
                                            <span class='radio-checkmark'></span>
                                        </label>
                                        <label class='option-group'>Post
                                            <input type='radio' id='post' name='prefcontact' value='post' ";
                                            if(isset($_GET["prefcontact"])) {
                                                if($prefContact == 'post') {
                                                    echo "checked='checked'";
                                                }
                                            }
                                            echo "/>
                                            <span class='radio-checkmark'></span>
                                        </label>
                                        <label class='option-group'>Phone
                                            <input type='radio' id='phone' name='prefcontact' value='phone' ";
                                            if(isset($_GET["prefcontact"])) {
                                                if($prefContact == 'phone') {
                                                    echo "checked='checked'";
                                                }
                                            }
                                            echo "/>
                                            <span class='radio-checkmark'></span>
                                        </label>
                                    </div>
                                    ";
                                    if(isset($_GET["err_prefcontact"])) echo "<p class='error'>Preffered contact must be phone, post, or email</p>";
                                    echo "
                                    <div class='group'>
                                        <input type='text' id='postcode' name='postcode' ";
                                        if(isset($_GET["postcode"])) echo "value='$postCode' ";
                                        echo " pattern='[0-9]{4}' required='required' />
                                        <span class='highlight'></span>
                                        <span class='bar'></span>
                                        <label for='postcode'><strong>Post Code: </strong></label>
                                    </div>";
                            
                                    if(isset($_GET["err_postcode"])) echo "<p class='error'>Post code must be 4 digits</p>";
                                    
                                    if(isset($_GET["addition"]) && $addition != "") echo "<p><strong>Additional information: </strong>$addition</p> ";
                                    echo "
                            </div>
                            <h2>Card Details:</h2>
                            <div id='card-details'>
                                <div>
                                    <label for='card-type'>Card Type: </label>
                                    <select name='card-type' required>
                                        <option value='visa'>VISA</option>
                                        <option value='master'>Mastercard</option>
                                        <option value='express'>American Express</option>
                                    </select>
                                </div>
                                ";
                                if(isset($_GET["card_type"])) echo "<p class='error'>Card type must be VISA, MasterCard, or American Express</p>";
                                echo "
                                <div>
                                    <label for='nameoncard'>Name on Card: </label>
                                    <input type='text' name='nameoncard' id='nameoncard' required/>
                                </div>
                                ";
                                if(isset($_GET["err_nameoncard"])) echo "<p class='error'>Invalid name</p>";
                                echo "
                                <div>
                                    <label for='cardnumber'>Card number: </label>
                                    <input type='text' name='cardnumber' id='cardnumber' required/>
                                </div>
                                ";
                                if(isset($_GET["visa_number"])) echo "<p class='error'>Visa card number must has 16 digits and starts with 4</p>";
                                if(isset($_GET["master_number"])) echo "<p class='error'>Mastercard number must has 16 digits and starts with 51 to 55</p>";
                                if(isset($_GET["express_number"])) echo "<p class='error'>American Express card must has 15 digits and starts with 34 to 37</p>";
                                echo "
                                <div>
                                    <label for='expiry'>Expiry date: </label>
                                    <input type='text' name='expiry' id='expiry' required/>
                                </div>
                                ";
                                if(isset($_GET["expiry"])) echo "<p class='error'>Expiry must be in the format of mm-yy</p>";
                                if(isset($_GET["expiry_month"])) echo "<p class='error'>Invalid expiry month</p>";
                                echo "
                                <div>
                                    <label for='cvv'>CVV: </label>
                                    <input type='text' name='cvv' id='cvv' required/>
                                </div>
                                ";
                                if(isset($_GET["cvv"])) echo "<p class='error'>CVV must be digits</p>";
                                echo "
                            </div>
                            <input type='submit' id='update-btn' class='shop-btn' value='Check out'/>
                        </form>
                    </div>
                </div>";
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <?php
        head_code();
    ?>
    <body>
        <?php
            header_code(2);
        ?>
        <main>
            <div id="container">
                <section>
                    <div class='tab payment' id='cart-tab'>
                        <h1>Payment</h1>
                        <?php 
                            if(!$_SESSION["user"] || $_SESSION["user"] == null
                            //     isset($_GET["fname"]) && isset($_GET["lname"]) && isset($_GET["email"]) && isset($_GET["street"]) && isset($_GET["town"]) && isset($_GET["state"])
                            //  && isset($_GET["phone"]) && isset($_GET["prefcontact"]) && isset($_GET["postcode"]) && isset($_GET["product"]) && isset($_GET["quantity"]) && isset($_GET["version"]) && isset($_GET["color"])
                             ) {

                                cartTab($conn, null);
                            } else {
                                cartTab($conn,$_SESSION["user"]["user_id"]); 
                            }
                        ?>
                        <div>
                            <?php 
                            if(!$_SESSION["user"] || $_SESSION["user"] == null) {
                                userInfoTab($conn, null);
                             } else {
                                userInfoTab($conn, $_SESSION["user"]["user_id"]);
                             }
                            ?>
                         </div>
                    </div>
                </section>
            </div>
        </main>
        <?php
            footer_code();
        ?>
    </body>
</html>