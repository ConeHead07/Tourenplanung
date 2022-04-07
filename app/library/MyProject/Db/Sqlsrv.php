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
    protected $query = '';
    protected $adapter = null;
    protected $fieldsMetaData = [];
    protected $fieldNames = [];
    protected $fieldTypes = [];

    public static $uniqueFoundFieldTypes = [];
    
    public function __construct($handle, $opt = array(), Sqlsrv $adapter ) {
        $this->handle = $handle;
        $this->adapter= $adapter;
        $this->defaultFetchMode = (isset($opt['fetchMode'])) ? $opt['fetchMode'] : SQLSRV_FETCH_NUMERIC;
		if (isset($opt['query'])) $this->query = $opt['query'];


        $this->fieldsMetaData = sqlsrv_field_metadata($this->handle);

        for($i = 0; $i < count($this->fieldsMetaData); $i++) {
            $this->fieldNames[$i] = $this->fieldsMetaData[$i]['Name'];
            $this->fieldTypes[$i] = $this->fieldsMetaData[$i]['Type'];

            if (!isset(self::$uniqueFoundFieldTypes[ $this->fieldTypes[$i] ])) {
                self::$uniqueFoundFieldTypes[$this->fieldTypes[$i]] = 1;
            } else {
                self::$uniqueFoundFieldTypes[$this->fieldTypes[$i]]++;
            }
        }
        // echo 'Field-Types: ' . print_r(array_combine($this->fieldNames, $this->fieldTypes), 1) . PHP_EOL;
	}
    
    public function fetch($FetchMode = null) {
        if (!$FetchMode) $FetchMode = $this->defaultFetchMode;

        // A row must be retrieved with sqlsrv_fetch before retrieving data with sqlsrv_get_field.
        if (!($resultOfSqlsrvFetch = sqlsrv_fetch( $this->handle ))) {
            // echo '#' . __LINE__ . " ERR resultOfSqlsrvFetch(..., $FetchMode) " . $resultOfSqlsrvFetch . '(' . gettype($resultOfSqlsrvFetch) . ')' . __METHOD__ . '<br>' . PHP_EOL;
            $err = sqlsrv_errors(SQLSRV_ERR_ALL);
            if ($err !== null) die('#' . __LINE__ . ' ' . __METHOD__ . '; ' . print_r($err,1));
            return false;

        } else {
            // echo '#' . __LINE__ . " OK resultOfSqlsrvFetch(..., $FetchMode) " . $resultOfSqlsrvFetch . '(' . gettype($resultOfSqlsrvFetch) . ')' . __METHOD__ . '<br>' . PHP_EOL;
        }


        $row = [];
        for($i = 0; $i < count($this->fieldNames); $i++) {
            $_field = $this->fieldNames[$i];
            switch($this->fieldTypes[$i]) {
                case 12:
                    $_value = sqlsrv_get_field($this->handle, $i, SQLSRV_PHPTYPE_STRING("UTF-8"));
                    break;

                case 93:
                    $_v = sqlsrv_get_field($this->handle, $i);
                    $_value = ($_v instanceof DateTime && !is_null($_v)) ? $_v->format('Y-m-d H:i:s') : null;
                    break;

                default:
                    $_value = sqlsrv_get_field($this->handle, $i);
            }

            if ($FetchMode == SQLSRV_FETCH_NUMERIC || $FetchMode == SQLSRV_FETCH_BOTH) {
                $row[$i] = $_value;
            }
            if ($FetchMode == SQLSRV_FETCH_ASSOC || $FetchMode == SQLSRV_FETCH_BOTH) {
                $row[$_field] = $_value;
            }
        }

        // die(print_r($row,1));
        $err = sqlsrv_errors(SQLSRV_ERR_ALL);
        if ($err !== null) die(print_r($err,1));

        return $row;
    }   
    
    public function is_resource() 
    {
        return (!is_null($this->adapter) && $this->handle !== null);
    }

    public function num_rows() 
    {
        return sqlsrv_num_rows($this->handle);
    }
    
    public function __destruct() {
        if ($this->is_resource()) {
            // On Script-Shutdown stmt-Handle is already closed or invalid
            // Hence, we wrap it into try-catch
			try {
                // sqlsrv_free_stmt($this->handle);
                $this->handle = null;
            } catch(Exception $e) {
                error_log("Can not free sqlsrv-stmt: " . $e->getMessage() );
            }
			Sqlsrv::errorLog( $this->query );
		}
    }
}

