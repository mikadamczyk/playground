<?php
namespace Service\Widget\Adapter;

use Service\Core;
use Service\Widget;

/**
 * Description of Pdo_Mysql
 *
 * @author Łukasz Miłkowski <lmilkowski@sosoftware.pl>
 * @refactorization Bartosz Cieplik
 */
class Pdo_Mysql extends Core\Adapter\Pdo_Mysql implements Core\Adapter\DataAccess {
      
      
    private $_type;
    private $_conference_only;
    
    public function __construct($config) {
        parent::__construct($config);
    }
    
    
    public function setConferenceOnly() {
        $this->_conference_only = true;
        return $this;
    }
    
    public function getPageIpByNodeId($nodeId){
    	if(is_numeric($nodeId)) {
    		$oSelect = $this->getAdapter()->select();
    		
    		$oSelect->from(array('t1' => 'socms_tbl_tree_content'), 
    				array( 
    						"t1.ip AS ip"
    						))
    		->where('t1.id = ?', $nodeId);
    		$this->_object = $oSelect;
    		return $oSelect;    		
    		
    	}
    }
    
    public function getPageIpByWidgetsTree($id_widgetsTree){
    	if(is_numeric($id_widgetsTree)) {
    		$oSelect = $this->getAdapter()->select();
    		
    		$oSelect->from(array('t1' => 'socms_tbl_widgets_has_tbl_tree'),
    				array(
    						"t1.id_widgetsTree AS id_widgetsTree",
    						"t1.type_id AS type_id",
    						"t1.widget_id AS widget_id",
    						"t1.tree_id AS tree_id",
    						"t1.disabled AS disabled",
    						"t1.name_widgetsTree AS name_widgetsTree",
    						"t1.template AS template",
    						"t1.position AS position",
    						"t1.data AS data",
    						"t1.config AS config",
    						"t1.date_add AS date_add",
    						"t1.users_id AS users_id",
    						"t1.sequence AS sequence",
    						"t1.showNode AS showNode",
    						"t1.inherit AS inherit",
    						"t1.headlink AS headlink",
    						"t1.version_type AS version_type",
    						"(CASE WHEN (t1.providers != '') THEN t1.providers ELSE t2.providers END) AS providers",
    						"t2.name AS type_name",
    						"(CASE WHEN (t3.url_path is NULL) THEN 'MAIN PAGE' ELSE t3.url_path END) AS tree_url_path",
    						"t3.ip AS tree_ip",
    						"t4.name AS base_type"
    				))
    				->where('id_widgetsTree = ?', $id_widgetsTree)
    				->joinLeft(array('t2' => 'socms_tbl_widgets_custom_types'), 't2.id = t1.type_id', array())
    				->join(array('t3' => 'socms_tbl_tree_content'), 't3.id = t1.tree_id', array())
    				->join(array('t4' => 'socms_tbl_widgets'),'t4.id = t1.widget_id', array())
    				;
    		
/*     		if(!empty($tree_id)){
    			$iSelect = $this->getAdapter()->select();
    			$iSelect->from('socms_tbl_tree_content', array('ip') );
    			$iSelect->where('id = ?', $tree_id);
    			$outData = $this->getAdapter()->fetchAll($iSelect);
    			//$oSelect->where('socms_tbl_widgets_has_tbl_tree.tree_id = ?', $tree_id);
    		
    			$oSelect->where("t1.tree_id IN (" . str_replace('.', ',', $outData[0]['ip']) . ") AND (t1.inherit = 1 OR t1.tree_id = '{$tree_id}')");
    		} */
    		
    		$oSelect->order('sequence');
    		
    		$this->_object = $oSelect;
    		return $oSelect;
    	}
    }
    
