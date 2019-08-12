<?php

    ob_start();
    require_once 'config.php';

    /***************************************************************
     * SESSION IS SET
     ***************************************************************/

     if (isset($_SESSION['user_id'])) { // Start Session is Set
        header('Location: dashboard.php');
        die();
     } // End Session is Set

    /***************************************************************
    * COOKIE IS SET
    ***************************************************************/

    if (isset($_COOKIE['remember'])) { // Start Cookie is Set
        // Re-Authenticating The User
        $user_id = Cookie::re_authenticate();
        // Creating username and password sessions
        $query = "SELECT * FROM users WHERE id = :user_id";
        $stmt = Connection::conn()->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        extract($stmt->fetch(PDO::FETCH_ASSOC));
        $_SESSION['username'] = $username;
        $_SESSION['password'] = "Give it another try -:)";
        $_SESSION['id'] = $id;
    } // End Cookie is Set

    /***************************************************************
    * POST REQUEST
    ***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Start POST Request

        // unsetting errors
        unset($_SESSION['error']);

        if (isset($_POST['username']) && isset($_POST['password'])) { // Start username and password set

            /***************************************************************
            * FIRST LOGIN
            ***************************************************************/
            $query = "SELECT COUNT(*) AS count FROM users";
            $conn = Connection::conn();
            $stmt = $conn->prepare($query);
            $stmt->execute();
            extract($stmt->fetch(PDO::FETCH_ASSOC));
            if (!$count) {
                // Adding the user to the database
                $username = filter_var(testInput($_POST['username']), FILTER_SANITIZE_STRING);
                $password = filter_var(testInput($_POST['password']), FILTER_SANITIZE_STRING);
                $query = "INSERT INTO users (username, password) VALUES (:username, :password)";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':password', Hash::password_hash_function($password), PDO::PARAM_STR);
                $stmt->execute();
                // Setting the cookie if rememeber-me is selected
                if (isset($_POST['remember-me']) && $_POST['remember-me'] == 1)
                    Cookie::set_cookie(1);
                // Setting the Session and Redirect to dashboard
                $_SESSION['user_id'] = 1;
                header('Location: dashboard.php');
                die();
            }

            /***************************************************************
            * AUTHENTICATED USER LOGIN
            ***************************************************************/

            if (isset($_SESSION['id']) && $_SESSION['username'] == $_POST['username'] && $_POST['password'] == 'Give it another try -:)') {
                // Setting the session
                $_SESSION['user_id'] = $_SESSION['id'];
                // Unsetting username, password and id
                unset($_SESSION['username']);
                unset($_SESSION['password']);
                unset($_SESSION['id']);
                // setting the cookie if remember-me is selected
                if (isset($_POST['remember-me']) && $_POST['remember-me'] == '1') {
                    Cookie::set_cookie($id);
                }
                // unsetting the cookie if remember-me is not selected
                else {
                    Cookie::unset_cookie($id);
                }
                header('Location: dashboard.php');
                die();
            }

            /***************************************************************
            * NEW LOGIN
            ***************************************************************/

            else {
                // unsetting the previous cookie if exists
                if (isset($_SESSION['id'])) {
                    Cookie::unset_cookie($_SESSION['id']);
                    unset($_SESSION['id']);
                    unset($_SESSION['username']);
                    unset($_SESSION['password']);
                }
                // Check username exists
                $userUsername = filter_var(testInput($_POST['username']), FILTER_SANITIZE_STRING);
                $userPassword = filter_var(testInput($_POST['password']), FILTER_SANITIZE_STRING);
                $query = "SELECT * FROM users WHERE username = :username";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':username', $userUsername, PDO::PARAM_STR);
                $stmt->execute();

                /***************************************************************
                * USERNAME EXISTS IN THE DATABASE
                ***************************************************************/

                if ($stmt->rowCount()) { // Start User Exists
                    // Password is valid
                    extract($stmt->fetch(PDO::FETCH_ASSOC));
                    if (Hash::password_verify_function($userPassword, $password)) {
                        // setting the user session
                        $_SESSION['user_id'] = $id;
                        // setting the cookie if remember-me is selected
                        if (isset($_POST['remember-me']) && $_POST['remember-me'] == '1') {
                            Cookie::set_cookie($id);
                        }
                        // unsetting username and password if set
                        if (isset($_SESSION['username'])) {
                            unset($_SESSION['username']);
                            unset($_SESSION['password']);
                        }
                        header('Location: dashboard.php');
                        die();
                    }
                    // Password is not valid
                    else {
                        $_SESSION['username'] = $_POST['username'];
                        $_SESSION['password'] = $_POST['password'];
                        $_SESSION['error'] = language("login_error", $_SESSION['lang']);
                        header('Location: index.php');
                        die();
                    }

                } // End User Exists

                /***************************************************************
                * USERNAME DOESN'T EXIST
                ***************************************************************/

                else { // Start User Doesn't Exist
                    $_SESSION['username'] = $_POST['username'];
                    $_SESSION['password'] = $_POST['password'];
                    $_SESSION['error'] = language("login_error", $_SESSION['lang']);
                    header('Location: index.php');
                    die();
                } // End User Doesn't Exist
            } // End New Login
        } // End username and password set
        else { // Start username and password not set
            $_SESSION['error'] = language("required_field", $_SESSION['lang']);
        } // End username and password not set
    } // End POST Request

