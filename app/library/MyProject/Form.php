<?php

/**
 * Description of Form
 * @author rybka
 */
class MyProject_Form extends Zend_Form 
{
    //put your code here
    
    public $formDecorators = array(
        'FormElements',
        array('HtmlTag', array('tag'=>'div', 'class'=>'myform')),
        'Form',
    );
    
    public $elementDecorators = array(
        'ViewHelper',
        'Errors',
        array(array('data'=>'HtmlTag'), array('tag'=>'div', 'class'=>'element')),
        array('Label', array('class'=>'left')),
        array(array('row'=>'HtmlTag'), array('tag'=>'div', 'class'=>'row')),
    );
    
    public $buttonDecorators = array(
        'ViewHelper',
        array(array('data'=>'HtmlTag'), array('tag'=>'div', 'class'=>'element')),
        array(array('row'=>'HtmlTag'), array('tag'=>'div')),
    );
    
    public $hiddenDecorators = array(
        'ViewHelper',
    );
    
    /* @var $_model MyProject_Model_Abstract */
    protected $_model = null;
    
    public function __construct($options = null)
    {
        if (is_subclass_of($options, 'MyProject_Model_Abstract')) {
            $this->setModel($options);
        }
        
        parent::__construct($options);
        
        $this->addElementPrefixPath(
            'MyProject_Validate', 
            'MyProject/Validate',
            'validate'
        );
        
        $this->addElementPrefixPath(
            'MyProject_Filter', 
            'MyProject/Filter',
            'filter'
        );
        
        $this->addElementPrefixPath(
            'MyProject_Decorator', 
            'MyProject/Decorator',
            'decorator'
        );
        
        $this->setMyDefaultDecorators();
        $this->renderProjectDecorators();
                
    }
    
    public function setMyDefaultDecorators() 
    {
        /* @var $config Zend_Config_Ini */
//        $configDecorators = new Zend_Config_Ini( APPLICATION_PATH . '/configs/forms/decoratorstpl.ini');
//        $this->setElementDecorators($configDecorators->elementDecorators->toArray());
//        $this->setElementDecorators(array('Composite'));
//        $this->setDecorators( $configDecorators->decorators->toArray() );
//        $this->setDecorators( array('Composite') );
    }
    
    
    
    /** @return MyProject_Model_Abstract */
    public function getModel() 
    {
        return $this->_model;
    }
    
    public function renderProjectDecorators() {
        /* @var $element Zend_Form_Element */
        $elements = $this->getElements();
        
        $this->setDecorators($this->formDecorators);
        foreach($this->getElements() as $element) {
            switch($element->getType()) {
                case 'Zend_Form_Element_Button':
                case 'Zend_Form_Element_Submit':
                    $element->setDecorators($this->buttonDecorators);
                    break;
                
                case 'Zend_Form_Element_Hidden':
                    $element->setDecorators($this->hiddenDecorators);
                    break;
                
                default:
                    $element->setDecorators($this->elementDecorators);
            }
        }
    }


    /** 
     * @param MyProject_Model_Abstract $model 
     * @return void
     */
    public function setModel(MyProject_Model_Abstract $model) 
    {
        $this->_model = $model;
    }


}

