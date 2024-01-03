<?php
    include("head.inc");
    include("header.inc");
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
                        <div class="login-bg">
                            <div>
                                <h1>Vape Vape Vape Vape Vape Vape Vape Vape Vape Vape Vape Vape Vape Vape Vape Vape&nbsp;</h1>
                            </div>
                            <div>
                                <h1>Vape is healthy Vape is healthy Vape is healthy Vape is healthy&nbsp;</h1>
                            </div>
                            <div>
                                <h1>Lets make smoking easier Lets make smoking easier Lets make smoking easier Lets make smoking easier&nbsp;</h1>
                            </div>
                            <div>
                                <h1>Why not try some vape Why not try some vape Why not try some vape Why not try some vape&nbsp;</h1>
                            </div>
                            <div>
                                <h1>Vape Vape Vape Vape Vape Vape Vape Vape Vape Vape Vape Vape Vape Vape Vape Vape&nbsp;</h1>
                            </div>
                        </div>
                        <div class="login-form-container">
                            <img src="images/logo/png/logo-no-background.png" alt=""/>
                            <!-- Information is sent to processRegister.php for checking and querying -->
                            <form method="POST" action="processRegister.php" novalidate>
                                <h1>Login</h1>
                                <div class="input-fields register">
                                    <div>
                                        <div class="group">
                                            <input type="text" class="text-input" id="1stname" name="1stname" pattern="[A-Za-z]{0-25}" required="required" />
                                            <span class="highlight"></span>
                                            <span class="bar"></span>
                                            <label for="1stname"><strong>First Name: </strong></label>
                                        </div>
                                        <?php
                                            if(isset($_GET["err_fname"])) echo "<p class='error'>First name must contain only text and maximum length of 25 characters</p>";
                                        ?>
                                        <div class="group">
                                            <input type="text" id="lastname" name="lastname" pattern="[A-Za-z]{0-25}" required="required" />
                                            <span class="highlight"></span>
                                            <span class="bar"></span>
                                            <label for="lastname"><strong>Last Name: </strong></label>
                                        </div>
                                        <?php
                                            if(isset($_GET["err_lname"])) echo "<p class='error'>Last name must contain only text and maximum length of 25 characters</p>";
                                        ?>
                                        <div class="group">
                                            <input type="text" id="phonenumber" name="phone" pattern="[+][0-9]{8-10}" required="required" />
                                            <span class="highlight"></span>
                                            <span class="bar"></span>
                                            <label for="phonenumber"><strong>Phone Number: </strong></label>
                                        </div>
                                        <?php
                                            if(isset($_GET["err_phone"])) echo "<p class='error'>Phone number must be digits and has maximum length of 10</p>";
                                        ?>
                                    </div>
                                    <div>
                                        <div class="group">
                                            <input type="email" class="text-input" id="email" name="email" required="required" />
                                            <span class="highlight"></span>
                                            <span class="bar"></span>
                                            <label for="email"><strong>Email: </strong></label>
                                        </div>
                                        <?php
                                        if(isset($_GET["err_email"])) echo "<p class='error'>Wrong email syntax (e.g. example@example.com)</p>";
                                        if(isset($_GET["err_email_exists"])) echo "<p class='error'>An account with this email exists, please login</p>";
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
                                        <div class="group">
                                            <input type="password" class="text-input" id="repassword" name="repassword" required="required" />
                                            <span class="highlight"></span>
                                            <span class="bar"></span>
                                            <label for="repassword"><strong>Re-type Password: </strong></label>
                                        </div>
                                        <?php
                                            if(isset($_GET["err_no_match_pwd"])) echo "<p class='error'>Passwords must match</p>";
                                        ?>
                                    </div>
                                </div>
                                <input class="form-btn" type="submit" value="Register"/>
                                <p>Have an account? <a href="index.php">Login</a> now!</p>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>