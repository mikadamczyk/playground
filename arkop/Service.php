<?php
namespace Service\Widget;

use Service\Core\Manager;

use Service\Widget;

/**
 * Main Class for Widget Service 
 * @package Service\Widget
 * @author Łukasz Miłkowski <lmilkowski@sosoftware.pl>
 * @refactorization Bartosz Cieplik
 */
class Service extends \Service\Core\Service implements Widget\DataAccess {
    
    private $_typeFamily;
    protected $name = "Widget";

    public function __construct($configuration = null) {
        parent::__construct($configuration);
        $this->_typeFamily = null;
    }
    
    public function _Widget($Widget  = null, $config = null) {
        
        if($this->_collection) {
            
            $this->getDatasource('database')->getWidget($Widget, $config);
            
            $this->_collection->add($this->getDatasource('database'), new Widget\Model\Widget());
            
            foreach( $this->_collection as $widget) {
//                   $this->getDatasource()->getTemplates($widget);
//                   $obj = $this->getDatasource()->getObject();
//                   $this->getDatasource()->setObject(null);
//                   $template = $this->getDatasource()->translate($this->getDatasource()->processData($obj), new Widget\Model\Template());
//              
//                   if($template && $template->getId()) {
//                       /* $template->setText_content( $this->getDatasource('filesystem')->getFileContents(  lcfirst($template->widget_name) . DIRECTORY_SEPARATOR .
//                                                                                                 'frontend' . DIRECTORY_SEPARATOR .
//                                                                                                 'views' . DIRECTORY_SEPARATOR .
//                                                                                                 'scripts'  . DIRECTORY_SEPARATOR .
//                                                                                                    $template->name . ".phtml") );*/
//                                                                                               //$template->name . ".phtml") );
//                       
//                       try{
//                            $template->setText_content(  $this->getDatasource('filesystem')->getFileContents( lcfirst($template->widget_name) . DIRECTORY_SEPARATOR .
//                                                                                                 'frontend' . DIRECTORY_SEPARATOR .
//                                                                                                 'views' . DIRECTORY_SEPARATOR .
//                                                                                                 'scripts'  . DIRECTORY_SEPARATOR .
//                                                                                                 $template->name . ".soiml")
//                            );
//                       } catch( \Service\Core\Exception $e) {
//                       }
//                       
//                       try{
//                       $template->setPhp_content(  $this->getDatasource('filesystem')->getFileContents( lcfirst($template->widget_name) . DIRECTORY_SEPARATOR .
//                                                                                                 'frontend' . DIRECTORY_SEPARATOR .
//                                                                                                 'views' . DIRECTORY_SEPARATOR .
//                                                                                                 'scripts'  . DIRECTORY_SEPARATOR .
//                                                                                                 $template->name . ".phtml")
//                        );
//                        } catch( \Service\Core\Exception $e) {
//                        }   
//                   
//                        try{
//                            $template->setHtml_content(  $this->getDatasource('filesystem')->getFileContents( lcfirst($template->widget_name) . DIRECTORY_SEPARATOR .
//                                                                                                 'frontend' . DIRECTORY_SEPARATOR .
//                                                                                                 'views' . DIRECTORY_SEPARATOR .
//                                                                                                 'scripts'  . DIRECTORY_SEPARATOR .
//                                                                                                 $template->name . ".html")
//                            );
//                        } catch( \Service\Core\Exception $e) {
//                        }
//                   }
                   $widget->setData($widget->getData());
//                   $widget->setTemplate_o($template);
                   
            }
            
            $this->_collection = $this->getDatasource()->parseWidgetNodeConfig($this->_collection, $Widget->tree_ip);
            
            return $this->_collection;
            
        } else {
            
            $this->getDatasource()->getWidget($Widget, $config);
            $obj = $this->getDatasource()->getObject();
            $this->getDatasource()->setObject(null);
            return $this->getDatasource()->translate($this->getDatasource()->processData($obj), new Widget\Model\Widget());
            
        }
    }
  