class Sqlsrv {
    public $connid = null;
    public $fetchMode;
    public $scrollable = SQLSRV_CURSOR_FORWARD;
    protected static $_instances = array();
	protected static $lastQuery = '';
    
    const FETCH_ASSOC = SQLSRV_FETCH_ASSOC;
    const FETCH_NUM   = SQLSRV_FETCH_NUMERIC;

    const TYPE_DATETIME = 93;
    const TYPE_VARCHAR = 12;

    
    /**
     * @abstract Lets you move one row at a time starting at the first row of 
     * the result set until you reach the end of the result set.
     * This is the default cursor type.
     * sqlsrv_num_rows returns an error for result sets created with this cursor type.
     * forward is the abbreviated form of SQLSRV_CURSOR_FORWARD.
     */
    const SCROLLABLE_FORWARD = SQLSRV_CURSOR_FORWARD;
	
	protected $aRegisteredCursorList = array();
        
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
		
		$this->aRegisteredCursorList = array(
			SQLSRV_CURSOR_FORWARD  => 'SQLSRV_CURSOR_FORWARD',
			SQLSRV_CURSOR_STATIC   => 'SQLSRV_CURSOR_STATIC',
			SQLSRV_CURSOR_DYNAMIC => 'SQLSRV_CURSOR_DYNAMIC',
			SQLSRV_CURSOR_KEYSET => 'SQLSRV_CURSOR_KEYSET',
			SQLSRV_CURSOR_CLIENT_BUFFERED => 'SQLSRV_CURSOR_CLIENT_BUFFERED',
		);
        $this->connid = sqlsrv_connect( $serverName, $connectionInfo);
//    	die('<pre>' . print_r(array('serverName'=>$serverName, 'connectionInfo' => $connectionInfo, 'connid'=>$this->connid), 1));

        $this->setFetchMode(SQLSRV_FETCH_NUMERIC);

        if( !$this->connid ) {
			self::errorLog();
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
		$cursorInfo = (
			isset($this->aRegisteredCursorList[$cursor]) 
			? $this->aRegisteredCursorList[$cursor] 
			: ': Not Registered in Class Sqlsrv'
		);
		error_log('SQLSRV INFO Set Default Scrollable Cursor to $cursor ' . $cursorInfo);
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
		self::$lastQuery = $Query;
		
		$aStmtOpts = array(
			'query' => $Query,
		);
        
        $errors = self::errorLog($Query);
		if ($errors) throw new Exception( $errors );
        return ($stmt) ? new Sqlsrv_Stmt($stmt, $aStmtOpts, $this) : null;
    }
	
	public static function errorLog($sql = '') {
		$err = sqlsrv_errors();
		if (is_null($err) || empty($err) || count($err) == 0 ) return false;
		try { 
			throw new Exception(); 
		} catch(Exception $e) {
			$stackTrace = $e->getTraceAsString(); 
		}
		$logText = '['.date('d-M-Y H:i:s DE') . '] SQLSRV Error ' . print_r($err,1) . PHP_EOL
			. '-> Stack-Trace:' . PHP_EOL
			. '-> ' . $stackTrace . PHP_EOL
			. ($err ? '-> SQL: ' . PHP_EOL . '-> ' . substr($sql, 0, 500) . PHP_EOL : '');
			
		error_log($logText );
		return $logText;
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
               throw new Exception('#' . __LINE__ . ' ERR: ' . print_r(sqlsrv_errors(), 1) . '<br/>' . PHP_EOL);
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