?>

<!DOCTYPE html>
<?php if ($_SESSION['lang']) { ?>
<html lang="ar" dir="rtl">
<?php } else { ?>
<html lang="en" dir="ltr">
<?php } ?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo language("admin_login", $_SESSION['lang']); ?>">
    <meta name="author" content="Mohamed Alansary">
    <link rel="shortcut icon" href="<?php echo $ico; ?>settings.png">

    <title><?php echo language("admin_login", $_SESSION['lang']); ?></title>

    <!-- Bootstrap CSS -->    
    <link href="<?php echo $css; ?>bootstrap.min.css" rel="stylesheet">
    <!-- bootstrap theme -->
    <link href="<?php echo $css; ?>bootstrap-theme.min.css" rel="stylesheet">
    <!--external css-->
    <!-- font icon -->
    <link href="<?php echo $css; ?>elegant-icons-style.css" rel="stylesheet" />
    <link href="<?php echo $css; ?>font-awesome.min.css" rel="stylesheet" />
    <!-- Custom styles -->
    <link href="<?php echo $css; ?>style.css" rel="stylesheet">
    <link href="<?php echo $css; ?>style-responsive.css" rel="stylesheet" />
    <link href="<?php echo $css; ?>custom.css" rel="stylesheet" />

</head>

  <body class="login-img3-body">
    <div class="container">
        <?php if (isset($_SESSION['error'])) { ?>
            <div class="alert alert-danger" id="index-error">
                <?php
                    echo $_SESSION['error'];
                ?>
            </div>
        <?php } ?>
      <form class="login-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">        
        <div class="login-wrap">
            <p class="login-img"><i class="icon_lock_alt"></i></p>
            <div class="input-group">
              <span class="input-group-addon"><i class="icon_profile"></i></span>
              <input type="text" class="form-control" placeholder="<?php echo language("username", $_SESSION['lang']); ?>" autofocus name="username" autocomplete="off" required="required" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : '' ?>">
            </div>
            <div class="input-group">
                <span class="input-group-addon"><i class="icon_key_alt"></i></span>
                <input id="password" type="password" class="form-control" placeholder="<?php echo language("password", $_SESSION['lang']); ?>" name="password" autocomplete="new-password" required="required" value="<?php echo isset($_SESSION['password']) ? $_SESSION['password'] : '' ?>">
            </div>
            <label class="checkbox">
                <?php if (isset($_SESSION['id'])) { ?>
                    <input type="checkbox" value="1" name="remember-me" checked> <?php echo language("remember_me", $_SESSION['lang']); ?>
                <?php } else { ?>
                    <input type="checkbox" value="1" name="remember-me"> <?php echo language("remember_me", $_SESSION['lang']); ?>
                <?php } ?>
                <?php if ($_SESSION['lang']) { ?>
                    <span class="pull-left"> <a href="contact.php"> <?php echo language("forgot_password", $_SESSION['lang']); ?></a></span>
                <?php } else { ?>
                    <span class="pull-right"> <a href="contact.php"> <?php echo language("forgot_password", $_SESSION['lang']); ?></a></span>
                <?php } ?>
            </label>
            <button class="btn btn-primary btn-lg btn-block" type="submit"><?php echo language("login", $_SESSION['lang']); ?></button>
        </div>
      </form>
    <div class="text-right">
            <div class="credits">
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?lang=ar">العربية</a>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?lang=en">English</a>
            </div>
        </div>
    </div>
  </body>
</html>
<?php ob_end_flush(); ?>