var Scanner;

// jquery way: $(document).ready(function(){
document.addEventListener('DOMContentLoaded', function(){ 
  Scanner=document.getElementById("codeinput");
  if (Scanner) {
    Scanner.addEventListener("focus",focusin);
    Scanner.addEventListener("focusout",focusout);
    Scanner.addEventListener("keyup",keypress);
  }
}, false);

function focusin() {
  Scanner.placeholder="Ready...";
}

function focusout() {
  Scanner.placeholder="Not ready!";
  Scanner.value = "";
}

function keypress() {
  var info
  info=document.getElementById("infodiv");
  if (info) info.style.visibility="hidden"
}
