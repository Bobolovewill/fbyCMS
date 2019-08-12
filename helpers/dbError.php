<?php

function dbError($stmt, $location) {
    if (isset($stmt->errorInfo()[2])) { // Start DB Error
        $_SESSION['error'] = $stmt->errorInfo()[2];
        header('Location: '.$location);
        die();
    } // End DB Error
}