    public function getWidget(\Service\Widget\Model\Widget $obj = null, $config = null) {
        
        if(empty($this->_object)) {
            $oSelect = $this->getAdapter()->select();
        } else {
            $oSelect = $this->_object;
        }
        
        if(!empty($obj)) {
            
            if(is_numeric($obj)) {
                $id = $obj;
            } elseif($obj instanceof Widget\Model\Widget) {
                $tree_id = $obj->getTree_id();
                $idWT = $obj->id_widgetsTree;            
            }
        }
        
        
        $oSelect->from(array('t1' => 'socms_tbl_widgets_has_tbl_tree'), 
                      array(
                          "t1.id_widgetsTree AS id_widgetsTree",
                          "t1.type_id AS type_id",
                          "t1.widget_id AS widget_id",
                          "t1.tree_id AS tree_id",
                          "t1.disabled AS disabled",
                          "t1.name_widgetsTree AS name_widgetsTree",
                          "t1.template AS template",
                          "t1.position AS position",
                          "t1.data AS data",
                          "t1.config AS config",
                          "t1.date_add AS date_add",
                          "t1.users_id AS users_id",
                          "t1.sequence AS sequence",
                          "t1.showNode AS showNode",
                          "t1.inherit AS inherit",
                          "t1.headlink AS headlink",
                          "t1.version_type AS version_type",
                          "(CASE WHEN (t1.providers != '') THEN t1.providers ELSE t2.providers END) AS providers",
                          "t2.name AS type_name",
                          "(CASE WHEN (t3.url_path is NULL) THEN 'MAIN PAGE' ELSE t3.url_path END) AS tree_url_path",
                          "t3.ip AS tree_ip",
                          "t4.name AS base_type"
                      ))
        ->joinLeft(array('t2' => 'socms_tbl_widgets_custom_types'), 't2.id = t1.type_id', array())
        ->join(array('t3' => 'socms_tbl_tree_content'), 't3.id = t1.tree_id', array())        
        ->join(array('t4' => 'socms_tbl_widgets'),'t4.id = t1.widget_id', array())
        ;
        
        if(!empty($idWT)){

            $oSelect->where('t1.id_widgetsTree = ?', $idWT);

        } 
        elseif(!empty($tree_id))
        {                

            $iSelect = $this->getAdapter()->select();
            $iSelect->from('socms_tbl_tree_content', array('ip') );
            $iSelect->where('id = ?', $tree_id);
            $outData = $this->getAdapter()->fetchAll($iSelect);
            //$oSelect->where('socms_tbl_widgets_has_tbl_tree.tree_id = ?', $tree_id);

            $oSelect->where("t1.tree_id IN (" . str_replace('.', ',', $outData[0]['ip']) . ") AND (t1.inherit = 1 OR t1.tree_id = '{$tree_id}')");
        } 

        if($config['showAll'] ) {

                $oSelect->where('t1.disabled = 0');

        } else {

                $oSelect->where('t1.disabled = 0');
        }
            
        // ORDER -----------------------------------------------------------

        $oSelect->order('sequence');

        $this->_object = $oSelect;
        return $oSelect;
    }
    
