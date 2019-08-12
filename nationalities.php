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
* VIEW NATIONALITIES
***************************************************************/

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo language("nationalities-heading", $_SESSION['lang']); ?>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-responsive table-hover text-center">
            <thead>
                <tr>
                    <th><?php echo language('id', $_SESSION['lang']); ?></th>
                    <th><?php echo language('nationalities-nat', $_SESSION['lang']); ?></th>
                    <th><?php echo language('nationalities-manage', $_SESSION['lang']); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM nationalities";
                $stmt = Connection::conn()->prepare($query);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo $nationality; ?></td>
                        <td>
                            <a href="nationalities.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $id; ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                <?php echo language('nationalities-edit', $_SESSION['lang']); ?>
                            </a>
                            <form action="nationalities.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <span class="glyphicon glyphicon-trash"></span> <?php echo language('delete', $_SESSION['lang']);?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="panel-footer">
        <a href="nationalities.php?lang=<?php echo $selectedLang; ?>&manage=add" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            <?php echo language('nationalities-add', $_SESSION['lang']); ?>
        </a>
    </div>
</div>

<?php } elseif (isset($_GET['manage']) && $_GET['manage'] == 'add') {

/***************************************************************
* ADD NATIONALITIES
***************************************************************/

?>
    <!-- ADDING NATIONALITY -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?php echo language('nationalities-add', $_SESSION['lang']); ?>
        </div>
        <div class="panel-body">
            <form action="nationalities.php?manage=store" method="POST" data-parsley-validate="">
                <div class="form-group">
                    <label for="nat"><?php echo language('nationalities-nat', $_SESSION['lang']); ?></label>
                    <input type="text" placeholder="<?php echo language('nationalities-nat', $_SESSION['lang']); ?>"
                        value="<?php echo isset($_SESSION['nat']) ? $_SESSION['nat'] : ''; ?>" required=""
                        class="form-control" name="nat" data-parsley-length="[1, 50]"
                        data-parsley-required="true"
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
* STORE NATIONALITY
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['nat'])) {
            $nat = filter_var(testInput($_POST['nat']), FILTER_SANITIZE_STRING);
            // if (strlen($nat) > 50 || strlen($nat) < 1) {
            //     $_SESSION['error'] = language('nat-check-error', $_SESSION['lang']);
            //     header('Location: nationalities.php?manage=add&lang='.$selectedLang);
            //     die();
            // }
            try {
                $query = "INSERT INTO nationalities (nationality) VALUES (:nationality)";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':nationality', $nat, PDO::PARAM_STR);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'nationalities.php?manage=add&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('nationalities-add-success', $_SESSION['lang']);
            header('Location: nationalities.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('nationalities-required', $_SESSION['lang']);
            header('Location: nationalities.php?manage=add&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: nationalities.php?manage=add&lang='.$selectedLang);
        die();
    }
    
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'edit') {

/***************************************************************
* EDIT NATIONALITY
***************************************************************/

?>
    <?php
    if (isset($_GET['id'])) {
        $id = filter_var(testInput($_GET['id']), FILTER_VALIDATE_INT);
        $query = "SELECT * FROM nationalities WHERE id = :id";
        $stmt = Connection::conn()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount()) {
            extract($stmt->fetch(PDO::FETCH_ASSOC));
        ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo language('nationalities-edit', $_SESSION['lang']); ?>
                </div>
                <div class="panel-body">
                    <form action="nationalities.php?manage=update" method="POST" data-parsley-validate="">
                        <div class="form-group">
                            <label for="nat"><?php echo language('nationalities-nat', $_SESSION['lang']); ?></label>
                            <input type="text" placeholder="<?php echo language('nationalities-nat', $_SESSION['lang']); ?>"
                                value="<?php echo $nationality; ?>" name="nat" required="" class="form-control"
                                data-parsley-length="[1, 50]" data-parsley-required="true"
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
            header('nationalities.php?lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('id-required', $_SESSION['lang']);
        header('Location: nationalities.php?lang='.$selectedLang);
        die();
    }

} elseif (isset($_GET['manage']) && $_GET['manage'] == 'update') {

/***************************************************************
* UPDATE NATIONALITY
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['nat'])) {
            $nat = filter_var(testInput($_POST['nat']), FILTER_SANITIZE_STRING);
            // if (strlen($nat) > 50 || strlen($nat) < 1) {
            //     $_SESSION['error'] = language('nat-check-error', $_SESSION['lang']);
            //     header('Location: nationalities.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            //     die();
            // }
            try {
                $query = "UPDATE nationalities SET nationality = :nationality WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':nationality', $nat, PDO::PARAM_STR);
                $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'nationalities.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('nationalities-update-success', $_SESSION['lang']);
            header('Location: nationalities.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('nationalities-required', $_SESSION['lang']);
            header('Location: nationalities.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: nationalities.php?manage=view&lang='.$selectedLang);
        die();
    }
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'delete') {

/***************************************************************
* DELETE NATIONALITY
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['id'])) {
            $id = filter_var(testInput($_POST['id']), FILTER_VALIDATE_INT);
            try {
                $query = "DELETE FROM nationalities WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['success'] = language("nationalities-delete-success", $_SESSION['lang']);
                header('Location: nationalities.php?manage=view&lang='.$selectedLang);
                die();
            } catch (PDOException $e) {
                dbError($stmt, 'nationalities.php?manage=view&lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('id-required', $_SESSION['lang']);
            header('Location: nationalities.php?manage=view&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: nationalities.php?manage=view&lang='.$selectedLang);
        die();
    }

} else {
    header('Location: nationalities.php?manage=view&lang='.$selectedLang);
    die();
} ?>

<?php require_once "layouts/footer.php"; ?>