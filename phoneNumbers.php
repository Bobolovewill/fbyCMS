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
* VIEW PHONENUMBERS
***************************************************************/

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo language("phoneNumbers-heading", $_SESSION['lang']); ?>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-responsive table-hover text-center">
            <thead>
                <tr>
                    <th><?php echo language('id', $_SESSION['lang']); ?></th>
                    <th><?php echo language('phoneNumbers-number', $_SESSION['lang']); ?></th>
                    <th><?php echo language('phoneNumbers-typeDiscriminator', $_SESSION['lang']); ?></th>
                    <th><?php echo language('phoneNumbers-phoneOf', $_SESSION['lang']); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM phoneNumbers";
                $stmt = Connection::conn()->prepare($query);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $phone_id = $id;
                    ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo $number; ?></td>
                        <td><?php echo $typeDiscriminator; ?></td>
                        <td>
                            <?php
                                if ($typeDiscriminator == 'Employee' || $typeDiscriminator == 'موظف') {
                                    $subQuery = "SELECT * FROM employees WHERE id = :phoneOf_id";
                                    $subStmt = Connection::conn()->prepare($subQuery);
                                    $subStmt->bindParam(':phoneOf_id', $phoneOf_id, PDO::PARAM_INT);
                                    $subStmt->execute();
                                    extract($subStmt->fetch(PDO::FETCH_ASSOC));
                                    echo $firstName.' '.$middleName.' '.$lastName;
                                }
                                if ($typeDiscriminator == 'Patient' || $typeDiscriminator == 'مريض') {
                                    $subQuery = "SELECT * FROM patients WHERE id = :phoneOf_id";
                                    $subStmt = Connection::conn()->prepare($subQuery);
                                    $subStmt->bindParam(':phoneOf_id', $phoneOf_id, PDO::PARAM_INT);
                                    $subStmt->execute();
                                    extract($subStmt->fetch(PDO::FETCH_ASSOC));
                                    echo $firstName.' '.$middleName.' '.$lastName;
                                }
                            ?>
                        </td>
                        <td>
                            <a href="phoneNumbers.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $phone_id; ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                <?php echo language('phoneNumbers-edit', $_SESSION['lang']); ?>
                            </a>
                            <form action="phoneNumbers.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
                                <input type="hidden" name="id" value="<?php echo $phone_id; ?>">
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
</div>

