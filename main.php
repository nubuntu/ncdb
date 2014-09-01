<?php
class NCDB{
	private $secret;
	private $config;
	public function run(){
		if(!file_exists('config.php')){
			$this->install();
		}
	}
	private function install(){
		if(!isset($_SESSION['install'])){
			$_SESSION['install']=1
		}
		switch($_SESSION['install']){
			case 1:
				
			break;
			case 2:
			break;
		}
	}
function encrypt($sValue)
{
    return rtrim(
        base64_encode(
            mcrypt_encrypt(
                MCRYPT_RIJNDAEL_256,
                $this->secret, $sValue, 
                MCRYPT_MODE_ECB, 
                mcrypt_create_iv(
                    mcrypt_get_iv_size(
                        MCRYPT_RIJNDAEL_256, 
                        MCRYPT_MODE_ECB
                    ), 
                    MCRYPT_RAND)
                )
            ), "\0"
        );
}

function decrypt($sValue)
{
    return rtrim(
        mcrypt_decrypt(
            MCRYPT_RIJNDAEL_256, 
            $this->secret, 
            base64_decode($sValue), 
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
}
$ncdb=new NCDB;
$ncdb->run();
?>