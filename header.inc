<?php
    session_start();
    function header_code($current) {
        $username = "Customer";
        if(array_key_exists("user",$_SESSION) && $_SESSION["user"] != null) {
            $username = $_SESSION["user"]["fname"];
        }
        echo "
            <header>
                <section class='logo'>
                    <a href='home.php'>
                        <img src='images/logo/png/logo-no-background.png' alt=''/>
                    </a>
                </section>
                <section id='nav'>
                    <label id='expand-btn' for='openclose'>
                        <img src='images/menu.svg' alt=''/>
                    </label>
                    <input type='checkbox' name='openclose' id='openclose'/>
                    <nav>";
    
        if($current == 0) {
            echo "
            <a class='nav-item active' href='home.php'>
                <h3>HOME</h3>
            </a>
            ";
        } else {
            echo "
            <a class='nav-item' href='home.php'>
                <h3>HOME</h3>
            </a>
            ";
        }
        if($current == 1) {
            echo "
            <a class='nav-item active' href='products.php'>
                <h3>PRODUCTS</h3>
            </a>
            ";
        } else {
            echo "
            <a class='nav-item' href='products.php'>
                <h3>PRODUCTS</h3>
            </a>
            ";
        }
        if($current == 5) {
            echo "
            <a class='nav-item last-nav-item active'
             href='";
             if(array_key_exists("user",$_SESSION) && $_SESSION["user"] != null) {
                 echo "manager.php";
             } else {
                echo "index.php";
             }
             echo "'
             >
                <h3>Hello, $username</h3>
            </a>
            ";
        } else {
            echo "
            <a class='nav-item last-nav-item' 
            href='";
            if(array_key_exists("user",$_SESSION) && $_SESSION["user"] != null) {
                echo "manager.php";
            } else {
               echo "index.php";
            }
            echo "'
            >
                <h3>Hello, $username</h3>
            </a>
            ";
        }
        echo "
                    </nav>
                </section>
            </header>
        ";
    }
?>