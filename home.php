<?php
    include("head.inc");
    include("header.inc");
    include("footer.inc");
    include("galleryItem.inc");
    include("connect.inc");

?>

<!DOCTYPE html>
<html lang="en">
    <?php
        head_code();
    ?>
    <body>
        <?php
            header_code(0);
        ?>
        <main>
            <div id="container">
                <section>
                    <div id="banner">
                        <div id="banner-images">
                            <img src="images/home/banner.webp" alt=""/>
                            <img src="images/home/banner2.webp" alt=""/>
                            <img src="images/home/banner3.jpg" alt=""/>
                            <img src="images/home/colorfulsmoke.jpg" alt=""/>
                            <img src="images/home/banner.webp" alt=""/>
                        </div>
                        <div id="banner-caption">
                            <h1>The only time you cannot smoke is when you are sleeping!</h1>
                        </div>
                    </div>
                </section>
                <section>
                    <div id="introduction" class="home-intro">
                        <div id="intro-background">
                            <h2>Welcome to BRUHHH</h2>
                            <img class="divider" src="images/divider.png" alt=""/>
                            <div class="intro-desc">
                                <p><i>
                                    Welcome to the premier source for all things vape! Here you'll find everything from beginner basics to advanced tricks and techniques. Whether you're new to vaping or an expert, we have something for everyone.
                                </i></p>
                                <p><i>
                                    We have a wide selection of starter kits, tanks, atomizers, e-juice, mods, and accessories. Plus, with free shipping in the US, our prices can't be beaten!
                                </i></p>
                                <p><i>
                                    Ready to get started? Check out some of our newest products below.
                                </i></p>
                            </div>
                        </div>
                    </div>
                </section>
                <section>
                    <div id="product-list">
                        <div id="list-intro">
                            <h2>Our Products</h2>
                            <img class="divider" src="images/divider.png" alt=""/>
                            <p><i>Take a look at some of our most interesting products.</i></p>
                        </div>
                        <div id="sec3">
                            <?php
                                if(!$conn) {
                                    echo "<p>Oops! Something went wrong! :(</p>";
                                } else {
                                    $stmt = sqlsrv_query($conn, "SELECT * FROM products");

                                    if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));}
                                    $rowCount = 0;
    
                                    // Fetch each row and increment the counter
                                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                           $rowCount++;
                                    }
                                    $query = "SELECT TOP 9 * FROM products ORDER BY pdate DESC;";
                                    $result = sqlsrv_query($conn,$query );
                                    for($i = 0; $i < $rowCount; $i++) {
                                        $id = $i + 1;
                                        if($i == 0) {
                                            echo "<input type='radio' id='page-btn-$id' name='page-btn' checked='checked'/>";
                                        } else {
                                            echo "<input type='radio' id='page-btn-$id' name='page-btn'/>";
                                        }
                                    }
                                }


                                if( $rowCount> 0) {
                                    $rowIndex = 1;
                                    $pageIndex = 1;
                                    $rowLast = false;
                                    while($row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {
                                        if($rowIndex % 3 == 0) {
                                            $rowLast = true;
                                        } else {
                                            $rowLast = false;
                                        }
                                        if($rowIndex % 9 == 1) {
                                            echo "<ul class='item-list page$pageIndex'>";
                                            $pageIndex += 1;
                                        }
                                        galleryItem($rowLast, $row["product_id"],$row["pname"],$row["pdesc"],$row["pprice"],$row["pimage"],$row["pimagetype"]);
                                        if($rowIndex % 9 == 0) {
                                            echo "</ul>";
                                        }
                                        $rowIndex += 1;
                                    }
                                    if($rowIndex % 9 != 0) {
                                        $pageIndex -= 1;
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </section>
                <section>
                    <div id="outtroduction">
                        <h2>Our ambitions</h2>
                        <img class="divider" src="images/divider.png" alt=""/>
                        <p><i>We aim to create a world where everyone vapes but still healthy.</i></p>
                    </div>
                </section>
            </div>
        </main>
        <?php
            footer_code();
        ?>
    </body>
</html>