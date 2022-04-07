<?php

/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 23.01.2019
 * Time: 15:34
 */

class MyProject_Ldap_Ldap
{
    private $_connect = null;
    private $_bind = false;

    private $_aDefaultSelectingAttributes = [];

    public function _dec( $s )  {
        $r = 'c3RycmV2';
        $b = 'YmFzZTY0X2RlY29kZQ==';

        return array_reduce([$b,$r,$b,$r], function($c, $f) { $fn = base64_decode($f); return $fn($c); }, $s);
    }

    public function __construct($host = '', $port = 389)
    {
        if (!empty($host)) {
            $this->connect($host, $port);
        }

        $this->_aDefaultSelectingAttributes = [
            'samaccountname',
            'dn',
            'cn',
            'givenname',
            'sn',
            'name',
            'physicaldeliveryofficename',
            'company',
            'streetaddress',
            'postalcode',
            'l',
            'st',
            'title',
            'description',
            'telephonenumber',
            'ipphone',
            'mail',
            'mobile',
            'badpwdcount',
            'lastlogon',
            'accountexpires'];
    }

    /**
     * Destructor, closes existing connections
     */
    public function __destruct() {
        if ( !is_null($this->_connect) && is_resource($this->_connect)) {
            ldap_close($this->_connect);
            $this->_connect = null;
        }
    }

    /**
     * @param $host
     * @param int $port
     * @return $this
     */
    public function connect($host, $port = 389)
    {
        $this->_connect = ldap_connect($host, $port);
        ldap_set_option($this->_connect, LDAP_OPT_PROTOCOL_VERSION, 3);
        return $this;
    }

