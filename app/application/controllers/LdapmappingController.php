<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 24.01.2019
 * Time: 09:14
 */

// namespace app\controllers\rest\admin\user;

use MyProject_Controller_RestAbstract as RestAbstract;
use MyProject_Ldap_Ldap as Ldap;
use MyProject_Ldap_LdapResult as LdapResult;

class LdapmappingController  extends RestAbstract
{
    /**
     * @var Ldap
     */
    private $_ldap = null;

    public function init()
    {
        $this->_initLdapSession();

        // psiburg => Pascal Schäfers
        // dkaminski => d.koenigs
        // wareneingang
        // dispo
        // innendienst
    }

    public function indexAction() {
        die('Hello FROM Controller ' . __CLASS__ . ' with DefaultAction ' . __METHOD__);
    }

    private function _initLdapSession()
    {
        $cnf = Zend_Registry::get('ldap');

        $this->_ldap = new Ldap($cnf['host'], $cnf['port']);
        $this->_ldap->bind($cnf['dn'], $this->_ldap->_dec($cnf['pass']));
    }

    public function listAction()
    {

        $userModel = new Model_User();
        $userRows = $userModel->fetchEntries( ['where' => 'user_id <> 117 && deleted=0']);

        $aList = [
            'num_users' => 0,
            'num_failed' => 0,
            'num_ambigous' => 0,
            'num_exists' => 0,
            'failedUsers' => [],
            'ambigUsers' => [],
            'existsUsers' => []
        ];

        foreach($userRows as $_user) {

            $step = 0;
            $stepLimit = 4;
            $stepName = '';
            $found = false;
            $foundBy = '';
            $ldapEntry = null;
            do {
                switch($step) {

                    case 0:
                        if ($_user['ldap_user']) {
                            $ldapEntry = $this->_ldap->searchUser($_user['ldap_user']);
                        }
                        $stepName = 'LdapUser';
                        break;

                    case 1:
                        if ($_user['email']) {
                            $ldapEntry = $this->_ldap->searchEmail($_user['email']);
                        }
                        $stepName = 'Email';
                        break;

                    case 2:
                        if ($_user['user_name'][1] != '.') {
                            $_username = $_user['user_name'][0] . '.' . substr($_user['user_name'], 1);
                        } else {
                            $_username = $_user['user_name'];
                        }
                        $ldapEntry = $this->_ldap->searchUser( $_username);
                        $stepName = 'User';
                        break;

                    case 3:
                        if ($_user['vorname'] && $_user['nachname']) {
                            $ldapEntry = $this->_ldap->searchRealname($_user['vorname'], $_user['nachname']);
                        }
                        $stepName = 'Name';
                        break;

                    default:
                        // Nothing
                }
                $step++;

                if (!is_null($ldapEntry) && $ldapEntry->count() == 1) {
                    $entries = $this->_ldap->flatten($ldapEntry->entries());
                    $aData = [];
                    if ($entries[0]['samaccountname']) {
                        $aData['ldap_user'] = $entries[0]['samaccountname'];
                    }
                    if (!empty($entries[0]['email'])) {
                        $aData['email'] = $entries[0]['email'];
                    }
                    if (!empty($entries[0]['mail'])) {
                        $aData['email'] = $entries[0]['mail'];
                    }
                    if (!empty($entries[0]['givenname'])) {
                        $aData['vorname'] = $entries[0]['givenname'];
                    }
                    if (!empty($entries[0]['sn'])) {
                        $aData['nachname'] = $entries[0]['sn'];
                    }
                    if (0) {
                        print_r(['<pre>', __LINE__, __FILE__, __METHOD__,
                            'entries for user' => $_user,
                            'ldapEntry->entries' => $ldapEntry->entries(),
                            'flatten entries:' => $entries,
                            'updateable data:' => $aData,
                            '</pre>']);
                        exit;
                    }
                    $found = true;
                    $foundBy = $stepName;
                }
            } while($found === false && $step < $stepLimit);

            if ($found) {
                $_user['foundBy'] = $foundBy;
            }

            $_sUser = strtr( json_encode(array_values($_user)), ['\\' => '', '"' => ' ']);

            $aList['num_users']++;

            if (is_null($ldapEntry) || $ldapEntry->count() == 0) {
                $aList['num_failed']++;
                $aList['failedUsers'][] = $_sUser;

            } elseif ($ldapEntry->count() == 1) {
                $entries = $this->_ldap->flatten($ldapEntry->entries());
                $aList['num_exists']++;
                $aList['existsUsers'][] =
                    $_sUser . ' => ['
                    . $entries[0]['samaccountname'] . ' => '
                    . $entries[0]['name'] . ' '
                    . ($entries[0]['email'] ?? '') . ']';
                $aData = [];
                if ($entries[0]['samaccountname']) {
                    $aData['ldap_user'] = $entries[0]['samaccountname'];
                }
                if (!empty($entries[0]['email'])) {
                    $aData['email'] = $entries[0]['email'];
                }
                if (!empty($entries[0]['mail'])) {
                    $aData['email'] = $entries[0]['mail'];
                }

            } else {
                $entries = $ldapEntry->entries();
                $aSortEntries = array_map($entries, function($aEntry) {
                    return '[' . $aEntry['samaccountname'] . ' => ' . $aEntry['name'] . ']';
                });
                $aList['num_ambigous']++;
                $aList['ambigUsers'][] = $_sUser . implode(', ', $aSortEntries);
                // $userModel->update([(int)$_user['user_id']], ['ldap_user' => null]);
            }
        }

        $this->sendJSONSuccess($aList);
    }

