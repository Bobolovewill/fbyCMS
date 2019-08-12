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
* VIEW MACHINES
***************************************************************/

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo language("machines-heading", $_SESSION['lang']); ?>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-responsive table-hover text-center">
            <thead>
                <tr>
                    <th><?php echo language('id', $_SESSION['lang']); ?></th>
                    <th><?php echo language('machines-arName', $_SESSION['lang']); ?></th>
                    <th><?php echo language('machines-enName', $_SESSION['lang']); ?></th>
                    <th><?php echo language('machines-description', $_SESSION['lang']); ?></th>
                    <th><?php echo language('machines-type', $_SESSION['lang']); ?></th>
                    <th><?php echo language('machines-manage', $_SESSION['lang']); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM machines";
                $stmt = Connection::conn()->prepare($query);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo $arName; ?></td>
                        <td><?php echo $enName; ?></td>
                        <td><?php echo empty($description) ? language('desc-not-found', $_SESSION['lang']) : $description; ?></td>
                        <td>
                            <?php
                            $subQuery = "SELECT * FROM machineTypes WHERE id = :type_id";
                            $subStmt = Connection::conn()->prepare($subQuery);
                            $subStmt->bindParam(':type_id', $type_id, PDO::PARAM_STR);
                            $subStmt->execute();
                            $row = $subStmt->fetch(PDO::FETCH_ASSOC);
                            echo $row['type'];
                            ?>
                        </td>
                        <td>
                            <a href="machines.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $id; ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                <?php echo language('machines-edit', $_SESSION['lang']); ?>
                            </a>
                            <form action="machines.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
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
        <?php
        $query = "SELECT COUNT(*) AS count FROM machineTypes";
        $stmt = Connection::conn()->prepare($query);
        $stmt->execute();
        extract($stmt->fetch(PDO::FETCH_ASSOC));
        if ($count) {
        ?>
        <a href="machines.php?lang=<?php echo $selectedLang; ?>&manage=add" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            <?php echo language('machines-add', $_SESSION['lang']); ?>
        </a>
        <?php } else { ?>
            <div class="alert alert-warning">
                <?php echo language('add-machine-warning', $_SESSION['lang']); ?>
            </div>
        <?php } ?>
    </div>
</div>

