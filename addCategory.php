<?php
    include("connect.inc");
    if(!$conn) {
        echo "<p>Oops! Something went wrong! :(</p>";
    } else {
       
        $catname = htmlspecialchars(trim($_POST["catname"]));

        if(!isset(($_POST["catname"]))) {
            echo "<p>Oops! Something went wrong! :(</p>";
        } else {
            $deleteCat = $_POST["delete-cat"];
            if(isset($_POST["delete"])) {
                $query = "DELETE FROM category WHERE cat_id = $deleteCat;";
                $result = sqlsrv_query($conn,$query);
                header("Location: manager.php?page=5");
            } else {
                    $query .= "INSERT INTO category (cat_name) VALUES 
                    (UPPER('$catname'));";
                    $result = sqlsrv_query($conn,$query);
                    
                    header("Location: manager.php?page=5");
            }
        }
    }
?>