<?php

    function disCat($cats) {
        $html = "<div class='cats'>";
        while($row = sqlsrv_fetch_array($cats,SQLSRV_FETCH_ASSOC))
        {
            $html .= "
            <div class='cat-link cat-item'>
                <a href='products.php?cat=" . (string)$row["cat_id"] . "'>" . $row["cat_name"] . "</a>
            </div>
           
        ";
        }
        $html .= "</div>";
        return $html;
    }
    include("head.inc");
    include("header.inc");
    include("footer.inc");
    include("galleryItem.inc");
    include("connect.inc");
    $query = "SELECT * FROM category ORDER BY cat_name;" ;
    $result = sqlsrv_query($conn,$query) ;

?>

<!DOCTYPE html>
<html lang="en">
    
    <?php
        head_code();
    ?>
    <body>
        <?php
            header_code(1);
        ?>
        <main>
            <div id="container">
                <section>
                    <div id="introduction">
                        <div id="intro-background" class="intro-products">
                            <div id=intro-left>
                                <h2>Our products</h2>
                                <img class=" divider" src="images/divider.png" alt=""/>
                                <div class="intro-desc">
                                    <p><i>BRUHHH is a shop that sell smoking products for healthier life style.</i></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form id="search-box" method='POST' action='products.php'>
                        <input type="text" name="searchkey" id="searchkey" placeholder="What are you looking for? ..."/>
                        <div id="filter-box">
                        <select id="order" class="order" name="order">
                            <option value="old-up">Latest arrival</option>
                            <option value="old-down">Earliest arrival</option>
                            <option value="price-up">Price: Low to high</option>
                            <option value="price-down">Price: High to low</option>
                            <option value="name-up">A-Z</option>
                            <option value="name-down">Z-A</option>
                        </select>
                        </div>
                        <button id="search-btn" type="submit">
                            <img src="images/Search.svg" alt="">
                        </button>
                    </form>
                    <?php
                        echo disCat($result);
                    ?>
                </section>
                <section>
                    <div id="product-list">
                        <?php
                            if(!$conn) {
                                echo "<p>Oops! Something went wrong! :(</p>";
                            } else {
                                $orderBy = "pdate DESC;";
                                $query = "SELECT * FROM dbo.products WHERE 1 = 1";
                                if(isset($_POST["searchkey"])) {
                                    $searchKey = $_POST["searchkey"];
                                    $query .= " AND pname LIKE '%$searchKey%' ";
                                }
                                if(isset($_GET["cat"])) {
                                    $cat = $_GET["cat"];
                                    $query .= "AND cat_id = '$cat' ";
                                }
                                if(isset($_POST["order"])) {
                                    switch($_POST["order"]) {
                                        case 'price-up':
                                            $orderBy = "pprice ASC";
                                            break; 
                                        case 'price-down':
                                            $orderBy = "pprice DESC";
                                            break; 
                                        case 'old-up':
                                            $orderBy = "pdate DESC";
                                            break; 
                                        case 'old-down':
                                            $orderBy = "pdate ASC";
                                            break; 
                                        case 'name-up':
                                            $orderBy = "pname ASC";
                                            break; 
                                        case 'name-down':
                                            $orderBy = "pname DESC";
                                            break;
                                    }
                                }

                                $query .= "ORDER BY $orderBy;";
                                $result = sqlsrv_query($conn, $query);
                              
                                $stmt = sqlsrv_query($conn, "SELECT * FROM products");

                                if ($stmt === false) {
                                die(print_r(sqlsrv_errors(), true));}
                                $rowCount = 0;

                                // Fetch each row and increment the counter
                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                       $rowCount++;
                                }

                                for($i = 0; $i < $rowCount; $i++) {
                                    $id = $i + 1;
                                    if($i == 0) {
                                        echo "<input type='radio' id='page-btn-$id' name='page-btn' checked='checked'/>";
                                    } else {
                                        echo "<input type='radio' id='page-btn-$id' name='page-btn'/>";
                                    }
                                }
                            }
                           
                        ?>
                        <div id="sec3">
                            <?php
                                if( $rowCount > 0) {
                                    $rowIndex = 1;
                                    $pageIndex = 1;
                                    $rowLast = false;

                                    while($row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {

                                        // The last item in every row in the gallery has a different animation
                                        if($rowIndex % 3 == 0) {
                                            $rowLast = true;
                                        } else {
                                            $rowLast = false;
                                        }
                                        
                                        // Every item-list contains 9 items is located in a <ul> tag
                                        if($rowIndex % 9 == 1) {
                                            echo "<ul class='item-list page$pageIndex'>";
                                            $pageIndex += 1;
                                        }
                                        galleryItem($rowLast, $row["product_id"],$row["pname"],$row["pdesc"],
                                                    $row["pprice"],$row["pimage"],$row["pimagetype"],$row["discount"]);

                                      
                                        if($rowIndex % 9 == 0) {
                                            echo "</ul>";
                                        }
                                        $rowIndex += 1;
                                    }

                                    // Avoid redundant page 
                                    if($rowIndex % 9 != 0) {
                                        $pageIndex -= 1;
                                    }
                                }
                            ?>
                        </div>
                        <div id="page-btn">
                            <?php
                                if($rowCount > 0) {
                                    for($i = 1; $i <= $pageIndex; $i++) {
                                        echo "
                                            <label class='option-group' for='page-btn-$i' id='label-$i'>
                                                <span class='radio-checkmark'></span>
                                            </label>
                                        ";
                                    }
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

