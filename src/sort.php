<?php
 session_start();
 $_SESSION['sort'] = $_GET['sort'];
 $_SESSION['order'] = $_GET['order'];
 header('Location: ./?dir=' . $_GET['dir']);
?>