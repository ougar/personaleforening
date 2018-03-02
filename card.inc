<?php
require_once("kdb.inc");
class Card {
  
  public $id;
  public $code;
  public $wnr;
  public $name;
  public $member;
  private $table="skatcards_all";

  public function __construct() {
  }

  public function getFromID($id) {
    $this->id=$id;
    $res=Database::get_handle()->kquery(sprintf("select * from {$this->table} where id=%d and enabled",$id));
    if (!$data=$res->fetch_assoc()) return(false);
    $this->id=$data["id"];
    $this->code=$data["code"];
    $this->name=$data["name"];
    $this->member=$data["member"];
    return(true);
  }

  public function getFromCode($code) {
    $this->code=$code;
    $res=Database::get_handle()->kquery(sprintf("select * from {$this->table} where code=%s and enabled",$code));
    if (!$data=$res->fetch_assoc()) return(false);
    $this->id=$data["id"];
    $this->code=$data["code"];
    $this->wnr=$data["wnr"];
    $this->name=$data["name"];
    $this->member=$data["member"];
    return(true);
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
