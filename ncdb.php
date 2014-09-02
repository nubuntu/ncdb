<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

define('NCDB',1);
session_start();
class Table{
	var $records;
	function __construct($array){
		$this->records=$array;
	}
	function equals($key,$val){
		$rows = $this->records;
		$array=array();
		foreach($rows as $k => $v){
			if($v->$key==$val){
				$array[]=$v;
			}
		}
		return $this->records=$array;
	}
}

class NCDB{
	public $secret="22222";
	public $config;
	public $request;
	public function run(){
		if(!file_exists('config.php')){
			$this->install();
		}else{
			$this->config = $this->sread("config");
		}
		$this->parseRequest();
		if(isset($this->request->cmd)){
			$cmd = $this->request->cmd;
			if(method_exists($this,$cmd)){
				$this->$cmd();
			}
		}
	}
	private function install(){
		include("install.php");
	}
	function encrypt($value){
		return $this->aesEncrypt($this->config->secret,$value);
	}
	function decrypt($value){
		return $this->aesDecrypt($this->config->secret,$value);
	}
	public function aesEncrypt($secret,$value){
    	return rtrim(
            mcrypt_encrypt(
                MCRYPT_RIJNDAEL_256,
                $secret, $value, 
                MCRYPT_MODE_ECB, 
                mcrypt_create_iv(
                    mcrypt_get_iv_size(
                        MCRYPT_RIJNDAEL_256, 
                        MCRYPT_MODE_ECB
                    ), 
                    MCRYPT_RAND)
                )
            , "\0"
        );
	}
	public function aesDecrypt($secret,$value){
	    return rtrim(
        mcrypt_decrypt(
            MCRYPT_RIJNDAEL_256, 
            $secret, 
            $value, 
            MCRYPT_MODE_ECB,
            mcrypt_create_iv(
                mcrypt_get_iv_size(
                    MCRYPT_RIJNDAEL_256,
                    MCRYPT_MODE_ECB
                ), 
                MCRYPT_RAND
            )
        ), "\0"
    	);
	}
	public function read($file){
		$handle = fopen($file.".php", 'r');
		$data = fread($handle,filesize($file.".php"));
		$f=str_replace("<?php if(!defined('NCDB'))die('NCDB-NoSQL');?>","",$data);		
		return json_decode($this->decrypt($f));
	}
	function write($file,$array){
		$handle = fopen($file.'.php', 'w') or die('Cannot open file:  '.$file);
		$array = "<?php if(!defined('NCDB'))die('NCDB-NoSQL');?>".$this->encrypt(json_encode($array));
		fwrite($handle, $array);
	}
	function sread($file){
		$handle = fopen($file.".php", 'r');
		$data = fread($handle,filesize($file.".php"));
		$f=str_replace("<?php if(!defined('NCDB'))die('NCDB-NoSQL');?>","",$data);		
		return json_decode($this->aesDecrypt($this->secret,$f));
	}
	function swrite($file,$array){
		$handle = fopen($file.'.php', 'w') or die('Cannot open file:  '.$file);
		$array = "<?php if(!defined('NCDB'))die('NCDB-NoSQL');?>".$this->aesEncrypt($this->secret,json_encode($array));
		fwrite($handle, $array);
	}
	function parseRequest(){
		$this->request=new stdClass();
		$file = pathinfo(__FILE__, PATHINFO_FILENAME).".php/";
		$file = explode($file,$_SERVER['PHP_SELF']);
		$request = explode("/", $file[1]);
		$this->request->cmd=$request[0];
		for($i=1;$i<count($request);$i++){
			$request[$i]."<br/>";
			$var=explode(":",$request[$i]);
			$this->request->$var[0]=isset($var[1])?$var[1]:null;
		}
	}
	function connect(){
		$rows=$this->table('system/user')->equals("username","root");
		var_dump($rows);			
	}
	function select($table){
		return $this->read($table);
	}
	function table($tbl){
		return new Table($this->select($tbl));
	}
	

}
$ncdb=new NCDB;
$ncdb->run();
?>