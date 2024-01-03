<?php
    include("head.inc");
    include("header.inc");
    include("footer.inc");

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

    require_once("settings.php");
    $conn = @mysqli_connect($host, $user, $password, $sql_db);
    if(!$conn) {
        header("Location: home.php");
    } else {
        if(count($_GET) == 0 && count($_POST) == 0) {
            header("Location: home.php");
        } else {
            if(!isset($_GET["orderId"])) {
                echo "<p>Sth went wrong</p>";
            } else {
                $orderId = $_GET["orderId"];
                $query = "SELECT order_id, order_status, order_cost, order_time, 
                            card_type, fname, lname, phone, street, town, state, 
                            post_code, email, nameoncard, card_number FROM orders 
                          WHERE order_id = $orderId;";
                $result = mysqli_query($conn, $query);
                $order = mysqli_fetch_array($result);
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
            <div id='container'>
                <section>
                    <div id='receipt'>
                        <h1>Payment Receipt</h1>
                        <?php
                                    
                        if(!$conn) {
                            echo "<p>Sth went wrong!:(</P>";
                        } else {
                                $query = "SELECT * FROM order_products 
                                            JOIN products ON order_products.product_id = products.product_id 
                                            WHERE order_id = $orderId;";
                                $result = mysqli_query($conn, $query);
                                $cart_array = [];
                                $itemCount = 0;
                                while($cart = mysqli_fetch_assoc($result)) {
                                    array_push($cart_array, $cart);
                                    $itemCount += $cart["quantity"];
                                }
                            echo "
                            
                            <div >
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
                                        cartItem(true,null,$cart_array[$i]["product_id"], $cart_array[$i]["pname"],$cart_array[$i]["pprice"],$cart_array[$i]["color"],$cart_array[$i]["version"],$cart_array[$i]["quantity"],base64_encode($cart_array[$i]["pimage"]),$cart_array[$i]["pimagetype"]);
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
                                </div>
                            </div>
                            ";
                        }
                        ?>
                        <h2>Total: <span class='price'>$<?php if($order) echo $order["order_cost"];?></span></h2>
                        <div id='payment-detail'>
                            <div class='payment-info'>
                                <strong>Order ID: </strong>
                                <span><?php if($order) echo $order["order_id"];?></span>
                            </div>
                            <div class='payment-info'>
                                <strong>Order Status: </strong>
                                <span><?php if($order) echo $order["order_status"];?></span>
                            </div>
                            <div class='payment-info'>
                                <strong>Date: </strong>
                                <span><?php if($order) echo $order["order_time"];?></span>
                            </div>
                            <div class='payment-info'>
                                <strong>Method: </strong>
                                <span><?php if($order) echo $order["card_type"];?></span>
                            </div>
                            <div class='payment-info'>
                                <strong>Name on Card: </strong>
                                <span><?php
                                    $hiddenName = "";
                                    if($order) {
                                        for($i = 0; $i < strlen($order["nameoncard"]); $i++) {
                                            if($i > strlen($order["nameoncard"]) - 4) {
                                                $hiddenName .= $order["nameoncard"][$i];
                                            } else {
                                                $hiddenName .= '*';
                                            }
                                        }
                                    }
                                    echo $hiddenName;
                                ?></span>
                            </div>
                            <div class='payment-info'>
                                <strong>Card number: </strong>
                                <span><?php
                                    $hiddenNum = "";
                                    if($order) {
                                        for($i = 0; $i < strlen($order["card_number"]); $i++) {
                                            if($i > strlen($order["card_number"]) - 4) {
                                                $hiddenNum .= $order["card_number"][$i];
                                            } else {
                                                $hiddenNum .= '*';
                                            }
                                        }
                                    }
                                    echo $hiddenNum;
                                ?></span>
                            </div>
                        </div>
                        <div id='payer-detail'>
                            <div class='payment-info'>
                                <strong>Name: </strong>
                                <span><?php if($order) echo $order["fname"] . " " . $order["lname"];?></span>
                            </div>
                            <div class='payment-info'>
                                <strong>Email: </strong>
                                <span><?php if($order) echo $order["email"];?></span>
                            </div>
                            <div class='payment-info'>
                                <strong>Phone: </strong>
                                <span><?php if($order) echo $order["phone"];?></span>
                            </div>
                            <div class='payment-info'>
                                <strong>Address: </strong>
                                <span><?php if($order) echo $order["street"] . ", " . $order["town"] . ", " . $order["state"] . " " . $order["post_code"];?></span>
                            </div>
                        </div>
                        <a class='shop-btn' href='products.php'>Continue Shopping</a>
                    </div>
                </section>
            </div>
        </main>
        <?php
            footer_code();
        ?>
    </body>
</html>