      public function _Templates($Widget  = null, $config = null) {
        
        if($this->_collection) {
            $this->getDatasource()->getTemplates($Widget, $config);
            
            $this->_collection->add($this->getDatasource(), new Widget\Model\Template());
            
            $notUsedTemplates = array();
            
            foreach( $this->_collection as $key => $template) 
            {
                if($template->getId()) 
                {
                   
                   try
                   {
                        if($template->getConfig() == null)
                        {   
                            array_push ($notUsedTemplates, $key); 
                            continue;
                        }
                        
                        if($template->getConfig()->type_id == 0)
                        {
                            if($template->getConfig()->id_widgetsTree != $Widget->id_widgetsTree)
                            {
                                array_push ($notUsedTemplates, $key); 
                                continue;
                            }
                            $baseDir = 'widget_' . $template->getConfig()->id_widgetsTree;
                            
                        } else {
                            
                            if($template->getConfig()->type_id != $Widget->type_id)
                            {
                                array_push ($notUsedTemplates, $key); 
                                continue;
                            }
                            $baseDir = 'type_' . $template->getConfig()->type_id;
                        }
                            
                       
                        $html = $this->getDatasource('filesystem')->getFileContents( lcfirst($template->widget_name) . DIRECTORY_SEPARATOR .
                                                                                      'frontend' . DIRECTORY_SEPARATOR .
                                                                                      'views' . DIRECTORY_SEPARATOR .
                                                                                      'scripts'  . DIRECTORY_SEPARATOR .
                                                                                      $baseDir . DIRECTORY_SEPARATOR .
                                                                                      $template->name . ".phtml");
      
                        $template->setHtml_content($html);
                       
                   } catch( \Service\Core\Exception $e) {
                       
                       
                   }
               }
            }
            
            foreach($notUsedTemplates as $index)
            {
                unset($this->_collection->_collection[$index]);
            }
            
            return $this->_collection;
            
        } else {            
            throw new \Core\Exception("implement me.. please!");
            /*
            $this->getDatasource()->getTemplates($Widget, $config);
            $obj = $this->getDatasource()->getObject();
            $this->getDatasource()->setObject(null);
            return $this->getDatasource()->translate($this->getDatasource()->processData($obj), new Widget\Model\Template());
             * 
             */
        }       
    }
    
     public function _widgetTypes($Widget  = null, $config = null) {    
        if($this->_collection) {
            $this->getDatasource()->getWidgetType($Widget, $config);
            
            $this->_collection->add($this->getDatasource(), new Widget\Model\Type());   
            return $this->_collection;
        } else {            
            $this->getDatasource()->getWidgetType($Widget, $config);
            $obj = $this->getDatasource()->getObject();
            $this->getDatasource()->setObject(null);
            return $this->getDatasource()->translate($this->getDatasource()->processData($obj), new Widget\Model\Type());
        }       
    }
    
    /**
     * Get legacy widget types
     * 
     * @return array
     */
    public function getLegacyTypes()
    {
        return $this->getDatasource()->getLegacyTypes();
    }
    
    /**
     * Alias for edit
     * 
     * @return Service\Core\Collection<Service\Widget\Model\Widget>
     */
    public function getAllWidget() {
        $this->asList();                
        $node = new Widget\Model\Widget();
        $config = array('showAll' => 1);
        return $this->_Widget($node, $config);
    }
    
    /**
     * Alias for edit
     * @param int $input
     * @return Service\Widget\Model\Widget
     */
    public function getWidgetById($input) {
        
        $this->asList();  
        
        $node = new Widget\Model\Widget($input);
        
        $this->getDatasource('database')->getWidget($node);
        $obj = $this->getDatasource()->getObject();
        $this->getDatasource()->setObject(null);
        $widget = $this->getDatasource('database')->translate($this->getDatasource('database')->processData($obj), new Widget\Model\Widget());
        return array($widget);
    }
    
