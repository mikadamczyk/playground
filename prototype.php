<?php 
abstract class Book{
	protected $title;
	protected $topic;
	abstract function __clone();
	
	public function getTitle(){
		return $this->title;
	}
	public function setTitle($title){
		$this->title = $title;
	}
	
	public function getTopic(){
		return $this->topic;
	}
}

class PHPBook extends Book{
	public function __construct(){
		$this->topic = 'PHP';
	}
	function __clone(){}
}
class JAVABook extends Book{
	public function __construct(){
		$this->topic = 'Java';
	}
	public function __clone(){}
}

$phpbook1 = new PHPBook();
$phpbook1->setTitle('Ksiązka PHP 1');
$phpbook2 = clone $phpbook1;
$phpbook2->setTitle('Książka PHP 2');
$javabook1 = new JAVABook();
$javabook1->setTitle("Ksiazka1");
$javabook2 = clone $javabook1;
$javabook2->setTitle("Ksiazka2");

echo "Kategoria: ".$phpbook1->getTopic()." Tytul: ".$phpbook1->getTitle()."<br />";
echo "Kategoria: ".$phpbook2->getTopic()." Tytul: ".$phpbook2->getTitle()."<br />";
echo "Kategoria: ".$javabook1->getTopic()." Tytul: ".$javabook1->getTitle()."<br />";
echo "Kategoria: ".$javabook2->getTopic()." Tytul: ".$javabook2->getTitle()."<br />";
?>