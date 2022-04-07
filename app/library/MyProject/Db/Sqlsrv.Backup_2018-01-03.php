<?php 
class Sqlsrv_Exception extends Exception {}

class Sqlsrv_Expr {
    protected $expr = '';
    public function __construct($expr) {
        $this->expr = $expr;
    }
    
    public function __toString() {
        return $this->expr;
    }
}

class Sqlsrv_Stmt {
    protected $handle;
    protected $connid;
    protected $defaultFetchMode;
    
    public function __construct($handle, $opt = array() ) {
        $this->handle = $handle;        
        $this->defaultFetchMode = (isset($opt['fetchMode'])) ? $opt['fetchMode'] : SQLSRV_FETCH_NUMERIC;
    }
    
    public function fetch($FetchMode = null) {
        if (!$FetchMode) $FetchMode = $this->defaultFetchMode;
        return sqlsrv_fetch_array( $this->handle, $FetchMode );
    }   
    
    public function is_resource() 
    {
        return ($this->handle !== null);
    }

    public function num_rows() 
    {
        return sqlsrv_num_rows($this->handle);
    }
    
    public function __destruct() {
        if ($this->is_resource()) {
			sqlsrv_free_stmt( $this->handle );
		}
    }
}

class Sqlsrv {
    public $connid = null;
    public $fetchMode;
    public $scrollable = SQLSRV_CURSOR_FORWARD;
    protected static $_instances = array();
    
    const FETCH_ASSOC = SQLSRV_FETCH_ASSOC;
    const FETCH_NUM   = SQLSRV_FETCH_NUMERIC;
    
    /**
     * @abstract Lets you move one row at a time starting at the first row of 
     * the result set until you reach the end of the result set.
     * This is the default cursor type.
     * sqlsrv_num_rows returns an error for result sets created with this cursor type.
     * forward is the abbreviated form of SQLSRV_CURSOR_FORWARD.
     */
    const SCROLLABLE_FORWARD = SQLSRV_CURSOR_FORWARD;
        
    /**
     * Lets you access rows in any order but will not reflect changes in the database.
     * static is the abbreviated form of SQLSRV_CURSOR_STATIC.
     */
    const SCROLLABLE_STATIC  = SQLSRV_CURSOR_STATIC;
	
     /**
     * @abstract Lets you access rows in any order and will reflect changes in the database.
     * sqlsrv_num_rows returns an error for result sets created with this cursor type.
     * dynamic is the abbreviated form of SQLSRV_CURSOR_DYNAMIC.
     */
    const SCROLLABLE_DYNAMIC = SQLSRV_CURSOR_DYNAMIC;
    
    /**
     * @abstract Lets you access rows in any order. 
     * However, a keyset cursor does not update the row count if a row is 
     * deleted from the table (a deleted row is returned with no values).
     * keyset is the abbreviated form of SQLSRV_CURSOR_KEYSET.
     */
    const SCROLLABLE_KEYSET = SQLSRV_CURSOR_KEYSET;
	
    /**
     * @abstract 
     * Lets you access rows in any order. Creates a client-side cursor query.
     * buffered is the abbreviated form of SQLSRV_CURSOR_CLIENT_BUFFERED.
     */
    const SCROLLABLE_BUFFERED = SQLSRV_CURSOR_CLIENT_BUFFERED;

    protected function __construct($serverName, $connectionInfo) 
    {
        $this->connid = sqlsrv_connect( $serverName, $connectionInfo);
//    	die('<pre>' . print_r(array('serverName'=>$serverName, 'connectionInfo' => $connectionInfo, 'connid'=>$this->connid), 1));

        $this->setFetchMode(SQLSRV_FETCH_NUMERIC);

        if( !$this->connid ) {
             die( print_r( sqlsrv_errors(), true));
        }
    }
    
