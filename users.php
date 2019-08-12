<?php require_once "layouts/header.php"; ?>
<?php if (isset($_SESSION['error'])) { ?>
    <div class="alert alert-danger">
        <?php
            echo $_SESSION['error'];
            unset($_SESSION['error']);
        ?>
    </div>
<?php } ?>
<?php if (isset($_SESSION['success'])) { ?>
    <div class="alert alert-success">
        <?php
            echo $_SESSION['success'];
            unset($_SESSION['success']);
        ?>
    </div>
<?php } ?>

<?php

if (isset($_GET['manage']) && $_GET['manage'] == 'view') {

/***************************************************************
* VIEW USERS
***************************************************************/

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo language("users-heading", $_SESSION['lang']); ?>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-responsive table-hover text-center">
            <thead>
                <tr>
                    <th><?php echo language('id', $_SESSION['lang']); ?></th>
                    <th><?php echo language('users-username', $_SESSION['lang']); ?></th>
                    <th><?php echo language('users-manage', $_SESSION['lang']); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM users";
                $stmt = Connection::conn()->prepare($query);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo $username; ?></td>
                        <td>
                            <a href="users.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $id; ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                <?php echo language('users-edit', $_SESSION['lang']); ?>
                            </a>
                            <form action="users.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <span class="glyphicon glyphicon-trash"></span> <?php echo language("delete", $_SESSION['lang']); ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="panel-footer">
        <a href="users.php?lang=<?php echo $selectedLang; ?>&manage=add" class="btn btn-primary btn-lg">
            <i class="fa fa-user-plus fa-1x" aria-hidden="true"></i>
            <?php echo language('users-add', $_SESSION['lang']); ?>
        </a>
    </div>
</div>

<?php } elseif (isset($_GET['manage']) && $_GET['manage'] == 'add') {

/***************************************************************
* ADD USER
***************************************************************/

?>
    <!-- ADDING USER -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?php echo language('users-add', $_SESSION['lang']); ?>
        </div>
        <div class="panel-body">
            <form action="users.php?manage=store" method="POST" data-parsley-validate="">
                <div class="form-group">
                    <label for="username"><?php echo language('users-username', $_SESSION['lang']); ?></label>
                    <input type="text" placeholder="<?php echo language('users-username', $_SESSION['lang']); ?>"
                        value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>" required=""
                        class="form-control" name="username" data-parsley-length="[4, 50]"
                        data-parsley-required="true"
                    />
                </div>
                <div class="form-group">
                    <label for="password"><?php echo language('users-password', $_SESSION['lang']); ?></label>
                    <input type="password" placeholder="password" required="required" class="form-control" name="password"
                    data-parsley-length="[6, 100]" data-parsley-required="true"/>
                </div>
        </div>
        <div class="panel-footer">
                <input type="submit" value="<?php echo language('add', $_SESSION['lang']); ?>"
                    class="btn btn-primary btn-lg"/>
            </form>
        </div>
    </div>
<?php } elseif (isset($_GET['manage']) && $_GET['manage'] == 'store') {

/***************************************************************
* STORE USER
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = filter_var(testInput($_POST['username']), FILTER_SANITIZE_STRING);
            $password = filter_var(testInput($_POST['password']), FILTER_SANITIZE_STRING);
            // if (strlen($username) > 50 || strlen($username) < 4) {
            //     $_SESSION['error'] = language('username-check-error', $_SESSION['lang']);
            //     header('Location: users.php?manage=add&lang='.$selectedLang);
            //     die();
            // }
            // if (strlen($password) > 100 || strlen($password) < 6) {
            //     $_SESSION['error'] = language('password-check-error', $_SESSION['lang']);
            //     header('Location: users.php?manage=add&lang='.$selectedLang);
            //     die();
            // }
            try {
                $query = "INSERT INTO users (username, password) VALUES (:username, :password)";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':password', Hash::password_hash_function($password), PDO::PARAM_STR);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'users.php?manage=add&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('users-add-success', $_SESSION['lang']);
            header('Location: users.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('users-required', $_SESSION['lang']);
            header('Location: users.php?manage=add&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: users.php?manage=add&lang='.$selectedLang);
        die();
    }
    
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'edit') {

/***************************************************************
* EDIT USERS
***************************************************************/

?>
    <?php
    if (isset($_GET['id'])) {
        $id = filter_var(testInput($_GET['id']), FILTER_VALIDATE_INT);
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = Connection::conn()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount()) {
            extract($stmt->fetch(PDO::FETCH_ASSOC));
        ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo language('users-edit', $_SESSION['lang']); ?>
                </div>
                <div class="panel-body">
                    <form action="users.php?manage=update" method="POST" data-parsley-validate="">
                        <div class="form-group">
                            <label for="username"><?php echo language('users-username', $_SESSION['lang']); ?></label>
                            <input type="text" placeholder="<?php echo language('users-username', $_SESSION['lang']); ?>"
                                value="<?php echo $username; ?>" name="username" required="" class="form-control"
                                data-parsley-length="[4, 50]" data-parsley-required="true"
                            />
                        </div>
                        <div class="form-group">
                            <label for="password"><?php echo language('users-password', $_SESSION['lang']); ?></label>
                            <input type="password" placeholder="password" name="password" required="required" class="form-control"
                            data-parsley-length="[6, 100]" data-parsley-required="true"/>
                        </div>
                </div>
                <div class="panel-footer">
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" value="<?php echo language('edit', $_SESSION['lang']); ?>"
                            class="btn btn-primary btn-lg">
                    </form>
                </div>
            </div>
        <?php } else {
            $_SESSION['error'] = language('id-not-found', $_SESSION['lang']);
            header('users.php?lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('id-required', $_SESSION['lang']);
        header('Location: users.php?lang='.$selectedLang);
        die();
    }

} elseif (isset($_GET['manage']) && $_GET['manage'] == 'update') {

/***************************************************************
* UPDATE USER
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = filter_var(testInput($_POST['username']), FILTER_SANITIZE_STRING);
            $password = filter_var(testInput($_POST['password']), FILTER_SANITIZE_STRING);
            // if (strlen($username) > 50 || strlen($username) < 4) {
            //     $_SESSION['error'] = language('username-check-error', $_SESSION['lang']);
            //     header('Location: users.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            //     die();
            // }
            // if (strlen($password) > 100 || strlen($password) < 6) {
            //     $_SESSION['error'] = language('password-check-error', $_SESSION['lang']);
            //     header('Location: users.php?manage=add&id='.$_POST['id'].'&lang='.$selectedLang);
            //     die();
            // }
            try {
                $query = "UPDATE users SET username = :username, password = :password WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':password', Hash::password_hash_function($password), PDO::PARAM_STR);
                $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'users.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('users-update-success', $_SESSION['lang']);
            header('Location: users.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('users-required', $_SESSION['lang']);
            header('Location: users.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: users.php?manage=view&lang='.$selectedLang);
        die();
    }
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'delete') {

/***************************************************************
* DELETE USER
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['id'])) {
            $id = filter_var(testInput($_POST['id']), FILTER_VALIDATE_INT);
            try {
                $query = "DELETE FROM users WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['success'] = language("users-delete-success", $_SESSION['lang']);
                header('Location: users.php?manage=view&lang='.$selectedLang);
                die();
            } catch (PDOException $e) {
                dbError($stmt, 'users.php?manage=view&lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('id-required', $_SESSION['lang']);
            header('Location: users.php?manage=view&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: users.php?manage=view&lang='.$selectedLang);
        die();
    }

} else {
    header('Location: users.php?manage=view&lang='.$selectedLang);
    die();
} ?>

<?php require_once "layouts/footer.php"; ?>