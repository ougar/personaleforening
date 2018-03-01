var Scanner;

// jquery way: $(document).ready(function(){
document.addEventListener('DOMContentLoaded', function(){ 
  Scanner=document.getElementById("codeinput");
  Scanner.addEventListener("focus",focusin);
  Scanner.addEventListener("focusout",focusout);
}, false);

function focusin() {
  Scanner.placeholder="Ready...";
}

function focusout() {
  Scanner.placeholder="Not ready!";
  Scanner.value = "";
}
