<?php
    function galleryItem($rowLast, $id, $name, $desc, $price, $image, $imageType) {
        
        echo "
        <li>
            <a href='productDesc.php?productId=$id' class='item-container'>
                <div class='item' id='item-8'>
                    <img src='<?php echo $desc; ?>' alt='BEAUTIFUL'/>
                    <div class='item-desc'>
                        <h3>$name</h3>
                        <p>$desc</p>
                    </div>
                    <div class='brief-info'>
                        <h3>$name</h3>
                        <p class='product-price'>$$price</p>
                    </div>
                </div>
                <aside class='additional-info ";
                if($rowLast) {
                    echo "row-last";
                }
                echo "'>
                    <table class='info-table'>
                        <tr>
                            <th></th>
                            <th>Voltage</th>
                            <th>Weight</th>
                            <th>Price</th>
                        </tr>
                        <tr class='table-row'>
                            <td><strong>Base</strong></td>
                            <td>1.0 V</td>
                            <td>200 Grams</td>
                            <td class='price'>$500.00</td>
                        </tr>
                        <tr class='table-row'>
                            <td><strong>Premium</strong></td>
                            <td>1.5 V</td>
                            <td>250 Grams</td>
                            <td class='price'>$600.00</td>
                        </tr>
                        <tr class='table-row'>
                            <td><strong>Luxury</strong></td>
                            <td>2 V</td>
                            <td>300 Grams</td>
                            <td class='price'>$700.00</td>
                        </tr>
                    </table>
                    <div class='avail-color'>
                        <strong>Available Colors and Designs:</strong>
                        <ol>
                            <li>Royal Vermilion Red</li>
                            <li>Acient Verdigris Green</li>
                            <li>Colossus Titian Brown</li>
                        </ol>
                    </div>
                </aside>
            </a>
        </li>
        ";
    }
?>