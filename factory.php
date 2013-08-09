<?php 

/**
 * We wzorcu Fabryka, metody fabryki definiują jakie funkcje powinny być dostępne w nieabstrakcyjnej 
 * lub koncretnej fabryce. Te funkcje muszą mieć możliwość tworzenia obiektów, które są rozszeżeniami 
 * konkretnej klasy. Które dokładnie podklasy będą tworzone będzie zależeć od parametru przekazywanego 
 * do funkcji. 
 * 
 * W tym przykładzie mamy metody fabryki, AbstractFactoryMethod, która określa funkcję MakePHPBook($param).
 *
 * konkretna klasa fabryki OReillyFactoryMethod rozszerza AbstractFactoryMethod i potrafi stworzyć 
 * konkretne rozszerzenie klasy AbstractPHPBook, na podstawie wartości $param.
 */
 
abstract class AbstractFactoryMethod{
    abstract function makePHPBook($param);
}

class OReillyFactoryMethod extends AbstractFactoryMethod {
    
    private $context = "OReilly";
    
    function makePHPBook($param) {
        $book = null;
        
        switch ($param){
            case ('us'):
                $book = new OreilyPHPbook;
            break;
            case "other":
                $book = new SamsPHPBook;
                break;
            default:
                $book = new OReillyPHPBook;
                break;
        }
        return $book;                
    }
}

class SamsFactoryMethod extends AbstractFactoryMethod {
    private $context = "Sams";
    function makePHPBook($param) {
        $book = NULL;
        switch ($param) {
            case "us":
                $book = new SamsPHPBook;
                break;
            case "other":
                $book = new OReillyPHPBook;
                break;
            case "otherother":
                $book = new VisualQuickstartPHPBook;
                break;
            default:
                $book = new SamsPHPBook;
                break;
        }
        return $book;
    }
}
abstract class AbstractBook {
    abstract function getAuthor();
    abstract function getTitle();
}

abstract class AbstractPHPBook {
    private $subject = "PHP";
}

 ?>