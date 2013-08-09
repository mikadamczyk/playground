<?php 

class FunnyException extends Exception{}
class IntException extends FunnyException{}

try{
    $zmienna = 'jasio';
    if( !is_int($zmienna) ){
        throw new IntException(
                'zmienna nie jest typu integer');
    }
    echo 'to sie nie wykona gdy wystapi wyjatek,
    interpreter od razu przejdzie do blokow catch';
}
catch(FunnyException $wyjatek){
    echo 'Stalo sie cos zlego!'.$wyjatek->getMessage();
}
catch(Exception $wyjatek){
    echo 'Standardowy wyjatek'.$wyjatek->getMessage();
}
catch(IntException $wyjatek){
    echo 'Nastapil blad typu zmiennej
         '.$wyjatek->getMessage();
}

?>