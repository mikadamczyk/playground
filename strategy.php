<?php 
interface Tax{
	public function count($net);
}
class TaxPL implements Tax{
	public function count($net){
		return 0.23*$net;
	}
}
class TaxEN implements Tax{
	public function count($net){
		return 0.15*$net;
	}
}
class TaxDE implements Tax{
	public function count($net){
		return 0.3*$net;
	}
}
class TexError implements Tax{
	public function count($net){
		return 'TexError';
	}
}

class Context{
	private $strategy;
	
	public function setCountry($country){
		$className = 'Tax'.$country;
		if(class_exists($className)){
			$this->strategy = new $className();
		}else{
			$this->strategy = new TexError();
		}
	}
	public function getTax() {
		return $this->strategy;
	}
}
$tax = new Context();
$tax->setCountry("RU");
echo $tax->getTax()->count(100); // wyswietla "23"
$tax->setCountry("EN");
echo $tax->getTax()->count(100); // wyswietla "15"
$tax->setCountry("DE");
echo $tax->getTax()->count(100); // wyswietla "30"
?>