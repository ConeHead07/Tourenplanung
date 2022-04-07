<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of userController
 * @author rybka
 */
class UserController extends Zend_Controller_Action 
{

    public function init() {
        parent::init();
        $this->_rq = $this->getRequest();
        $this->view->layout = $this->_rq->getParam( 'layout', 1);

        if ($this->view->layout === 0 || $this->view->layout === '0') {
            $this->getHelper( 'layout' )->disableLayout();
        }
    }
    
    //put your code here
    public function indexAction() {
        $db = Zend_Registry::get('db');
        $user = MyProject_Model_Database::loadModel('user');
        $this->view->userlist = $user->fetchEntries();
    }

    public function loginAction() 
    {

        if (!Zend_Auth::getInstance()->hasIdentity()) {
            //require_once 'forms/User/Login.php';
            $form = new Form_User_Login();
            Zend_Registry::set('pageTitel', 'Login');

            $form->getElement( 'user_name')->setValue('*');

            $this->view->pageTitel = 'Login';
            $rq = Zend_Controller_Front::getInstance()->getRequest();
            $uname = $rq->getPost('user_name');
            $upass = $rq->getPost('user_pw');

            if ($uname) {
                $form->getElement( 'user_name')->setValue((string)$uname);
            }
            $this->view->login = $form->render();

            if (!$uname || !$upass) {
                $this->view->error = 'Es wurden keine oder unvollständige Anmeldedaten angegeben!';
                return;
            }

            $auth = Zend_Auth::getInstance();
            if (1) {
                $authAdapter = new MyProject_Auth_Adapter($uname, $upass);
            } else {
                $authAdapter = new Zend_Auth_Adapter_DbTable(
                                Zend_Registry::get('db'),
                                'mr_user',
                                'user_name',
                                'user_pw',
                                'MD5(?)'
                );
                $authAdapter->setIdentity($uname);
                $authAdapter->setCredential($upass);
            }
            $result = $auth->authenticate($authAdapter);
            if (!$result->isValid()) {
                $auth->clearIdentity();
                switch ($result->getCode()) {
                    case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                        $this->view->error = 'Ungültiger Benutzername!';
                        break;

                    case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                        $this->view->error = 'Ungültiger Benutzername oder Passwort!';
                        break;

                    default:
//                        echo 'authentication successful.move forward';
                        $this->redirect('/user/login');
                        break;
                }
            } else {
                // $result->getCode() == Zend_Auth_Result::SUCCESS;
                $identity = $authAdapter->getResultRowObject(
                    NULL, array('user_pw')
                );
                
                $auth->getStorage()->write($identity);
                $this->redirect('/');
                echo Zend_Debug::dump(Zend_Auth::getInstance()->getIdentity());
                echo "LinkToLogout";
            }
        }
        return;
    }

    public function logoutAction() {
        $user_id = MyProject_Auth_Adapter::getUserId();

        if ($user_id) {
            $userModel = new Model_User();
            $userModel->setLogout($user_id);
        }

        Zend_Auth::getInstance()->clearIdentity();
        session_regenerate_id( true );
        $this->_helper->viewRenderer('login');
    }

