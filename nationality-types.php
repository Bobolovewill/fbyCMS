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
* VIEW NATIONALITY TYPES
***************************************************************/

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo language("nationalityTypes-heading", $_SESSION['lang']); ?>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-responsive table-hover text-center">
            <thead>
                <tr>
                    <th><?php echo language('id', $_SESSION['lang']); ?></th>
                    <th><?php echo language('natType-type', $_SESSION['lang']); ?></th>
                    <th><?php echo language('natType-manage', $_SESSION['lang']); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM nationalityTypes";
                $stmt = Connection::conn()->prepare($query);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo $type; ?></td>
                        <td>
                            <a href="nationality-types.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $id; ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                <?php echo language('natType-edit', $_SESSION['lang']); ?>
                            </a>
                            <form action="nationality-types.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
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
        <a href="nationality-types.php?lang=<?php echo $selectedLang; ?>&manage=add" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            <?php echo language('natType-add', $_SESSION['lang']); ?>
        </a>
    </div>
</div>

<?php } elseif (isset($_GET['manage']) && $_GET['manage'] == 'add') {

/***************************************************************
* ADD NATIONALITY TYPE
***************************************************************/

?>
    <!-- ADDING NATIONALITY TYPE -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?php echo language('natType-add', $_SESSION['lang']); ?>
        </div>
        <div class="panel-body">
            <form action="nationality-types.php?manage=store" method="POST" data-parsley-validate="">
                <div class="form-group">
                    <label for="nattype"><?php echo language('natType-type', $_SESSION['lang']); ?></label>
                    <input type="text" placeholder="<?php echo language('natType-type', $_SESSION['lang']); ?>"
                        value="<?php echo isset($_SESSION['nattype']) ? $_SESSION['nattype'] : ''; ?>" required=""
                        class="form-control" name="nattype" data-parsley-maxlength="11" data-parsley-required="true"
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
* STORE NATIONALITY TYPE
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['nattype'])) {
            $natType = filter_var(testInput($_POST['nattype']), FILTER_SANITIZE_STRING);
            // $natTypes = array('kuwaiti', 'Not kuwaiti', 'كويتى', 'غير كويتى');
            // if (in_array($natType, $natTypes)) {
            //     try {
            //         $query = "INSERT INTO nationalityTypes (type) VALUES (:type)";
            //         $stmt = Connection::conn()->prepare($query);
            //         $stmt->bindParam(':type', $natType, PDO::PARAM_STR);
            //         $stmt->execute();
            //     } catch(PDOException $e) {
            //         dbError($stmt, 'nationality-types.php?manage=add&lang='.$selectedLang);
            //         die();
            //     }
            //     $_SESSION['success'] = language('natType-add-success', $_SESSION['lang']);
            //     header('Location: nationality-types.php?manage=view&lang='.$selectedLang);
            //     die();
            // }
            // else {
            //     $_SESSION['error'] = language('nationalityTypes-check-error', $_SESSION['lang']);
            //     header('Location: nationality-types.php?manage=add&lang='.$selectedLang);
            //     die();
            // }
            try {
                $query = "INSERT INTO nationalityTypes (type) VALUES (:type)";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':type', $natType, PDO::PARAM_STR);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'nationality-types.php?manage=add&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('natType-add-success', $_SESSION['lang']);
            header('Location: nationality-types.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('nationalityTypes-required', $_SESSION['lang']);
            header('Location: nationality-types.php?manage=add&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: nationality-types.php?manage=add&lang='.$selectedLang);
        die();
    }
    
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'edit') {

/***************************************************************
* EDIT NATIONALITY TYPE
***************************************************************/

?>
    <?php
    if (isset($_GET['id'])) {
        $id = filter_var(testInput($_GET['id']), FILTER_VALIDATE_INT);
        $query = "SELECT * FROM nationalityTypes WHERE id = :id";
        $stmt = Connection::conn()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount()) {
            extract($stmt->fetch(PDO::FETCH_ASSOC));
        ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo language('natType-edit', $_SESSION['lang']); ?>
                </div>
                <div class="panel-body">
                    <form action="nationality-types.php?manage=update" method="POST" data-parsley-validate="">
                        <div class="form-group">
                            <label for="nattpye"><?php echo language('natType-type', $_SESSION['lang']); ?></label>
                            <input type="text" placeholder="<?php echo language('natType-type', $_SESSION['lang']); ?>"
                                value="<?php echo $type; ?>" name="nattype" required="required" class="form-control"
                                 data-parsley-maxlength="11" data-parsley-required="true"
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
            header('nationality-types.php?lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('id-required', $_SESSION['lang']);
        header('Location: nationality-types.php?lang='.$selectedLang);
        die();
    }

} elseif (isset($_GET['manage']) && $_GET['manage'] == 'update') {

/***************************************************************
* UPDATE NATIONALITY TYPE
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['nattype'])) {
            $natType = filter_var(testInput($_POST['nattype']), FILTER_SANITIZE_STRING);
            // if (in_array($natType, array('kuwaiti', 'Not kuwaiti', 'كويتى', 'غير كويتى'))) {
            //     try {
            //         $query = "UPDATE nationalityTypes SET type = :type WHERE id = :id";
            //         $stmt = Connection::conn()->prepare($query);
            //         $stmt->bindParam(':type', $natType, PDO::PARAM_STR);
            //         $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
            //         $stmt->execute();
            //     } catch(PDOException $e) {
            //         dbError($stmt, 'nationality-types.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            //         die();
            //     }
            //     $_SESSION['success'] = language('nationalityTypes-update-success', $_SESSION['lang']);
            //     header('Location: nationality-types.php?manage=view&lang='.$selectedLang);
            //     die();
            // }
            // else {
            //     $_SESSION['error'] = language('nationalityTypes-check-error', $_SESSION['lang']);
            //     header('Location: nationality-types.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            //     die();
            // }
            try {
                $query = "UPDATE nationalityTypes SET type = :type WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':type', $natType, PDO::PARAM_STR);
                $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'nationality-types.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('nationalityTypes-update-success', $_SESSION['lang']);
            header('Location: nationality-types.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('nationalityTypes-required', $_SESSION['lang']);
            header('Location: nationality-types.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: nationality-types.php?manage=view&lang='.$selectedLang);
        die();
    }
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'delete') {

/***************************************************************
* DELETE NATIONALITY TYPE
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['id'])) {
            $id = filter_var(testInput($_POST['id']), FILTER_VALIDATE_INT);
            try {
                $query = "DELETE FROM nationalityTypes WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['success'] = language("nationalityTypes-delete-success", $_SESSION['lang']);
                header('Location: nationality-types.php?manage=view&lang='.$selectedLang);
                die();
            } catch (PDOException $e) {
                dbError($stmt, 'nationality-types.php?manage=view&lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('id-required', $_SESSION['lang']);
            header('Location: nationality-types.php?manage=view&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: nationality-types.php?manage=view&lang='.$selectedLang);
        die();
    }

} else {
    header('Location: nationality-types.php?manage=view&lang='.$selectedLang);
    die();
} ?>

<?php require_once "layouts/footer.php"; ?>