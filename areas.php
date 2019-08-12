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
* VIEW AREAS
***************************************************************/

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo language("areas-heading", $_SESSION['lang']); ?>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-responsive table-hover text-center">
            <thead>
                <tr>
                    <th><?php echo language('id', $_SESSION['lang']); ?></th>
                    <th><?php echo language('areas-area', $_SESSION['lang']); ?></th>
                    <th><?php echo language('areas-city', $_SESSION['lang']); ?></th>
                    <th><?php echo language('areas-manage', $_SESSION['lang']); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM areas";
                $stmt = Connection::conn()->prepare($query);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo $name; ?></td>
                        <td>
                            <?php
                            $subQuery = "SELECT * FROM cities WHERE id = :city_id";
                            $subStmt = Connection::conn()->prepare($subQuery);
                            $subStmt->bindParam(':city_id', $city_id, PDO::PARAM_STR);
                            $subStmt->execute();
                            $row = $subStmt->fetch(PDO::FETCH_ASSOC);
                            echo $row['name'];
                            ?>
                        </td>
                        <td>
                            <a href="areas.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $id; ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                <?php echo language('areas-edit', $_SESSION['lang']); ?>
                            </a>
                            <form action="areas.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
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
        $query = "SELECT COUNT(*) AS count FROM cities";
        $stmt = Connection::conn()->prepare($query);
        $stmt->execute();
        extract($stmt->fetch(PDO::FETCH_ASSOC));
        if ($count) {
        ?>
        <a href="areas.php?lang=<?php echo $selectedLang; ?>&manage=add" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            <?php echo language('areas-add', $_SESSION['lang']); ?>
        </a>
        <?php } else { ?>
            <div class="alert alert-warning">
                <?php echo language('add-area-warning', $_SESSION['lang']); ?>
            </div>
        <?php } ?>
    </div>
</div>

