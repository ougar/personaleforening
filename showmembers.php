<?php
require("setup.inc");

$data=$dbh->kquery("select *,if(member,'Ja','Nej') as medlem from skat_personale where enabled and member order by wnr");

$antal=$data->num_rows;

$header->add_css("http://ougar.dk/javascript/sortable-tables.min.css");
$header->add_javascript("http://ougar.dk/javascript/sortable-tables.min.js");
$header->display();
print("<body>\n".
      "<input type='text' id='searchfield' onkeyup='search()' placeholder='Søg... (deaktiveret)'>\n".
      "<p>\n".
      "I alt $antal medlemmer\n".
      "<p>\n".
      "<table class='sortable-table'>\n".
      "  <thead> <tr> <th> W-nummer <th> Navn <th> Medlem <th> Indmeldt </thead>\n".
      "  <tbody>\n");
      while ($row=$data->fetch_assoc()) {
        printf("    <tr> <td> w%05d <td> %-20s <td> %3s <td> %s\n",
               $row['wnr'],$row['name'],$row['medlem'],$row['payment']);
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
