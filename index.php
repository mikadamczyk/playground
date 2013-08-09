<?php 

class Config{
	
	private static $instance;
	private $config = array(
			'login'=> 'mojlogin'
			,'password' => 'hasło'
			,'language' => 'pl'
			);
	private function __construct(){}
	private function __clone(){}

	public static function getInstance(){
		if(self::$instance === null){
			self::$instance = new Config();
		}
		return self::$instance;
	}
	
	public function setLanguage($lang){
		$this->config['language'] = $lang;
	}
	
	public function getLanguage(){
		return $this->config['language'];
	}
	
}

$conf1 = Config::getInstance();
echo $conf1->getLanguage();
$conf2 = Config::getInstance();
$conf2->setLanguage('en');
echo $conf2->getLanguage();
?>