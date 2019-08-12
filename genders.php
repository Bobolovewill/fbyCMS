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
* VIEW GENDERS
***************************************************************/

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo language("genders-heading", $_SESSION['lang']); ?>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-responsive table-hover text-center">
            <thead>
                <tr>
                    <th><?php echo language('id', $_SESSION['lang']); ?></th>
                    <th><?php echo language('genders-gender', $_SESSION['lang']); ?></th>
                    <th><?php echo language('genders-manage', $_SESSION['lang']); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM genders";
                $stmt = Connection::conn()->prepare($query);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo $gender; ?></td>
                        <td>
                            <a href="genders.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $id; ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                <?php echo language('genders-edit', $_SESSION['lang']); ?>
                            </a>
                            <form action="genders.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
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
        <a href="genders.php?lang=<?php echo $selectedLang; ?>&manage=add" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            <?php echo language('genders-add', $_SESSION['lang']); ?>
        </a>
    </div>
</div>

<?php } elseif (isset($_GET['manage']) && $_GET['manage'] == 'add') {

/***************************************************************
* ADD GENDER
***************************************************************/

?>
    <!-- ADDING GENDER -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?php echo language('genders-add', $_SESSION['lang']); ?>
        </div>
        <div class="panel-body">
            <form action="genders.php?manage=store" method="POST" data-parsley-validate="">
                <div class="form-group">
                    <label for="gender"><?php echo language('genders-gender', $_SESSION['lang']); ?></label>
                    <input type="text" placeholder="<?php echo language('genders-gender', $_SESSION['lang']); ?>"
                        value="<?php echo isset($_SESSION['gender']) ? $_SESSION['gender'] : ''; ?>" required=""
                        class="form-control" name="gender" data-parsley-required="true"
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
* STORE GENDER
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['gender'])) {
            $gender = filter_var(testInput($_POST['gender']), FILTER_SANITIZE_STRING);
            $genders = array('Male', 'Female', 'ذكر', 'انثى');
            if (in_array($gender, $genders)) {
                try {
                    $query = "INSERT INTO genders (gender) VALUES (:gender)";
                    $stmt = Connection::conn()->prepare($query);
                    $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
                    $stmt->execute();
                } catch(PDOException $e) {
                    dbError($stmt, 'genders.php?manage=add&lang='.$selectedLang);
                    die();
                }
                $_SESSION['success'] = language('genders-add-success', $_SESSION['lang']);
                header('Location: genders.php?manage=view&lang='.$selectedLang);
                die();
            }
            else {
                $_SESSION['error'] = language('genders-check-error', $_SESSION['lang']);
                header('Location: genders.php?manage=add&lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('genders-required', $_SESSION['lang']);
            header('Location: genders.php?manage=add&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: genders.php?manage=add&lang='.$selectedLang);
        die();
    }
    
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'edit') {

/***************************************************************
* EDIT GENDER
***************************************************************/

?>
    <?php
    if (isset($_GET['id'])) {
        $id = filter_var(testInput($_GET['id']), FILTER_VALIDATE_INT);
        $query = "SELECT * FROM genders WHERE id = :id";
        $stmt = Connection::conn()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount()) {
            extract($stmt->fetch(PDO::FETCH_ASSOC));
        ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo language('genders-edit', $_SESSION['lang']); ?>
                </div>
                <div class="panel-body">
                    <form action="genders.php?manage=update" method="POST" data-parsley-validate="">
                        <div class="form-group">
                            <label for="gender"><?php echo language('genders-gender', $_SESSION['lang']); ?></label>
                            <input type="text" placeholder="<?php echo language('genders-gender', $_SESSION['lang']); ?>"
                                value="<?php echo $gender; ?>" name="gender" required="" class="form-control"
                                data-parsley-required="true"
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
            header('genders.php?lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('id-required', $_SESSION['lang']);
        header('Location: genders.php?lang='.$selectedLang);
        die();
    }

} elseif (isset($_GET['manage']) && $_GET['manage'] == 'update') {

/***************************************************************
* UPDATE GENDER
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['gender'])) {
            $gender = filter_var(testInput($_POST['gender']), FILTER_SANITIZE_STRING);
            if (in_array($gender, array('Male', 'Female', 'ذكر', 'انثى'))) {
                try {
                    $query = "UPDATE genders SET gender = :gender WHERE id = :id";
                    $stmt = Connection::conn()->prepare($query);
                    $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
                    $stmt->execute();
                } catch(PDOException $e) {
                    dbError($stmt, 'genders.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
                    die();
                }
                $_SESSION['success'] = language('genders-update-success', $_SESSION['lang']);
                header('Location: genders.php?manage=view&lang='.$selectedLang);
                die();
            }
            else {
                $_SESSION['error'] = language('genders-check-error', $_SESSION['lang']);
                header('Location: genders.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('genders-required', $_SESSION['lang']);
            header('Location: genders.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: genders.php?manage=view&lang='.$selectedLang);
        die();
    }
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'delete') {

/***************************************************************
* DELETE GENDER
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['id'])) {
            $id = filter_var(testInput($_POST['id']), FILTER_VALIDATE_INT);
            try {
                $query = "DELETE FROM genders WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['success'] = language("genders-delete-success", $_SESSION['lang']);
                header('Location: genders.php?manage=view&lang='.$selectedLang);
                die();
            } catch (PDOException $e) {
                dbError($stmt, 'genders.php?manage=view&lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('id-required', $_SESSION['lang']);
            header('Location: genders.php?manage=view&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: genders.php?manage=view&lang='.$selectedLang);
        die();
    }

} else {
    header('Location: genders.php?manage=view&lang='.$selectedLang);
    die();
} ?>

<?php require_once "layouts/footer.php"; ?>