    /**
     * 
     * @param mixed $widget
     */
    public function reindexSearch($widget) {
       // $this->asList();                
        
        /*$widgetNode = new Widget\Model\Widget();
        
        $treeNode = new \Service\Tree\Model\Node();                
        $treeNode->setId($widgetNode->tree_id);       */
        $tree = $this->getDatasource('tree');
        $tree->asList(); 
    //    var_dump($tree);
    //    die();
        $treeNode = $tree->getNode($widget);
        
        //->_getNode($treeNode);
        //->asObject();
        
        
        //$treeNodes = $this->getDatasource('tree')->getDataTree($treeNode)->asList();
        
    }
    
    /**
     * Get widget list for tree_id
     * 
     * @param int $input
     * @return Service\Core\Collection<Service\Widget\Model\Widget>
     */
    public function getPageWidgets($input) {
        $this->asList();  
        
        $node = new Widget\Model\Widget();
        if(!empty($input['tree_id'])){
                $node->setTree_id($input['tree_id']);    
        }
        if(!empty($input['tree_ip'])){
            $node->setTree_ip($input['tree_ip']);
        }
        
        return $this->_Widget($node);
    }
    
    
    /**
    * 
    * Geting all page widgets
    * 
    * @Secured
    * 
    * @return Service\Core\Collection<Service\Widget\Model\Widget>
    */     
    public function getAllPageWidgets($input) {
       
        $this->asList();                
        $node = new Widget\Model\Widget();
        
        $config = array('showAll' => 1);
        
        if(!empty($input['tree_id'])){
            $node->setTree_id($input['tree_id']);    
        }        
        if(!empty($input['tree_ip'])){
            $node->setTree_ip($input['tree_ip']);
        }
                
        return $this->_Widget($node, $config);
    }

    /**
     * Get widgets from node
     * 
     * @Secured
     * 
     * @param type $data
     * @return type
     */
    public function getNodeWidgets($data)
    {
        // Function requires authorization beforehand
        new \Service\Core\Auth\Auth($this, __FUNCTION__);
        
        return $this->getDatasource()->getNodeWidgets($data);
    }
    
    /**
     * Alias for edit
     * @return Service\Core\Collection
     */
    public function getTypes() {
        $this->asList();        
        return $this->_widgetTypes();
    }
    
    
    /**
     * Get widget tempaltes
     * 
     * @Secured
     * 
     * @return Service\Core\Collection<Service\Widget\Model\Template>
     */
    public function getTemplates($input) {
        
        $this->asList();     
        $node = new Widget\Model\Widget();
        if(!empty($input['widget_id'])){
                $node->setWidget_id($input['widget_id']);    
                $node->setType_id($input['type_id']);    
                $node->setId_widgetsTree($input['id_widgetsTree']);
        }
        
        
        return $this->_Templates($node);
    }
      
    
    /**
     * Add new template for widget
     * 
     * @Secured
     * 
     * @param mixed $widget widget throught obj or array from json
     * @return \Service\Widget\Model\Widget
     */
    public function addTemplate($data) {
        
        $w = new \Service\Widget\Model\Widget($data);
        
        if($w->type_id == 0)
        {
            $this->getDatasource('filesystem')->createEmptyFile($w->template . ".phtml", 
                            'custom' . DIRECTORY_SEPARATOR .
                            'frontend' . DIRECTORY_SEPARATOR .
                            'views' . DIRECTORY_SEPARATOR . 
                            'scripts' . DIRECTORY_SEPARATOR .
                            'widget_' . $w->id_widgetsTree
                            );
        } else {

            $this->getDatasource('filesystem')->createEmptyFile($w->template . ".phtml", 
                            'custom' . DIRECTORY_SEPARATOR .
                            'frontend' . DIRECTORY_SEPARATOR .
                            'views' . DIRECTORY_SEPARATOR . 
                            'scripts' . DIRECTORY_SEPARATOR .
                            'type_' . $w->type_id
                            );

        }
        
        $this->getDatasource()->addTemplate($w);
        $obj = $this->getDatasource()->getObject();
        $this->getDatasource()->setObject(null);
        \Service\Core\MongoLog::getInstance()->info('Add new template for widget', $w->getWidgetsTreeIp(), array('widgetId'=>$w->id_widgetsTree));
        return $this->getDatasource()->translate($this->getDatasource()->processData($obj), new Widget\Model\Template());
    }
    
