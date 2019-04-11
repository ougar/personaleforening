<?php

require("setup.inc");

$id=$get->add("id",null,"");

// If no ID recieved. Go to start page
if ($id=="") header("Location: index.php");

// Cast id as an integer
$id=(int)$id;

$card=new Card();
$knowncard=$card->getFromID($id);

if (!$knowncard) {
  $_SESSION["error"]="Can't delete. Unknown ID";
  header("Location: index.php");
  die();
}

$card->delete();
header("Location: showall.php");

?>
