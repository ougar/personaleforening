<?php
require_once("kdb.inc");
class Card {
  
  public $id;
  public $code;
  public $wnr;
  public $name;
  public $member;
  public $lokation;
  public $obgpers=false;
  public $thumbnail=null;
  private $table="skat_personale";

  public function __construct() {
  }

  public function getFromID($id) {
    $res=Database::get_handle()->kquery(sprintf("select * from {$this->table} where id='%d' and enabled",$id));
    return($this->populate($res));
  }

  public function getFromCode($code) {
    $res=Database::get_handle()->kquery(sprintf("select * from {$this->table} where code='%s' and enabled",$code));
    return($this->populate($res));
  }

  public function getFromWnr($wnr) {
    if ($wnr[0]=="w" or $wnr[0]=="W") $wnr=substr($wnr,1);
    $res=Database::get_handle()->kquery(sprintf("select * from {$this->table} where wnr='%s' and enabled",$wnr));
    return($this->populate($res));
  }

  private function set_image() {
    $name = sprintf("img/w%05d.jpg",$this->wnr);
    if (file_exists($name)) $this->thumbnail=$name;
  }

  private function populate($res) {
    if (!$data=$res->fetch_assoc()) return(false);
    $this->id=$data["id"];
    $this->wnr=$data["wnr"];
    $this->code=$data["code"];
    $this->name=$data["name"];
    $this->member=$data["member"];
    $this->lokation=$data["lokation"];
    $this->set_lokation();
    $this->set_image();
    return(true);
  }

  private function set_lokation() {
    if (preg_match("/Havnehol/",$this->lokation) or
        preg_match("/Kalvebod/",$this->lokation) or
        preg_match("/Præstøga/",$this->lokation) or
        preg_match("/Østbaneg/",$this->lokation)) $this->obgpers=true;
  }

  public function update() {
    $dbh=Database::get_handle();
    $sql=sprintf("update {$this->table} set name='%s', wnr=%d, code=%s, member=%d where id=%d",
      $this->name,$this->wnr,$this->code,$this->member,$this->id);
    $dbh->kquery($sql);
    if ($dbh->affected_rows>0) $this->updated();
  }

  public function insert() {
    $sql=sprintf("insert into {$this->table} (name,wnr,code,member,lastchange,lastscan) values ('%s',%d,%s,%d,current_timestamp(),current_timestamp())",
      $this->name,$this->wnr,$this->code,$this->member);
    Database::get_handle()->kquery($sql);
    $this->id=Database::get_handle()->insert_id;
  }

  public function insertupdate() {
    if ($this->id) $this->update();
    else $this->insert();
  }

  public function query($sql) {
    return(Database::get_handle()->kquery($sql));
  }
  
  public function delete() {
    $this->query(sprintf("update {$this->table} set enabled=0, lastchange=current_timestamp() where id='%d'",$this->id));
  }

  public function updated() {
    Database::get_handle()->kquery("update {$this->table} set lastchange=current_timestamp() where id={$this->id}");
  }

  public function scanned() {
    Database::get_handle()->kquery("update {$this->table} set lastscan=current_timestamp() where id={$this->id}");
  }

  public function payment() {
    Database::get_handle()->kquery("update {$this->table} set member=1, payment=current_timestamp() where id={$this->id}");
  }

  public function validName() {
    return(!preg_match("/[^\p{L} æøåÆØÅ]/",$this->name));
  }

  public function validWnr() {
    if (!preg_match("/[wW]?[\d]{5}/",trim($this->wnr))) return(false);
    $this->wnr=(int)preg_replace("/[^\d]/","",$this->wnr);
    if ($this->wnr<1 || $this->wnr>99999) return(false);
    return(true);
  }

  // Check that code is a 10 digit number
  public function validCode() {
    return(preg_match("/^\d{6,12}$/",trim($this->code)));
  }

  public function validError() {
    if (!$this->validCode()) return("Invalid card code: {$this->code}");
    if (!$this->validWnr())  return("Invalid W-nr: {$this->wnr}");
    if (!$this->validName()) return("Invalid name: {$this->name}");
    return(false);
  }
}
?>
