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
* VIEW DISABILITY CARDS
***************************************************************/

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo language("disabilities-heading", $_SESSION['lang']); ?>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-responsive table-hover text-center">
            <thead>
                <tr>
                    <th><?php echo language('id', $_SESSION['lang']); ?></th>
                    <th><?php echo language('disabilities-description', $_SESSION['lang']); ?></th>
                    <th><?php echo language('disabilities-amount', $_SESSION['lang']); ?></th>
                    <th><?php echo language('disabilities-article', $_SESSION['lang']); ?></th>
                    <th><?php echo language('disabilities-date', $_SESSION['lang']); ?></th>
                    <th><?php echo language('disabilities-manage', $_SESSION['lang']); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM disabilities";
                $stmt = Connection::conn()->prepare($query);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['amount']; ?></td>
                        <td>
                            <?php
                                $result = $row['article_id'].' ';
                                $subQuery = "SELECT * FROM articles WHERE id = :id";
                                $subStmt = Connection::conn()->prepare($subQuery);
                                $subStmt->bindParam(':id', $row['article_id'], PDO::PARAM_INT);
                                $subStmt->execute();
                                $subRow = $subStmt->fetch(PDO::FETCH_ASSOC);
                                $subSubQuery = "SELECT * FROM patients WHERE id = :id";
                                $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                $subSubStmt->bindParam(':id', $subRow['patient_id'], PDO::PARAM_INT);
                                $subSubStmt->execute();
                                $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                $result .= '( '.$subSubRow['firstName'].' '.$subSubRow['middleName'].' '.$subSubRow['lastName'].' )';
                                $subSubQuery = "SELECT * FROM machines WHERE id = :id";
                                $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                $subSubStmt->bindParam(':id', $subRow['machine_id'], PDO::PARAM_INT);
                                $subSubStmt->execute();
                                $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                if ($_SESSION['lang']) {
                                    $result .= ' ('.$subSubRow['arName'].' )';
                                }
                                else {
                                    $result .= ' ('.$subSubRow['enName'].' )';
                                }
                                $subSubQuery = "SELECT * FROM departments WHERE id = :id";
                                $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                $subSubStmt->bindParam(':id', $subRow['department_id'], PDO::PARAM_INT);
                                $subSubStmt->execute();
                                $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                $result .= ' ('.$subSubRow['name'].' )';
                                echo $result;
                            ?> 
                        </td>
                        <td> <?php echo $row['date']; ?></td>
                        <td>
                        <a href="disabilities.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $row['id']; ?>">
                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                            <?php echo language('disabilities-edit', $_SESSION['lang']); ?>
                        </a>
                        <form action="disabilities.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
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
        <a href="disabilities.php?lang=<?php echo $selectedLang; ?>&manage=add" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            <?php echo language('disabilities-add', $_SESSION['lang']); ?>
        </a>
    </div>
</div>

