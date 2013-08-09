<?php
namespace Service\Widget\Model;
use Service\Widget\Model;
use Service\Core;

/**
 * Data Object for Category Objects
 * @package Service\Tree\Model
 * @author Łukasz Miłkowski <lmilkowski@sosoftware.pl>
 */
class Widget extends Core\Model {

    
    public $id_widgetsTree;
    
    public $widget_id;    
    
    public $tree_id;
    public $tree_ip;
    public $tree_url_path;
    
    public $type_id;    
    public $type_name;
    public $base_type;
    
    public $disabled;    
    public $config;    
    public $name_widgetsTree;    
    
    public $position;    
    
    public $date_add;
    
    public $users_id;   
    
    public $template;  
    
    public $sequence;    
    public $showNode;    
    public $inherit;    
    public $headlink;   
    
    
    public $data;
    public $providers;
    
    
    /** @var Service\Widget\Model\Template template object */
    public $template_o;   
    
    
    public function getData() {
        
        if(is_string($this->data)) {
            return json_decode($this->data);
        } else {
            return $this->data;
        }
    }
    
    public function setData($data)
    {
        if(is_string($data)) {
            $this->data = json_decode($data);
        } else {
            $this->data = $data;
        }
    }
    
    public function setConfig($data) {
        
        if(is_string($data)) {
            
            $array = json_decode($data);
            !is_array($array) && $array = unserialize($data);
            $this->config = $array;
                    
        } else {
            
            $this->config = $data;
            
        }
    }
    
    public function setHeadlink($headlink)
    {
        if(is_string($headlink))
        {
            $this->headlink = unserialize($headlink);
        } else {
            $this->headlink = $headlink;
        }
    }
    
    public function setProviders($data) {
        if(is_string($data)) {
            $this->providers = json_decode($data);
        } else {
            $this->providers = $data;
        }
    }
 
    public function setTemplate_o($data) {
        if(is_string($data)) {
            $this->template_o = json_decode($data);
        } else {
            if(is_array($data)) {
                $this->template_o = new \Service\Widget\Model\Template($data);    
            } else {
                $this->template_o = $data;
            }
        }
    }
    
// LEGACY METHODS ==============================================================    
    
    public function getId()
    {
        return $this->widget_id;
    }
    
    public function getContent($param = false)
    {
        if($param)
        {
            if(isset($this->config[$param]))
            {
                return $this->config[$param];
            } else {
                return "";
            }
        } else {
            return $this->config;
        }
    }
    
    public function setContent($content)
    {
        $this->config = array_merge($this->config, $content);
    }
    
    public function getWidgetsTreeId()
    {
        return $this->tree_id;
    }
    
    public function getWidgetsTreeIp()
    {
        return $this->tree_ip;
    }
    
    public function getHeadlinkUrl()
    {
        if(isset($this->headlink['url']))
        {
            return $this->headlink['url'];
        }
    }

    public function getHeadlinkOut()
    {
        if(isset($this->headlink['out']))
        {
            return $this->headlink['out'];
        }
    }

    public function getHeadlinkNofollow()
    {
        if(isset($this->headlink['nofollow']))
        {
            return $this->headlink['nofollow'];
        }
    }
    
    public function getLabel()
    {
        return $this->name_widgetsTree;
    }
    
}