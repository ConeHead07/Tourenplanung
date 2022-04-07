<?php

class Dummie_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
	echo __CLASS__. '::' . __METHOD__. '<br>'."\n";
    }

    public function indexAction()
    {
        $this->_helper->layout->setLayout( 'layoutfoo' );
        // action body
	echo __CLASS__. '::' . __METHOD__. '<br>'."\n";
        $this->view->listData = array(
        	"Eins",
        	"Zwei",
        	"Drei",
        	"Vier",
        	"F�nf",
			"Dummie"
        );
    }
}
