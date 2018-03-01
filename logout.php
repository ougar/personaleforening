<?php
require("setup.inc");

if (array_key_exists("admin",$_SESSION))
  unset($_SESSION["admin"]);
header("Location: index.php");

?>
