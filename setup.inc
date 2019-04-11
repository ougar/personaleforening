<?php
session_start();

require_once("kdb.inc");
require_once("docheader.inc");
require_once("getvar.inc");
require_once("card.inc");

$get=new GetVars();
$get->allowpost=true;

$dbh=MyDatabase::connect("diverse2");

$header=new DocHeader();
$header->set_html5();
$header->set_title("Medlemskartotek");
$header->add_css("table.css");
$header->add_css("member.css");

// Ensure that user is authorized
function need_admin() {
  if (!array_key_exists("admin",$_SESSION)) {
    gologin();
  }
}

function gologin() {
  global $header;

  $header->display();

  $error="";
  if (array_key_exists("error",$_SESSION)) {
    $error=$_SESSION["error"];
    unset($_SESSION['error']);
  }

  print("<body>\n".
        "  <div id='logindiv'>\n".
        "    <form id='loginform' action='login.php' method='POST'>\n".
        "      <span> <label for='username'> Login </label> </span> <input type='text' id='username' name='username'><br>\n".
        "      <span> <label for='password'> Password </label> </span> <input type='password' id='password' name='password'><br>\n".
        "      <input type='submit' value='Login'>\n".
        "    </form>\n".
        "    <div class='error'>$error</div>\n".
        "  </div>\n".
        "</body>\n".
        "</html>\n");
  die();
}

need_admin();

function has_error() {
  return(array_key_exists("error",$_SESSION) && $_SESSION["error"]);
}
function has_info() {
  return(array_key_exists("info",$_SESSION) && $_SESSION["info"]);
}

function get_error() {
  if (has_error())
    return($_SESSION["error"]);
  else
    return("");
}

function reset_error() {
  $_SESSION["error"]="";
  unset($_SESSION["error"]);
}

function get_info() {
  if (has_info())
    return($_SESSION["info"]);
  else
    return("");
}

function reset_info() {
  $_SESSION["info"]="";
  unset($_SESSION["info"]);
}