    public function updateAction()
    {
        $userModel = new Model_User();
        $userRows = $userModel->fetchEntries(['where' => 'user_id <> 117 && deleted=0']);

        $db = $userModel->getStorage()->getAdapter();
        $db->update(
            $userModel->getStorage()->info(Zend_Db_Table::NAME), ['ldap_user' => 'd.koenigs'], ['user_name LIKE ?' => 'dkaminski']
        );
        $db->update(
            $userModel->getStorage()->info(Zend_Db_Table::NAME), ['ldap_user' => 'p.schaefers'], [ 'user_name LIKE ?' => 'psiburg' ]
        );

        $aList = [
            'num_users' => 0,
            'num_failed' => 0,
            'num_ambigous' => 0,
            'num_exists' => 0,
            'failedUsers' => [],
            'ambigUsers' => [],
            'existsUsers' => []
        ];

        foreach($userRows as $_user) {

            $step = 0;
            $stepLimit = 4;
            $stepName = '';
            $found = false;
            $foundBy = '';
            $ldapEntry = null;
            do {
                switch($step) {

                    case 0:
                        if ($_user['ldap_user']) {
                            $ldapEntry = $this->_ldap->searchUser($_user['ldap_user']);
                        }
                        $stepName = 'LdapUser';
                        break;

                    case 1:
                        if ($_user['email']) {
                            $ldapEntry = $this->_ldap->searchEmail($_user['email']);
                        }
                        $stepName = 'Email';
                        break;

                    case 2:
                        if ($_user['user_name'][1] != '.') {
                            $_username = $_user['user_name'][0] . '.' . substr($_user['user_name'], 1);
                        } else {
                            $_username = $_user['user_name'];
                        }
                        $ldapEntry = $this->_ldap->searchUser( $_username);
                        $stepName = 'User';
                        break;

                    case 3:
                        if ($_user['vorname'] && $_user['nachname']) {
                            $ldapEntry = $this->_ldap->searchRealname($_user['vorname'], $_user['nachname']);
                        }
                        $stepName = 'Name';
                        break;

                    default:
                        // Nothing
                }
                $step++;

                if (!is_null($ldapEntry) && $ldapEntry->count() == 1) {
                    $found = true;
                    $foundBy = $stepName;
                }
            } while($found === false && $step < $stepLimit);

            if ($found) {
                $_user['foundBy'] = $foundBy;
            }

            $_sUser = strtr( json_encode(array_values($_user)), ['\\' => '', '"' => ' ']);

            $aList['num_users']++;

            if (is_null($ldapEntry) || $ldapEntry->count() == 0) {

                if (in_array($_user['user_name'], [ 'wareneingang', 'dispo', 'innendienst'])) {
                    // Nothing
                } else {
                    $aList['num_failed']++;
                    $aList['failedUsers'][] = $_sUser;

                    $userModel->update([
                        'deleted' => 1,
                        'freigegeben' => 'Nein'],
                        (int)$_user['user_id']);
                }

            } elseif ($ldapEntry->count() == 1) {
                $entries = $this->_ldap->flatten($ldapEntry->entries());
                $aList['num_exists']++;
                $aList['existsUsers'][] =
                    $_sUser . ' => ['
                    . $entries[0]['samaccountname'] . ' => '
                    . $entries[0]['name'] . ' '
                    . ($entries[0]['email'] ?? '') . ']';
                $aData = [];
                if ($entries[0]['samaccountname']) {
                    $aData['ldap_user'] = $entries[0]['samaccountname'];
                }
                if (!empty($entries[0]['email'])) {
                    $aData['email'] = $entries[0]['email'];
                }
                if (!empty($entries[0]['mail'])) {
                    $aData['email'] = $entries[0]['mail'];
                }
                if (!empty($entries[0]['givenname'])) {
                    $aData['vorname'] = $entries[0]['givenname'];
                }
                if (!empty($entries[0]['sn'])) {
                    $aData['nachname'] = $entries[0]['sn'];
                }
                if (!empty($aData)) {
                    $userModel->update($aData, (int)$_user['user_id']);
                }

            } else {
                $entries = $ldapEntry->entries();
                $aSortEntries = array_map($entries, function($aEntry) {
                    return '[' . $aEntry['samaccountname'] . ' => ' . $aEntry['name'] . ']';
                });
                $aList['num_ambigous']++;
                $aList['ambigUsers'][] = $_sUser . implode(', ', $aSortEntries);
                // $userModel->update([(int)$_user['user_id']], ['ldap_user' => null]);
            }
        }

        $this->sendJSONSuccess($aList);
    }

    public function searchUser($query = '' ) {
        $search = $this->_ldap->searchUser( $query );

        $this->sendResult($search);
    }

    public function searchMail( string $query ) {
        $search = $this->_ldap->searchEmail( $query );

        $this->sendResult($search);
    }

    public function searchRealname( string $name, string $surname = '' ) {

        if (!$surname) {
            $search = $this->_ldap->search('displayname=:name', [':name' => str_replace('+', ' ', $name)]);
        } else {
            $search = $this->_ldap->searchRealname($name, $surname);
        }

        $this->sendResult($search);
    }

    public function searchSurname( string $surname ) {
        $search = $this->_ldap->search( 'sn=:surname', [':surname' => $surname] );

        $this->sendResult($search);
    }

    /**
     * @param $searchResult
     */
    public function sendResult(LdapResult $searchResult) {
        $count = $searchResult->count();
        $entries = $searchResult->entries();

        $this->sendJSONSuccess([
            'filter' => $searchResult->getFilter(),
            'count' => $count,
            'entriesFlatten' => $this->_ldap->flatten($entries),
            'entriesRaw' => $this->_ldap->cleanup($entries),
        ]);
    }

}