<?php } elseif (isset($_GET['manage']) && $_GET['manage'] == 'add') {

/***************************************************************
* ADD DISABILITY CARD
***************************************************************/
$query = "SELECT COUNT(*) AS count FROM articles";
$stmt = Connection::conn()->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row['count']) {
    $_SESSION['error'] = language("disability-card-no-articles-error", $_SESSION['lang']);
    header('Location: articles.php?manage=view&lang='.$selectedLang);
    die();
}
?>
    <!-- ADDING DISABILITY CARD -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?php echo language('disabilities-add', $_SESSION['lang']); ?>
        </div>
        <div class="panel-body">
            <form action="disabilities.php?manage=store" method="POST" data-parsley-validate="">
                <div class="form-group">
                    <label for="description"><?php echo language('disabilities-description', $_SESSION['lang']); ?></label>
                    <input type="text" placeholder="<?php echo language('disabilities-description', $_SESSION['lang']); ?>"
                        value="<?php echo isset($_SESSION['description']) ? $_SESSION['desciption'] : ''; ?>"  data-parsley-maxlength="255"
                        class="form-control" name="description"
                    />
                </div>
                <div class="form-group">
                    <label for="amount"><?php echo language('disabilities-amount', $_SESSION['lang']); ?></label>
                    <input type="text" placeholder="<?php echo language('disabilities-amount', $_SESSION['lang']); ?>"
                        value="<?php echo isset($_SESSION['amount']) ? $_SESSION['amount'] : ''; ?>" required=""
                        class="form-control" name="amount" data-parsley-length="[1, 8]" data-parsley-required="true"
                    />
                </div>
                <div>
                    <?php
                        if(isset($_GET['article_id'])) {
                            $passed_article_id = filter_var(testInput($_GET['article_id']), FILTER_VALIDATE_INT);
                            $subQuery = "SELECT * FROM articles WHERE id = :article_id";
                            $subStmt = Connection::conn()->prepare($subQuery);
                            $subStmt->bindParam(':article_id', $passed_article_id, PDO::PARAM_INT);
                            $subStmt->execute();
                            if ($subStmt->rowCount()) {
                                $subRow = $subStmt->fetch(PDO::FETCH_ASSOC); ?>
                                <label for="article_id"><?php echo language('disabilities-article', $_SESSION['lang']); ?></label>
                                <select name="article_id" class="form-control">
                                    <option value="<?php echo $subRow['id']; ?>"><?php echo $subRow['id']; ?></option>
                                </select>
                            <?php }
                            else {
                                $_SESSION['error'] = language('article-not-found', $_SESSION['lang']);
                                header('disabilities.php?lang='.$selectedLang);
                                die();
                            }
                        }
                        else {
                    ?>
                        <label for="article_id"><?php echo language('disabilities-article', $_SESSION['lang']); ?></label>
                        <select name="article_id" class="form-control">
                            <?php
                            $subQuery = "SELECT * FROM articles";
                            $subStmt = Connection::conn()->prepare($subQuery);
                            $subStmt->execute();
                            while ($subRow = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                $inner = $subRow['id'].' ';
                                $subSubQuery = "SELECT * FROM patients WHERE id = :id";
                                $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                $subSubStmt->bindParam(':id', $subRow['patient_id']);
                                $subSubStmt->execute();
                                $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                $inner .= '( '.$subSubRow['firstName'].' '.$subSubRow['middleName'].' '.$subSubRow['lastName'].' )';
                                $subSubQuery = "SELECT * FROM machines WHERE id = :id";
                                $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                $subSubStmt->bindParam(':id', $subRow['machine_id']);
                                $subSubStmt->execute();
                                $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                if ($_SESSION['lang']) {
                                    $inner .= ' ('.$subSubRow['arName'].' )';
                                }
                                else {
                                    $inner .= ' ('.$subSubRow['enName'].' )';
                                }
                                $subSubQuery = "SELECT * FROM departments WHERE id = :id";
                                $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                $subSubStmt->bindParam(':id', $subRow['department_id']);
                                $subSubStmt->execute();
                                $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                $inner .= ' ('.$subSubRow['name'].' )';
                                ?>
                                <option value="<?php echo $subRow['id']; ?>"><?php echo $inner; ?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                </div>
                <div class="bootstrap-iso">
                    <div class="form-group">
                        <label for="date"><?php echo language("disabilities-date", $_SESSION['lang']); ?></label>
                        <div class="input-group date" id="datetimepickerarticleDate">
                            <input type="text" required="" data-parsley-required="true" name="date"/>
                            <span class="input-group-addon pull-left">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
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
* STORE DISABILITY CARD
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['amount']) && isset($_POST['date'])) {
            // description Validation
            $description = filter_var(testInput($_POST['description']), FILTER_SANITIZE_STRING);
            // if (strlen($description) > 255) {
            //     $_SESSION['error'] = language('disabilities-desc-length-error', $_SESSION['lang']);
            //     header('Location: disabilities.php?manage=add&lang='.$selectedLang);
            //     die();
            // }
            // amount Validation
            $amount = filter_var(testInput($_POST['amount']), FILTER_VALIDATE_FLOAT);
            // date Validation
            $date = testInput($_POST['date']);
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
                $_SESSION['error'] = language('date-wrong-format-error', $_SESSION['lang']);
                header('Location: disabilities.php?manage=add&lang='.$selectedLang);
                die();
            }
            // getting last data
            $article_id = $_POST['article_id'];
            try {
                $query = "INSERT INTO disabilities (description, amount, article_id, date)
                    VALUES (:description, :amount, :article_id, :date)";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                $stmt->bindParam(':amount', $amount);
                $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
                $stmt->bindParam(':date', $date);
                $stmt->execute();
            }
            catch(PDOException $e) {
                $_SESSION['error'] = language('disabilities-add-error', $_SESSION['lang']);
                dbError($stmt, 'disabilities.php?manage=add&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('disabilities-add-success', $_SESSION['lang']);
            header('Location: disabilities.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('disabilities-required', $_SESSION['lang']);
            header('Location: disabilities.php?manage=add&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: disabilitites.php?manage=add&lang='.$selectedLang);
        die();
    }
    
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'edit') {

/***************************************************************
* EDIT DISABILITY CARD
***************************************************************/

?>
    <?php
    if (isset($_GET['id'])) {
        $id = filter_var(testInput($_GET['id']), FILTER_VALIDATE_INT);
        $query = "SELECT * FROM disabilities WHERE id = :id";
        $stmt = Connection::conn()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount()) {
            extract($stmt->fetch(PDO::FETCH_ASSOC));
        ?>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <?php echo language('disabilities-edit', $_SESSION['lang']); ?>
            </div>
            <div class="panel-body">
                <form action="disabilities.php?manage=update" method="POST" data-parsley-validate="">
                    <input type="hidden" value="<?php echo $_GET['id']; ?>" name="id"/>
                    <div class="form-group">
                        <label for="description"><?php echo language('disabilities-description', $_SESSION['lang']); ?></label>
                        <input type="text" placeholder="<?php echo language('disabilities-description', $_SESSION['lang']); ?>"
                            value="<?php echo $description; ?>"  data-parsley-maxlength="255"
                            class="form-control" name="description"
                        />
                    </div>
                    <div class="form-group">
                        <label for="amount"><?php echo language('disabilities-amount', $_SESSION['lang']); ?></label>
                        <input type="text" placeholder="<?php echo language('disabilities-amount', $_SESSION['lang']); ?>"
                            value="<?php echo $amount; ?>" required=""
                            class="form-control" name="amount" data-parsley-length="[1, 8]" data-parsley-required="true"
                        />
                    </div>
                    <div>
                        <label for="article_id"><?php echo language('disabilities-article', $_SESSION['lang']); ?></label>
                        <select name="article_id" class="form-control">
                            <option value="<?php echo article_id; ?>">
                                <?php
                                $subQuery = "SELECT * FROM articles WHERE id = :id";
                                $subStmt = Connection::conn()->prepare($subQuery);
                                $subStmt->bindParam(':id', $article_id, PDO::PARAM_INT);
                                $subStmt->execute();
                                $subRow = $subStmt->fetch(PDO::FETCH_ASSOC);
                                $inner = $subRow['id'].' ';
                                $subSubQuery = "SELECT * FROM patients WHERE id = :id";
                                $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                $subSubStmt->bindParam(':id', $subRow['patient_id']);
                                $subSubStmt->execute();
                                $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                $inner .= '( '.$subSubRow['firstName'].' '.$subSubRow['middleName'].' '.$subSubRow['lastName'].' )';
                                $subSubQuery = "SELECT * FROM machines WHERE id = :id";
                                $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                $subSubStmt->bindParam(':id', $subRow['machine_id']);
                                $subSubStmt->execute();
                                $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                if ($_SESSION['lang']) {
                                    $inner .= ' ('.$subSubRow['arName'].' )';
                                }
                                else {
                                    $inner .= ' ('.$subSubRow['enName'].' )';
                                }
                                $subSubQuery = "SELECT * FROM departments WHERE id = :id";
                                $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                $subSubStmt->bindParam(':id', $subRow['department_id']);
                                $subSubStmt->execute();
                                $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                $inner .= ' ('.$subSubRow['name'].' )';
                                echo $inner;
                                ?>
                            </option>
                            <?php
                            $subQuery = "SELECT * FROM articles WHERE id != :id";
                            $subStmt = Connection::conn()->prepare($subQuery);
                            $subStmt->bindParam(':id', $article_id, PDO::PARAM_INT);
                            $subStmt->execute();
                            while ($subRow = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                $inner = $subRow['id'].' ';
                                $subSubQuery = "SELECT * FROM patients WHERE id = :id";
                                $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                $subSubStmt->bindParam(':id', $subRow['patient_id']);
                                $subSubStmt->execute();
                                $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                $inner .= '( '.$subSubRow['firstName'].' '.$subSubRow['middleName'].' '.$subSubRow['lastName'].' )';
                                $subSubQuery = "SELECT * FROM machines WHERE id = :id";
                                $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                $subSubStmt->bindParam(':id', $subRow['machine_id']);
                                $subSubStmt->execute();
                                $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                if ($_SESSION['lang']) {
                                    $inner .= ' ('.$subSubRow['arName'].' )';
                                }
                                else {
                                    $inner .= ' ('.$subSubRow['enName'].' )';
                                }
                                $subSubQuery = "SELECT * FROM departments WHERE id = :id";
                                $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                $subSubStmt->bindParam(':id', $subRow['department_id']);
                                $subSubStmt->execute();
                                $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                $inner .= ' ('.$subSubRow['name'].' )';
                                ?>
                                <option value="<?php echo $subRow['id']; ?>"><?php echo $inner; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="bootstrap-iso">
                        <div class="form-group">
                            <label for="date"><?php echo language("disabilities-date", $_SESSION['lang']); ?></label>
                            <div class="input-group date" id="datetimepickerarticleDate">
                                <input type="text" required="" data-parsley-required="true" name="date" value="<?php echo $date; ?>"/>
                                <span class="input-group-addon pull-left">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="panel-footer">
                    <input type="submit" value="<?php echo language('edit', $_SESSION['lang']); ?>"
                        class="btn btn-primary btn-lg"/>
                </form>
            </div>
        </div>
        <?php } else {
            $_SESSION['error'] = language('id-not-found', $_SESSION['lang']);
            header('disabilities.php?lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('id-required', $_SESSION['lang']);
        header('Location: disabilities.php?lang='.$selectedLang);
        die();
    }

} elseif (isset($_GET['manage']) && $_GET['manage'] == 'update') {

/***************************************************************
* UPDATE DISABILITY CARD
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['amount']) && isset($_POST['date'])) {
            // description Validation
            $description = filter_var(testInput($_POST['description']), FILTER_SANITIZE_STRING);
            // if (strlen($description) > 255) {
            //     $_SESSION['error'] = language('disabilities-desc-length-error', $_SESSION['lang']);
            //     header('Location: disabilities.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            //     die();
            // }
            // amount Validation
            $amount = filter_var(testInput($_POST['amount']), FILTER_VALIDATE_FLOAT);
            // date Validation
            $date = testInput($_POST['date']);
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
                $_SESSION['error'] = language('date-wrong-format-error', $_SESSION['lang']);
                header('Location: disabilities.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
                die();
            }
            // getting last data
            $article_id = $_POST['article_id'];
            $id = $_POST['id'];
            try {
                $query = "UPDATE disabilities SET description = :description, amount = :amount,
                    article_id = :article_id, date = :date WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                $stmt->bindParam(':amount', $amount);
                $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
            }
            catch(PDOException $e) {
                $_SESSION['error'] = language('disabilities-add-error', $_SESSION['lang']);
                dbError($stmt, 'disabilities.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('disabilities-add-success', $_SESSION['lang']);
            header('Location: disabilities.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('disabilities-required', $_SESSION['lang']);
            header('Location: disabilities.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: disabilitites.php?manage=view&lang='.$selectedLang);
        die();
    }

} elseif (isset($_GET['manage']) && $_GET['manage'] == 'delete') {

/***************************************************************
* DELETE DISABILITY CARD
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['id'])) {
            $id = filter_var(testInput($_POST['id']), FILTER_VALIDATE_INT);
            try {
                $query = "DELETE FROM disabilities WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['success'] = language("disabilities-delete-success", $_SESSION['lang']);
                header('Location: disabilities.php?manage=view&lang='.$selectedLang);
                die();
            } catch (PDOException $e) {
                dbError($stmt, 'disabilities.php?manage=view&lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('id-required', $_SESSION['lang']);
            header('Location: disabilities.php?manage=view&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: disabilities.php?manage=view&lang='.$selectedLang);
        die();
    }

} else {
    header('Location: disabilities.php?manage=view&lang='.$selectedLang);
    die();
} ?>

<?php require_once "layouts/footer.php"; ?>