    public function parseWidgetNodeConfig($collection, $tree_ip)
    {
        $nodes_id = explode(".",$tree_ip);
        \Zend_Db_Table::setDefaultAdapter($this->getAdapter());
        $table = new \Socms_Tbl_Tree_Content();
        $select = $table->select()->where("id IN (?)", $nodes_id)->order("id DESC");
        $nodes = $table->fetchAll($select);
        
        $first = 0;
        foreach($nodes as $node)
        {
            $widget_config = json_decode($node['widget_config'],true);
            
            // Node of current page ============================================
            if($first == 0)
            {
                
                // position
                $widget_position = json_decode($node['widget_position'], true);
                if(is_array($widget_position))
                {
                    foreach($collection->_collection as &$widget)
                    {
                        if(isset($widget_position[$widget->position][$widget->id_widgetsTree]))
                        {
                            $widget->sequence = $widget_position[$widget->position][$widget->id_widgetsTree];
                        }
                    }
                }
                usort($collection->_collection, function($a, $b) {
                    return ($a->sequence < $b->sequence) ? -1 : 1;
                });
                
                // visibility
                
                foreach($collection->_collection as &$widget)
                {
                    if($widget->showNode == '0' || $widget->showNode == NULL)
                    {
                        $widget->hidden = 1;
                        
                    } else {
                        
                        $widget->hidden = 0;
                    }
                }
                
                if(isset($widget_config['hidden']))
                {
                    $hidden = $widget_config['hidden'];
                    foreach($collection->_collection as &$widget)
                    {
                        if(isset($hidden[$widget->id_widgetsTree]))
                        {
                            $widget->hidden = $hidden[$widget->id_widgetsTree];
                            
                        }
                    }
                }
                
            }
            
            // STATE ===========================================================
            if(isset($widget_config['state']))
            {
                $state = $widget_config['state'];
                foreach($collection->_collection as &$widget)
                {
                    if(isset($state[$widget->id_widgetsTree]))
                    {
                        if($first == 0) $widget->state = $state[$widget->id_widgetsTree];
                        if(!isset($widget->status) || $widget->status == 'auto')
                        {
                            if($first == (count($nodes) -1) && $state[$widget->id_widgetsTree] == 'auto')
                            {
                                $widget->status = '1';
                            } else {
                                $widget->status = $state[$widget->id_widgetsTree];
                            }
                        }
                        
                    } 
                }
            }
            
            // INHERIT =========================================================
            if(isset($widget_config['inherit']))
            {
                $inherit = $widget_config['inherit'];
                $widgetToDelete = array();
                foreach($collection->_collection as $key => &$widget)
                {
                    
                    if(isset($inherit[$widget->id_widgetsTree]))
                    {
                        if($first == 0)
                        {
                            $widget->inherit = $inherit[$widget->id_widgetsTree];
                        } else {
                            if($inherit[$widget->id_widgetsTree] == "0")
                            {
                                array_push($widgetToDelete, $key);
                            }
                        }
                    }
                }
                foreach($widgetToDelete as $widgetKey)
                {
                    unset($collection->_collection[$widgetKey]);
                }
            }
            $first++;
        }   
        return $collection;
    }
    
    public function nodeSetWidgetConfig($data)
    {
        \Zend_Db_Table::setDefaultAdapter($this->getAdapter());
        $table = new \Socms_Tbl_Tree_Content();
        $row = $table->fetchRow($table->select()->where("id = ?", $data['tree_id']));
        
        $widget_config = json_decode($row->widget_config, true);
        
        !$widget_config && $widget_config = array();
        $widget_config[$data['param']][$data['widget_id']] = $data['value'];
        
        $row->widget_config = json_encode($widget_config);
        $row->save();
        
        if($data['param'] == 'state')
        {
            $nodes_id = explode(".",$data['tree_ip']);
            \Zend_Db_Table::setDefaultAdapter($this->getAdapter());
            $table = new \Socms_Tbl_Tree_Content();
            $select = $table->select()->where("id IN (?)", $nodes_id)->order("id DESC");
            $nodes = $table->fetchAll($select);
            
            foreach($nodes as $node)
            {
                $widget_config = json_decode($node['widget_config'],true);
                if(isset($widget_config['state']))
                {
                    $state = $widget_config['state'];
                    if(isset($state[$data['widget_id']]))
                    {
                        if(!isset($status) || $status == 'auto')
                        {
                            $status = $state[$data['widget_id']];
                        }
                    }
                }
            }
            
            !isset($status) && $status = 1;
            
            return array('status' => $status);
        }
        
        return new \stdClass();
    }
    
    public function getWidgetType($obj = null) {
        
        if(empty($this->_object)) {
                $oSelect = $this->getAdapter()->select();
            } else {
                $oSelect = $this->_object;
            }
        
            $oSelect->from('socms_tbl_widgets_custom_types');
            
                $this->_object = $oSelect;
    }
    
    public function getLegacyTypes()
    {
        $oSelect = $this->getAdapter()->select();
        $oSelect->from('socms_tbl_widgets');
        
        return $this->getAdapter()->fetchAll($oSelect);
    }
    
    public function saveWidgetData($widget)
    {
        $where = $this->getAdapter()->quoteInto("id_widgetsTree = ?", $widget->id_widgetsTree);
        $data = array(
          "data"             => json_encode($widget->data),
          "name_widgetsTree" => $widget->name_widgetsTree,
          "template"         => $widget->template 
        );
        $this->getAdapter()->update('socms_tbl_widgets_has_tbl_tree', $data, $where);
    }