    public function registerAction() {
//        echo '#' . __LINE__ . ' ' . __METHOD__ . "<br>\n";
        /* @var $modelUser Model_User */
        $modelUser = MyProject_Model_Database::loadModel('user');

        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $form = new Form_User_Register();

            /* @var $rq Zend_Controller_Request_Http */
            $rq = Zend_Controller_Front::getInstance()->getRequest();
            if ($rq->isPost() &&
                    false !== $rq->getPost('register', FALSE)
            ) {
                if ($form->isValid($rq->getPost())) {
                    $modelUser->insert($form->getValues());
                }
            }
            //require_once 'forms/User/Register.php';
            $this->view->login = $form->render();
        } else {
            $this->_redirect('/');
        }
    }

    public function insertdummiesAction() {
        /* @var $modelUser Model_User */
        $modelUser = MyProject_Model_Database::loadModel('user');
        $modelUser->insertDummies();
        $this->_forward('index');
    }

    public function listAction() {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $this->view->grideditAllowed = Zend_Registry::get('acl')->isAllowed(
                $identity->user_role, 'user', 'grideditdata');
    }

    public function datalistAction() {
        /* @var $modelUser Model_User */
        $modelUser = MyProject_Model_Database::loadModel('user');
        $this->view->datalist = $modelUser->fetchEntries();
//        echo Zend_Debug::dump($this->view->datalist);
    }
    
    public function profileAction()
    {
        $rq = $this->getRequest();
        $id = $rq->getParam('id');
        
        $modelLager = new Model_Lager();
        
        /** @var $modelUser Model_User */
        $modelUser = MyProject_Model_Database::loadModel('user');
        
        $this->view->id = $id;
        $this->view->format = $rq->getParam('format', '');
        $this->view->user = (object)$modelUser->fetchEntry($id);
        $modelProfile = new Model_UserProfile();
        
        if ($rq->isPost()) {
            $data = $rq->getParam('profile');
            $modelProfile->updateProfile($id, $data);
        }
        $this->view->dataConfig = array(
            (object)array(
                'label' => 'Standort',
                'key' => 'standort',
                'type' => 'select',
                'options' => (array)$modelLager->getAssocLagerNames()
            )
        );
        $this->view->data = (array)$modelProfile->getProfile($id);
        
    }
    
    public function grideditdataAction()
    {
        $return = new stdClass();
        
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        /* @var $modelUser Model_User */
        $modelUser = MyProject_Model_Database::loadModel('user');
        $data = $_REQUEST;
        if ($data['oper'] == 'edit' || $data['oper'] == 'add') {
            if ($data['user_pw'] && $data['user_pw'] == $data['user_pw_wh']) {
                $data['user_pw'] = md5($data['user_pw']);
            } else {
                unset($data['user_pw']);
            }
        }
        
        try {
            switch($data['oper']) {
                case 'edit':
                    $modelUser->update($data, $data['id']);
                    break;

                case 'add':
                    $data['ldap_user'] = $data['user_name'];
                    $modelUser->insert($data);
                    break;

                case 'del':
                    $modelUser->delete($data['id']);
                    break;                
            }
            $return->type = 'success';
            $return->msg = 'OK';
        } catch(Exception $e) {
            $return->type = 'error';
            $return->err = $e->getMessage();
        }
        
        $this->_helper->json($return);
    }

    public function gridresponsedataAction() 
    {
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        
        /* @var $modelUser Model_User */
        $modelUser = MyProject_Model_Database::loadModel('user');
        
        /* @var $storageUser Model_Db_User */
        $storageUser = $modelUser->getStorage();
        
        $TblCnf = include APPLICATION_PATH . '/configs/dbtables/user.inc.php';
        
        
        $response = new stdClass();
        
        $TblCnfParser = MyProject_Parser_TableConf::getInstance();
        $TblCnfParser->parse_conf($TblCnf);

        $mitarbeiter = $this->getRequest()->getParam('username');
        
        $page  = (int) $this->getRequest()->getParam('page', 1);
        $limit = (int) $this->getRequest()->getParam('rows', 100);
        $sidx  = $this->getRequest()->getParam('sidx', null);
        $sord  = $this->getRequest()->getParam('sord', 'ASC');
        $pid   = $this->getRequest()->getParam('pid', '');
        
        if (!in_array(strtoupper($sord), array('ASC', 'DESC'))) $sord = 'ASC';
        
        $opt = array("additionalFields" => array('standort'));
        $sqlWhere = JqGridSearch::getSqlBySearch($TblCnf, $opt);
        header('X-Debug-GetWherePartBySearch: '.json_encode($sqlWhere));
        header('X-Debug-SearchRequest: '.json_encode($_REQUEST));
        
        /* @var $select Zend_Db_Table_Select */
        $select = $db->select();
        $select->from(array('u'=>$storageUser->info(Zend_Db_Table::NAME)), new Zend_Db_Expr('COUNT(*) AS count'));
        $select->joinLeft( array('p' => 'mr_user_profile'), 'p.user_id = u.user_id' );
        if ($sqlWhere) $select->where ($sqlWhere);
//        die($select->assemble());
        $count = $db->fetchOne($select);

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page = $total_pages;
        
        $start = max(0, $limit * $page - $limit); // do not put $limit*($page - 1)
        
        /* @var $select Zend_Db_Table_Select */
        $select = $db->select();
        $select->from(array('u'=>$storageUser->info(Zend_Db_Table::NAME)), new Zend_Db_Expr('u.*'));
        $select->joinLeft( array('p' => 'mr_user_profile'), 'p.user_id = u.user_id', new Zend_Db_Expr('p.standorte') );
        if ($sqlWhere) $select->where ($sqlWhere);
        if ($sidx) $select->order( $sidx . ' ' . $sord );
        
        $select->limit($limit, $start);
        
        /* @var $result Zend_Db_Statement */
        header('X-Debug-SQL: ' . json_encode($select->assemble()) );
        $result = $db->query($select);
        $num_fields = $result->columnCount();

        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->rows = $result->fetchAll(Zend_Db::FETCH_ASSOC);
        
        foreach($response->rows as $i => $_) {
            $response->rows[$i]['user_pw'] = '';
            $response->rows[$i]['user_pw_wh'] = '';
        }
        
        $this->view->gridresponsedata = $response;
        
    }

}

