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
* VIEW RECEIPTS
***************************************************************/

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo language("receipts-heading", $_SESSION['lang']); ?>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-responsive table-hover text-center">
            <thead>
                <tr>
                    <th><?php echo language('id', $_SESSION['lang']); ?></th>
                    <th><?php echo language('receipts-receiptDate', $_SESSION['lang']); ?></th>
                    <th><?php echo language('receipts-deliveryDate', $_SESSION['lang']); ?></th>
                    <th><?php echo language('receipts-article', $_SESSION['lang']); ?></th>
                    <th><?php echo language('receipts-machine', $_SESSION['lang']); ?></th>
                    <th><?php echo language('receipts-technician', $_SESSION['lang']); ?></th>
                    <th><?php echo language('receipts-manage', $_SESSION['lang']); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM receipts";
                $stmt = Connection::conn()->prepare($query);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['receiptDate']; ?></td>
                        <td><?php echo $row['deliveryDate']; ?></td>
                        <td>
                            <?php
                            $subQuery = "SELECT * FROM articles WHERE id = :id";
                            $subStmt = Connection::conn()->prepare($subQuery);
                            $subStmt->bindParam(':id', $row['article_id'], PDO::PARAM_STR);
                            $subStmt->execute();
                            $subRow = $subStmt->fetch(PDO::FETCH_ASSOC);
                            $result = $subRow['id'];
                            $subSubQuery = "SELECT * FROM patients WHERE id = :patient_id";
                            $subSubStmt = Connection::conn()->prepare($subSubQuery);
                            $subSubStmt->bindParam(':patient_id', $subRow['patient_id'], PDO::PARAM_INT);
                            $subSubStmt->execute();
                            $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                            $result .= ' '.$subSubRow['firstName'].' '.$subSubRow['middleName'].' '.$subSubRow['lastName'];
                            echo $result;
                            ?>
                        </td>
                        <td>
                            <?php
                                $subQuery = "SELECT * FROM machines WHERE id = :id";
                                $subStmt = Connection::conn()->prepare($subQuery);
                                $subStmt->bindParam(':id', $row['machine_id'], PDO::PARAM_INT);
                                $subStmt->execute();
                                $subRow = $subStmt->fetch(PDO::FETCH_ASSOC);
                                if ($_SESSION['lang']) {
                                    echo $subRow['arName'];
                                }
                                else {
                                    echo $subRow['enName'];
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                $subQuery = "SELECT * FROM employees WHERE id = :employee_id";
                                $subStmt = Connection::conn()->prepare($subQuery);
                                $subStmt->bindParam(':employee_id', $row['employee_id'], PDO::PARAM_INT);
                                $subStmt->execute();
                                $subRow = $subStmt->fetch(PDO::FETCH_ASSOC);
                                echo $subRow['id'].' '.$subRow['firstName'].' '.$subRow['middleName'].' '.$subRow['lastName'];
                            ?>
                        </td>
                        <td>
                            <a href="receipts.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $row['id']; ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                <?php echo language('receipts-edit', $_SESSION['lang']); ?>
                            </a>
                            <form action="receipts.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
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
        <a href="receipts.php?lang=<?php echo $selectedLang; ?>&manage=add" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            <?php echo language('receipts-add', $_SESSION['lang']); ?>
        </a>
    </div>
</div>

<?php } elseif (isset($_GET['manage']) && $_GET['manage'] == 'add') {

/***************************************************************
* ADD RECEIPT
***************************************************************/

?>
    <!-- ADDING RECEIPT -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?php echo language('receipts-add', $_SESSION['lang']); ?>
        </div>
        <div class="panel-body">
            <form action="receipts.php?manage=store" method="POST" data-parsley-validate="">
                <div class="bootstrap-iso">
                    <div class="form-group">
                        <label for="receiptDate"><?php echo language("receipts-receiptDate", $_SESSION['lang']); ?></label>
                        <div class="input-group date" id="datetimepickerreceiptDate">
                            <input type="text" required="" data-parsley-required="true" name="receiptDate"/>
                            <span class="input-group-addon pull-left">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="bootstrap-iso">
                    <div class="form-group">
                        <label for="deliveryDate"><?php echo language("receipts-deliveryDate", $_SESSION['lang']); ?></label>
                        <div class="input-group date" id="datetimepickerreceiptdeliveryDate">
                            <input type="text" required="" data-parsley-required="true" name="deliveryDate"/>
                            <span class="input-group-addon pull-left">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?php
                        if (isset($_GET['article_id'])) {
                            $article_id = filter_var(testInput($_GET['article_id']), FILTER_VALIDATE_INT);
                            $subQuery = "SELECT * FROM articles WHERE id = :id";
                            $subStmt = Connection::conn()->prepare($subQuery);
                            $subStmt->bindParam(':id', $article_id);
                            $subStmt->execute();
                            if ($subStmt->rowCount()) { ?>
                                <input type="hidden" value="<?php echo $article_id; ?>" name="article_id"/>
                            <?php }
                            else {
                                $_SESSION['error'] = language('article-not-found', $_SESSION['lang']);
                                header('Location: articles.php?manage=view&lang='.$selectedLang);
                                die();
                            }
                        }
                        else { ?>
                            <label for="article_id"><?php echo language('receipts-article', $_SESSION['lang']); ?></label>
                            <select name="article_id" class="form-control">
                            <?php
                            $subQuery = "SELECT * FROM articles";
                            $subStmt = Connection::conn()->prepare($subQuery);
                            $subStmt->execute();
                            while ($subRow = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                $patient = '';
                                $subSubQuery = "SELECT * FROM patients WHERE id = :patient_id";
                                $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                $subSubStmt->bindParam(':patient_id', $subRow['patient_id'], PDO::PARAM_INT);
                                $subSubStmt->execute();
                                $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                $patient = $subSubRow['firstName'].' '.$subSubRow['middleName'].' '.$subSubRow['lastName'];
                                ?>
                                <option value="<?php echo $subRow['id']; ?>"><?php echo $patient; ?></option>
                            <?php } ?>
                            </select>
                        <?php } ?>
                </div>
                <div class="form-group">
                    <label for="machine_id"><?php echo language('receipts-machine', $_SESSION['lang']); ?></label>
                    <select name="machine_id" class="form-control">
                        <?php
                        $subQuery = "SELECT * FROM machines";
                        $subStmt = Connection::conn()->prepare($subQuery);
                        $subStmt->execute();
                        while ($subRow = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                            <option value="<?php echo $subRow['id']; ?>">
                                <?php
                                if ($_SESSION['lang']) {
                                    echo $subRow['arName'];
                                }
                                else {
                                    echo $subRow['enName'];
                                }
                                ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="employee_id"><?php echo language('receipts-technician', $_SESSION['lang']); ?></label>
                    <select name="employee_id" class="form-control">
                        <?php
                        $subQuery = "SELECT * FROM employees";
                        $subStmt = Connection::conn()->prepare($subQuery);
                        $subStmt->execute();
                        while ($subRow = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                            <option value="<?php echo $subRow['id']; ?>">
                                <?php
                                    echo $subRow['firstName'].' '.$subRow['middleName'].' '.$subRow['lastName'];
                                ?>
                            </option>
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
* STORE RECEIPT
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['receiptDate']) && isset($_POST['deliveryDate'])) {
            // receiptDate Validation
            $receiptDate = testInput($_POST['receiptDate']);
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$receiptDate)) {
                $_SESSION['error'] = language('date-wrong-format-error', $_SESSION['lang']);
                header('Location: receipts.php?manage=add&lang='.$selectedLang);
                die();
            }
            // deliveryDate Validation
            $deliveryDate = testInput($_POST['deliveryDate']);
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$deliveryDate)) {
                $_SESSION['error'] = language('date-wrong-format-error', $_SESSION['lang']);
                header('Location: receipts.php?manage=add&lang='.$selectedLang);
                die();
            }
            // getting other data
            $article_id = $_POST['article_id'];
            $machine_id = $_POST['machine_id'];
            $employee_id = $_POST['employee_id'];
            try {
                $query = "INSERT INTO receipts (receiptDate, deliveryDate, article_id,
                    machine_id, employee_id) VALUES (:receiptDate, :deliveryDate, :article_id,
                    :machine_id, :employee_id)";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':receiptDate', $receiptDate);
                $stmt->bindParam(':deliveryDate', $deliveryDate);
                $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
                $stmt->bindParam(':machine_id', $machine_id, PDO::PARAM_INT);
                $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
                $stmt->execute();
            } catch(PDOException $e) {
                $_SESSION['error'] = language('receipts-add-error', $_SESSION['lang']);
                dbError($stmt, 'receipts.php?manage=add&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('receipts-add-success', $_SESSION['lang']);
            header('Location: receipts.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('receipts-required', $_SESSION['lang']);
            header('Location: receipts.php?manage=add&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: receipts.php?manage=add&lang='.$selectedLang);
        die();
    }
    
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'edit') {
    
    /***************************************************************
    * EDIT RECEIPT
    ***************************************************************/
    
    ?>
        <?php
        if (isset($_GET['id'])) {
            $id = intval(filter_var(testInput($_GET['id']), FILTER_VALIDATE_INT));
            $query = "SELECT * FROM receipts WHERE id = :id";
            $stmt = Connection::conn()->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount()) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <?php echo language('receipts-edit', $_SESSION['lang']); ?>
                    </div>
                    <div class="panel-body">
                        <form action="receipts.php?manage=update" method="POST" data-parsley-validate="">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>"/>
                            <div class="bootstrap-iso">
                                <div class="form-group">
                                    <label for="receiptDate"><?php echo language("receipts-receiptDate", $_SESSION['lang']); ?></label>
                                    <div class="input-group date" id="datetimepickerreceiptDate">
                                        <input type="text" required="" data-parsley-required="true" name="receiptDate"
                                        value="<?php echo $row['receiptDate']; ?>"/>
                                        <span class="input-group-addon pull-left">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="bootstrap-iso">
                                <div class="form-group">
                                    <label for="deliveryDate"><?php echo language("receipts-deliveryDate", $_SESSION['lang']); ?></label>
                                    <div class="input-group date" id="datetimepickerreceiptdeliveryDate">
                                        <input type="text" required="" data-parsley-required="true" name="deliveryDate"
                                        value="<?php echo $row['deliveryDate']; ?>"/>
                                        <span class="input-group-addon pull-left">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="article_id"><?php echo language('receipts-article', $_SESSION['lang']); ?></label>
                                <select name="article_id" class="form-control">
                                    <?php
                                    $subQuery = "SELECT * FROM articles WHERE id = :article_id";
                                    $subStmt = Connection::conn()->prepare($subQuery);
                                    $subStmt->bindParam(':article_id', $row['article_id'], PDO::PARAM_INT);
                                    $subStmt->execute();
                                    $subRow = $subStmt->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <option value="<?php echo $subRow['id']; ?>">
                                    <?php
                                        $subSubQuery = "SELECT * FROM patients WHERE id = :patient_id";
                                        $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                        $subSubStmt->bindParam(':patient_id', $subRow['patient_id'], PDO::PARAM_INT);
                                        $subSubStmt->execute();
                                        $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                        echo $subRow['id'].'. '.$subSubRow['firstName'].' '.$subSubRow['middleName'].' '.$subSubRow['lastName'];
                                    ?>
                                    </option>
                                    <?php
                                    $subQuery = "SELECT * FROM articles WHERE id != :article_id";
                                    $subStmt = Connection::conn()->prepare($subQuery);
                                    $subStmt->bindParam(':article_id', $row['article_id'], PDO::PARAM_INT);
                                    $subStmt->execute();
                                    while ($subRow = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                    ?>
                                    <option value="<?php echo $subRow['id']; ?>">
                                    <?php
                                        $subSubQuery = "SELECT * FROM patients WHERE id = :patient_id";
                                        $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                        $subSubStmt->bindParam(':patient_id', $subRow['patient_id'], PDO::PARAM_INT);
                                        $subSubStmt->execute();
                                        $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                        echo $subRow['id'].'. '.$subSubRow['firstName'].' '.$subSubRow['middleName'].' '.$subSubRow['lastName'];
                                    ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="machine_id"><?php echo language('receipts-machine', $_SESSION['lang']); ?></label>
                                <select name="machine_id" class="form-control">
                                    <?php
                                    $subQuery = "SELECT * FROM machines WHERE id = :id";
                                    $subStmt = Connection::conn()->prepare($subQuery);
                                    $subStmt->bindParam(':id', $row['machine_id'], PDO::PARAM_INT);
                                    $subStmt->execute();
                                    $subRow = $subStmt->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <option value="<?php echo $subRow['id']; ?>">
                                        <?php
                                            if ($_SESSION['lang']) {
                                                echo $subRow['arName'];
                                            } else {
                                                echo $subRow['enName'];
                                            }
                                        ?>
                                    </option>
                                    <?php
                                    $subQuery = "SELECT * FROM machines WHERE id != :id";
                                    $subStmt = Connection::conn()->prepare($subQuery);
                                    $subStmt->bindParam(':id', $row['machine_id'], PDO::PARAM_INT);
                                    $subStmt->execute();
                                    while ($subRow = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                        <option value="<?php echo $subRow['id']; ?>">
                                            <?php
                                                if ($_SESSION['lang']) {
                                                    echo $subRow['arName'];
                                                } else {
                                                    echo $subRow['enName'];
                                                }
                                            ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="employee_id"><?php echo language('receipts-technician', $_SESSION['lang']); ?></label>
                                <select name="employee_id" class="form-control">
                                    <?php
                                    $subQuery = "SELECT * FROM employees WHERE id = :id";
                                    $subStmt = Connection::conn()->prepare($subQuery);
                                    $subStmt->bindParam(':id', $row['employee_id'], PDO::PARAM_INT);
                                    $subStmt->execute();
                                    $subRow = $subStmt->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <option value="<?php echo $subRow['id']; ?>"><?php echo $subRow['firstName'].' '.$subRow['middleName'].' '.$subRow['lastName']; ?></option>
                                    <?php
                                    $subQuery = "SELECT * FROM employees WHERE id != :id";
                                    $subStmt = Connection::conn()->prepare($subQuery);
                                    $subStmt->bindParam(':id', $row['employee_id'], PDO::PARAM_INT);
                                    $subStmt->execute();
                                    while ($subRow = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                        <option value="<?php echo $subRow['id']; ?>"><?php echo $subRow['firstName'].' '.$subRow['middleName'].' '.$subRow['lastName']; ?></option>
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
                header('receipts.php?lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('id-required', $_SESSION['lang']);
            header('Location: receipts.php?lang='.$selectedLang);
            die();
        }
    
    } elseif (isset($_GET['manage']) && $_GET['manage'] == 'update') {

/***************************************************************
* UPDATE RECEIPT
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['receiptDate']) && isset($_POST['deliveryDate'])) {
            // receiptDate Validation
            $receiptDate = testInput($_POST['receiptDate']);
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$receiptDate)) {
                $_SESSION['error'] = language('date-wrong-format-error', $_SESSION['lang']);
                header('Location: receipts.php?manage=add&lang='.$selectedLang);
                die();
            }
            // deliveryDate Validation
            $deliveryDate = testInput($_POST['deliveryDate']);
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$deliveryDate)) {
                $_SESSION['error'] = language('date-wrong-format-error', $_SESSION['lang']);
                header('Location: receipts.php?manage=add&lang='.$selectedLang);
                die();
            }
            // getting other data
            $article_id = $_POST['article_id'];
            $machine_id = $_POST['machine_id'];
            $employee_id = $_POST['employee_id'];
            $id = $_POST['id'];
            try {
                $query = "UPDATE receipts SET receiptDate = :receiptDate, deliveryDate = :deliveryDate,
                    article_id = :article_id, machine_id = :machine_id, employee_id = :employee_id
                    WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':receiptDate', $receiptDate);
                $stmt->bindParam(':deliveryDate', $deliveryDate);
                $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
                $stmt->bindParam(':machine_id', $machine_id, PDO::PARAM_INT);
                $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            } catch(PDOException $e) {
                dbError($stmt, 'receipts.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('receipts-update-success', $_SESSION['lang']);
            header('Location: receipts.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('receipts-required', $_SESSION['lang']);
            header('Location: receipts.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: receipts.php?manage=view&lang='.$selectedLang);
        die();
    }
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'delete') {

/***************************************************************
* DELETE RECEIPT
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['id'])) {
            $id = filter_var(testInput($_POST['id']), FILTER_VALIDATE_INT);
            try {
                $query = "DELETE FROM receipts WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['success'] = language("receipts-delete-success", $_SESSION['lang']);
                header('Location: receipts.php?manage=view&lang='.$selectedLang);
                die();
            } catch (PDOException $e) {
                dbError($stmt, 'receipts.php?manage=view&lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('id-required', $_SESSION['lang']);
            header('Location: receipts.php?manage=view&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: receipts.php?manage=view&lang='.$selectedLang);
        die();
    }

} else {
    header('Location: receipts.php?manage=view&lang='.$selectedLang);
    die();
} ?>

<?php require_once "layouts/footer.php"; ?>