<?php
    include("head.inc");
    include("header.inc");
    include("footer.inc");
    include("connect.inc");
    
?>

<!DOCTYPE html>
<html lang="en">
    <?php
        head_code();
    ?>
    <body>
        <?php
            header_code(1);
        if(!isset($_GET["productId"])) {
            header("Location: products.php");
        } else {
            $productId = $_GET["productId"];
            if(!$conn) {
                header("Location: products.php");
            } else {
                $query = "SELECT * FROM products WHERE product_id = $productId;";
                $result = sqlsrv_query($conn,$query);
                $product = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC);
                $id = $product["product_id"];
                $name = $product["pname"];
                $desc = $product["pdesc"];
                $price = $product["pprice"];
                $image = $product["pimage"];
                $imageType = $product["pimagetype"];

                if(isset($_SESSION["user"]) && $_SESSION["user"] != null) {
                    $userId = $_SESSION["user"]["user_id"];
                }
            }
        }
        ?>
        <main>
            <div id="container">
                <section>
                    <?php 
                    if(!$product) {
                        header("Location: products.php");
                    } else {
                        echo "
                        
                    <div id='item $id' class='product'>
                    <img class='product-background' <img src='$image' alt=''/>/>
                    <div class='details'>
                        <aside><img src='$image' alt=''/></aside>
                        <div class='description'>
                            <h3>$name</h3>
                            <p class='price'>$$price</p>
                            <p>$desc</p>
                            <table class='info-table'>
                                <tr>
                                    <th></th>
                                    <th>Voltage</th>
                                    <th>Weight</th>
                                    <th>Price</th>
                                </tr>
                                <tr class='table-row'>
                                    <td><strong>Elite</strong></td>
                                    <td>1.0 V</td>
                                    <td>200 Grams</td>
                                    <td class='price'>+ $50.00</td>
                                </tr>
                                <tr class='table-row'>
                                    <td><strong>Premium</strong></td>
                                    <td>1.5 V</td>
                                    <td>250 Grams</td>
                                    <td class='price'>+ $100.00</td>
                                </tr>
                                <tr class='table-row'>
                                    <td><strong>Luxury</strong></td>
                                    <td>2 V</td>
                                    <td>300 Grams</td>
                                    <td class='price'> + $200.00</td>
                                </tr>
                            </table>
                            <div class='avail-color'>
                                <p><strong>Available Colors and Designs:</strong></p>
                                <ol>
                                    <li>Royal Vermilion Red</li>
                                    <li>Acient Verdigris Green</li>
                                    <li>Colossus Titian Brown</li>
                                </ol>
                            </div>
                            <form method='POST' action='addToCart.php?
                            ";
                            if(isset($_SESSION["user"]) && $_SESSION["user"] != null) {
                                $userId = $_SESSION["user"]["user_id"];
                            }
                            echo "
                            &productId=$id'>
                                <div  class='other-group box-group'>
                                    <label><strong>Version: </strong></label>
                                    <label for='fea1' class='option-group'>Elite
                                        <input type='checkbox' id='fea1' name='fea1' value='fea1' checked='checked'/>
                                        <span class='box-checkmark'></span>
                                    </label>
                                    <label for='fea2' class='option-group'>Premium
                                        <input type='checkbox' id='fea2' name='fea2' value='fea2'/>
                                        <span class='box-checkmark'></span>
                                    </label>
                                    <label for='fea3' class='option-group'>Luxury
                                        <input type='checkbox' id='fea3' name='fea3' value='fea3'/>
                                        <span class='box-checkmark'></span>
                                    </label>
                                </div>
                                <div class='other-group select'>
                                    <select id='color' name='color' required='required'>
                                        <option value='' hidden>Select color</option>
                                        <option value='red'>Red</option>
                                        <option value='green'>Green</option>
                                        <option value='brown'>Brown</option>
                                    </select>
                                </div>
                                <div class='group'>
                                    <input type='number' id='quantity' name='quantity' required='required' value='1' />
                                    <span class='highlight'></span>
                                    <span class='bar'></span>
                                    <label for='quantity'><strong>Quantity: </strong></label>
                                </div>
                                ";
                                if(isset($_SESSION["user"]) && $_SESSION["user"] != null) {
                                    $userId = $_SESSION["user"]["user_id"];
                                } else {
                                    echo"<a href='index.php' class='shop-btn'>Login to continue</a>";
                                }
                            echo "</form>
                        </div>
                    </div>
                </div>
                        ";
                    }
                    ?>
                </section>
            </div>
        </main>
        <?php
            footer_code();
        ?>
    </body>
</html>