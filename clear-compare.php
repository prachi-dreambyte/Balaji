<?php
session_start();
unset($_SESSION['compare_list']);
header("Location: compare-list.php");
exit;
