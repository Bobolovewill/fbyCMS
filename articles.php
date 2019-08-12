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
* VIEW ARTICLES
***************************************************************/

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo language("articles-heading", $_SESSION['lang']); ?>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-responsive table-hover text-center">
            <thead>
                <tr>
                    <th><?php echo language('id', $_SESSION['lang']); ?></th>
                    <th><?php echo language('articles-price', $_SESSION['lang']); ?></th>
                    <th><?php echo language('articles-date', $_SESSION['lang']); ?></th>
                    <th><?php echo language('articles-patient', $_SESSION['lang']); ?></th>
                    <th><?php echo language('articles-transfer', $_SESSION['lang']); ?></th>
                    <th><?php echo language('articles-machine', $_SESSION['lang']); ?></th>
                    <th><?php echo language('articles-department', $_SESSION['lang']); ?></th>
                    <th><?php echo language('articles-manage', $_SESSION['lang']); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM articles";
                $stmt = Connection::conn()->prepare($query);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    ?>
                    <tr>
                        <td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?manage=show&lang=<?php echo $selectedLang; ?>&id=<?php echo $id; ?>"><?php echo $id; ?></a></td>
                        <td><?php echo $price; ?></td>
                        <td><?php echo $date; ?></td>
                        <td>
                            <?php
                            $subQuery = "SELECT * FROM patients WHERE id = :id";
                            $subStmt = Connection::conn()->prepare($subQuery);
                            $subStmt->bindParam(':id', $patient_id, PDO::PARAM_STR);
                            $subStmt->execute();
                            $row = $subStmt->fetch(PDO::FETCH_ASSOC);
                            echo $row['firstName'].' '.$row['middleName'].' '.$row['lastName'];
                            ?>
                        </td>
                        <td>
                            <?php
                                if (is_null($transfer_id)) {
                                    echo language('not-transfered', $_SESSION['lang']);
                                } else {
                                    $subQuery = "SELECT * FROM transfers WHERE id = :id";
                                    $subStmt = Connection::conn()->prepare($subQuery);
                                    $subStmt->bindParam(':id', $transfer_id, PDO::PARAM_INT);
                                    $subStmt->execute();
                                    $row = $subStmt->fetch(PDO::FETCH_ASSOC);
                                    echo $row['name'];
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                $subQuery = "SELECT * FROM machines WHERE id = :id";
                                $subStmt = Connection::conn()->prepare($subQuery);
                                $subStmt->bindParam(':id', $machine_id, PDO::PARAM_INT);
                                $subStmt->execute();
                                $row = $subStmt->fetch(PDO::FETCH_ASSOC);
                                // if Arabic is the language of the session
                                if ($_SESSION['lang']) {
                                    echo $row['arName'];
                                }
                                // if English is the language of the session
                                else {
                                    echo $row['enName'];
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                $subQuery = "SELECT * FROM departments WHERE id = :id";
                                $subStmt = Connection::conn()->prepare($subQuery);
                                $subStmt->bindParam(':id', $department_id, PDO::PARAM_INT);
                                $subStmt->execute();
                                $row = $subStmt->fetch(PDO::FETCH_ASSOC);
                                echo $row['name'];
                            ?>
                        </td>
                        <td>
                            <a href="articles.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $id; ?>">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                <?php echo language('articles-edit', $_SESSION['lang']); ?>
                            </a>
                            <form action="articles.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
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
        <a href="articles.php?lang=<?php echo $selectedLang; ?>&manage=add" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            <?php echo language('articles-add', $_SESSION['lang']); ?>
        </a>
    </div>
</div>

<?php } elseif (isset($_GET['manage']) && $_GET['manage'] == 'add') {

/***************************************************************
* ADD ARTICLE
***************************************************************/

?>
    <!-- ADDING ARTICLE -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?php echo language('articles-add', $_SESSION['lang']); ?>
        </div>
        <div class="panel-body">
            <form action="articles.php?manage=store" method="POST" data-parsley-validate="">
                <div class="form-group">
                    <label for="price"><?php echo language('articles-price', $_SESSION['lang']); ?></label>
                    <input type="text" placeholder="<?php echo language('articles-price', $_SESSION['lang']); ?>"
                        value="<?php echo isset($_SESSION['price']) ? $_SESSION['price'] : ''; ?>" required=""
                        class="form-control" name="price" data-parsley-length="[1, 8]" data-parsley-required="true"
                    />
                </div>
                <div class="bootstrap-iso">
                    <div class="form-group">
                        <label for="date"><?php echo language("articles-date", $_SESSION['lang']); ?></label>
                        <div class="input-group date" id="datetimepickerarticleDate">
                            <input type="text" required="" data-parsley-required="true" name="date"/>
                            <span class="input-group-addon pull-left">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?php
                        if (isset($_GET['patient_id'])) {
                            $patient_id = filter_var(testInput($_GET['patient_id']), FILTER_VALIDATE_INT);
                            $subQuery = "SELECT * FROM patients WHERE id = :id";
                            $subStmt = Connection::conn()->prepare($subQuery);
                            $subStmt->bindParam(':id', $patient_id);
                            $subStmt->execute();
                            if ($subStmt->rowCount()) { ?>
                                <input type="hidden" value="<?php echo $patient_id; ?>" name="patient_id"/>
                            <?php }
                            else {
                                $_SESSION['error'] = language('patient-not-found-error', $_SESSION['lang']);
                                header('Location: patients.php?manage=view&lang='.$selectedLang);
                                die();
                            }
                        }
                        else { ?>
                        <label for="patient_id"><?php echo language('articles-patient', $_SESSION['lang']); ?></label>
                            <select name="patient_id" class="form-control">
                            <?php
                            $subQuery = "SELECT * FROM patients";
                            $subStmt = Connection::conn()->prepare($subQuery);
                            $subStmt->execute();
                            while ($row = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                extract($row);
                                ?>
                                <option value="<?php echo $id; ?>"><?php echo $firstName.' '.$middleName.' '.$lastName; ?></option>
                            <?php } ?>
                        </select>
                        <?php } ?>
                </div>
                <div class="form-group">
                    <label for="transfer_id"><?php echo language('articles-transfer', $_SESSION['lang']); ?></label>
                    <select name="transfer_id" class="form-control">
                        <option value="0" ><?php echo language('not-transfered', $_SESSION['lang']); ?></option>
                        <?php
                        $subQuery = "SELECT * FROM transfers";
                        $subStmt = Connection::conn()->prepare($subQuery);
                        $subStmt->execute();
                        while ($row = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                            extract($row);
                            ?>
                            <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="machine_id"><?php echo language('articles-machine', $_SESSION['lang']); ?></label>
                    <select name="machine_id" class="form-control">
                        <?php
                        $subQuery = "SELECT * FROM machines";
                        $subStmt = Connection::conn()->prepare($subQuery);
                        $subStmt->execute();
                        while ($row = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                            extract($row);
                            ?>
                            <option value="<?php echo $id; ?>">
                                <?php
                                    if ($_SESSION['lang']) {
                                        echo $arName;
                                    } else {
                                        echo $enName;
                                    }
                                ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="department_id"><?php echo language('articles-department', $_SESSION['lang']); ?></label>
                    <select name="department_id" class="form-control">
                        <?php
                        $subQuery = "SELECT * FROM departments";
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
* STORE ARTICLE
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['price']) && isset($_POST['date'])) {
            // price Validation
            $price = filter_var(testInput($_POST['price']), FILTER_VALIDATE_FLOAT);
            // date Validation
            $date = testInput($_POST['date']);
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
                $_SESSION['error'] = language('date-wrong-format-error', $_SESSION['lang']);
                header('Location: articles.php?manage=add&lang='.$selectedLang);
                die();
            }
            // getting other data
            $patient_id = $_POST['patient_id'];
            $transfer_id = $_POST['transfer_id'];
            $machine_id = $_POST['machine_id'];
            $department_id = $_POST['department_id'];
            try {
                if ($transfer_id) {
                    $query = "INSERT INTO articles (price, date, patient_id, transfer_id,
                        machine_id, department_id) VALUES (:price, :date, :patient_id, :transfer_id,
                        :machine_id, :department_id)";
                    $stmt = Connection::conn()->prepare($query);
                    $stmt->bindParam(':price', $price);
                    $stmt->bindParam(':date', $date);
                    $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
                    $stmt->bindParam(':transfer_id', $transfer_id, PDO::PARAM_INT);
                    $stmt->bindParam(':machine_id', $machine_id, PDO::PARAM_INT);
                    $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    $query = "INSERT INTO articles (price, date, patient_id,
                        machine_id, department_id) VALUES (:price, :date, :patient_id,
                        :machine_id, :department_id)";
                    $stmt = Connection::conn()->prepare($query);
                    $stmt->bindParam(':price', $price);
                    $stmt->bindParam(':date', $date);
                    $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
                    $stmt->bindParam(':machine_id', $machine_id, PDO::PARAM_INT);
                    $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);
                    $stmt->execute();
                }
            } catch(PDOException $e) {
                $_SESSION['error'] = language('articles-add-error', $_SESSION['lang']);
                dbError($stmt, 'articles.php?manage=add&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('articles-add-success', $_SESSION['lang']);
            header('Location: articles.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('articles-required', $_SESSION['lang']);
            header('Location: articles.php?manage=add&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: articles.php?manage=add&lang='.$selectedLang);
        die();
    }
    
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'edit') {
    
    /***************************************************************
    * EDIT ARTICLE
    ***************************************************************/
    
    ?>
        <?php
        if (isset($_GET['id'])) {
            $id = intval(filter_var(testInput($_GET['id']), FILTER_VALIDATE_INT));
            $query = "SELECT * FROM articles WHERE id = :id";
            $stmt = Connection::conn()->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount()) {
                extract($stmt->fetch(PDO::FETCH_ASSOC));
            ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <?php echo language('articles-edit', $_SESSION['lang']); ?>
                    </div>
                    <div class="panel-body">
                        <form action="articles.php?manage=update" method="POST" data-parsley-validate="">
                            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                            <div class="form-group">
                                <label for="price"><?php echo language('articles-price', $_SESSION['lang']); ?></label>
                                <input type="text" placeholder="<?php echo language('articles-price', $_SESSION['lang']); ?>"
                                    value="<?php echo $price; ?>" required=""
                                    class="form-control" name="price" data-parsley-length="[1, 8]" data-parsley-required="true"
                                />
                            </div>
                            <div class="bootstrap-iso">
                                <div class="form-group">
                                    <label for="date"><?php echo language("articles-date", $_SESSION['lang']); ?></label>
                                    <div class='input-group date' id='datetimepickerarticleDate'>
                                        <input type='text' required="" data-parsley-required="true" name="date"
                                        value="<?php echo $date; ?>"/>
                                        <span class="input-group-addon pull-left">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="patient_id"><?php echo language('articles-patient', $_SESSION['lang']); ?></label>
                                <select name="patient_id" class="form-control">
                                    <?php
                                    $subQuery = "SELECT * FROM patients WHERE id = :id";
                                    $subStmt = Connection::conn()->prepare($subQuery);
                                    $subStmt->bindParam(':id', $patient_id, PDO::PARAM_INT);
                                    $subStmt->execute();
                                    $row = $subStmt->fetch(PDO::FETCH_ASSOC);
                                    extract($row);
                                    ?>
                                    <option value="<?php echo $id; ?>"><?php echo $firstName.' '.$middleName.' '.$lastName; ?></option>
                                    <?php
                                    $subQuery = "SELECT * FROM patients WHERE id != :id";
                                    $subStmt = Connection::conn()->prepare($subQuery);
                                    $subStmt->bindParam(':id', $patient_id, PDO::PARAM_INT);
                                    $subStmt->execute();
                                    while ($row = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                        extract($row);
                                        ?>
                                        <option value="<?php echo $id; ?>"><?php echo $firstName.' '.$middleName.' '.$lastName; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="transfer_id"><?php echo language('articles-transfer', $_SESSION['lang']); ?></label>
                                <select name="transfer_id" class="form-control">
                                    <?php
                                    $subQuery = "SELECT * FROM transfers WHERE id = :id";
                                    $subStmt = Connection::conn()->prepare($subQuery);
                                    $subStmt->bindParam(':id', $transfer_id);
                                    $subStmt->execute();
                                    if ($subStmt->rowCount()) {
                                        $row = $subStmt->fetch(PDO::FETCH_ASSOC);
                                        ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                                        <option value="0"><?php echo language('not-transfered', $_SESSION['lang']); ?></option>
                                        <?php
                                        $subQuery = "SELECT * FROM transfers WHERE id != :id";
                                        $subStmt = Connection::conn()->prepare($subQuery);
                                        $subStmt->bindParam(':id', $transfer_id, PDO::PARAM_INT);
                                        $subStmt->execute();
                                        while ($row = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                            extract($row);
                                            ?>
                                            <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                                            <?php
                                        } ?>
                                </select>
                                    <?php } else { ?>
                                        <option value="0"><?php echo language('not-transfered', $_SESSION['lang']); ?></option>
                                        <?php
                                            $subQuery = "SELECT * FROM transfers";
                                            $subStmt = Connection::conn()->prepare($subQuery);
                                            $subStmt->execute();
                                            while ($row = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                                extract($row);
                                                ?>
                                                <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                                    <?php } ?>
                                </select>    
                                        <?php }
                                    ?>
                            </div>
                            <div class="form-group">
                                <label for="machine_id"><?php echo language('articles-machine', $_SESSION['lang']); ?></label>
                                <select name="machine_id" class="form-control">
                                    <?php
                                    $subQuery = "SELECT * FROM machines WHERE id = :id";
                                    $subStmt = Connection::conn()->prepare($subQuery);
                                    $subStmt->bindParam(':id', $machine_id, PDO::PARAM_INT);
                                    $subStmt->execute();
                                    $row = $subStmt->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <option value="<?php echo $id; ?>">
                                        <?php
                                            if ($_SESSION['lang']) {
                                                echo $row['arName'];
                                            } else {
                                                echo $row['enName'];
                                            }
                                        ?>
                                    </option>
                                    <?php
                                    $subQuery = "SELECT * FROM machines WHERE id != :id";
                                    $subStmt = Connection::conn()->prepare($subQuery);
                                    $subStmt->bindParam(':id', $machine_id, PDO::PARAM_INT);
                                    $subStmt->execute();
                                    while ($row = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                        <option value="<?php echo $id; ?>">
                                            <?php
                                                if ($_SESSION['lang']) {
                                                    echo $row['arName'];
                                                } else {
                                                    echo $row['enName'];
                                                }
                                            ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="department_id"><?php echo language('articles-department', $_SESSION['lang']); ?></label>
                                <select name="department_id" class="form-control">
                                    <?php
                                    $subQuery = "SELECT * FROM departments WHERE id = :id";
                                    $subStmt = Connection::conn()->prepare($subQuery);
                                    $subStmt->bindParam(':id', $department_id, PDO::PARAM_INT);
                                    $subStmt->execute();
                                    $row = $subStmt->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                                    <?php
                                    $subQuery = "SELECT * FROM departments WHERE id != :id";
                                    $subStmt = Connection::conn()->prepare($subQuery);
                                    $subStmt->bindParam(':id', $department_id, PDO::PARAM_INT);
                                    $subStmt->execute();
                                    while ($row = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
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
                header('articles.php?lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('id-required', $_SESSION['lang']);
            header('Location: articles.php?lang='.$selectedLang);
            die();
        }
    
    } elseif (isset($_GET['manage']) && $_GET['manage'] == 'update') {

/***************************************************************
* UPDATE ARTICLE
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['price']) && isset($_POST['date'])) {
            // price Validation
            $price = filter_var(testInput($_POST['price']), FILTER_VALIDATE_FLOAT);
            // date Validation
            $date = testInput($_POST['date']);
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
                $_SESSION['error'] = language('date-wrong-format-error', $_SESSION['lang']);
                header('Location: articles.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
                die();
            }
            // getting other data
            $patient_id = $_POST['patient_id'];
            $transfer_id = $_POST['transfer_id'];
            $machine_id = $_POST['machine_id'];
            $department_id = $_POST['department_id'];
            $id = $_POST['id'];
            try {
                if ($transfer_id) {
                    $query = "UPDATE articles SET price = :price, date = :date, patient_id = :patient_id,
                        transfer_id = :transfer_id, machine_id = :machine_id, department_id = :department_id
                        WHERE id = :id";
                    $stmt = Connection::conn()->prepare($query);
                    $stmt->bindParam(':price', $price);
                    $stmt->bindParam(':date', $date);
                    $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
                    $stmt->bindParam(':transfer_id', $transfer_id, PDO::PARAM_INT);
                    $stmt->bindParam(':machine_id', $machine_id, PDO::PARAM_INT);
                    $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    $query = "UPDATE articles SET price = :price, date = :date, patient_id = :patient_id,
                        transfer_id = NULL, machine_id = :machine_id, department_id = :department_id
                        WHERE id = :id";
                    $stmt = Connection::conn()->prepare($query);
                    $stmt->bindParam(':price', $price);
                    $stmt->bindParam(':date', $date);
                    $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
                    $stmt->bindParam(':machine_id', $machine_id, PDO::PARAM_INT);
                    $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                }
            } catch(PDOException $e) {
                dbError($stmt, 'articles.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
                die();
            }
            $_SESSION['success'] = language('articles-update-success', $_SESSION['lang']);
            header('Location: articles.php?manage=view&lang='.$selectedLang);
            die();
        }
        else {
            $_SESSION['error'] = language('articles-required', $_SESSION['lang']);
            header('Location: articles.php?manage=edit&id='.$_POST['id'].'&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: articles.php?manage=view&lang='.$selectedLang);
        die();
    }
} elseif (isset($_GET['manage']) && $_GET['manage'] == 'delete') {

/***************************************************************
* DELETE ARTICLE
***************************************************************/

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['id'])) {
            $id = filter_var(testInput($_POST['id']), FILTER_VALIDATE_INT);
            try {
                $query = "DELETE FROM articles WHERE id = :id";
                $stmt = Connection::conn()->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['success'] = language("articles-delete-success", $_SESSION['lang']);
                header('Location: articles.php?manage=view&lang='.$selectedLang);
                die();
            } catch (PDOException $e) {
                dbError($stmt, 'articles.php?manage=view&lang='.$selectedLang);
                die();
            }
        }
        else {
            $_SESSION['error'] = language('id-required', $_SESSION['lang']);
            header('Location: articles.php?manage=view&lang='.$selectedLang);
            die();
        }
    }
    else {
        $_SESSION['error'] = language('method_not_allowed_error', $_SESSION['lang']);
        header('Location: articles.php?manage=view&lang='.$selectedLang);
        die();
    }

} elseif (isset($_GET['manage']) && $_GET['manage'] == 'show') {
/***************************************************************
* SHOW ARTICLE
***************************************************************/
    if (isset($_GET['id'])) {
        if (!filter_var(testInput($_GET['id']), FILTER_VALIDATE_INT)) {
            $_SESSION['error'] = language('page-not-found', $_SESSION['lang']);
            header('Location: articles.php?manage=view&lang='.$selectedLang);
            die();
        }
        $query = "SELECT * FROM articles WHERE id = :id";
        $stmt = Connection::conn()->prepare($query);
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();
        if (!$stmt->rowCount()) {
            $_SESSION['error'] = language('article-not-found', $_SESSION['lang']);
            header('Location: articles.php?manage=view&lang='.$selectedLang);
            die();
        }
        extract($stmt->fetch(PDO::FETCH_ASSOC));
        ?>
        <div class="panel panel-default article-panel">
        <div class="panel-heading">
            <?php echo language('article-heading', $_SESSION['lang']); ?>
        </div>
        <div class="panel-body">
            <h4><?php echo language('id', $_SESSION['lang']).': '.$id; ?></h4>
            <h4><?php echo language('articles-price', $_SESSION['lang']).': '.$price; ?></h4>
            <h4><?php echo language('articles-date', $_SESSION['lang']).': '.$date; ?></h4>
            <h4>
                <?php
                    echo language('articles-patient', $_SESSION['lang']).': ';
                    $subQuery = "SELECT * FROM patients WHERE id = :patient_id";
                    $subStmt = Connection::conn()->prepare($subQuery);
                    $subStmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
                    $subStmt->execute();
                    $subRow = $subStmt->fetch(PDO::FETCH_ASSOC);
                    echo $subRow['firstName'].' '.$subRow['middleName'].' '.$subRow['lastName'];
                ?>
            </h4>
            <h4>
                <?php
                    echo language('articles-transfer', $_SESSION['lang']).': ';
                    $subQuery = "SELECT name FROM transfers WHERE id = :transfer_id";
                    $subStmt = Connection::conn()->prepare($subQuery);
                    $subStmt->bindParam(':transfer_id', $transfer_id, PDO::PARAM_INT);
                    $subStmt->execute();
                    if ($subStmt->rowCount()) {
                        $subRow = $subStmt->fetch(PDO::FETCH_ASSOC);
                        echo $subRow['name'];
                    }
                    else {
                        echo language('no-transfer', $_SESSION['lang']);
                    }
                ?>
            </h4>
            <h4>
                <?php
                    echo language('articles-machine', $_SESSION['lang']).': ';
                    $subQuery = "SELECT * FROM machines WHERE id = :machine_id";
                    $subStmt = Connection::conn()->prepare($subQuery);
                    $subStmt->bindParam(':machine_id', $machine_id, PDO::PARAM_INT);
                    $subStmt->execute();
                    $subRow = $subStmt->fetch(PDO::FETCH_ASSOC);
                    if ($_SESSION['lang']) {
                        echo $subRow['arName'];
                    }
                    else {
                        echo $subRow['enName'];
                    }
                ?>
            </h4>
            <h4>
                <?php
                    echo language('articles-department', $_SESSION['lang']).': ';
                    $subQuery = "SELECT name FROM departments WHERE id = :department_id";
                    $subStmt = Connection::conn()->prepare($subQuery);
                    $subStmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);
                    $subStmt->execute();
                    $subRow = $subStmt->fetch(PDO::FETCH_ASSOC);
                    echo $subRow['name'];
                ?>
            </h4>
        </div>
        <div class="panel-footer">
            <p>
                <?php echo language('articles-manage', $_SESSION['lang']); ?>
            </p>
            <a href="articles.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $_GET['id']; ?>">
                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                <?php echo language('articles-edit', $_SESSION['lang']); ?>
            </a>
            <form action="articles.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <button type="submit" class="btn btn-danger btn-sm">
                    <span class="glyphicon glyphicon-trash"></span> <?php echo language("delete", $_SESSION['lang']); ?>
                </button>
            </form>
        </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo language('disabilities', $_SESSION['lang']); ?>
            </div>
            <div class="panel-body">
                <?php
                    $subQuery = "SELECT * FROM disabilities WHERE article_id = :id";
                    $subStmt = Connection::conn()->prepare($subQuery);
                    $subStmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
                    $subStmt->execute();
                    if ($subStmt->rowCount()) { ?>
                            <table class="table table-striped table-hover table-responsive text-center">
                                <thead>
                                    <tr>
                                        <td><?php echo language('disabilities-id', $_SESSION['lang']); ?></td>
                                        <td><?php echo language('disabilities-description', $_SESSION['lang']); ?></td>
                                        <td><?php echo language('disabilities-amount', $_SESSION['lang']); ?></td>
                                        <td><?php echo language('disabilities-date', $_SESSION['lang']); ?></td>
                                        <td><?php echo language('disabilities-manage', $_SESSION['lang']); ?></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        while ($row = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                            extract($row);
                                    ?>
                                    <tr>
                                        <td><?php echo $id; ?></td>
                                        <td><?php echo $description; ?></td>
                                        <td><?php echo $amount; ?></td>
                                        <td><?php echo $date; ?></td>
                                        <td>
                                            <a href="disabilities.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $id; ?>">
                                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                <?php echo language('disabilities-edit', $_SESSION['lang']); ?>
                                            </a>
                                            <form action="disabilities.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
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
                    <?php }
                    else { ?>
                        <h3><?php echo language('noDisabilityCards', $_SESSION['lang']); ?></h3>
                    <?php }
                ?>
            </div>
            <div class="panel-footer">
                <a href="disabilities.php?lang=<?php echo $selectedLang; ?>&manage=add&article_id=<?php echo $_GET['id']; ?>" class="btn btn-primary btn-lg">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    <?php echo language('disabilities-add', $_SESSION['lang']); ?>
                </a>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo language('financials', $_SESSION['lang']); ?>
            </div>
            <div class="panel-body">
                <?php
                    $subQuery = "SELECT * FROM financialAids WHERE article_id = :id";
                    $subStmt = Connection::conn()->prepare($subQuery);
                    $subStmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
                    $subStmt->execute();
                    if ($subStmt->rowCount()) { ?>
                            <table class="table table-striped table-hover table-responsive text-center">
                                <thead>
                                    <tr>
                                        <td><?php echo language('financials-id', $_SESSION['lang']); ?></td>
                                        <td><?php echo language('financials-description', $_SESSION['lang']); ?></td>
                                        <td><?php echo language('financials-amount', $_SESSION['lang']); ?></td>
                                        <td><?php echo language('financials-date', $_SESSION['lang']); ?></td>
                                        <td><?php echo language('financials-manage', $_SESSION['lang']); ?></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        while ($row = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                            extract($row);
                                    ?>
                                    <tr>
                                        <td><?php echo $id; ?></td>
                                        <td><?php echo $description; ?></td>
                                        <td><?php echo $amount; ?></td>
                                        <td><?php echo $date; ?></td>
                                        <td>
                                            <a href="financials.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $id; ?>">
                                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                <?php echo language('financials-edit', $_SESSION['lang']); ?>
                                            </a>
                                            <form action="financials.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
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
                    <?php }
                    else { ?>
                        <h3><?php echo language('noFinancialAid', $_SESSION['lang']); ?></h3>
                    <?php }
                ?>
            </div>
            <div class="panel-footer">
                <a href="financials.php?lang=<?php echo $selectedLang; ?>&manage=add&article_id=<?php echo $_GET['id']; ?>" class="btn btn-primary btn-lg">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    <?php echo language('financials-add', $_SESSION['lang']); ?>
                </a>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo language('machine-delivery', $_SESSION['lang']); ?>
            </div>
            <div class="panel-body">
                <?php
                    $subQuery = "SELECT * FROM receipts WHERE article_id = :id";
                    $subStmt = Connection::conn()->prepare($subQuery);
                    $subStmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
                    $subStmt->execute();
                    if ($subStmt->rowCount()) { ?>
                            <table class="table table-striped table-hover table-responsive text-center">
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
                                        while ($subRow = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $subRow['id']; ?></td>
                                        <td><?php echo $subRow['receiptDate']; ?></td>
                                        <td><?php echo $subRow['deliveryDate']; ?></td>
                                        <td>
                                            <?php
                                            $subSubQuery = "SELECT * FROM articles WHERE id = :id";
                                            $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                            $subSubStmt->bindParam(':id', $subRow['article_id'], PDO::PARAM_STR);
                                            $subSubStmt->execute();
                                            $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                            $result = $subSubRow['id'];
                                            $subSubSubQuery = "SELECT * FROM patients WHERE id = :patient_id";
                                            $subSubSubStmt = Connection::conn()->prepare($subSubSubQuery);
                                            $subSubSubStmt->bindParam(':patient_id', $subSubRow['patient_id'], PDO::PARAM_INT);
                                            $subSubSubStmt->execute();
                                            $subSubSubRow = $subSubSubStmt->fetch(PDO::FETCH_ASSOC);
                                            $result .= ' '.$subSubSubRow['firstName'].' '.$subSubSubRow['middleName'].' '.$subSubSubRow['lastName'];
                                            echo $result;
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                                $subSubQuery = "SELECT * FROM machines WHERE id = :id";
                                                $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                                $subSubStmt->bindParam(':id', $subRow['machine_id'], PDO::PARAM_INT);
                                                $subSubStmt->execute();
                                                $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                                if ($_SESSION['lang']) {
                                                    echo $subSubRow['arName'];
                                                }
                                                else {
                                                    echo $subSubRow['enName'];
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                                $subSubQuery = "SELECT * FROM employees WHERE id = :employee_id";
                                                $subSubStmt = Connection::conn()->prepare($subSubQuery);
                                                $subSubStmt->bindParam(':employee_id', $subRow['employee_id'], PDO::PARAM_INT);
                                                $subSubStmt->execute();
                                                $subSubRow = $subSubStmt->fetch(PDO::FETCH_ASSOC);
                                                echo $subSubRow['id'].' '.$subSubRow['firstName'].' '.$subSubRow['middleName'].' '.$subSubRow['lastName'];
                                            ?>
                                        </td>
                                        <td>
                                            <a href="receipts.php?lang=<?php echo $selectedLang; ?>&manage=edit&id=<?php echo $subRow['id']; ?>">
                                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                <?php echo language('receipts-edit', $_SESSION['lang']); ?>
                                            </a>
                                            <form action="receipts.php?lang=<?php echo $selectedLang; ?>&manage=delete" method="POST">
                                                <input type="hidden" name="id" value="<?php echo $subRow['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <span class="glyphicon glyphicon-trash"></span> <?php echo language("delete", $_SESSION['lang']); ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                    <?php }
                    else { ?>
                        <h3><?php echo language('noReceipts', $_SESSION['lang']); ?></h3>
                    <?php }
                ?>
            </div>
            <div class="panel-footer">
                <a href="receipts.php?lang=<?php echo $selectedLang; ?>&manage=add&article_id=<?php echo $_GET['id']; ?>" class="btn btn-primary btn-lg">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    <?php echo language('receipts-add', $_SESSION['lang']); ?>
                </a>
            </div>
        </div>
    <?php }
    else {
        header('Location: articles.php?manage=view&lang='.$selectedLang);
        die();
    }

} else {
    header('Location: articles.php?manage=view&lang='.$selectedLang);
    die();
} ?>

<?php require_once "layouts/footer.php"; ?>