    public function saveWidgetStructure($widget)
    {
        if($widget->type_id == 0) // standalone widget
        {
            $where = $this->getAdapter()->quoteInto("id_widgetsTree = ?", $widget->id_widgetsTree);
            
            $config = json_encode($widget->config);
            $providers = json_encode($widget->providers);
            
            $data = array(
              "config" => $config == 'false' ? '' : $config,
              "providers" => $providers ? $providers : ''
            );
            $this->getAdapter()->update('socms_tbl_widgets_has_tbl_tree', $data, $where);
        }
        else // type widget
        {
            $where = $this->getAdapter()->quoteInto("id = ?", $widget->type_id);
            
            $config = json_encode($widget->config);
            $data = array(
              "config" => $config == 'false' ? '' : $config
            );
            $this->getAdapter()->update('socms_tbl_widgets_custom_types', $data, $where);
        }
    }

    /**
     * Adds new widget 
     * @param Widget\Model\Widget $obj
     * @return Widget\Model\Widget New Added Widget
     **/
    public function addWidget( Widget\Model\Widget $obj) 
    {
        
        $db = $this->getAdapter();
        try {
            $data = array (
                'disabled' => 0,
                'name_widgetsTree' => $obj->name_widgetsTree,
                'position' => $obj->getPosition(),
                'tree_id' => $obj->tree_id,
                'config' => ($obj->config && $obj->type_id == 0) ? json_encode($obj->config) : "",
                'providers' => ($obj->getProviders() && $obj->type_id == 0) ? json_encode($obj->getProviders()) : "", 
                'widget_id' => $obj->widget_id,
                'type_id' => $obj->type_id,
                'users_id' => 1
            );

            $db->insert('socms_tbl_widgets_has_tbl_tree', $data);
            $wid = $this->getAdapter()->lastInsertId();
            if($wid) {
                $obj->id_widgetsTree = $wid;
            }
        } catch(Exception $e) {
        	\Service\Core\MongoLog::getInstance()->info('There was an error updating widget', $obj->tree_id, array('widgetId'=>$obj->id_widgetsTree));
            throw new Core/Exception("There was an error updating widget");
        }

        return $this->getWidget($obj);
    }

    public function addWidgets($widgetsToImport)
    {
        \Zend_Db_Table::setDefaultAdapter($this->getAdapter());
        $table = new \Socms_Tbl_Widgets_Has_Tbl_Tree();
        
        $widgets = array();
        foreach($widgetsToImport as $obj)
        {
              $widget = $table->fetchRow($table->select()->where('id_widgetsTree = ?', $obj->id_widgetsTree));
              $widget = $widget->toArray();
              
              unset($widget['id_widgetsTree']);
              
              $widget['inherit'] = '0';
              $widget['tree_id'] = $obj->tree_id;
              $widget['position'] = $obj->position;
              
              $table->insert($widget);
              $widget['id_widgetsTree'] = $this->getAdapter()->lastInsertId();
              
              $widget = $this->getAdapter()->fetchRow( $this->getWidget(new \Service\Widget\Model\Widget($widget)) );
              $this->setObject(null);
              
              array_push($widgets, new \Service\Widget\Model\Widget($widget));
        }
        
        return $widgets;
    }
    
        /**
     * Updates widget 
     * @param Widget\Model\Widget $obj
     **/
    public function update( Widget\Model\Widget $obj) {
        $db = $this->getAdapter();
        try {
            $data = array (
         //       'id_widgetsTree' => $obj->id_widgetsTree,
                'disabled' => $obj->getDisabled(),
                'config' => json_encode($obj->getConfig()),
                'data' => json_encode($obj->getData()),
                'name_widgetsTree' => $obj->name_widgetsTree,
                'template' => $obj->getTemplate(),
                'position' => $obj->getPosition(),
                'inherit' => $obj->getInherit(),
                'providers' => json_encode($obj->getProviders()),
            );

            $db->update('socms_tbl_widgets_has_tbl_tree', $data, "id_widgetsTree = '{$obj->id_widgetsTree}' ");

        } catch(Exception $e) {
            throw new Core/Exception("There was an error updating widget");
        }

        $db->insert('socms_tbl_widgets_history', array( 'content' => json_encode($obj), 'id_widgetsTree' => $obj->id_widgetsTree , 'user_id' => 1 ));
    }
    

