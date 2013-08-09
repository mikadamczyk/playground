<?php

/**
 * Socms
 *
 * LICENSE
 *
 * This source file is subject to the Closed Source license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://socms.sointeractive.pl/license
 *
 * @category   Socms
 * @package    Socms_frontend_controllers
 * @copyright  Copyright (c) 2008-2009 SoInteractive (http://www.sointeractive.pl)
 * @license    http://socms.sointeractive.pl/license	Closed Source License
 */

require_once 'application/modules/dywersyfikator/frontend/models/BaseModel.php';

/**
 * @category   Socms
 * @package    Socms_frontend_controllers
 * @copyright  Copyright (c) 2008-2009 SoInteractive (http://www.sointeractive.pl)
 * @license    http://socms.sointeractive.pl/license	Closed Source License
 */
class Dywersyfikator_Module_Frontend_BaseController extends Socms_Module_Controller_Frontend
{
    
    /**
     * @var Service\Form\Service
     */
    private $_serviceForm;
    
    /**
     * @var Service\Validate\Service
     */
    private $_serviceValidate;
    
    /**
     * @var Service\Filter\Service
     */
    private $_serviceFilter;
    
    public function init()
    {
        parent::init();
    
        require_once CMS_DIR . '../Service/Core/Autoload.php';
        new Service\Core\Autoload();
    
        $this->_serviceForm = \Service\Core\Manager::getsService('Form',null,realpath(CMS_DIR ."/../site/config/frontend/services.xml"));
        $this->_serviceValidate = \Service\Core\Manager::getsService('Validate',null,realpath(CMS_DIR ."/../site/config/frontend/services.xml"));
        $this->_serviceFilter = \Service\Core\Manager::getsService('Filter',null,realpath(CMS_DIR ."/../site/config/frontend/services.xml"));
    }
        
    public function indexAction()
    {
        Zend_Session::start();
        
        $iTreeId = $this->getPage()->getId();
        
        if($this->getRequest()->isPost()) {
            
            $config = array(
                    'structure'=>array(),
            );
            $postData = $this->getRequest()->getPost();
            foreach($postData['products'] as $pageId => $page){
                foreach($page as $groupId => $group){
                    foreach($group as $ctrlName => $ctrlValue){
                        $config['structure']['pages'][$pageId]['groups'][$groupId]['controls'][$ctrlName]['name'] = $ctrlName;
                        $config['structure']['pages'][$pageId]['groups'][$groupId]['controls'][$ctrlName]['value'] = $ctrlValue;
                    }
                }
            }
            $pushedData = array(
                    'id' => 8,
                    'tree_id' => 15,
                    'date_add' => '2013-06-05 10:39:01',
                    'title' => '',
                    'structure' => '',
                    'config' => json_encode($config),
                    'disabled' => 0,
                    'version' => 44);
            $sessionModel = $this->_serviceForm->getFormStorageContener('DF', $pushedData);
            
            $formObj = $sessionModel->getForm();            
            
            $formPostData = $postData['products'];
//          $sessionModel->setPostData($formPostData, $isValid, $formPageIdx, $this->_serviceValidate->getErrors());
            $sessionModel->setPostData($formPostData, true, '0');
// echo '<pre>';
// print_r($sessionModel);
// echo '</pre>';
// die();

//             echo '<pre>';
            #print_r(Zend_Json::decode('{"products":[{"created_at":"","title":"Eko.Lokata","description":{"main":"Zarobisz 10.000 zł","footer":"Szacowana kwota w skali roku"},"link":{"label":"Dowiedz się więcej","href":"/news"},"amount":{"min":0,"max":"1000"},"regulations":[{"label":"Dyspozycja dotycząca sposobu przesyłania zmian regulacji:","text":"Zmiany (ewentualnie nową treść) w: „Ogólnych warunkach otwierania i prowadzenia rachunków oszczędnościowych i oszczędnościowo-rozliczeniowych dla osób fizycznych, „Regulaminie otwierania i  prowadzenia xxxxx x  xx xxxxx"}],"target_results":{"title":"Odsetki od kwoty lokaty proszę przekazać:","elements":[{"title":"Wybierz rachunek","additional_input":{"show":0,"label":""}},{"title":"Nie mam rachunku w Banku Ochrony Środowiska. Proszę o założenie rachunku ROR \"Konto bez Kantów\", na który będą przelewane odsetki od lokaty.","additional_input":{"show":0,"label":""}},{"title":"Na rachunek prowadzony w Banku Ochrony Środowiska S.A","additional_input":{"show":true,"label":"Numer rachunku"}}]},"image":"/files/file_4/eko_lokatal.png"},{"created_at":"","title":"Nazwa produktu #2","description":{"main":"Zarobisz 10.000 zł ","footer":"Szacowana kwota w skali roku"},"link":{"label":"Dowiedz się więcej","href":"/asdlozz-asdf"},"amount":{"min":0,"max":"1000"},"regulations":[{"label":"Dyspozycja dotycząca sposobu przesyłania zmian regulacji:","text":"Zmiany (ewentualnie nową treść) w: „Ogólnych warunkach otwierania i prowadzenia rachunków oszczędnościowych i oszczędnościowo-rozliczeniowych dla osób fizycznych, „Regulaminie otwierania i  prowadzenia xxxxx x  xx xxxxx"}],"target_results":{"title":"Odsetki od kwoty lokaty proszę przekazać:","elements":[{"title":"Wybierz rachunek","additional_input":{"show":0,"label":""}},{"title":"Nie mam rachunku w Banku Ochrony Środowiska. Proszę o założenie rachunku ROR \"Konto bez Kantów\", na który będą przelewane odsetki od lokaty.","additional_input":{"show":0,"label":""}},{"title":"Na rachunek prowadzony w Banku Ochrony Środowiska S.A","additional_input":{"show":true,"label":"Numer rachunku"}}]}},{"created_at":"","title":"Nazwa produktu #3","description":{"main":"Zarobisz 15.000 zł ","footer":"Szacowana kwota w skali roku"},"link":{"label":"Dowiedz się więcej","href":"/lokaty"},"amount":{"min":0,"max":"500"},"regulations":[{"label":"Dyspozycja dotycząca sposobu przesyłania zmian regulacji:","text":"Zmiany (ewentualnie nową treść) w: „Ogólnych warunkach otwierania i prowadzenia rachunków oszczędnościowych i oszczędnościowo-rozliczeniowych dla osób fizycznych, „Regulaminie otwierania i  prowadzenia xxxxx x  xx xxxxx"}],"target_results":{"title":"Odsetki od kwoty lokaty proszę przekazać:","elements":[{"title":"Wybierz rachunek","additional_input":{"show":0,"label":""}},{"title":"Nie mam rachunku w Banku Ochrony Środowiska. Proszę o założenie rachunku ROR \"Konto bez Kantów\", na który będą przelewane odsetki od lokaty.","additional_input":{"show":0,"label":""}},{"title":"Na rachunek prowadzony w Banku Ochrony Środowiska S.A","additional_input":{"show":true,"label":"Numer rachunku"}}]}}]}'));
            
            
//             print_r($this->getRequest()->getPost());
//             exit();
            
        }
        
        
        $aProducts = $oModel->getProducts($iTreeId);
        $this->view->products = $aProducts;
    }
}