    /**
     * Remove template
     * 
     * @Secured
     * 
     * @param \Service\Widget\Model\Template $template
     */
    public function removeTemplate($data)
    {
        // Function requires authorization beforehand
        new \Service\Core\Auth\Auth($this, __FUNCTION__);
        
        $template = new \Service\Widget\Model\Template($data);
        
        $this->getDatasource()->removeTemplate($template);
        $this->getDatasource('filesystem')->removeTemplate($template);
        
        $this->getDatasource()->getPageIpByWidgetsTree($data['config']['id_widgetsTree']);
        $w = $this->getDatasource()->processData();
        \Service\Core\MongoLog::getInstance()->info('Remove template', $w['tree_ip'], array('widgetId'=>$w['id_widgetsTree'])); 
        return new \stdClass();        
    }
      
    
    /**
     * Save an existing template widget
     * 
     * @Secured
     * 
     * @param mixed $widget widget throught obj or array from json
     * @return void
     */
    public function saveWidgetTemplate($template) {
        
        if($template['config']['type_id'] == 0)
        {
            $this->getDatasource('filesystem')->updateFile($template['html_content'] , $template['name'] . ".phtml", 
                            'custom' . DIRECTORY_SEPARATOR .
                            'frontend' . DIRECTORY_SEPARATOR .
                            'views' . DIRECTORY_SEPARATOR . 
                            'scripts' . DIRECTORY_SEPARATOR . 
                            'widget_' . $template['config']['id_widgetsTree']
                            );
        } else {
            
            $this->getDatasource('filesystem')->updateFile($template['html_content'] , $template['name'] . ".phtml", 
                            'custom' . DIRECTORY_SEPARATOR .
                            'frontend' . DIRECTORY_SEPARATOR .
                            'views' . DIRECTORY_SEPARATOR . 
                            'scripts' . DIRECTORY_SEPARATOR . 
                            'type_' . $template['config']['type_id']
                            );
            
        }
        $this->getDatasource()->getPageIpByWidgetsTree($template['config']['id_widgetsTree']);
        $w = $this->getDatasource()->processData();
        
        \Service\Core\MongoLog::getInstance()->info('Save an existing template widget', $w['tree_ip'], array('widgetId'=>$w['id_widgetsTree']));
        return new \stdClass();
    }
    
    /**
     * Trigger widget's data providers
     * @param \Service\Widget\Model\Widget $widget Passed widget to trigger datasources
     * @return void
     */
    public function triggerProviders(\Service\Widget\Model\Widget &$widget) {
        
        if(!empty($widget->providers->services)) {            
            foreach($widget->providers->services as $service) {
                $serv = new \Service\Widget\Model\Provider\Service($service);
                if($widget->data) {
                    $serv->linkDynamicValues($widget->data);
                } else {
                    $widget->data = new \stdClass();
                }
                $tempData = $serv->runAgainst($this);
                foreach($tempData as $k => $v) {
                    $widget->data->{$k} = $v;
                }
            }
        }
    }

    
    public function parseContentData(Widget\Model\Widget $obj) {
        $content = $obj->getConfig();
        return $this->_filterContentData($content);
    }
    
