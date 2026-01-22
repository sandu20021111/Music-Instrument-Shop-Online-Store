<?php
session_start();
session_destroy(); // සියලුම Session දත්ත මකා දමයි
header("Location: login.php"); // නැවත Login පිටුවට යවයි
exit();
?>