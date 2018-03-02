<?php
require("setup.inc");

need_admin();

// Get card code from POST/GET 
$code=trim($get->add("code",null,""));
// If nothing was recieved, just go to index page
if ($code=="") {
  $_SESSION["error"]="No card ID recieved in betalt.php";
  header("Location: index.php");
}
// Check number format
if (!preg_match("/^\d{6,12}$/",$code)) {
  $_SESSION["error"]="Invalid card ID";
  header("Location: index.php");
}

$error="";
$card=new Card();
$cardknown=$card->getFromCode($code);
if (!$cardknown) {
  $_SESSION["error"]="Ukendt kort. Kan ikke betale";
  header("Location: index.php");
}

$card->payment();
header("Location: checkmember.php?code=$code");

?>