<?php } elseif (isset($_GET['manage']) && $_GET['manage'] == 'add') {

/***************************************************************
* ADD PHONENUMBER
***************************************************************/

?>
    <!-- ADDING PHONENUMBER -->
    <?php if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (isset($_GET['typeDiscriminator']) && isset($_GET['phoneOf_id'])) {
            // Checking the validity of the id
            $id = filter_var(testInput($_GET['phoneOf_id']), FILTER_VALIDATE_INT);
            // if (!is_numeric($id)) {
            //     $_SESSION['error'] = language("page-not-found", $_SESSION['lang']);
            //     header('Location: dashboard.php?lang='.$selectedLang);
            //     die();
            // }
            if ($_GET['typeDiscriminator'] == "Employee" || $_GET['typeDiscriminator'] == "موظف") {
                $query = "SELECT * FROM employees WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                if (!$stmt->rowCount()) {
                    $_SESSION['error'] = language("page-not-found", $_SESSION['lang']);
                    header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
                    die();
                }
            }
            elseif ($_GET['typeDiscriminator'] == "Patient" || $_GET['typeDiscriminator'] == "مريض") {
                $query = "SELECT * FROM patients WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                if (!$stmt->rowCount()) {
                    $_SESSION['error'] = language("page-not-found", $_SESSION['lang']);
                    header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
                    die();
                }
            }
            else {
                $_SESSION['error'] = language("page-not-found", $_SESSION['lang']);
                header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
                die();
            }
        ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo language('phoneNumbers-add', $_SESSION['lang']); ?>
                </div>
                <div class="panel-body">
                    <form action="phoneNumbers.php?manage=store" method="POST" data-parsley-validate="">
                        <input type="hidden" name="typeDiscriminator" value="<?php echo $_GET['typeDiscriminator']; ?>"/>
                        <input type="hidden" name="phoneOf_id" value="<?php echo $_GET['phoneOf_id']; ?>"/>
                        <div class="form-group">
                            <label for="number"><?php echo language('phoneNumbers-number', $_SESSION['lang']); ?></label>
                            <input type="text" placeholder="<?php echo language('phoneNumbers-number', $_SESSION['lang']); ?>"
                                value="<?php echo isset($_SESSION['number']) ? $_SESSION['number'] : ''; ?>" required=""
                                class="form-control" name="number" data-parsley-required="true" data-parsley-length="[11, 11]"
                            />
                        </div>
                </div>
                <div class="panel-footer">
                        <input type="submit" value="<?php echo language('add', $_SESSION['lang']); ?>"
                            class="btn btn-primary btn-lg"/>
                    </form>
                </div>
            </div>
        <?php } else {
            $_SESSION['error'] = language("page-not-found", $_SESSION['lang']);
            header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
            die();
        }
    } else {
        $_SESSION['error'] = language("page-not-found", $_SESSION['lang']);
        header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
        die();  
    }
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'store') {

/***************************************************************
* STORE PHONENUMBERS
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['number'])) {
            $number = filter_var(testInput($_POST['number']), FILTER_SANITIZE_STRING);
            // if (strlen($number) != 11 || !is_numeric($number)) {
            //     $_SESSION['error'] = language("phoneNumber-check-error", $_SESSION['lang']);
            //     header('Location: phoneNumbers.php?manage=add&lang='.$selectedLang);
            //     die();
            // }
            // if (strlen($number) != 11) {
            //     $_SESSION['error'] = language("phoneNumber-check-error", $_SESSION['lang']);
            //     header('Location: phoneNumbers.php?manage=add&lang='.$selectedLang);
            //     die();
            // }
            $typeDiscriminator = $_POST['typeDiscriminator'];
            $phoneOf_id = $_POST['phoneOf_id'];
            try {
                $query = "INSERT INTO phoneNumbers (number, typeDiscriminator, phoneOf_id)
                    VALUES (:number, :typeDiscriminator, :phoneOf_id)";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':number', $number, PDO::PARAM_STR);
                $stmt->bindParam(':typeDiscriminator', $typeDiscriminator, PDO::PARAM_STR);
                $stmt->bindParam(':phoneOf_id', $phoneOf_id, PDO::PARAM_INT);
                $stmt->execute();
            } catch(PDOException $e) {
                $_SESSION['error'] = language('phoneNumber-add-error', $_SESSION['lang']);
                header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('phoneNumber-add-success', $_SESSION['lang']);
            header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('phoneNumber-required', $_SESSION['lang']);
            header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
        die();
    }
    
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'edit') {

/***************************************************************
* EDIT PHONENUMBER
***************************************************************/

?>
    <?php
    if (isset($_GET['id'])) {
        $id = filter_var(testInput($_GET['id']), FILTER_VALIDATE_INT);
        $query = "SELECT * FROM phoneNumbers WHERE id = :id";
        $stmt = Connection::conn()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount()) {
            extract($stmt->fetch(PDO::FETCH_ASSOC));
        ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo language('phoneNumbers-edit', $_SESSION['lang']); ?>
                </div>
                <div class="panel-body">
                    <form action="phoneNumbers.php?manage=update" method="POST" data-parsley-validate="">
                        <div class="form-group">
                            <label for="number"><?php echo language('phoneNumbers-number', $_SESSION['lang']); ?></label>
                            <input type="text" placeholder="<?php echo language('phoneNumbers-number', $_SESSION['lang']); ?>"
                                value="<?php echo $number; ?>" name="number" required="" class="form-control"
                                data-parsley-required="true" data-parsley-length="[11, 11]"
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
            header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('id-required', $_SESSION['lang']);
        header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
        die();
    }

} elseif (isset($_GET['manage']) && $_GET['manage'] == 'update') {

/***************************************************************
* UPDATE PHONENUMBER
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['number'])) {
            $number = filter_var(testInput($_POST['number']), FILTER_SANITIZE_STRING);
            // if (strlen($number) != 11 || !is_numeric($number)) {
            //     $_SESSION['error'] = language("phoneNumber-check-error", $_SESSION['lang']);
            //     header('Location: phoneNumbers.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            //     die();
            // }
            // if (strlen($number) != 11) {
            //     $_SESSION['error'] = language("phoneNumber-check-error", $_SESSION['lang']);
            //     header('Location: phoneNumbers.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            //     die();
            // }
            try {
                $query = "UPDATE phoneNumbers SET number = :number WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':number', $number, PDO::PARAM_STR);
                $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'phoneNumbers.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('phoneNumbers-update-success', $_SESSION['lang']);
            header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('phoneNumber-required', $_SESSION['lang']);
            header('Location: phoneNumbers.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
        die();
    }
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'delete') {

/***************************************************************
* DELETE PHONENUMBER
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['id'])) {
            $id = filter_var(testInput($_POST['id']), FILTER_VALIDATE_INT);
            try {
                $query = "DELETE FROM phoneNumbers WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['success'] = language("phoneNumbers-delete-success", $_SESSION['lang']);
                header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
                die();
            } catch (PDOException $e) {
                dbError($stmt, 'phoneNumbers.php?manage=view&lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('id-required', $_SESSION['lang']);
            header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
        die();
    }

} else {
    header('Location: phoneNumbers.php?manage=view&lang='.$selectedLang);
    die();
} ?>

<?php require_once "layouts/footer.php"; ?>