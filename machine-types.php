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
* VIEW MACHINE TYPES
***************************************************************/

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo language("machine-types-heading", $_SESSION['lang']); ?>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-responsive table-hover text-center">
            <thead>
                <tr>
                    <th><?php echo language('id', $_SESSION['lang']); ?></th>
                    <th><?php echo language('machine-types-type', $_SESSION['lang']); ?></th>
                    <th><?php echo language('machine-types-manage', $_SESSION['lang']); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM machineTypes";
                $stmt = Connection::conn()->prepare($query);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo $type; ?></td>
                        <td>
                            <a href="machine-types.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $id; ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                <?php echo language('machine-types-edit', $_SESSION['lang']); ?>
                            </a>
                            <form action="machine-types.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
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
        <a href="machine-types.php?lang=<?php echo $selectedLang; ?>&manage=add" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            <?php echo language('machine-types-add', $_SESSION['lang']); ?>
        </a>
    </div>
</div>

<?php } elseif (isset($_GET['manage']) && $_GET['manage'] == 'add') {

/***************************************************************
* ADD MACHINE TYPE
***************************************************************/

?>
    <!-- ADDING MACHINE TYPE -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?php echo language('machine-types-add', $_SESSION['lang']); ?>
        </div>
        <div class="panel-body">
            <form action="machine-types.php?manage=store" method="POST" data-parsley-validate="">
                <div class="form-group">
                    <label for="type"><?php echo language('machine-types-type', $_SESSION['lang']); ?></label>
                    <input type="text" placeholder="<?php echo language('machine-types-type', $_SESSION['lang']); ?>"
                        value="<?php echo isset($_SESSION['type']) ? $_SESSION['type'] : ''; ?>" required=""
                        class="form-control" name="type" data-parsley-length="[1, 100]"
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
* STORE MACHINE TYPE
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['type'])) {
            $type = filter_var(testInput($_POST['type']), FILTER_SANITIZE_STRING);
            // if (strlen($type) >100 || strlen($type) < 1) {
            //     $_SESSION['error'] = language('machine-type-check-error', $_SESSION['lang']);
            //     header('Location: machine-types.php?manage=add&lang='.$selectedLang);
            //     die();
            // }
            try {
                $query = "INSERT INTO machineTypes (type) VALUES (:type)";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':type', $type, PDO::PARAM_STR);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'machine-types.php?manage=add&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('machine-types-add-success', $_SESSION['lang']);
            header('Location: machine-types.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('machine-types-required', $_SESSION['lang']);
            header('Location: machine-types.php?manage=add&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: machine-types.php?manage=add&lang='.$selectedLang);
        die();
    }
    
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'edit') {

/***************************************************************
* EDIT MACHINE TYPE
***************************************************************/

?>
    <?php
    if (isset($_GET['id'])) {
        $id = filter_var(testInput($_GET['id']), FILTER_VALIDATE_INT);
        $query = "SELECT * FROM machineTypes WHERE id = :id";
        $stmt = Connection::conn()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount()) {
            extract($stmt->fetch(PDO::FETCH_ASSOC));
        ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo language('machine-types-edit', $_SESSION['lang']); ?>
                </div>
                <div class="panel-body">
                    <form action="machine-types.php?manage=update" method="POST" data-parsley-validate="">
                        <div class="form-group">
                            <label for="type"><?php echo language('machine-types-type', $_SESSION['lang']); ?></label>
                            <input type="text" placeholder="<?php echo language('machine-types-type', $_SESSION['lang']); ?>"
                                value="<?php echo $type; ?>" name="type" required="" class="form-control"
                                data-parsley-length="[1, 100]" data-parsley-required="true"
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
            header('machine-types.php?lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('id-required', $_SESSION['lang']);
        header('Location: machine-types.php?lang='.$selectedLang);
        die();
    }

} elseif (isset($_GET['manage']) && $_GET['manage'] == 'update') {

/***************************************************************
* UPDATE MACHINE TYPE
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['type'])) {
            $type = filter_var(testInput($_POST['type']), FILTER_SANITIZE_STRING);
            // if (strlen($type) >100 || strlen($type) < 1) {
            //     $_SESSION['error'] = language('machine-type-check-error', $_SESSION['lang']);
            //     header('Location: machine-types.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            //     die();
            // }
            try {
                $query = "UPDATE machineTypes SET type = :type WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':type', $type, PDO::PARAM_STR);
                $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'machine-types.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('machine-types-update-success', $_SESSION['lang']);
            header('Location: machine-types.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('machine-types-required', $_SESSION['lang']);
            header('Location: machine-types.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: machine-types.php?manage=view&lang='.$selectedLang);
        die();
    }
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'delete') {

/***************************************************************
* DELETE MACHINE TYPE
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['id'])) {
            $id = filter_var(testInput($_POST['id']), FILTER_VALIDATE_INT);
            try {
                $query = "DELETE FROM machineTypes WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['success'] = language("machine-types-delete-success", $_SESSION['lang']);
                header('Location: machine-types.php?manage=view&lang='.$selectedLang);
                die();
            } catch (PDOException $e) {
                dbError($stmt, 'machine-types.php?manage=view&lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('id-required', $_SESSION['lang']);
            header('Location: machine-types.php?manage=view&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: machine-types.php?manage=view&lang='.$selectedLang);
        die();
    }

} else {
    header('Location: machine-types.php?manage=view&lang='.$selectedLang);
    die();
} ?>

<?php require_once "layouts/footer.php"; ?>