    /**
     * Updates widgets position only
     * @param Widget\Model\Widget $obj
     **/
    public function updatePosition( Widget\Model\Widget $obj) {
        $db = $this->getAdapter();
        try {
            $data = array (
                'position' => $obj->getPosition(),
            );

            $db->update('socms_tbl_widgets_has_tbl_tree', $data, "id_widgetsTree = '{$obj->id_widgetsTree}' ");

        } catch(Exception $e) {
            throw new Core/Exception("There was an error updating widget");
        }

        $db->insert('socms_tbl_widgets_history', array( 'content' => json_encode($obj), 'id_widgetsTree' => $obj->id_widgetsTree , 'user_id' => 1 ));
        
    }
    
    public function getPositionsConfig($nodeId)
    {
        \Zend_Db_Table::setDefaultAdapter($this->getAdapter());
        $table = new \Socms_Tbl_Tree_Content();
        
        $tableRow = $table->fetchRow($table->select()->where('id = ?', $nodeId));        
        
        $positionConfig = json_decode($tableRow->widget_position);
        if($positionConfig == null) $positionConfig = new \stdClass ();
        
        $positions = array();
        foreach($positionConfig as $name => $data)
        {
            $positions[$name] = array();
            
            if(is_array($data))
            {
                $positions[$name]['hasConfig'] = (count($data)) > 1 ? true : false;
                
            } else {
                
                $ref = new \ReflectionObject($data);
                $props = $ref->getProperties();
                $positions[$name]['hasConfig'] = (count($props)) > 1 ? true : false;
                
            }
        }
        return $positions;
    }
    
    /**
     * Update wigets position in node
     * 
     * @param type $positionData
     * @param type $nodeId
     */
    public function updateWidgetsOrder($positionData, $nodeId)
    {
        \Zend_Db_Table::setDefaultAdapter($this->getAdapter());
        
        foreach($positionData as $position)
        {
            if(count($position) > 0)
            {
                foreach($position as $widget_id => $order)
                {
                    $sql = "UPDATE socms_tbl_widgets_has_tbl_tree "
                         . "SET sequence = CASE WHEN tree_id = '{$nodeId}' THEN '{$order}' ELSE sequence END "
                         . "WHERE id_widgetsTree = '{$widget_id}'";
                    $this->getAdapter()->query($sql);
                }
            }
        }
                
        $table = new \Socms_Tbl_Tree_Content();
        
        $tableRow = $table->fetchRow($table->select()->where('id = ?', $nodeId));        
        
        $positionConfig = json_decode($tableRow->widget_position);
        if($positionConfig == null) $positionConfig = new \stdClass ();
        
        foreach($positionData as $name => $data)        
        {
            $positionConfig->{$name} = $data;
        }
        
        $tableRow->widget_position = json_encode($positionConfig, JSON_FORCE_OBJECT);
        $tableRow->save();
    }
    
    public function clearPositionOrder($position, $nodeId)
    {
        \Zend_Db_Table::setDefaultAdapter($this->getAdapter());
        $table = new \Socms_Tbl_Tree_Content();
        $row = $table->fetchRow($table->select()->where('id = ?', $nodeId));
        
        $positionConfig = json_decode($row->widget_position);
        if($positionConfig == null) $positionConfig = new \stdClass ();
        
        $positionConfig->{$position} = new \stdClass();
        $row->widget_position = json_encode($positionConfig);
        $row->save();
        
    }
    
