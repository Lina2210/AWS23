<?php
    session_start();
    if (isset($_SESSION["user_name"])) { unset($_SESSION["user_name"]); }
    if (isset($_SESSION["mail"])) { unset($_SESSION["mail"]); }
    if (isset($_SESSION["user_id"])) { unset($_SESSION["user_id"]); }
    header("Location: login.php");
?>