<?php } elseif (isset($_GET['manage']) && $_GET['manage'] == 'add') {

/***************************************************************
* ADD MACHINE
***************************************************************/

?>
    <!-- ADDING MACHINE -->
    <?php
    $query = "SELECT COUNT(*) AS count FROM machineTypes";
    $stmt = Connection::conn()->prepare($query);
    $stmt->execute();
    extract($stmt->fetch(PDO::FETCH_ASSOC));
    if (!$count) {
        $_SESSION["error"] = language("no-machineTypes-error", $_SESSION['lang']);
        header('Location: machines.php?manage=view&lang='.$selectedLang);
        die();
    }
    ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?php echo language('machines-add', $_SESSION['lang']); ?>
        </div>
        <div class="panel-body">
            <form action="machines.php?manage=store" method="POST" data-parsley-validate="">
                <div class="form-group">
                    <label for="arName"><?php echo language('machines-arName', $_SESSION['lang']); ?></label>
                    <input type="text" placeholder="<?php echo language('machines-arName', $_SESSION['lang']); ?>"
                        value="<?php echo isset($_SESSION['arName']) ? $_SESSION['arName'] : ''; ?>" required=""
                        class="form-control" name="arName" data-parsley-length="[1, 100]" data-parsley-required="true"
                    />
                </div>
                <div class="form-group">
                    <label for="enName"><?php echo language('machines-enName', $_SESSION['lang']); ?></label>
                    <input type="text" placeholder="<?php echo language('machines-enName', $_SESSION['lang']); ?>"
                        value="<?php echo isset($_SESSION['enName']) ? $_SESSION['enName'] : ''; ?>" required=""
                        class="form-control" name="enName" data-parsley-length="[1, 100]" data-parsley-required="true"
                    />
                </div>
                <div class="form-group">
                    <label for="description"><?php echo language('machines-description', $_SESSION['lang']); ?></label>
                    <input type="text" placeholder="<?php echo language('machines-description', $_SESSION['lang']); ?>"
                        value="<?php echo isset($_SESSION['description']) ? $_SESSION['description'] : ''; ?>"
                        class="form-control" name="description" data-parsley-length="[0, 255]"
                    />
                </div>
                <div class="form-group">
                    <label for="type_id"><?php echo language('machines-type', $_SESSION['lang']); ?></label>
                    <select name="type_id" class="form-control">
                        <option value="0">Select Type</option>
                        <?php
                        $subQuery = "SELECT * FROM machineTypes";
                        $subStmt = Connection::conn()->prepare($subQuery);
                        $subStmt->execute();
                        while ($row = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                            extract($row);
                            ?>
                            <option value="<?php echo $id; ?>"><?php echo $type; ?></option>
                        <?php } ?>
                    </select>
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
* STORE MACHINE
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['arName']) && isset($_POST['enName']) && isset($_POST['type_id'])) {
            // if (!intval($_POST['type_id'])) {
            //     $_SESSION['error'] = language('choose-type-error', $_SESSION['lang']);
            //     header('Location: machines.php?manage=add&lang='.$selectedLang);
            //     die();
            // }
            $arName = filter_var(testInput($_POST['arName']), FILTER_SANITIZE_STRING);
            // if (strlen($arName) > 100 || strlen($arName) < 1) {
            //     $_SESSION['error'] = language("machines-check-error", $_SESSION['lang']);
            //     header('Location: machines.php?manage=add&lang='.$selectedLang);
            //     die();
            // }
            $enName = filter_var(testInput($_POST['enName']), FILTER_SANITIZE_STRING);
            // if (strlen($enName) > 100 || strlen($enName) < 1) {
            //     $_SESSION['error'] = language("machines-check-error", $_SESSION['lang']);
            //     header('Location: machines.php?manage=add&lang='.$selectedLang);
            //     die();
            // }
            $description = (isset($_POST['description'])) ? filter_var(testInput($_POST['description']), FILTER_SANITIZE_STRING) : '';
            // if (strlen($description) > 255) {
            //     $_SESSION['error'] = language("machines-description-check-error", $_SESSION['lang']);
            //     header('Location: machines.php?manage=add&lang='.$selectedLang);
            //     die();
            // }
            $type_id = intval($_POST['type_id']);
            try {
                $query = "INSERT INTO machines (arName, enName, description, type_id) VALUES (:arName, :enName, :description, :type_id)";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':arName', $arName, PDO::PARAM_STR);
                $stmt->bindParam(':enName', $enName, PDO::PARAM_STR);
                $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                $stmt->bindParam(':type_id', $type_id, PDO::PARAM_INT);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'machines.php?manage=add&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('machines-add-success', $_SESSION['lang']);
            header('Location: machines.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('machines-required', $_SESSION['lang']);
            header('Location: machines.php?manage=add&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: machines.php?manage=add&lang='.$selectedLang);
        die();
    }
    
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'edit') {

/***************************************************************
* EDIT MACHINE
***************************************************************/

?>
    <?php
    if (isset($_GET['id'])) {
        $id = intval(filter_var(testInput($_GET['id']), FILTER_VALIDATE_INT));
        $query = "SELECT * FROM machines WHERE id = :id";
        $stmt = Connection::conn()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount()) {
            extract($stmt->fetch(PDO::FETCH_ASSOC));
        ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo language('machines-edit', $_SESSION['lang']); ?>
                </div>
                <div class="panel-body">
                    <form action="machines.php?manage=update" method="POST" data-parsley-validate="">
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <div class="form-group">
                            <label for="arName"><?php echo language('machines-arName', $_SESSION['lang']); ?></label>
                            <input type="text" placeholder="<?php echo language('machines-arName', $_SESSION['lang']); ?>"
                                value="<?php echo $arName; ?>" name="arName" required="" class="form-control"
                                data-parsley-required="true" data-parsley-length="[1, 100]"
                            />
                        </div>
                        <div class="form-group">
                            <label for="enName"><?php echo language('machines-enName', $_SESSION['lang']); ?></label>
                            <input type="text" placeholder="<?php echo language('machines-enName', $_SESSION['lang']); ?>"
                                value="<?php echo $enName; ?>" name="enName" required="" class="form-control"
                                data-parsley-required="true" data-parsley-length="[1, 100]"
                            />
                        </div>
                        <div class="form-group">
                            <label for="description"><?php echo language('machines-description', $_SESSION['lang']); ?></label>
                            <input type="text" placeholder="<?php echo language('machines-description', $_SESSION['lang']); ?>"
                                value="<?php echo $description; ?>" name="description" class="form-control"
                                data-parsley-length="[0, 255]"
                            />
                        </div>
                        <div class="form-group">
                            <label for="type_id"><?php echo language('machines-type', $_SESSION['lang']); ?></label>
                            <select name="type_id" class="form-control">
                                <?php
                                $subQuery = "SELECT * FROM machineTypes WHERE id = :type_id";
                                $subStmt = Connection::conn()->prepare($subQuery);
                                $subStmt->bindParam(':type_id', $type_id);
                                $subStmt->execute();
                                $row = $subStmt->fetch(PDO::FETCH_ASSOC);
                                ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['type']; ?></option>
                                <?php
                                $subQuery = "SELECT * FROM machineTypes WHERE id != :type_id";
                                $subStmt = Connection::conn()->prepare($subQuery);
                                $subStmt->bindParam(':type_id', $type_id, PDO::PARAM_INT);
                                $subStmt->execute();
                                while ($row = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                    extract($row);
                                    ?>
                                    <option value="<?php echo $id; ?>"><?php echo $type; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                </div>
                <div class="panel-footer">
                        <input type="submit" value="<?php echo language('edit', $_SESSION['lang']); ?>"
                            class="btn btn-primary btn-lg">
                    </form>
                </div>
            </div>
        <?php } else {
            $_SESSION['error'] = language('id-not-found', $_SESSION['lang']);
            header('machines.php?lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('id-required', $_SESSION['lang']);
        header('Location: machines.php?lang='.$selectedLang);
        die();
    }

} elseif (isset($_GET['manage']) && $_GET['manage'] == 'update') {

/***************************************************************
* UPDATE MACHINE
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['arName']) && isset($_POST['enName']) && isset($_POST['type_id'])) {
            // if (!intval($_POST['type_id'])) {
            //     $_SESSION['error'] = language('choose-type-error', $_SESSION['lang']);
            //     header('Location: machines.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            //     die();
            // }
            $arName = filter_var(testInput($_POST['arName']), FILTER_SANITIZE_STRING);
            // if (strlen($arName) > 100 || strlen($arName) < 1) {
            //     $_SESSION['error'] = language("machines-check-error", $_SESSION['lang']);
            //     header('Location: machines.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            //     die();
            // }
            $enName = filter_var(testInput($_POST['enName']), FILTER_SANITIZE_STRING);
            // if (strlen($enName) > 100 || strlen($enName) < 1) {
            //     $_SESSION['error'] = language("machines-check-error", $_SESSION['lang']);
            //     header('Location: machines.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            //     die();
            // }
            $description = (isset($_POST['description'])) ? filter_var(testInput($_POST['description']), FILTER_SANITIZE_STRING) : '';
            // if (strlen($description) > 255) {
            //     $_SESSION['error'] = language("machines-description-check-error", $_SESSION['lang']);
            //     header('Location: machines.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            //     die();
            // }
            $type_id = intval($_POST['type_id']);
            try {
                $query = "UPDATE machines SET arName = :arName, enName = :enName, description = :description, type_id = :type_id WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':arName', $arName, PDO::PARAM_STR);
                $stmt->bindParam(':enName', $enName, PDO::PARAM_STR);
                $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                $stmt->bindParam(':type_id', $type_id, PDO::PARAM_INT);
                $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'machines.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('machines-update-success', $_SESSION['lang']);
            header('Location: machines.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('machines-required', $_SESSION['lang']);
            header('Location: machines.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: machines.php?manage=view&lang='.$selectedLang);
        die();
    }
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'delete') {

/***************************************************************
* DELETE MACHINE
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['id'])) {
            $id = filter_var(testInput($_POST['id']), FILTER_VALIDATE_INT);
            try {
                $query = "DELETE FROM machines WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['success'] = language("machines-delete-success", $_SESSION['lang']);
                header('Location: machines.php?manage=view&lang='.$selectedLang);
                die();
            } catch (PDOException $e) {
                dbError($stmt, 'machines.php?manage=view&lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('id-required', $_SESSION['lang']);
            header('Location: machines.php?manage=view&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: machines.php?manage=view&lang='.$selectedLang);
        die();
    }

} else {
    header('Location: machines.php?manage=view&lang='.$selectedLang);
    die();
} ?>

<?php require_once "layouts/footer.php"; ?>