    private function _filterContentData($content) {
        
        $outputArray = array();
        
        if(is_array($content) && !empty($content['value']) )  {
            return $content['value'];
        }        
        
        foreach($content as $k => $c) {
            if (is_string($c)) {
                $outputArray[] = $c;        
             /*   if(is_object($c)) {
                    $outputArray[$c->name] = $c->value;        
                } else {
                    var_dump($c);
                    $outputArray[$c['name']] = $c['value'];        
                }*/
            } elseif (is_array($c)) {
                $array_c = $c;
                $c = (object) $c;

                if(empty($array_c['type'])) {
                    $temp = array();
                    foreach($array_c as $object) {
                        
                        if(is_array($object) && !empty($object['type']) && $object['type'] == 'group'  ) {
                            $temp[$object['name']] = $this->_filterContentData($object['config']); 
                            
                        } elseif(is_array($object) && empty($object['name']) ) {
                            //$asd = $this->_filterContentData($object); 
                            //$temp[] = $asd;
                        } else {
                            $temp[$object['name']] = $this->_filterContentData($object); 
                        }
                    }         
                    $outputArray[] = $temp;
                } elseif( !empty($array_c['type']) && $array_c['type'] == 'group') {
                    
                   $node = new Widget\Model\Widget();    
                   foreach($array_c as $key => $property) {
                        
                        $node->{$key} = $property; 
                    }
                    $outputArray[$c->name] = $this->parseContentData($node); 
                } else {               
                    $outputArray[$c->name] = $c->value; 
                }
            }
        }    
        return $outputArray;
    }
    
    /**
     * Adds new Widget
     * 
     * @Secured
     * 
     */
    public function addWidget($object) {
        
        if($object instanceof Widget\Model\Widget) {
            $node = $object; 
        } elseif(is_array($object)) {
            $node = new Widget\Model\Widget();    
            foreach($object as $key => $property) {
                $node->{$key} = $property; 
            }       
        }
        
        $this->getDatasource()->addWidget($node);
         
        $obj = $this->getDatasource()->getObject();
        $this->getDatasource()->setObject(null);
        $widgetArray = $this->getDatasource()->processData($obj);
        \Service\Core\MongoLog::getInstance()->info('Add new Widget',$widgetArray['tree_ip'], array('widgetId'=>$widgetArray['id_widgetsTree']));
        return $this->getDatasource()->translate($this->getDatasource()->processData($obj), new Widget\Model\Widget());
   
    }
    
    public function addWidgets($array)
    {
        $widgets = array();
        foreach($array as $widget)
        {
            array_push($widgets, new Widget\Model\Widget($widget));
        }
        return $this->getDatasource()->addWidgets($widgets);
    }
    
    /**
     * Clone windget as type
     * 
     * @Secured
     * 
     * @param mixed $object
     * @return \Service\Widget\Model\Type Object
     */
    public function cloneWidgetAsType($object, $widget_id) {
        
        $type = new Widget\Model\Type($object);    
        $_type = $this->getDatasource()->cloneWidgetAsType($type, $widget_id);
        $this->getDatasource('filesystem')->cloneWidgetAsType($_type, $widget_id);
        \Service\Core\MongoLog::getInstance()->info('Clone windget as type');
        return $_type;
    }
    
    /**
     * Save widget as type
     * 
     * @Secured
     * 
     * @param object $object
     * @param int $widget_id
     * @return type
     */
    public function saveWidgetAsType($object, $widget_id)
    {
        $type = new Widget\Model\Type($object);    
        
        $_type = $this->getDatasource()->saveWidgetAsType($type, $widget_id);
        $this->getDatasource('filesystem')->saveWidgetAsType($type, $widget_id);
        \Service\Core\MongoLog::getInstance()->info('Save widget as type');
        return $_type;
    }
    
    /**
     * Subtract widget from type
     * 
     * @Secured
     * 
     * @param object $object
     * @return \stdClass
     */
    public function subtractFromType($object)
    {
        // Function requires authorization beforehand
        new \Service\Core\Auth\Auth($this, __FUNCTION__);
        
        $widget = new Widget\Model\Widget($object);    
        $this->getDatasource()->subtractFromType($widget);
        $this->getDatasource('filesystem')->subtractFromType($widget);
        
        return new \stdClass();
    }
    
