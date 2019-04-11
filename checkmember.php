<?php
require("setup.inc");

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

  $error="";
  $card=new Card();
  // Check recieved input format
  // If it looks like a wnr, use that
  if (preg_match("/^[wW]?\d{5}$/",$code)) {
    $cardknown=$card->getFromWnr($code); 
    if (!$cardknown) $_SESSION["error"]="No active employee with this w-nr was found";
  } else if (preg_match("/^\d{6,12}$/",$code)) {
    $cardknown=$card->getFromCode($code);
    if (!$cardknown) $_SESSION["error"]="Card was not found in database";
  }
  else {
    $cardknown=0;
    $_SESSION["error"]="Invalid input (cardnumber or wnr expected)";
  }

  if (!$cardknown){
    # Save errornous card code
    $filename=dirname($_SERVER["SCRIPT_FILENAME"])."/log/scan_errors.log";
    $stamp=date("Ymd His");
    file_put_contents($filename,$stamp." - ".$code."\n",FILE_APPEND);
    header("Location: index.php");
  }

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
  if (!$card->obgpers) $class='external';
  print("<body>\n".
        "  <div id='scandiv'>\n".
        "    <form method='POST' action='checkmember.php'> <input type='hidden' name='scan' value='1'> <input id='codeinput' name='code' autofocus='autofocus'> </input> </form>\n".
        "  </div>\n".
        "  <p>\n".
        "  <div id='infodiv' class='$class'>\n".
        "    <h2> Kortoplysninger </h2>\n".
        "    <div id='textinfo'>\n".
        "      <span> Kort ID  </span> {$card->code} <br>\n".
        "      <span> W-nummer </span> {$card->wnr} <br>\n".
        "      <span> Navn     </span> {$card->name} <br>\n".
        "      <span> Lokation </span> {$card->lokation} <br>\n".
        "      <span> Medlem   </span> ".($card->member?"Ja":"Nej")." <br>\n".
        "    </div>\n".
        "    <div id='thumbinfo'>\n".
        ($card->thumbnail ? "<img class='thumbnail' src='{$card->thumbnail}'>\n" : "").
        "    </div>\n".
        "    <form action='index.php'> <input type='submit' value='OK'> </form>\n".
        "    <form action='checkmember.php' method='POST'> <input type='hidden' name='edit' value='1'><input type='hidden' name='code' value='{$card->code}'><input type='submit' value='Edit'> </form>\n".
        ($card->member?"":"    <form action='betalt.php' method='POST'> <input type='hidden' name='code' value='{$card->code}'><input type='submit' value='Betalt' id='knapbetalt'> </form>\n").
        "  </div>\n".
        "<body>\n".
        "</html>\n");
}

?>


