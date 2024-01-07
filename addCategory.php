<?php
    include("connect.inc");
    if(!$conn) {
        echo "<p>Oops! Something went wrong! :(</p>";
    } else {
       
        $catname = htmlspecialchars(trim($_POST["catname"]));
       

        if(!isset(($_POST["catname"]))) {
            echo "<p>Oops! Something went wrong! :(</p>";
        } else {
            // query to insert products information into products table
        
                $query .= "INSERT INTO category (cat_name) VALUES 
                ('$catname');";
                $result = sqlsrv_query($conn,$query);
                
            header("Location: manager.php");
        }
    }
?>