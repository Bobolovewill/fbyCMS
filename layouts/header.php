<?php
  require_once 'config.php';
  require_once 'inc/header.php';
  if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
  }
?>

<div class="container">
  <?php require_once 'inc/navbar.php'; ?>
