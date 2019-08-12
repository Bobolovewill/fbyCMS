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
* VIEW POSITION ROLES
***************************************************************/

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo language("positionRoles-heading", $_SESSION['lang']); ?>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-responsive table-hover text-center">
            <thead>
                <tr>
                    <th><?php echo language('id', $_SESSION['lang']); ?></th>
                    <th><?php echo language('positionRoles-role', $_SESSION['lang']); ?></th>
                    <th><?php echo language('positionRoles-manage', $_SESSION['lang']); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM positionRoles";
                $stmt = Connection::conn()->prepare($query);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo $role; ?></td>
                        <td>
                            <a href="position-roles.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $id; ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                <?php echo language('positionRoles-edit', $_SESSION['lang']); ?>
                            </a>
                            <form action="position-roles.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
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
        <a href="position-roles.php?lang=<?php echo $selectedLang; ?>&manage=add" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            <?php echo language('positionRoles-add', $_SESSION['lang']); ?>
        </a>
    </div>
</div>

<?php } elseif (isset($_GET['manage']) && $_GET['manage'] == 'add') {

/***************************************************************
* ADD POSITION ROLE
***************************************************************/

?>
    <!-- ADDING POSITION ROLE -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?php echo language('positionRoles-add', $_SESSION['lang']); ?>
        </div>
        <div class="panel-body">
            <form action="position-roles.php?manage=store" method="POST" data-parsley-validate="">
                <div class="form-group">
                    <label for="role"><?php echo language('positionRoles-role', $_SESSION['lang']); ?></label>
                    <input type="text" placeholder="<?php echo language('positionRoles-role', $_SESSION['lang']); ?>"
                        value="<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>" required=""
                        class="form-control" name="role" data-parsley-required="true" data-parsley-length="[1, 100]"
                    />
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
* STORE POSITION ROLE
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['role'])) {
            $role = filter_var(testInput($_POST['role']), FILTER_SANITIZE_STRING);
            // if (strlen($role) > 100 || strlen($role) < 1) {
            //     $_SESSION['error'] = language('positionRoles-check-error', $_SESSION['lang']);
            //     header('Location: position-roles.php?manage=add&lang='.$selectedLang);
            //     die();
            // }
            try {
                $query = "INSERT INTO positionRoles (role) VALUES (:role)";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':role', $role, PDO::PARAM_STR);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'position-roles.php?manage=add&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('positionRoles-add-success', $_SESSION['lang']);
            header('Location: position-roles.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('positionRoles-required', $_SESSION['lang']);
            header('Location: position-roles.php?manage=add&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: position-roles.php?manage=add&lang='.$selectedLang);
        die();
    }
    
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'edit') {

/***************************************************************
* EDIT POSITION ROLE
***************************************************************/

?>
    <?php
    if (isset($_GET['id'])) {
        $id = filter_var(testInput($_GET['id']), FILTER_VALIDATE_INT);
        $query = "SELECT * FROM positionRoles WHERE id = :id";
        $stmt = Connection::conn()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount()) {
            extract($stmt->fetch(PDO::FETCH_ASSOC));
        ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo language('positionRoles-edit', $_SESSION['lang']); ?>
                </div>
                <div class="panel-body">
                    <form action="position-roles.php?manage=update" method="POST" data-parsley-validate="">
                        <div class="form-group">
                            <label for="role"><?php echo language('positionRoles-role', $_SESSION['lang']); ?></label>
                            <input type="text" placeholder="<?php echo language('positionRoles-role', $_SESSION['lang']); ?>"
                                value="<?php echo $role; ?>" name="role" required="" class="form-control"
                                data-parsley-required="true" data-parsley-length="[1, 100]"
                            />
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
            header('position-roles.php?lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('id-required', $_SESSION['lang']);
        header('Location: position-roles.php?lang='.$selectedLang);
        die();
    }

} elseif (isset($_GET['manage']) && $_GET['manage'] == 'update') {

/***************************************************************
* UPDATE POSITION ROLE
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['role'])) {
            $role = filter_var(testInput($_POST['role']), FILTER_SANITIZE_STRING);
            // if (strlen($role) > 100 || strlen($role) < 1) {
            //     $_SESSION['error'] = language('positionRoles-check-error', $_SESSION['lang']);
            //     header('Location: position-roles.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            //     die();
            // }
            try {
                $query = "UPDATE positionRoles SET role = :role WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':role', $role, PDO::PARAM_STR);
                $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'position-roles.php?manage=view&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('positionRoles-update-success', $_SESSION['lang']);
            header('Location: position-roles.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('positionRoles-required', $_SESSION['lang']);
            header('Location: position-roles.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: position-roles.php?manage=view&lang='.$selectedLang);
        die();
    }
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'delete') {

/***************************************************************
* DELETE POSITION ROLE
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['id'])) {
            $id = filter_var(testInput($_POST['id']), FILTER_VALIDATE_INT);
            try {
                $query = "DELETE FROM positionRoles WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['success'] = language("positionRoles-delete-success", $_SESSION['lang']);
                header('Location: position-roles.php?manage=view&lang='.$selectedLang);
                die();
            } catch (PDOException $e) {
                dbError($stmt, 'position-roles.php?manage=view&lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('id-required', $_SESSION['lang']);
            header('Location: position-roles.php?manage=view&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: position-roles.php?manage=view&lang='.$selectedLang);
        die();
    }

} else {
    header('Location: position-roles.php?manage=view&lang='.$selectedLang);
    die();
} ?>

<?php require_once "layouts/footer.php"; ?>