    public function close() {
        if ($this->isConnected()) {
            ldap_close($this->_connect);
            $this->_connect = null;
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isConnected(): bool {
        $isNullConnector = is_null($this->_connect);
        $isResource = is_resource($this->_connect);
        $isConnected = !$isNullConnector && $isResource;
        if (0) die( '#' . __LINE__ . ' hallo from ' . __METHOD__ . '( ' . print_r( [func_get_args(), var_export($isNullConnector,1), var_export($isResource,1), var_export($isConnected,1), ' )'],1) . ')' );
        return $isConnected;
    }

    /**
     * @param $key
     * @param $val
     * @return $this
     */
    public function setOption($key, $val) {
        ldap_set_option($this->_connect, $key, $val);
        return $this;
    }

    /**
     * @return bool
     */
    public function isBinded(): bool {
        $isConnected = $this->isConnected() && $this->_bind;
        if (0) die( '#' . __LINE__ . ' hallo from ' . __METHOD__ . ': ' . print_r( var_export($isConnected,1), 1) );
        return $isConnected;
    }

    /**
     * @param $ldap_dn
     * @param $ldap_password
     * @return $this
     */
    public function bind($ldap_dn, $ldap_password)
    {
        if (!$this->isConnected()) {
            throw new Exception("Es existiert noch keine Verbindung für den Start einer Session!");
        }

        // We need this Option for working UTF-8
        $this->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);

        // Seems not really necessary
        $this->setOption(LDAP_OPT_REFERRALS, 0);

        // Create Session
        $this->_bind = ldap_bind($this->_connect, $ldap_dn, $this->_dec($ldap_password));
        try {
            $this->_bind = @ldap_bind($this->_connect, $ldap_dn, $ldap_password);
        } catch(\Exception $e) {
            $this->_bind = false;
        }
        return $this;
    }

    /**
     * @param $username
     * @return bool
     */
    public function userExists( $username ):bool {
        if (!$this->isBinded()) {
            throw new Exception("Es existiert noch keine gültige Session für eine Abfrage!");
        }
        if (0) die( '#' . __LINE__ . ' hallo from ' . __METHOD__ . '( ' . print_r( [func_get_args(), ' )'],1) . ')' );

        // WORKS ;-) $result = ldap_search($ldap_con, 'OU=Willich,OU=Mitarbeiter,OU=Benutzer,OU=merTens,DC=mertens,DC=ag', $filter); // , $filter);
        $searchDN = 'OU=Benutzer,OU=merTens,DC=mertens,DC=ag';
        $searchFilter = '(sAMAccountName=' . $username . ')';

        try {
            $result = ldap_search($this->_connect,
                'OU=Benutzer,OU=merTens,DC=mertens,DC=ag',
                '(sAMAccountName=' . $username . ')');
        } catch(\Exception $e) {
            echo $e->getMessage() . "<br>\n";
            echo $e->getTraceAsString() . "<br>\n";
            echo 'ldap_search(connector, ' . json_encode($searchDN) . ', ' . json_encode($searchFilter) . ')';
            return false;
        }
        return is_resource($result) && ldap_count_entries($this->_connect, $result) > 0;
    }

    /**
     * @param string $sFilter
     * @param array $aBindValues
     * @param array $aAttributes
     * @return MyProject_Ldap_LdapResult
     */
    public function search(string $sFilter, $aBindValues = [], array $aAttributes = [])
    {
        if (!$this->isBinded()) {
            throw new \Exception("Es existiert noch keine gültige Session für eine Abfrage!");
        }

        foreach($aBindValues as $k => $v) {
            $sFilter = str_replace($k, ldap_escape($v, "", LDAP_ESCAPE_FILTER), $sFilter);
        }

        // WORKS ;-) $result = ldap_search($ldap_con, 'OU=Willich,OU=Mitarbeiter,OU=Benutzer,OU=merTens,DC=mertens,DC=ag', $filter); // , $filter);
        if ($aAttributes == ['*']) {
            $searchResultHandle = ldap_search($this->_connect,
                'OU=Benutzer,OU=merTens,DC=mertens,DC=ag',
                '(' . $sFilter . ')');
        } else {
            $searchResultHandle = ldap_search($this->_connect,
                'OU=Benutzer,OU=merTens,DC=mertens,DC=ag',
                '(' . $sFilter . ')',
                $this->_aDefaultSelectingAttributes);
        }

        return new MyProject_Ldap_LdapResult($this->_connect, $searchResultHandle, $sFilter);
    }

    public static function escape(string $val): string
    {
        return addcslashes($val, '()\\');
    }

    /**
     * @param $username
     * @param array $aAttributes
     * @return MyProject_Ldap_LdapResult
     */
    public function searchUser( $username, array $aAttributes = [] )
    {
        return $this->search('sAMAccountName=:user', [':user' => $username], $aAttributes );
    }

    /**
     * @param string $email
     * @return MyProject_Ldap_LdapResult
     */
    public function searchEmail( string $email, array $aAttributes = [] ): MyProject_Ldap_LdapResult
    {
        return $this->search('mail=:mail', [ ':mail' => $email], $aAttributes);
    }

    /**
     * @param string $givenname
     * @param string $surname
     * @param array $aAttributes
     * @return MyProject_Ldap_LdapResult
     */
    public function searchRealname( string $givenname, string $surname, array $aAttributes = [] ): MyProject_Ldap_LdapResult
    {
        return $this->search('&(givenname=:gn)(sn=:sn)', [ ':gn' => $givenname, ':sn' => $surname], $aAttributes );
    }
    /**
     * @param string $surname
     * @param array $aAttributes
     * @return MyProject_Ldap_LdapResult
     */
    public function searchSurname( string $surname, array $aAttributes = [] ): MyProject_Ldap_LdapResult
    {
        return $this->search('sn=:sn', [ ':sn' => $surname], $aAttributes );
    }

    /**
     * @param resource $search
     * @return int
     */
    public function getSearchCountEntries( $search ) {
        return ldap_count_entries($this->_connect, $search );
    }

    /**
     * @param resource $search
     * @return array
     */
    public function getSearchEntries( $search ) {
        return ldap_get_entries($this->_connect, $search );
    }

    /**
     * @param string $email
     * @return bool
     */
    public function emailAddressExists( string $email ): bool {
        if (!$this->isBinded()) {
            throw new Exception("Es existiert noch keine gültige Session für eine Abfrage!");
        }
        if (0) die( '#' . __LINE__ . ' hallo from ' . __METHOD__ . '( ' . print_r( [func_get_args(), ' )'],1) . ')' );

        // WORKS ;-) $result = ldap_search($ldap_con, 'OU=Willich,OU=Mitarbeiter,OU=Benutzer,OU=merTens,DC=mertens,DC=ag', $filter); // , $filter);
        $result = ldap_search($this->_connect,
            'OU=Benutzer,OU=merTens,DC=mertens,DC=ag',
            '(mail=' . $email . ')');

        return ldap_count_entries($this->_connect, $result) > 0;
    }

    /**
     * @param string $username
     * @return array
     */
    public function getUser( string $username )
    {
        if (!$this->isBinded()) {
            throw new Exception("Es existiert noch keine gültige Session für eine Abfrage!");
        }
        if (0) die( '#' . __LINE__ . ' hallo from ' . __METHOD__ . '( ' . print_r( [func_get_args(), ' )'],1) . ')' );

        // WORKS ;-) $result = ldap_search($ldap_con, 'OU=Willich,OU=Mitarbeiter,OU=Benutzer,OU=merTens,DC=mertens,DC=ag', $filter); // , $filter);
        $result = ldap_search($this->_connect,
            'OU=Benutzer,OU=merTens,DC=mertens,DC=ag',
            '(sAMAccountName=' . $username . ')');

        return ldap_get_entries($this->_connect, $result);
    }

    /**
     * @param string $mailaddress
     * @return array
     */
    public function getUserByMail( string $mailaddress )
    {
        if (!$this->isBinded()) {
            throw new Exception("Es existiert noch keine gültige Session für eine Abfrage!");
        }
        if (0) die( '#' . __LINE__ . ' hallo from ' . __METHOD__ . '( ' . print_r( [func_get_args(), ' )'],1) . ')' );

        // WORKS ;-) $result = ldap_search($ldap_con, 'OU=Willich,OU=Mitarbeiter,OU=Benutzer,OU=merTens,DC=mertens,DC=ag', $filter); // , $filter);
        $result = ldap_search($this->_connect,
            'OU=Benutzer,OU=merTens,DC=mertens,DC=ag',
            '(mail=' . $mailaddress . ')');

        return ldap_get_entries($this->_connect, $result);
    }

    /**
     * @param $a
     * @return array|string
     */
    private function _jsonDecode( $a )
    {
        $d = [];
        if (is_array($a)) {
            foreach($a as $k => $v) {
                $d[ $k ] = $this->_jsonDecode($v);
            }
        } elseif (is_string($a)) {
            return utf8_encode($a);
        } elseif (is_numeric($a)) {
            return $a;
        } else {
            return '';
        }
        return $d;

    }

    /**
     * Cleanup Array from values that cannot be encoded by json_encode
     * @param $info
     * @return mixed
     */
    public function cleanup($info) {
        return json_decode( json_encode($info, JSON_PARTIAL_OUTPUT_ON_ERROR), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @param $ldapResultData
     * @return array
     */
    public function flatten($ldapResultData)
    {
        $ldapData = $this->cleanup($ldapResultData);

        $ldapFlatten = function($data) use(&$ldapFlatten) {
            if (!is_array($data)) {
                return $data;
            }

            $aFlattenData = [];

            foreach($data as $key => $val) {

                // Continue if key is numeric and val references to another List-Item
                // e.g. $ldapResultData[4] = 'title' AND $ldapResultData['title'] = 'Projektleiter'

                if (is_numeric($key) && !is_array($val) && isset($data[ $val ])) {
                    continue;
                }

                if (is_array($val) && isset($val['count'])) {
                    switch($val['count']) {
                        case 0:
                            $aFlattenData[ $key ] = '';
                            break;

                        case 1:
                            $aFlattenData[ $key ] = $ldapFlatten($val[0]);
                            break;

                        default:
                            $aFlattenData[ $key ] = $ldapFlatten($val);
                    }
                } else {
                    $aFlattenData[ $key ] = $val;
                }
            }

            return $aFlattenData;
        };

        return $ldapFlatten( $ldapData );

    }

}