    /**
     * 
     */
    public function setWidgetHeritage(Widget\Model\Widget $widget)
    {
        \Zend_Db_Table::setDefaultAdapter($this->getAdapter());
        
        $table = new \Socms_Tbl_Widgets_Has_Tbl_Tree();
        $select = $table->select()->where('id_widgetsTree = ?', $widget->id_widgetsTree);
        $row = $table->fetchRow($select);
        $row->inherit = $widget->inherit;
        $row->save();
   }
    
    
    /**
     * soft-delete widget
     * @param Widget\Model\Widget $obj
     **/
    public function deleteWidget( Widget\Model\Widget $obj) {
        
        $db = $this->getAdapter();
        try {
            $data = array (
                'disabled' => 1,
            );

            $db->update('socms_tbl_widgets_has_tbl_tree', $data, "id_widgetsTree = '{$obj->id_widgetsTree}' ");

        } catch(Exception $e) {
            throw new Core/Exception("There was an error updating widget");
        }
        
    }
    
    public function getNodeWidgets($data)
    {
        
        $select = $this->getAdapter()->select()
                                     ->from('socms_tbl_widgets_has_tbl_tree', array('position', 'id_widgetsTree','name_widgetsTree'))
                                     ->where('tree_id = ?', $data['node_id']);
        if(isset($data['position']))
        {
            $select->where('position = ?', $data['position']);
        }
        return $this->getAdapter()->fetchAll($select);
    }
    
// TYPES =======================================================================    
    
    /**
     * Clone widget as new widget type
     * 
     * @param Widget\Model\Type $obj
     * @param int $id_widgetsTree
     * @return Widget\Model\Type New Added Widget
     * @throws \Service\Core\Exception
     **/
    public function cloneWidgetAsType(Widget\Model\Type $obj, $id_widgetsTree) {
        
        $this->getAdapter()->beginTransaction();
        \Zend_Db_Table::setDefaultAdapter($this->getAdapter());
        
        try 
        {
            $obj = $this->_type_insertType($obj);
            $this->_type_cloneTemplatesForType($obj, $id_widgetsTree);
            
            $this->getAdapter()->commit();
            
        } catch(\Exception $e) {
            
            $this->getAdapter()->rollBack();
            throw new Core/Exception("CloneAsType in DATABASE failed!");

        } 
        
        return $obj;
    }
    
    /**
     * Save widget as new type
     * 
     * @param \Service\Widget\Model\Type $obj
     * @param type $id_widgetsTree
     * @return \Service\Widget\Model\Type
     * @throws \Service\Core\Exception
     */
    public function saveWidgetAsType(Widget\Model\Type $obj, $id_widgetsTree)
    {
        $this->getAdapter()->beginTransaction();
        \Zend_Db_Table::setDefaultAdapter($this->getAdapter());
        
        try 
        {
            $obj = $this->_type_insertType($obj);
            $this->_type_updateWidget($obj, $id_widgetsTree);
            $this->_type_updateTemplates($obj, $id_widgetsTree);
            
            $this->getAdapter()->commit();
            
        } catch(\Exception $e) {
            
            $this->getAdapter()->rollBack();
            throw new Core/Exception("CloneAsType in DATABASE failed!");
            
        } 
        
        return $obj;
    }
    
    public function subtractFromType(Widget\Model\Widget $widget)
    {
        $select = $this->getAdapter()->select()
                                     ->from('socms_tbl_widgets_custom_types')
                                     ->where('id = ?', $widget->type_id);
        
        $type = $this->getAdapter()->fetchRow($select);
        
        // update widget -------------------------------------------------------
        
        $table = new \Socms_Tbl_Widgets_Has_Tbl_Tree();
        $widgetRow = $table->fetchRow($table->select()
                                            ->where('id_widgetsTree = ?', $widget->id_widgetsTree));
        $widgetRow->config = $type['config'];
        $widgetRow->providers = $type['providers'];
        $widgetRow->type_id = 0;
        
        $widgetRow->save();
        
        // clone templates -----------------------------------------------------
        
        $this->_type_cloneTemplatesForWidget($widget);
        
    }
    
    // partials ----------------------------------------------------------------
    
