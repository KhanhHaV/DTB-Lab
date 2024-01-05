<?php
    include("head.inc");
    include("header.inc");
    include("footer.inc");
    if(!$_SESSION["user"] || $_SESSION["user"] == null) {
        header("Location: index.php");
    }
    include("connect.inc");
    
    function cartItem($isHistory,$userId, $productId, $name, $price, $color, $version, $quantity, $image, $imageType) {
        echo "
        <div class='cart-item'>
        <img src='$image' alt=''/>
            <div class='cart-item-info'>
                <a class='link-to-product' href='productDesc.php?productId=$productId'><h3>$name</h3></a>
                <p class='cart-item-price'><strong>Price: </strong><span class='price'>$$price</span></p>
                <p class='cart-item-price'><strong>Version: </strong><span>$version</span></p>
                <p class='cart-item-price'><strong>Color: </strong><span>$color</span></p>
                <div class='cart-item-quantity'>";
                    if(!$isHistory) echo "<a href='incAndDec.php?userId=$userId&productId=$productId&action=dec'><div>-</div></a>";
                    echo "<strong>x$quantity</strong>";
                    if(!$isHistory) echo "<a href='incAndDec.php?userId=$userId&productId=$productId&action=inc'><div>+</div></a>";
                echo "</div>
            </div>
            <img class='item-bg' src='$image' alt=''/>
        </div>
        ";
    }

    function userInfoTab($conn) {
        if(!$conn) {
            echo "<p>Sth went wrong!:(</P>";
        } else {
            $userId = $_SESSION["user"]["user_id"];
            $query = "SELECT fname, lname, email, address, phone FROM users WHERE user_id = $userId;";
            $result = sqlsrv_query($conn, $query);
            $row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC);
            $fname = $row["fname"];
            $lname = $row["lname"];
            $email = $row["email"];
            $address = $row["address"];
            $phone = $row["phone"];
            echo "
            <div class='tab' id='info-tab'>
                <h2>Your Information</h2>
                <form method='POST' action='updateInfo.php?userId=$userId' enctype='multipart/form-data'>
                    <div>
                        <label for='fname'>First name: </label>
                        <input type='text' name='fname' id='fname' value='$fname' required/>
                    </div>
                    <div>
                        <label for='lname'>Last name: </label>
                        <input type='text' name='lname' id='lname' value='$lname' required/>
                    </div>
                    <div>
                        <label for='email'>Email: </label>
                        <input type='email' name='email' id='email' value='$email' required/>
                    </div>
                    <div>
                        <label for='address'>Address: </label>
                        <input type='text' name='address' id='address' value='$address'/>
                    </div>
                    <div>
                        <label for='phone'>Phone number: </label>
                        <input type='text' name='phone' id='phone' value='$phone'/>
                    </div>
                    <div>
                        <label for='avt'>Avatar: </label>
                        <input type='file' name='avt' id='avt' accept='image/png, image/gif, image/jpeg'/>
                    </div>
                    <input type='submit' id='update-btn' class='shop-btn' value='Update'/>
                </form>
            </div>";
        }
    }

    function cartTab($conn, $userId) {
        if(!$conn) {
            echo "<p>Sth went wrong!:(</P>";
        } else {
            $query = "SELECT * FROM cart JOIN products ON cart.product_id = products.product_id WHERE user_id = $userId;";
            $result = sqlsrv_query($conn, $query);
            if ($result === false) {
                die(print_r(sqlsrv_errors(), true)); // This will output detailed error information
            }
            // $cart = mysqli_fetch_array($result);
            // $itemCount = count($cart);
            $cart = [];
            $itemCount = 0;
            while($row = sqlsrv_fetch_assoc($result,SQLSRV_FETCH_ASSOC)) {
                array_push($cart, $row);
                $itemCount += $row["quantity"];
            }
        echo "
        
        <div class='tab' id='cart-tab'>
            <h2>Your Cart ($itemCount item";
            if($itemCount > 1) echo "s";
            echo ")</h2>
            <div id='cart-list'>
                <ul>";
                $total = 0;
                $i = 0;
                while($i < count($cart)) {
                    echo "
                    <li>";
                    cartItem(false,$_SESSION["user"]["user_id"],$cart[$i]["product_id"], $cart[$i]["pname"],$cart[$i]["pprice"],$cart[$i]["color"],$cart[$i]["version"],$cart[$i]["quantity"],base64_encode($cart[$i]["pimage"]),$cart[$i]["pimagetype"]);
                    $total += $cart[$i]["pprice"];
                    if(strpos($cart[$i]["version"],'fea1') !== false) {
                        $total += 50;
                    }
                    if(strpos($cart[$i]["version"],'fea2') !== false) {
                        $total += 100;
                    }
                    if(strpos($cart[$i]["version"],'fea3') !== false) {
                        $total += 200;
                    }
                    $total *= $cart[$i]["quantity"];
                    echo "</li>
                    ";
                    $i++;
                }
                echo "</ul>
                <hr/>
                <h3>Total: <span class='price'>$$total</span></h3>
            </div>";
            if($itemCount > 0) echo "<a class='shop-btn' href='payment.php?userId=$userId'>Go to Checkout</a>";
        echo "</div>
        ";
        }
    }

    function historyTab($conn, $userId) {
        if(!$conn) {
            echo "<p>Sth went wrong!:(</P>";
        } else {

            // query to select order information of the logged in user
            $query = "SELECT * FROM orders WHERE user_id = $userId ORDER BY order_time DESC;";
            $result = sqlsrv_query($conn, $query);
            $history = [];
            while($row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {
                array_push($history, $row);
            }
            echo "
            
            <div class='tab' id='history-tab'>
            <h2>Order History</h2>
            <ul>
            ";
            $i= 0;

            // display each order
            while($i < count($history)) {
                $orderId = $history[$i]["order_id"];
                $date = $history[$i]["order_time"];
                $status = $history[$i]["order_status"];
                echo "
                <li>
                    <div class='prev-order'>    
                        <div class='general-order-info'>
                            <h3>Order ID: $orderId</h3>
                            <p><strong>Date: </strong>$date</p>
                            <p><strong>Status: </strong><span class='$status'>$status</span></p>
                        </div>
                        <hr/>
                        <div class='prev-order-list'>
                            <ul>
                ";

                // query to select product information based on product ID of an order
                $historyQuery = "SELECT * FROM order_products JOIN products ON order_products.product_id = products.product_id WHERE order_id = $orderId;";
                $historyResult = sqlsrv_query($conn, $historyQuery);
                
                $historyItem = [];
                while($row = sqlsrv_fetch_array($historyResult,SQLSRV_FETCH_ASSOC)) {
                    array_push($historyItem, $row);
                }
                $total = 0;
                $j = 0;

                // display products
                while($j < count($historyItem)) {
                    cartItem(true,$_SESSION["user"]["user_id"],$historyItem[$j]["product_id"], $historyItem[$j]["pname"],$historyItem[$j]["pprice"],$historyItem[$j]["color"],$historyItem[$j]["version"],$historyItem[$j]["quantity"],base64_encode($historyItem[$j]["pimage"]),$historyItem[$j]["pimagetype"]);
                   
                    $total += $historyItem[$j]["pprice"];
                    if(strpos($historyItem[$j]["version"],'fea1') !== false) {
                        $total += 50;
                    }
                    if(strpos($historyItem[$j]["version"],'fea2') !== false) {
                        $total += 100;
                    }
                    if(strpos($historyItem[$j]["version"],'fea3') !== false) {
                        $total += 200;
                    }
                    $total *= $historyItem[$j]["quantity"];
                    $j++;
                }

                echo "
                            </ul>
                        </div>
                        <hr/>
                        <h3>Total: <span class='price'>$$total</span></h3>
                    </div>
                </li>
                ";
                $i++;
            }
            echo "
            </ul>
            </div>
            ";
        }
    }


    function addProduct() {
        echo "
        <div class='tab products-tab'>
            <h2>Add a Product</h2>
            <form method='POST' action='addProduct.php' enctype='multipart/form-data'>
                <div>
                    <label for='pname'>Product name </label>
                    <input type='text' name='pname' id='pname' required/>
                </div>
                <div>
                    <label for='pdesc'>Product description </label>
                    <textarea type='text' name='pdesc' id='pdesc' required></textarea>
                </div>
                <div>
                    <label for='pprice'>Product price : $</label>
                    <input type='number' name='pprice' id='pprice' required/>
                </div>
                <div>
                    <label for='pimage'>Product image </label>
                    <input type='file' name='pimage' id='pimage' accept='image/png, image/gif, image/jpeg' required/>
                </div>
                <div>
                    <label for='discount'>Product discount </label>
                    <input type='number' min='0' max='100' name='discount' id='discount'>
                </div>
                <div>
                    <label for='pstock'>Product stock </label>
                    <input type='number' min='1' name='pstock' id='pstock' required>
                </div>
                
                <input type='submit' id='update-btn' class='shop-btn' value='Add'/>
            </form>
        </div>";
    }
function editProduct($conn) {
    echo "
    <div class='tab products-tab'>
        <h2>Manage Products</h2>
        <form method='POST' action='editProduct.php' enctype='multipart/form-data'>
            <div>
                <label for='editting-product'>Edit product: </label>
                <select name='editting-product' required>
                    <option value=''>Select</option>";
                    if(!$conn) {
                        echo "<p>Sth went wrong!:(</P>";
                    } else {
                        $query = "SELECT * FROM products ORDER BY pdate DESC;";
                        $result = sqlsrv_query($conn, $query);
                        while($row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {
                            $id = $row["product_id"];
                            $name = $row["pname"];
                            echo "<option value='$id'>ID: $id, Name: $name</option>";
                        }
                    }
                echo "</select>
            </div>
            <div>
                <label for='pname'>Product name: </label>
                <input type='text' name='pname' id='pname'/>
            </div>
            <div>
                <label for='pdesc'>Product description: </label>
                <textarea type='text' name='pdesc' id='pdesc'></textarea>
            </div>
            <div>
                <label for='pprice'>Product price: $</label>
                <input type='number' name='pprice' id='pprice'/>
            </div>
            <div>
                <label for='pimage'>Product image </label>
                <input type='file' name='pimage' id='pimage' accept='image/png, image/gif, image/jpeg'  />
            </div>
            <div>
                <label for='discount'>Product discount </label>
                <input type='number' min='0' max='100' name='discount' id='discount' >
            </div>
            <div>
            <label for='pstock'>Product stock </label>
            <input type='number' min = '1' name='pstock' id='pstock' >
            </div>
            <div id='edit-btns'>
                <input type='submit' id='update-btn' class='shop-btn' value='Edit'/>
                <input type='submit' id='delete-btn' name='delete' class='shop-btn' value='Delete'/>
            </div>
        </form>
    </div>
    ";
}
function orderRequest($conn) {
    if(!$conn) {
        echo "<p>Sth went wrong!:(</P>";
    } else {
        // form to post conditions to search and filter orders 
        echo "
        <div class='tab' id='order-request'>
            <h2>Pending Requests</h2>
            <div class='request-item search-item'>
            <form method='POST' action='manager.php?page=2' id='search-box'>
                <input type='text' name='searchkey' id='searchkey' placeholder='Search order with username ...'/>
                <div id='filter-box'>
                    <select id='order' class='order half' name='order'>
                        <option value=''>Filter</option>
                        <option value='old-up'>All - Latest orders</option>
                        <option value='old-down'>All - Earliest orders</option>
                        <option value='price-up'>Price: Low to high</option>
                        <option value='price-down'>Price: High to low</option>
                        <option value='pending'>Pending orders</option>
                    </select>
                    <select id='product' class='order half' name='product'>
                        <option value=''>Orders contain product</option>";
                        // select all products fromm database
                                $query = "SELECT * FROM products ORDER BY pdate DESC";
                                $result = sqlsrv_query($conn,$query);
                                $options = [];
                                while($row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {
                                    array_push($options, $row);
                                }
                                $i = 0;
                                while($i < count($options)) {
                                    $id = $options[$i]["product_id"];
                                    $name = $options[$i]["pname"];
                                    echo "<option value='$id'>$name</option>";
                                    $i++;
                                }
                    echo "</select>
                </div>
                <button id='search-btn' type='submit'>
                    <img src='images/search.svg' alt=''>
                </button>
            </form>
            </div>
        ";
        $orderBy = "order_time DESC;";
        $query = "SELECT order_id, user_id, fname, lname, 
                    phone, email, street, town, state, 
                    post_code, pref_contact, order_status, order_time 
                  FROM orders ";

        // select all orders except the orders that have status of ARCHIVED
        $query .= "WHERE order_status != 'ARCHIVED' AND ";
        $where = "";
        $whereCon = [];
        
        // check if the admin fill search by username and append conditions to query
        if(isset($_POST["searchkey"])) {
            array_push($whereCon,'searchKey');
            $searchKey = $_POST["searchkey"];
            $where .= "(fname LIKE '%$searchKey%' OR lname LIKE '%$searchKey%') AND ";
        } 

        // check if the admin select the option to display only orders that have status PENDING and append conditions to query
        if(isset($_POST["order"]) && $_POST["order"] == 'pending') {
            array_push($whereCon,'pending');
            $where .= "order_status = 'PENDING' AND ";
        }

        // check if the admin select to display orders that contain a particular product and append conditions to query
        if(isset($_POST["product"]) && $_POST["product"] != "") {
            $searchId = $_POST["product"];
            array_push($whereCon,'product');
            $incProductQuery = "SELECT order_id FROM order_products WHERE product_id = $searchId;";
            $result = sqlsrv_query($conn, $incProductQuery);
            $ordersIncProduct = [];
            $inString = "";
            while($row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {
                array_push($ordersIncProduct,$row["order_id"]);
            }
            for($i = 0; $i < count($ordersIncProduct); $i++) {
                $inString .= $ordersIncProduct[$i];
                if($i != count($ordersIncProduct) - 1) {
                    $inString .= ",";
                }
            }
            
            if($inString !="") {$where .= "order_id IN ($inString) AND ";} else {$where .= "-1 AND ";}
        }

        // solve the redundant AND issue when appending query
        $where .= "1 ";
        $query .= $where;

        //  check if admin select option to sort orders and append conditions to query
        if(isset($_POST["order"])) {
            switch($_POST["order"]) {
                case 'price-up':
                    $orderBy = "order_cost ASC";
                    break; 
                case 'price-down':
                    $orderBy = "order_cost DESC";
                    break; 
                case 'old-up':
                    $orderBy = "order_time DESC";
                    break; 
                case 'old-down':
                    $orderBy = "order_time ASC";
                    break;
            }
        }
        $query .= "ORDER BY $orderBy;";
        $result = sqlsrv_query($conn,$query);
        
        if ($result === false) {
            die(print_r(sqlsrv_errors(), true)); // This will output detailed error information
        }
        
        // display every order tab
        while($row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {
            $orderId = $row["order_id"];
            $userId = $row["user_id"];
            $fname = $row["fname"];
            $lname = $row["lname"];
            $phone = $row["phone"];
            $email = $row["email"];
            $street = $row["street"];
            $town = $row["town"];
            $state = $row["state"];
            $postCode = $row["post_code"];
            $prefContact = $row["pref_contact"];
            $status = $row["order_status"];
            $date = $row["order_time"];
            $avt = sqlsrv_fetch_array(sqlsrv_query($conn, "SELECT avatar, avatar_type FROM users WHERE user_id = ". $userId .";"),SQLSRV_FETCH_ASSOC);
            echo "
                <div class='request-item'>";
                    if($status == 'PENDING') {
                        echo "<a class='delete-btn' href='deleteRequest.php?orderId=$orderId'>
                            DELETE
                        </a>";
                    }
                    echo "
                    <div class='avtNname'>
                        <div class='small-avt'>";
                        if($avt["avatar"] != null && $avt["avatar_type"] != null) {
                            $type = $avt["avatar_type"];
                            $image = base64_encode($avt["avatar"]);
                            echo "<img src='data:image/$type;charset=utf8;base64,$image' alt=''/>";
                        } else {
                            echo "<img src='images/user/user.png' alt=''/>";
                        }
                        echo "
                        </div>
                        <h3>$fname $lname</h3>
                    </div>
                    <h4>Order ID: $orderId</h4>
                    <div class='order-general-info'><strong>Date: </strong><span>$date</span></div>
                    <div class='order-general-info'><strong>Email: </strong><span>$email</span></div>
                    <div class='order-general-info'><strong>Phone: </strong><span>$phone</span></div>
                    <div class='order-general-info'><strong>Address: </strong><span>$street, $town, $state $postCode</span></div>
                    <div class='order-general-info'><strong>Preffered contact: </strong><span>$prefContact</span></div>
                    <div class='order-request-items'>
                        <p><strong>Items Ordered:</strong></p>
                        <table class='info-table'>
                            <tr>
                                <th></th>
                                <th>Product ID</th>
                                <th>Name</th>
                                <th>Color</th>
                                <th>Version</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>";
                            if(!$conn) {
                                echo "<p>Sth went wrong!:(</P>";
                            } else {
                                $query = "SELECT * FROM order_products JOIN products ON order_products.product_id = products.product_id WHERE order_id = $orderId;";
                                $itemResult = sqlsrv_query($conn, $query);
                                $total = 0;
                                $i = 1;
                                while($itemRow = sqlsrv_fetch_array($itemResult,SQLSRV_FETCH_ASSOC)) {
                                    $productId = $itemRow["product_id"];
                                    $name = $itemRow["pname"];
                                    $color = $itemRow["color"];
                                    $version = $itemRow["version"];
                                    $quantity = $itemRow["quantity"];
                                    $price = $itemRow["pprice"];
                                    $subtotal = 0;
                                    $subtotal += $price;
                                    if(strpos($version,'fea1') !== false) {
                                        $subtotal += 50;
                                    }
                                    if(strpos($version,'fea2') !== false) {
                                        $subtotal += 100;
                                    }
                                    if(strpos($version,'fea3') !== false) {
                                        $subtotal += 200;
                                    }
                                    $subtotal *= $quantity;
                                    echo "
                                    <tr class='table-row'>
                                        <td><strong>$i</strong></td>
                                        <td><strong>$productId</strong></td>
                                        <td><strong>$name</strong></td>
                                        <td>$color</td>
                                        <td>$version</td>
                                        <td class='price'>$price</td>
                                        <td><strong>$quantity</strong></td>
                                        <td class='price'>$$subtotal</td>
                                    </tr>
                                    ";
                                    $i++;
                                    $total += $subtotal;
                                }
                            }
                            echo "
                            <tr class='table-row'>
                                <td><strong>Total</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class='price'>$$total</td>
                            </tr>
                        </table>
                    </div>
                    <form method='POST' action='changeStatus.php?orderId=$orderId'>
                        <div class='other-group radio-group'>
                            <p class='order-status-label'><strong>Order Status: </strong></p>
                            <label class='option-group order-status'><span class='PENDING'>PENDING</span>
                                <input type='radio' name='status' value='PENDING'";
                                 if($status == 'PENDING') echo "checked='checked'";
                                 echo "/>
                                <span class='radio-checkmark PENDING-check'></span>
                            </label>
                            <label class='option-group order-status'><span class='FULFILLED'>FULFILLED</span>
                                <input type='radio' name='status' value='FULFILLED'";
                                if($status == 'FULFILLED') echo "checked='checked'";
                                echo "/>
                                <span class='radio-checkmark FULFILLED-check'></span>
                            </label>
                            <label class='option-group order-status'><span class='PAID'>PAID</span>
                                <input type='radio' name='status' value='PAID'";
                                if($status == 'PAID') echo "checked='checked'";
                                echo "/>
                                <span class='radio-checkmark PAID-check'></span>
                            </label>
                            <label class='option-group order-status'><span class='ARCHIVED'>ARCHIVED</span>
                                <input type='radio' name='status' value='ARCHIVED'
                                />
                                <span class='radio-checkmark ARCHIVED-check'></span>
                            </label>
                        </div>
                        <input type='submit' class='shop-btn' value='Save'/>
                    </form>
                </div>
            ";
        }
        echo "
        </div>";
    }

}
function doneRequest($conn) {
    if(!$conn) {
        echo "<p>Sth went wrong!:(</P>";
    } else {
        echo "
        <div class='tab' id='order-request'>
            <h2>Done Requests</h2>
        ";
        $query = "SELECT order_id, user_id, fname, lname, phone, email, street, town, state, post_code, pref_contact, order_status, order_time FROM orders WHERE order_status = 'ARCHIVED' ORDER BY order_time DESC;";
        $result = sqlsrv_query($conn,$query);
        
        if ($result === false) {
            die(print_r(sqlsrv_errors(), true)); // This will output detailed error information
        }
        while($row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {
            $orderId = $row["order_id"];
            $userId = $row["user_id"];
            $fname = $row["fname"];
            $lname = $row["lname"];
            $phone = $row["phone"];
            $email = $row["email"];
            $street = $row["street"];
            $town = $row["town"];
            $state = $row["state"];
            $postCode = $row["post_code"];
            $prefContact = $row["pref_contact"];
            $status = $row["order_status"];
            $date = $row["order_time"];
            $avt = sqlsrv_fetch_array(sqlsrv_query($conn, "SELECT avatar, avatar_type FROM users WHERE user_id = ". $userId .";"),SQLSRV_FETCH_ASSOC);
            echo "
                <div class='request-item'>";
                    if($status == 'PENDING') {
                        echo "<a class='delete-btn' href='deleteRequest.php?orderId=$orderId'>
                            DELETE
                        </a>";
                    }
                    echo "
                    <div class='avtNname'>
                        <div class='small-avt'>";
                        if($avt["avatar"] != null && $avt["avatar_type"] != null) {
                            $type = $avt["avatar_type"];
                            $image = base64_encode($avt["avatar"]);
                            echo "<img src='data:image/$type;charset=utf8;base64,$image' alt=''/>";
                        } else {
                            echo "<img src='images/user/user.png' alt=''/>";
                        }
                        echo "
                        </div>
                        <h3>$fname $lname</h3>
                    </div>
                    <h4>Order ID: $orderId</h4>
                    <div class='order-general-info'><strong>Date: </strong><span>$date</span></div>
                    <div class='order-general-info'><strong>Email: </strong><span>$email</span></div>
                    <div class='order-general-info'><strong>Phone: </strong><span>$phone</span></div>
                    <div class='order-general-info'><strong>Address: </strong><span>$street, $town, $state $postCode</span></div>
                    <div class='order-general-info'><strong>Preffered contact: </strong><span>$prefContact</span></div>
                    <p><strong>Status: </strong><span class='$status'>$status</span></p>
                    <div class='order-request-items'>
                        <p><strong>Items Ordered:</strong></p>
                        <table class='info-table'>
                            <tr>
                                <th></th>
                                <th>Product ID</th>
                                <th>Name</th>
                                <th>Color</th>
                                <th>Version</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>";
                            if(!$conn) {
                                echo "<p>Sth went wrong!:(</P>";
                            } else {
                                $query = "SELECT * FROM order_products JOIN products ON order_products.product_id = products.product_id WHERE order_id = $orderId;";
                                $itemResult = sqlsrv_query($conn, $query);
                                $total = 0;
                                $i = 1;
                                while($itemRow = sqlsrv_fetch_array($itemResult,SQLSRV_FETCH_ASSOC)) {
                                    $productId = $itemRow["product_id"];
                                    $name = $itemRow["pname"];
                                    $color = $itemRow["color"];
                                    $version = $itemRow["version"];
                                    $quantity = $itemRow["quantity"];
                                    $price = $itemRow["pprice"];
                                    $subtotal = 0;
                                    $subtotal += $price;
                                    if(strpos($version,'fea1') !== false) {
                                        $subtotal += 50;
                                    }
                                    if(strpos($version,'fea2') !== false) {
                                        $subtotal += 100;
                                    }
                                    if(strpos($version,'fea3') !== false) {
                                        $subtotal += 200;
                                    }
                                    $subtotal *= $quantity;
                                    echo "
                                    <tr class='table-row'>
                                        <td><strong>$i</strong></td>
                                        <td><strong>$productId</strong></td>
                                        <td><strong>$name</strong></td>
                                        <td>$color</td>
                                        <td>$version</td>
                                        <td class='price'>$price</td>
                                        <td><strong>$quantity</strong></td>
                                        <td class='price'>$$subtotal</td>
                                    </tr>
                                    ";
                                    $i++;
                                    $total += $subtotal;
                                }
                            }
                            echo "
                            <tr class='table-row'>
                                <td><strong>Total</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class='price'>$$total</td>
                            </tr>
                        </table>
                    </div>
                </div>
            ";
        }
        echo "
        </div>";
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
            header_code(5);
        ?>
        <main>
            <div id="container">
                <section>
                    <?php
                    for ($i = 0; $i < 4; $i++) {
                        $tab = $i +1;
                        if(!isset($_GET["page"])) {
                            if($i == 0) {
                                echo "
                                <input class='tab-radio' type='radio' name='current-tab' id='radio-tab$tab' checked/>
                                ";
                            } else {
                                echo "
                                <input class='tab-radio' type='radio' name='current-tab' id='radio-tab$tab'/>
                                ";
                            }
                        } else {
                            if($i == $_GET["page"]) {
                                echo "
                                <input class='tab-radio' type='radio' name='current-tab' id='radio-tab$tab' checked/>
                                ";
                            } else {
                                echo "
                                <input class='tab-radio' type='radio' name='current-tab' id='radio-tab$tab'/>
                                ";
                            }
                        }
                    }
                    ?>
                    <div id="user">
                        <div id="user-left">
                            <div id="user-image">
                                <?php
                                    $query = "SELECT avatar, avatar_type FROM users WHERE user_id = ". $_SESSION["user"]["user_id"] .";";
                                    $result = sqlsrv_query($conn, $query);
                                    $row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC);
                                    if($row["avatar"] != null && $row["avatar_type"] != null) {
                                        $type = $row["avatar_type"];
                                        $image = base64_encode($row["avatar"]);
                                        echo "<img src='data:image/$type;charset=utf8;base64,$image' alt=''/>";
                                    } else {
                                        echo "<img src='images/user/user.png' alt=''/>";
                                    }
                                ?>
                            </div>
                            <?php
                                if($_SESSION["user"]["type"] == 0) {
                                    echo "
                                    <h2>".$_SESSION["user"]["fname"]." ".$_SESSION["user"]["lname"]."</h2>
                                    <div id='user-tabs'>
                                        <label for='radio-tab1'>
                                            Your information
                                        </label>
                                        <label for='radio-tab2'>
                                            Your Cart
                                        </label>
                                        <label for='radio-tab3'>
                                            Order history
                                        </label>
                                    </div>
                                    ";
                                } elseif($_SESSION["user"]["type"] == 1) {
                                    echo "
                                    <h2>Admin</h2>
                                    <div id='user-tabs'>
                                        <label for='radio-tab1'>
                                            Add a Product
                                        </label>
                                        <label for='radio-tab2'>
                                            Manage Products
                                        </label>
                                        <label for='radio-tab3'>
                                            Order Requests
                                        </label>
                                        <label for='radio-tab4'>
                                            Done Requests
                                        </label>
                                    </div>
                                    ";
                                }
                            ?>
                            <a href="processLogout.php">
                                <div id="logout-btn">
                                    Log out
                                </div>
                            </a>
                        </div>
                        <div id="user-right">
                            <div id="tabs-slider">
                            <?php
                                // If type == 0, show tabs for customers
                                if($_SESSION["user"]["type"] == 0) {
                                    userInfoTab($conn);
                                    cartTab($conn, $_SESSION["user"]["user_id"]);
                                    historyTab($conn, $_SESSION["user"]["user_id"]);
                                
                                // If type == 1, show tabs for admin
                                } elseif($_SESSION["user"]["type"] == 1) {
                                    addProduct();
                                    editProduct($conn);
                                    orderRequest($conn);
                                    doneRequest($conn);
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