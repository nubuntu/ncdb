<?php
session_start();
class NCDB{
	public $secret="22222";
	public $config;
	public function run(){
		if(!file_exists('config.php')){
			$this->install();
		}else{
			$file = file_get_contents('config.php');
			$config = json_decode($this->aesDecrypt($this->secret,$file));
			$this->config = $config;
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
		$f = file_get_contents($file.".php");
		return json_decode($this->decrypt($f));
	}
	function write($path,$array){
		$array = $this->encrypt(json_encode($array));
		file_put_contents($path.".php",$array);
	}
}

$ncdb=new NCDB;
$ncdb->run();
?>