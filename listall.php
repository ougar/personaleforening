<?php
require("setup.inc");

$data=$dbh->kquery("select *,if(member,'Ja','Nej') as medlem from skatcards_all where enabled order by name");

$header->display();
print("<body>\n".
      "<input type='text' id='searchfield' onkeyup='search()' placeholder='Søg... (deaktiveret)'>\n".
      "<p>\n".
      "<table>\n".
      "  <thead> <tr> <th> W-nummer <th> Navn <th> Medlem <th> Oprettet <th> </thead>\n".
      "  <tbody>\n");
      while ($row=$data->fetch_assoc()) {
        printf("    <tr> <td> %05d <td> %-20s <td> %3s <td> %s <td> \n".
               "         <form action='checkmember.php' method='POST'> <input type='hidden' name='edit' value='1'><input type='hidden' name='code' value='%s'><input type='submit' value='Edit'> </form>\n".
               "         <form action='delete.php' method='POST' onsubmit=\"return confirm('Er du sikker på at du vil slette denne person?');\"> <input type='hidden' name='id' value='%d'><input type='submit' value='Slet'> </form>\n",
               $row['wnr'],$row['name'],$row['medlem'],$row['created'],$row['code'],$row['id']);
     }
print("  </tbody>\n".
      "</table>\n".
      "</body>\n".
      "<p>\n".
      "<span> <a href='index.php'> Tilbage </a>\n".
      "</html>\n");

function pdbtable($data, $fields, $indent="") {
  $n="\n$indent";
  $p="$indent<table>$n".
     "  <thead> <tr>";
  foreach ($fields as $f) $p.=" <th> $f";
  $p.=" </thead>$n".
      "  <tbody>$n";
  while ($row=$data->fetch_assoc())
    $p.=$indent."    ".prow($row,array_keys($fields))."\n";
  $p.="  </tbody>$n</table>\n";
  return($p);
}

function prow($data, $fields) {
  $p="<tr>";
  foreach ($fields as $f) $p.=" <td> ".$data[$f];
  return($p);
}

?>
