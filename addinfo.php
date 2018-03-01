<?php

require("setup.inc");

need_admin();

$_SESSION["info"]="";
$_SESSION["error"]="";

$card = new Card();
$card->id     = $get->add("id",null,"");
$card->code   = $get->add("code",null,"");
$card->wnr    = $get->add("wnr",null,"");
$card->name   = $get->add("name",null,"");
$card->member = ($get->add("member",null,"")=="yes");

// Check that all input are valid
$error=$card->validError();
// If check fails, save inputs and return to edit-page with a message
if ($error) {
  $_SESSION["error"]=$error;
  $_SESSION["card"]=$card;
  header("Location: checkmember.php");
  die();
}

$card->insertupdate();

$_SESSION["info"]="New card added";
header("Location: index.php");

?>
