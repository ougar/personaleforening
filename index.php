<?php
require("setup.inc");

need_admin();

$header->add_javascript("member.js");
$header->display();

$error=get_error();
reset_error();
$info=get_info();
reset_info();
if ($error) $info="<div class='error'>$error</div>";
else        $info="<div class='info'>$info</div>";


?>

<body>
  <div id='scandiv'>
    Scan kort<br>
    <form method='POST' action="checkmember.php"> <input type='hidden' name='scan' value='1'> <input id='codeinput' name='code' placeholder='ready...' autofocus='autofocus'> </input> </form>
    <p><span> <a href='showmembers.php'>Vis alle medlemmer</a> </span> &nbsp; &nbsp; <span> <a href='listall.php'>Vis alle ansatte (MANGE)</a> </span>
    <?php echo $info?>
  </div>
</body>

</html>
