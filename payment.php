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

        function cartItem($isHistory,$userId, $productId, $name, $price, $color, $version, $quantity, $image, $imageType) {
            echo "
            <div class='cart-item'>
                <img src='data:image/$imageType;charset=utf8;base64,$image' alt=''/>
                <div class='cart-item-info'>
                    <a class='link-to-product' href='productDesc.php?productId=$productId'><h3>$name</h3></a>
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
                if(isset($_GET["userId"])) {
                    $query = "SELECT * FROM cart JOIN products ON cart.product_id = products.product_id WHERE user_id = $userId;";
                    $result = mysqli_query($conn, $query);
                    $cart_array = [];
                    $itemCount = 0;
                    while($cart = mysqli_fetch_assoc($result)) {
                        array_push($cart_array, $cart);
                        $itemCount += $cart["quantity"];
                    }
                    if($itemCount == 0) {
                        if(array_key_exists("user",$_SESSION) && $_SESSION["user"] != null) {
                            header("Location: products.php");
                        } else {
                            header("Location: enquiry.php");
                        }
                    }

                    // display products' image, name, price, feature, color selected, quantity, and total cost of the order. 
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
                                cartItem(true,$_SESSION["user"]["user_id"],$cart_array[$i]["product_id"], 
                                            $cart_array[$i]["pname"],$cart_array[$i]["pprice"],$cart_array[$i]["color"],
                                            $cart_array[$i]["version"],$cart_array[$i]["quantity"],
                                            base64_encode($cart_array[$i]["pimage"]),$cart_array[$i]["pimagetype"]);
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
                    if(isset($_POST["product"]) && $_POST["product"] != "" && isset($_POST["quantity"]) && $_POST["quantity"] != "" && isset($_POST["color"]) && (isset($_POST["fea1"]) || isset($_POST["fea2"]) || isset($_POST["fea3"]))) {
                        $product = htmlspecialchars(trim($_POST["product"]));
                        $quantity = htmlspecialchars(trim($_POST["quantity"]));
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
                        header("Location: enquiry.php?insufficient=12");
                    }

                }
            }
        }

        function userInfoTab($conn, $userId) {
            if(!$conn) {
                echo "<p>Sth went wrong!:(</P>";
            } else {
                if(isset($_GET["userId"])) {
                    $query = "SELECT fname, lname, email, address, phone FROM users WHERE user_id = $userId;";
                    $result = mysqli_query($conn, $query);
                    $row = mysqli_fetch_array($result);
                    $fname = $row["fname"];
                    $lname = $row["lname"];
                    $email = $row["email"];
                    $address = $row["address"];
                    $phone = $row["phone"];
                } else {
                    $fname = htmlspecialchars(trim($_POST["fname"]));   
                    $lname = htmlspecialchars(trim($_POST["lname"]));
                    $phone = htmlspecialchars(trim($_POST["phone"]));
                    $email = htmlspecialchars(trim($_POST["email"]));
                    $street = htmlspecialchars(trim($_POST["street"]));
                    $town = htmlspecialchars(trim($_POST["town"]));
                    $state = htmlspecialchars(trim($_POST["state"]));
                    $prefContact = htmlspecialchars(trim($_POST["prefcontact"]));
                    $postCode = htmlspecialchars(trim($_POST["postcode"]));
                    $addition = "";
                    if(isset($_POST["addition"])) {
                        $addition = htmlspecialchars(trim($_POST["addition"]));
                    }
                    if(isset($_POST["product"]) && isset($_POST["quantity"]) && isset($_POST["color"]) && (isset($_POST["fea1"]) || isset($_POST["fea2"]) || isset($_POST["fea3"]))) {
                        $product = htmlspecialchars(trim($_POST["product"]));
                        $quantity = htmlspecialchars(trim($_POST["quantity"]));
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
                    } else {
                        header("Location: enquiry.php?insufficient=13");
                    }
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
                                        <input type='text' name='fname' id='fname' value='$fname' required/>
                                        <span class='highlight'></span>
                                        <span class='bar'></span>
                                        <label for='fname'><strong>First Name: </strong></label>
                                    </div>
                                    <div class='group'>
                                        <input type='text' name='lname' id='lname' value='$lname' required/>
                                        <span class='highlight'></span>
                                        <span class='bar'></span>
                                        <label for='lname'><strong>Last Name: </strong></label>
                                    </div>
                                    <div class='group'>
                                        <input type='email' name='email' id='email' value='$email' required/>
                                        <span class='highlight'></span>
                                        <span class='bar'></span>
                                        <label for='email'><strong>Email: </strong></label>
                                    </div>
                                    <fieldset>
                                        <legend><strong>Address</strong></legend>
                                        <div class='group'>
                                            <input type='text' id='street' name='street' ";
                                            if(isset($_POST["street"])) echo "value='$street' ";
                                            echo "required='required' />
                                            <span class='highlight'></span>
                                            <span class='bar'></span>
                                            <label for='street'><strong>Street Address: </strong></label>
                                        </div>
                                        <div class='group'>
                                            <input type='text' id='town' name='town' ";
                                            if(isset($_POST["town"])) echo "value='$town' ";
                                            echo "required='required' />
                                            <span class='highlight'></span>
                                            <span class='bar'></span>
                                            <label for='town'><strong>Suburb/Town: </strong></label>
                                        </div>
                                        <div class='other-group select'>
                                            <label for='state'><strong>State: </strong></label>
                                            <select id='state' name='state' required='required'>
                                                <option value='' hidden>Select</option>
                                                <option value='vic'";
                                                if(isset($_POST["state"]) && $state == 'vic') echo "selected";
                                                echo ">VIC</option>
                                                <option value='nsw'";
                                                if(isset($_POST["state"]) && $state == 'nsw') echo "selected";
                                                echo ">NSW</option>
                                                <option value='qld'";
                                                if(isset($_POST["state"]) && $state == 'qld') echo "selected";
                                                echo ">QLD</option>
                                                <option value='nt'";
                                                if(isset($_POST["state"]) && $state == 'nt') echo "selected";
                                                echo ">NT</option>
                                                <option value='wa'";
                                                if(isset($_POST["state"]) && $state == 'wa') echo "selected";
                                                echo ">WA</option>
                                                <option value='sa'";
                                                if(isset($_POST["state"]) && $state == 'sa') echo "selected";
                                                echo ">SA</option>
                                                <option value='tas'";
                                                if(isset($_POST["state"]) && $state == 'tas') echo "selected";
                                                echo ">TAS</option>
                                                <option value='act'";
                                                if(isset($_POST["state"]) && $state == 'act') echo "selected";
                                                echo ">ACT</option>
                                            </select>
                                        </div>
                                    </fieldset>
                                    <div class='group'>
                                        <input type='text' name='phone' id='phonenumber' value='$phone' required/>
                                        <span class='highlight'></span>
                                        <span class='bar'></span>
                                        <label for='phonenumber'><strong>Phone Number: </strong></label>
                                    </div>
                                    <div class='other-group radio-group'>
                                        <p id='prefcontact'><strong>Preffered Contact: </strong></p>
                                        <label class='option-group'>Email
                                            <input type='radio' id='prefemail' name='prefcontact' value='email' ";
                                            if(isset($_POST["prefcontact"])) {
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
                                            if(isset($_POST["prefcontact"])) {
                                                if($prefContact == 'post') {
                                                    echo "checked='checked'";
                                                }
                                            }
                                            echo "/>
                                            <span class='radio-checkmark'></span>
                                        </label>
                                        <label class='option-group'>Phone
                                            <input type='radio' id='phone' name='prefcontact' value='phone' ";
                                            if(isset($_POST["prefcontact"])) {
                                                if($prefContact == 'phone') {
                                                    echo "checked='checked'";
                                                }
                                            }
                                            
                                            echo "/>
                                            <span class='radio-checkmark'></span>
                                        </label>
                                    </div>
                                    <div class='group'>
                                        <input type='text' id='postcode' name='postcode' ";
                                        if(isset($_POST["postcode"])) echo "value='$postCode' ";
                                        echo "pattern='[0-9]{4}' required='required' />
                                        <span class='highlight'></span>
                                        <span class='bar'></span>
                                        <label for='postcode'><strong>Post Code: </strong></label>
                                    </div>";
                                    if(isset($_POST["addition"]) && $addition != "") echo "<p><strong>Additional information: </strong>$addition</p> ";
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
                                <div>
                                    <label for='nameoncard'>Name on Card: </label>
                                    <input type='text' name='nameoncard' id='nameoncard' required/>
                                </div>
                                <div>
                                    <label for='cardnumber'>Card number: </label>
                                    <input type='text' name='cardnumber' id='cardnumber' required/>
                                </div>
                                <div>
                                    <label for='expiry'>Expiry date: </label>
                                    <input type='text' name='expiry' id='expiry' required/>
                                </div>
                                <div>
                                    <label for='cvv'>CVV: </label>
                                    <input type='text' name='cvv' id='cvv' required/>
                                </div>
                            </div>
                            <input type='submit' id='update-btn' class='shop-btn' value='Check out'/>
                        </form>
                    </div>
                </div>";
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
                            //     isset($_POST["fname"]) && isset($_POST["lname"]) && isset($_POST["email"]) && isset($_POST["street"]) && isset($_POST["town"]) && isset($_POST["state"])
                            //  && isset($_POST["phone"]) && isset($_POST["prefcontact"]) && isset($_POST["postcode"]) && isset($_POST["product"]) && isset($_POST["quantity"]) && isset($_POST["version"]) && isset($_POST["color"])
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