    /**
     * TYPES_PARTIAL - insert new type to database
     * 
     * @param \Service\Widget\Model\Type $obj
     * @return \Service\Widget\Model\Type
     */
    private function _type_insertType(Widget\Model\Type $obj)    
    {
        // socms_tbl_widgets_custom_types
            
        $data = array (
            'config' => json_encode($obj->getConfig()),
            'providers' => json_encode($obj->getProviders()),
            'name' => $obj->getName()
        );

        $this->getAdapter()->insert('socms_tbl_widgets_custom_types', $data);

        $id = $this->getAdapter()->lastInsertId();
        if($id) { $obj->id = $id; }
        
        return $obj;
    } 
    
    /**
     * TYPES_PARTIAL - update widget type_id
     * 
     * @param \Service\Widget\Model\Type $obj
     * @param int $id_widgetsTree
     * @return void
     */
    private function _type_updateWidget(Widget\Model\Type $obj, $id_widgetsTree)
    {
        // socms_tbl_widgets_has_tbl_tree
            
        $table = new \Socms_Tbl_Widgets_Has_Tbl_Tree();
        $row = $table->fetchRow( $table->select()->where('id_widgetsTree = ?', $id_widgetsTree) );
        $row->type_id = $obj->id;
        $row->config = "";
        $row->providers = "";
        $row->save();
    }
    
    /**
     * TYPES_PARTIAL - clone templates from type to widget
     * 
     * @param \Service\Widget\Model\Widget $widget
     * @return void
     */
    private function _type_cloneTemplatesForWidget(Widget\Model\Widget $widget)
    {
        $table = new \Socms_Tbl_Templatewidget();
        $select = $table->select()
                        ->where('config LIKE ?', '%"type_id":"'.$widget->type_id.'"%')
                        ->orWhere('config LIKE ?', '%"id_widgetsTree":'.$widget->type_id.'%');

        $templates = $table->fetchAll($select)->toArray();
        foreach($templates as $template)
        {
            unset($template['id']);
            $config = json_decode($template['config']);
            $config->id_widgetsTree = $widget->id_widgetsTree;
            $config->type_id = 0;
            $template['config'] = json_encode($config);
            $table->insert($template);
        }
    }
    
    /**
     * TYPES_PARTIAL - clone templates from widget to new type
     * 
     * @param \Service\Widget\Model\Type $obj
     * @param int $id_widgetsTree
     * @return void
     */
    private function _type_cloneTemplatesForType(Widget\Model\Type $obj, $id_widgetsTree)
    {
        $table = new \Socms_Tbl_Templatewidget();
        $select = $table->select()
                        ->where('config LIKE ?', '%"id_widgetsTree":"'.$id_widgetsTree.'"%')
                        ->orWhere('config LIKE ?', '%"id_widgetsTree":'.$id_widgetsTree.'%');

        $templates = $table->fetchAll($select)->toArray();
        foreach($templates as $template)
        {
            unset($template['id']);
            $config = json_decode($template['config']);
            $config->id_widgetsTree = 0;
            $config->type_id = $obj->id;
            $template['config'] = json_encode($config);
            $table->insert($template);
        }
    }
    
    /**
     * TYPES_PARTIAL - update templates for widget
     * 
     * @param \Service\Widget\Model\Type $obj
     * @param type $id_widgetsTree
     * @return void
     */
    public function _type_updateTemplates(Widget\Model\Type $obj, $id_widgetsTree)
    {
        $table = new \Socms_Tbl_Templatewidget();
        $select = $table->select()
                        ->where('config LIKE ?', '%"id_widgetsTree":"'.$id_widgetsTree.'"%')
                        ->orWhere('config LIKE ?', '%"id_widgetsTree":'.$id_widgetsTree.'%');

        $templates = $table->fetchAll($select);
        
        foreach($templates as $template)
        {
            $config = json_decode($template->config);
            $config->id_widgetsTree = 0;
            $config->type_id = $obj->id;
            $template->config = json_encode($config);
            $template->save();
        }
    }
    
// TEMPLATES ===================================================================    
    