<?php } elseif (isset($_GET['manage']) && $_GET['manage'] == 'add') {

/***************************************************************
* ADD AREA
***************************************************************/

?>
    <!-- ADDING AREA -->
    <?php
    $query = "SELECT COUNT(*) AS count FROM cities";
    $stmt = Connection::conn()->prepare($query);
    $stmt->execute();
    extract($stmt->fetch(PDO::FETCH_ASSOC));
    if (!$count) {
        $_SESSION["error"] = language("no-cities-error", $_SESSION['lang']);
        header('Location: areas.php?manage=view&lang='.$selectedLang);
        die();
    }
    ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?php echo language('areas-add', $_SESSION['lang']); ?>
        </div>
        <div class="panel-body">
            <form action="areas.php?manage=store" method="POST" data-parsley-validate="">
                <div class="form-group">
                    <label for="name"><?php echo language('areas-area', $_SESSION['lang']); ?></label>
                    <input type="text" placeholder="<?php echo language('areas-area', $_SESSION['lang']); ?>"
                        value="<?php echo isset($_SESSION['name']) ? $_SESSION['name'] : ''; ?>" required=""
                        class="form-control" name="name" data-parsley-length="[1, 50]" data-parsley-required="true"
                    />
                </div>
                <div class="form-group">
                    <label for="city_id"><?php echo language('areas-city', $_SESSION['lang']); ?></label>
                    <select name="city_id" class="form-control">
                        <option value="0">Select City</option>
                        <?php
                        $subQuery = "SELECT * FROM cities";
                        $subStmt = Connection::conn()->prepare($subQuery);
                        $subStmt->execute();
                        while ($row = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                            extract($row);
                            ?>
                            <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
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
* STORE AREA
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['name']) && isset($_POST['city_id'])) {
            // if (!intval($_POST['city_id'])) {
            //     $_SESSION['error'] = language('choose-city-error', $_SESSION['lang']);
            //     header('Location: areas.php?manage=add&lang='.$selectedLang);
            //     die();
            // }
            $name = filter_var(testInput($_POST['name']), FILTER_SANITIZE_STRING);
            // if (strlen($name) > 50 || strlen($name) < 1) {
            //     $_SESSION['error'] = language("areas-check-error", $_SESSION['lang']);
            //     header('Location: areas.php?manage=add&lang='.$selectedLang);
            //     die();
            // }
            try {
                $query = "INSERT INTO areas (name, city_id) VALUES (:name, :city_id)";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':city_id', intval($_POST['city_id']), PDO::PARAM_INT);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'areas.php?manage=add&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('areas-add-success', $_SESSION['lang']);
            header('Location: areas.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('areas-required', $_SESSION['lang']);
            header('Location: areas.php?manage=add&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: areas.php?manage=add&lang='.$selectedLang);
        die();
    }
    
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'edit') {

/***************************************************************
* EDIT AREA
***************************************************************/

?>
    <?php
    if (isset($_GET['id'])) {
        $id = intval(filter_var(testInput($_GET['id']), FILTER_VALIDATE_INT));
        $query = "SELECT * FROM areas WHERE id = :id";
        $stmt = Connection::conn()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount()) {
            extract($stmt->fetch(PDO::FETCH_ASSOC));
        ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo language('areas-edit', $_SESSION['lang']); ?>
                </div>
                <div class="panel-body">
                    <form action="areas.php?manage=update" method="POST" data-parsley-validate="">
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <div class="form-group">
                            <label for="name"><?php echo language('areas-area', $_SESSION['lang']); ?></label>
                            <input type="text" placeholder="<?php echo language('areas-area', $_SESSION['lang']); ?>"
                                value="<?php echo $name; ?>" name="name" required="" class="form-control"
                                data-parsley-required="true" data-parsley-length="[1, 50]"
                            />
                        </div>
                        <div class="form-group">
                            <label for="city_id"><?php echo language('areas-city', $_SESSION['lang']); ?></label>
                            <select name="city_id" class="form-control">
                                <?php
                                $subQuery = "SELECT * FROM cities WHERE id = :id";
                                $subStmt = Connection::conn()->prepare($subQuery);
                                $subStmt->bindParam(':id', $city_id);
                                $subStmt->execute();
                                $row = $subStmt->fetch(PDO::FETCH_ASSOC);
                                ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                                <?php
                                $subQuery = "SELECT * FROM cities WHERE id != :city_id";
                                $subStmt = Connection::conn()->prepare($subQuery);
                                $subStmt->bindParam(':city_id', $city_id, PDO::PARAM_INT);
                                $subStmt->execute();
                                while ($row = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                    extract($row);
                                    ?>
                                    <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
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
            header('areas.php?lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('id-required', $_SESSION['lang']);
        header('Location: areas.php?lang='.$selectedLang);
        die();
    }

} elseif (isset($_GET['manage']) && $_GET['manage'] == 'update') {

/***************************************************************
* UPDATE CITY
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['name']) && isset($_POST['city_id'])) {
            // if (!intval($_POST['city_id'])) {
            //     $_SESSION['error'] = language('choose-city-error', $_SESSION['lang']);
            //     header('Location: areas.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            //     die();
            // }
            $name = filter_var(testInput($_POST['name']), FILTER_SANITIZE_STRING);
            // if (strlen($name) > 50 || strlen($name) < 1) {
            //     $_SESSION['error'] = language("areas-check-error", $_SESSION['lang']);
            //     header('Location: areas.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            //     die();
            // }
            $city_id = intval($_POST['city_id']);
            try {
                $query = "UPDATE areas SET name = :name, city_id = :city_id WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':city_id', $city_id, PDO::PARAM_STR);
                $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'areas.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('areas-update-success', $_SESSION['lang']);
            header('Location: areas.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('areas-required', $_SESSION['lang']);
            header('Location: areas.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: areas.php?manage=view&lang='.$selectedLang);
        die();
    }
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'delete') {

/***************************************************************
* DELETE AREA
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['id'])) {
            $id = filter_var(testInput($_POST['id']), FILTER_VALIDATE_INT);
            try {
                $query = "DELETE FROM areas WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['success'] = language("areas-delete-success", $_SESSION['lang']);
                header('Location: areas.php?manage=view&lang='.$selectedLang);
                die();
            } catch (PDOException $e) {
                dbError($stmt, 'areas.php?manage=view&lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('id-required', $_SESSION['lang']);
            header('Location: areas.php?manage=view&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: areas.php?manage=view&lang='.$selectedLang);
        die();
    }

} else {
    header('Location: areas.php?manage=view&lang='.$selectedLang);
    die();
} ?>

<?php require_once "layouts/footer.php"; ?>