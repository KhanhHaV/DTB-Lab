<?php
    include("head.inc");
    include("header.inc");
    if(array_key_exists("user",$_SESSION) && $_SESSION["user"] != null) {
        header("Location: home.php");
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
                    <div class="login">
                        <div class="login-form-container">
                            <img src="images/logo/png/logo-no-background.png" alt=""/>
                            <!-- Information is sent to processLogin.php for checking and querying -->
                            <form method="POST" action="processLogin.php" novalidate>
                                <h1>Login</h1>
                                <div id="input-fields">
                                    <div class="group">
                                        <input type="email" class="text-input" id="email" name="email" required="required" />
                                        <span class="highlight"></span>
                                        <span class="bar"></span>
                                        <label for="email"><strong>Email: </strong></label>
                                    </div>
                                        <?php
                                        if(isset($_GET["err_email"])) echo "<p class='error'>Wrong email syntax (e.g. example@example.com)</p>";
                                        ?>
                                    <div class="group">
                                        <input type="password" class="text-input" id="password" name="password" required="required" />
                                        <span class="highlight"></span>
                                        <span class="bar"></span>
                                        <label for="password"><strong>Password: </strong></label>
                                    </div>
                                        <?php
                                            if(isset($_GET["err_pwd"])) echo "<p class='error'>Passwords must longer than 3 characters</p>";
                                        ?>
                                        <?php
                                            if(isset($_GET["err_wrong"])) echo "<p class='error'>Incorrect email or password</p>";
                                        ?>
                                </div>
                                <input class="form-btn" type="submit" value="Login"/>
                                <p>Don't have an account? <a href="register.php">Register</a> now!</p>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>