    /**
     * Updates widget 
     * @param \Service\Widget\Model\Widget $obj
     **/
    public function addTemplate( \Service\Widget\Model\Widget $obj) {
        
        $config = array(
                'type_id' => $obj->type_id,
                'id_widgetsTree' => ($obj->type_id != 0 ? 0 : $obj->id_widgetsTree)
        );
        
        try {
            
            $this->getAdapter()->insert('socms_tbl_templatewidget', array('name' => $obj->getTemplate(),
                                                                          'widget_id' => $obj->widget_id,  
                                                                          'config' => json_encode($config)
                                                                         ));
            $tid = $this->getAdapter()->lastInsertId();
            if($tid) {
                $tpl = new \Service\Widget\Model\Template();
                $tpl->setId($tid);
            }
            
            //$this->getAdapter()->update('socms_tbl_widgets_has_tbl_tree', array('template' =>  $obj->getTemplate() ), "id_widgetsTree = '{$obj->id_widgetsTree}' ");
            
            return $this->getTemplates($tpl);
            
        } catch(Exception $e) {
            
            throw new Core/Exception("There was an error updating widget");
        }

    }
        
    public function removeTemplate($template)
    {
        
        $widgets = $this->getWidgetsWhereUsed($template);
        
        if(empty($widgets))        
        {
            $where = $this->getAdapter()->quoteInto("id = ?", $template->id);
            $this->getAdapter()->delete('socms_tbl_templatewidget', $where);
            
        } else {
            
            $message = "Template can't be deleted, is used in: <br>";
            foreach($widgets as $widget)
            {
                $message .= "-{$widget['name_widgetsTree']}<br>";
            }
            throw new \Exception($message);
        }
    }
    
    public function getWidgetsWhereUsed($template)
    {
        
        $select = $this->getAdapter()->select();
        $select->from('socms_tbl_widgets_has_tbl_tree', array('name_widgetsTree'))
               ->where('template = ?', $template->name)
               ->where('(id_widgetsTree = ?', $template->config->id_widgetsTree)
               ->orWhere('type_id = ?)', $template->config->type_id);
        $row = $this->getAdapter()->fetchAll($select);
        
        return $row;
    }
    
    public function getTemplate($template_name)
    {
        
        try
        {
            $select = $this->getAdapter()->select();
            $select->from("socms_tbl_templatewidget", array("template_content"));
            $select->where("name = ?", $template_name);
            
            $result = $this->getAdapter()->fetchRow($select);
            
            return $result['template_content'];
        } 
        catch(Exception $ex)
        {
           throw new Core/Exception("Error occurred during data acquiring"); 
        }
    }
    
    public function getTemplates($obj = null) {
        $id = null;
        $widgetid = null;
        if(!empty($obj)) {
            
            if(is_numeric($obj)) {
                $id = $obj;
            } elseif($obj instanceof \Service\Widget\Model\Widget) {
                $widgetid = $obj->widget_id;                    
            } elseif($obj instanceof \Service\Widget\Model\Template) {
                $id = $obj->id;                    
            }
        }
        
        if(empty($this->_object)) {
                $oSelect = $this->getAdapter()->select();
            } else {
                $oSelect = $this->_object;
            }
        
        $oSelect->from('socms_tbl_templatewidget');
        $oSelect->join('socms_tbl_widgets', 'socms_tbl_widgets.id = socms_tbl_templatewidget.widget_id', array('widget_name' => 'socms_tbl_widgets.name'));
        
        if( $widgetid ) {
            $oSelect->where('widget_id = ?', $widgetid);    
        }
        
        if( $id ) {
            $oSelect->where('socms_tbl_templatewidget.id = ?', $id);            
        }
        
        
            
        $this->_object = $oSelect;
    }

// TRANSLATE ===================================================================    
    
    public function translate($data, $obj) {
        
        if($data) { 
           if($obj instanceof Widget\Model\Template) {               
                foreach($data as $k => $d ) {
                    $name = 'set'. ucfirst($k);
                    try {
                        $obj->{$name}($d);
                    } catch (Core\Exception $e) {
                        $e->getMessage();
                    }

                }
                
           } else {

                foreach($data as $key => $value ) {
                    $name = 'set'. ucfirst($key);
                    
                    try {
                        
                        $obj->{$name}($value);
                        
                    } catch (Core\Exception $e) {
                        $e->getMessage();
                    }

                }
           }
        return $obj;
        } else {
            return $obj;
        }
    }
    
   
    
}
