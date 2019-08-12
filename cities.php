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
* VIEW CITIES
***************************************************************/

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo language("cities-heading", $_SESSION['lang']); ?>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-responsive table-hover text-center">
            <thead>
                <tr>
                    <th><?php echo language('id', $_SESSION['lang']); ?></th>
                    <th><?php echo language('cities-city', $_SESSION['lang']); ?></th>
                    <th><?php echo language('cities-manage', $_SESSION['lang']); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM cities";
                $stmt = Connection::conn()->prepare($query);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo $name; ?></td>
                        <td>
                            <a href="cities.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $id; ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                <?php echo language('cities-edit', $_SESSION['lang']); ?>
                            </a>
                            <form action="cities.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
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
        <a href="cities.php?lang=<?php echo $selectedLang; ?>&manage=add" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            <?php echo language('cities-add', $_SESSION['lang']); ?>
        </a>
    </div>
</div>

<?php } elseif (isset($_GET['manage']) && $_GET['manage'] == 'add') {

/***************************************************************
* ADD CITY
***************************************************************/

?>
    <!-- ADDING CITY -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?php echo language('cities-add', $_SESSION['lang']); ?>
        </div>
        <div class="panel-body">
            <form action="cities.php?manage=store" method="POST" data-parsley-validate="">
                <div class="form-group">
                    <label for="name"><?php echo language('cities-city', $_SESSION['lang']); ?></label>
                    <input type="text" placeholder="<?php echo language('cities-city', $_SESSION['lang']); ?>"
                        value="<?php echo isset($_SESSION['name']) ? $_SESSION['name'] : ''; ?>" required=""
                        class="form-control" name="name" data-parsley-required="true" data-parsley-length="[1, 50]"
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
* STORE CITY
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['name'])) {
            $name = filter_var(testInput($_POST['name']), FILTER_SANITIZE_STRING);
            // if (strlen($name) > 50 || strlen($name) < 1) {
            //     $_SESSION['error'] = language("cities-check-error", $_SESSION['lang']);
            //     header('Location: cities.php?manage=add&lang='.$selectedLang);
            //     die();
            // }
            try {
                $query = "INSERT INTO cities (name) VALUES (:name)";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'cities.php?manage=add&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('cities-add-success', $_SESSION['lang']);
            header('Location: cities.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('cities-required', $_SESSION['lang']);
            header('Location: cities.php?manage=add&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: cities.php?manage=add&lang='.$selectedLang);
        die();
    }
    
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'edit') {

/***************************************************************
* EDIT CITY
***************************************************************/

?>
    <?php
    if (isset($_GET['id'])) {
        $id = filter_var(testInput($_GET['id']), FILTER_VALIDATE_INT);
        $query = "SELECT * FROM cities WHERE id = :id";
        $stmt = Connection::conn()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount()) {
            extract($stmt->fetch(PDO::FETCH_ASSOC));
        ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo language('cities-edit', $_SESSION['lang']); ?>
                </div>
                <div class="panel-body">
                    <form action="cities.php?manage=update" method="POST" data-parsley-validate="">
                        <div class="form-group">
                            <label for="name"><?php echo language('cities-city', $_SESSION['lang']); ?></label>
                            <input type="text" placeholder="<?php echo language('cities-city', $_SESSION['lang']); ?>"
                                value="<?php echo $name; ?>" name="name" required="" class="form-control"
                                data-parsley-required="true" data-parsley-length="[1, 50]"
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
            header('cities.php?lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('id-required', $_SESSION['lang']);
        header('Location: cities.php?lang='.$selectedLang);
        die();
    }

} elseif (isset($_GET['manage']) && $_GET['manage'] == 'update') {

/***************************************************************
* UPDATE CITY
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['name'])) {
            $name = filter_var(testInput($_POST['name']), FILTER_SANITIZE_STRING);
            // if (strlen($name) > 50 || strlen($name) < 1) {
            //     $_SESSION['error'] = language("cities-check-error", $_SESSION['lang']);
            //     header('Location: cities.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            //     die();
            // }
            try {
                $query = "UPDATE cities SET name = :name WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'cities.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('cities-update-success', $_SESSION['lang']);
            header('Location: cities.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('cities-required', $_SESSION['lang']);
            header('Location: cities.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: cities.php?manage=view&lang='.$selectedLang);
        die();
    }
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'delete') {

/***************************************************************
* DELETE CITY
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['id'])) {
            $id = filter_var(testInput($_POST['id']), FILTER_VALIDATE_INT);
            try {
                $query = "DELETE FROM cities WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['success'] = language("cities-delete-success", $_SESSION['lang']);
                header('Location: cities.php?manage=view&lang='.$selectedLang);
                die();
            } catch (PDOException $e) {
                dbError($stmt, 'cities.php?manage=view&lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('id-required', $_SESSION['lang']);
            header('Location: cities.php?manage=view&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: cities.php?manage=view&lang='.$selectedLang);
        die();
    }

} else {
    header('Location: cities.php?manage=view&lang='.$selectedLang);
    die();
} ?>

<?php require_once "layouts/footer.php"; ?>