<?php
session_start();
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (!empty($_SESSION['compare_list'])) {
        $_SESSION['compare_list'] = array_diff($_SESSION['compare_list'], [$id]);
    }
}
header("Location: compare-list.php");
exit;