    /**
     * 
     * @param string $host
     * @param string $user
     * @param string $pwd
     * @param string $dbname
     * @return SQLSRV
     */
    public static function getInstance($host = 'localhost', $user = '', $pwd = '', $dbname = null) 
    {
        $dsn = 'sqlsrv://' . $user . ($pwd ? ':'.$pwd:'') . '@' . $host . ($dbname ? '/' . $dbname : '');
        
        if (!isset(self::$_instances[$dsn])) {
            $connectionInfo = array();
            if (!$host)  $host = 'localhost';
            if ($user)   $connectionInfo['UID']      = $user;
            if ($pwd)    $connectionInfo['PWD']      = $pwd; 
            if ($dbname) $connectionInfo['Database'] = $dbname;
        
            $class = __CLASS__;
            self::$_instances[$dsn] = new $class($host, $connectionInfo);
        }
        return self::$_instances[$dsn]; 
    }
    
    public static function factory($dbParams)
    {
        $host   = (isset($dbParams['host']))     ? $dbParams['host']     : '';
        $user   = (isset($dbParams['username'])) ? $dbParams['username'] : '';
        $pwd    = (isset($dbParams['password'])) ? $dbParams['password'] : '';
        $dbname = (isset($dbParams['dbname']))   ? $dbParams['dbname']   : '';
        
        return self::getInstance($host, $user, $pwd, $dbname);
    }
        
    public function setFetchMode($fetchMode) 
    {
        $this->fetchMode = $fetchMode; // = SQLSRV_FETCH_ASSOC
    }
        
    public function setScrollableCursor($cursor) 
    {
        $this->scrollable = $cursor;
    }
        
    public function query($SQL, $params = array(), $opts = array() ) 
    {
        $Query = $SQL;
        if (is_array($params)) foreach($params as $_field => $_term) {
            if (is_numeric($_term)) {
                $Query = str_replace(":$_field", $_term, $Query);
            } elseif($_term === null) {
                $Query = str_replace(":$_field", 'NULL', $Query);
            } elseif ($_term instanceof Sqlsrv_Expr) {
                $Query = str_replace(":$_field", $_term, $Query);
            } else {
                $Query = str_replace(":$_field", "'" . str_replace("'", "\\\'", $_term) . "'", $Query);
            }
        }
		$scrollable = (isset($opts['scrollable']) ? $opts['scrollable'] : $this->scrollable);

        $stmt = sqlsrv_query($this->connid, $Query, array(), array('Scrollable' => $scrollable) );
        
        if (sqlsrv_errors()) {
            $err = print_r(sqlsrv_errors(), 1);
            throw new Sqlsrv_Exception( $Query . PHP_EOL . $err );
        }
        return ($stmt) ? new Sqlsrv_Stmt($stmt) : null;
    }
    
    public function fetchAll($SQL, $params = array(), $FetchMode = null) 
    {	
        if (!is_resource($this->connid)) return null;
        if (!$FetchMode) $FetchMode = $this->fetchMode;
        $rows = array();

        $stmt = $this->query($SQL, $params);

        if ($stmt !== null || $stmt->is_resource() )  {
            $n = $stmt->num_rows(); // sqlsrv_num_rows($stmt);

            if  ($n) {
                while($row = $stmt->fetch( $FetchMode ) ) {
                    $rows[] = $row;
                }
            } else {
                echo '#' . __LINE__ . ' ERR: ' . print_r(sqlsrv_errors(), 1) . '<br/>' . PHP_EOL;
            }
        }
        return $rows;
    }
    
    public function fetchOne($SQL, $params = array() ) 
    {	
        if (!is_resource($this->connid)) return null;
		
        $stmt = $this->query($SQL, $params, array('scrollable' => self::SCROLLABLE_STATIC) );

        if ($stmt !== null || $stmt->is_resource() )  {
            $n = $stmt->num_rows(); // sqlsrv_num_rows($stmt);

            if  ($n) {
                $row = $stmt->fetch( self::FETCH_NUM );
                return $row[0];
            } else {
                echo '#' . __LINE__ . ' ERR: ' . print_r(sqlsrv_errors(), 1) . '<br/>' . PHP_EOL;
            }
        }
        
        return false;
    }    
    
    public function close() 
    {
        if (!is_null($this->connid)) {
            sqlsrv_close( $this->connid );
        }
        $this->connid = null;
        return true;
    }
    
    public function __destruct() 
    {
        $this->close();
    }
}

class MyProject_Db_Sqlsrv extends Sqlsrv {}