    /**
     * Save widget data
     * 
     * @Secured
     * 
     * @return \stdClass
     */
    public function saveWidgetData($object) {
        
        $widget = new Widget\Model\Widget($object);    
        
        $this->getDatasource()->saveWidgetData($widget);
        \Service\Core\MongoLog::getInstance()->info('Save widget data', $widget->getWidgetsTreeIp(), array('widgrtId'=>$widget->id_widgetsTree));
        return new \stdClass();
    }
    
    /**
     * Save widget structure
     * 
     * @Secured
     * 
     * @param type $object
     * @return \stdClass
     */
    public function saveWidgetStructure($object) {
        
        $widget = new Widget\Model\Widget($object);    
        $this->getDatasource()->saveWidgetStructure($widget);
        \Service\Core\MongoLog::getInstance()->info('Save widget structure', $widget->getWidgetsTreeIp(), array('widgrtId'=>$widget->id_widgetsTree));
        return new \stdClass();
    }
    
    /**
     * Set widget heritage
     * 
     * @Secured
     * 
     * @param array $object
     * @return \stdClass
     */
    public function setWidgetHeritage($object)
    {
        $widget = new Widget\Model\Widget($object);   
        
        $this->getDatasource()->setWidgetHeritage($widget);
        \Service\Core\MongoLog::getInstance()->info('Set widget heritage', $widget->getWidgetsTreeIp(), array('widgrtId'=>$widget->id_widgetsTree));
        return new \stdClass();
    }
    
    public function getPositionsConfig($nodeId)
    {
        return $this->getDatasource()->getPositionsConfig($nodeId);
    }
    
    /**
     * Update widgets order
     * 
     * @Secured
     * 
     * @param array $positionData
     * @param int $nodeId
     * @return \stdClass
     */
    public function updateWidgetsOrder($positionData, $nodeId)
    {
    	$this->getDatasource()->getPageIpByNodeId($nodeId);
    	$w = $this->getDatasource()->processData();
        $this->getDatasource()->updateWidgetsOrder($positionData, $nodeId);
        \Service\Core\MongoLog::getInstance()->info('Update widgets order', $w['ip']);
        return new \stdClass();
    }
    
    public function clearPositionOrder($position, $nodeId)
    {
    	$this->getDatasource()->getPageIpByNodeId($nodeId);
    	$w = $this->getDatasource()->processData();
        $this->getDatasource()->clearPositionOrder($position, $nodeId);
        \Service\Core\MongoLog::getInstance()->info('Clear position order', $w['ip']);
        return new \stdClass();
    }
    
    /**
     * Set node widgets config
     * 
     * @Secured
     * 
     * @param array $data
     * @return type
     */
    public function nodeSetWidgetConfig($data)
    {
        return $this->getDatasource()->nodeSetWidgetConfig($data);
    } 
    
    /**
     * Remove widget
     * 
     * @Secured
     * 
     * @param \Service\Widget $object
     * @return \Service\Widget\Model\Widget
     */
    public function removeWidget($object) {
        
        if($object instanceof Widget\Model\Widget) {
            $node = $object; 
        } elseif(is_array($object)) {
            $node = new Widget\Model\Widget();    
            foreach($object as $key => $property) {
                $node->{$key} = $property; 
            }       
        }
        $this->getDatasource()->deleteWidget($node);
        require_once 'synergy_cms/library/Socms/MongoLog.php';
        \Service\Core\MongoLog::getInstance()->info('Remove widget', $node->tree_ip, array('widgetId'=>$node->id_widgetsTree));
        return $node;
    }
    
    /**
     * Update widget position (left,right etc..)
     * 
     * @Secured
     * 
     * @param \Service\Widget $object
     * @return \Service\Widget\Model\Widget
     */
    public function updateWidgetPosition($object) {
        
        if($object instanceof Widget\Model\Widget) {
            $node = $object; 
        } elseif(is_array($object)) {
            $node = new Widget\Model\Widget();    
            foreach($object as $key => $property) {
                $node->{$key} = $property; 
            }       
        }
        $this->getDatasource()->updatePosition($node);
        \Service\Core\MongoLog::getInstance()->info('Update widget position', $node->tree_ip);
        return $node;
    }

    
}