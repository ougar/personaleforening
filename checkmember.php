<?php
require("setup.inc");

need_admin();

// If saved card exists, check that.
if (@$_SESSION["card"]) {
  $card=$_SESSION["card"];
  $cardknown=($card->id>0);
  $error=get_error();
  unset($_SESSION["card"]);
  $edit=1;
} else {
  // Get card code from POST/GET 
  $code=trim($get->add("code",null,""));
  // If nothing was recieved, just go to index page
  if ($code=="") header("Location: index.php");
  // Check number format
  if (!preg_match("/^\d{6,12}$/",$code)) {
    $_SESSION["error"]="Invalid card ID";
    header("Location: index.php");
  }
  $error="";
  $card=new Card();
  $cardknown=$card->getFromCode($code);
  if ($cardknown && $get->add("scan",null,"")) $card->scanned();
  if ($get->add("edit",null,""))
    $edit=1;
  else
    $edit=0;
}

$header->add_javascript("member.js");
$header->display();

if (!$cardknown)          cardform($card,"Gem nyt kort");
if ($cardknown && $edit)  cardform($card,"Opdater kortoplysninger");
if ($cardknown && !$edit) cardinfo($card);

// Card IS in database - show info
function cardform($card, $title) {
  global $error;
  $c="checked='checked'";
  print("<body>\n".
        "  <div id='infodiv'>\n".
        "    <h2> $title </h2>\n".
        "    <form action='addinfo.php' method='POST'>\n".
        ($card->id?"<input type='hidden' name='id' value='{$card->id}'>\n":"").
        "      <span> <label for='code'> Kort ID  </label> </span> <input id='code' name='code' value='{$card->code}' readonly='readonly'><br>\n".
        "      <span> <label for='wnr'>  W-nummer </label> </span> <input id='wnr'  name='wnr' value='{$card->wnr}'><br>\n".
        "      <span> <label for='wnr'>  Navn     </label> </span> <input id='name' name='name' value='{$card->name}'><br>\n".
        "      <span> <label for='wnr'>  Medlem   </label> </span>\n".
        "         <input type='radio' name='member' id='memberyes' value='yes' ".($card->member?$c:"")."> <label for='memberyes'> Ja </label>\n".
        "         <input type='radio' name='member' id='memberno'  value='no'  ".($card->member?"":$c)."> <label for='memberno'> Nej </label><br>\n".
        "      <input type='submit' name='submit' value='Gem info'>\n".
        "      <input type='reset'  value='Reset'>\n".
        "    </form>\n".
        "    <form action='index.php'>\n".
        "      <input type='submit' value='Annuller'>\n".
        "    </form>\n".
        "    <div class='error'> $error </div>\n".
        "  </div>\n".
        "</body>\n".
        "<html>\n");
}

function cardinfo($card) {
  if ($card->member) $class='member'; else $class='notmember';
  print("<body>\n".
        "  <div id='scandiv'>\n".
        "    <form method='POST' action='checkmember.php'> <input type='hidden' name='scan' value='1'> <input id='codeinput' name='code' autofocus='autofocus'> </input> </form>\n".
        "  </div>\n".
        "  <p>\n".
        "  <div id='infodiv' class='$class'>\n".
        "    <h2> Kortoplysninger </h2>\n".
        "    <span> Kort ID  </span> {$card->code} <br>\n".
        "    <span> W-nummer </span> {$card->wnr} <br>\n".
        "    <span> Navn     </span> {$card->name} <br>\n".
        "    <span> Medlem   </span> ".($card->member?"Ja":"Nej")." <br>\n".
        "    <form action='index.php'> <input type='submit' value='OK'> </form>\n".
        "    <form action='checkmember.php' method='POST'> <input type='hidden' name='edit' value='1'><input type='hidden' name='code' value='{$card->code}'><input type='submit' value='Edit'> </form>\n".
        ($card->member?"":"    <form action='betalt.php' method='POST'> <input type='hidden' name='code' value='{$card->code}'><input type='submit' value='Betalt' id='knapbetalt'> </form>\n").
        "  </div>\n".
        "<body>\n".
        "</html>\n");
}

?>


