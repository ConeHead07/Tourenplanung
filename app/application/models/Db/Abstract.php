<?php

class Model_Db_Abstract extends Zend_Db_Table_Abstract
{
    protected static $_instances = [];

    public function tableName(): string {
        return $this->_name;
    }

    public function keys(): array {
        return $this->_primary;
    }

    public function key(int $idx = 0) {
        return ((array)$this->_primary)[$idx];
        if ($idx === 0) {
            return !is_array($this->_primary) ? $this->_primary : $this->_primary[0];
        }
        return $this->_primary[$idx];
    }

    public static function new(): self {
        $class = static::class;
        return new $class();
    }

    public static function obj(): self {
        $staticClass = static::class;

        if ( empty(static::$_instances[$staticClass])) {
            $class = static::class;
            static::$_instances[$staticClass] = new $class();
        }
        return static::$_instances[$staticClass];
    }

    public static function get($mixed): array {
        $aids = is_array($mixed) ? $mixed : func_get_args();
        /** @var Zend_Db_Table_Rowset $rows */
        $rows = call_user_func_array( [self::obj(), 'find'], $aids );
        return ($rows->count()) ? $rows->getRow(0)->toArray() : [];
    }
}
