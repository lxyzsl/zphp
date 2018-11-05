<?php
namespace control\core;

class ShowTable{
    private $_myTable=array();
    private $_tableResult="";
    private static $_myself=null;
    public function __construct(){
        $_myTable['header']=null;
        $_myTable['title']=null;
    }
    
    public static function getInstance(){
        if (self::$_myself instanceof ShowTable){
            return self::$_myself;
        } else {
            self::$_myself=new self();
            return self::$_myself;
        }
    }
    
    public function setHeader($header=null){
        if (empty($header)){
            return false;
        }
        $this->_myTable['header']=$header;   
    }
    public function setTitle($title=null){
        if (empty($title)){
            return false;
        }
        $this->_myTable['title']=$title;    
    }
    public function setBody($body=null){
        if (empty($body)){
            return false;
        }
        $this->_myTable['body']=$body;
    }
    private function arrayToStr(&$array){
        $str="";
        foreach ($array as $arrayKey => $arrayValue){
            if ($arrayKey!="th" && $arrayKey!="td"){
                $str.=' '.$arrayKey.'="'.$arrayValue.'" ';
            }
            
        }
        return $str;
    }
    private function arrayToTab(&$array , $t="td"){
        $str="";
        foreach ($array as $arrayKey => $arrayValue){
            $str.="<".$t;
            if (is_array($arrayValue)){
                $str.=$this->arrayToStr($arrayValue);
                $str.=">";
                $str.=$arrayValue[$t];
            } else {
                $str.=">";
                $str.=$arrayValue;
            }
            $str.="</".$t.">";
        }
        return $str;
    }
    private function tableHeader(){
        $header="";
        if (is_array($this->_myTable['header'])) {
            $header.=$this->arrayToStr($this->_myTable['header']);
        } else {
            $header.=$this->_myTable['header'];
        }
        $this->_tableResult.="<table ".$header.">";
    }
    
    private function tableFooter(){
        if(!empty($_myTable['header'])){
            $this->_tableResult.="</table>";
        }
        
    }
    
    private function tableTitle(){
        $title="<tr>";
        if (is_array($this->_myTable['title'])) {
            $title.=$this->arrayToTab($this->_myTable['title'], "th");
        } else {
            $title.=$this->_myTable['title'];
        }
        $title.="</tr>";
        $this->_tableResult.=$title;
    }
    
    private function tableBody($sort=false){
        $body="";
        $i=0;
        foreach ($this->_myTable['body'] as $bodyKey =>$bodyValue){
            $body.="<tr>";
            if ($sort){
            	$i++;
            	$body.="<td>".$i."</td>";
            }
            if (is_array($bodyValue)) {
                $body.=$this->arrayToTab($bodyValue, "td");        
            } else {
                $body.=$bodyValue;
            }
            $body.="</tr>";
        }
        $this->_tableResult.=$body;
    }
    public function getTable($sort=false){
        if (!empty($this->_myTable['header'])){
            $this->tableHeader();
        }
        if (!empty($this->_myTable['title'])){
            $this->tableTitle();
        }
        $this->tableBody($sort);
        if (!empty($this->_myTable['header'])){
            $this->tableFooter();
        }
        return $this->_tableResult;
    }
}