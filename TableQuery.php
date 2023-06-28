<?php

namespace Taf;

class TableQuery
{
    public $table_name = null;
    public $description = [];
    public function __construct($table_name)
    {
        $this->table_name = $table_name;
    }
    function dynamicCondition($data_condition, $operation)
    {
        if (empty($data_condition)) {
            return "";
        }
        $keyOperateurValue = array();
        foreach ($data_condition as $key => $value) {
            $keyOperateurValue[] = addslashes($key) . " " . $operation . " '" . addslashes($value) . "'";
        }
        return "where " . implode(" and ", $keyOperateurValue);
    }
    function dynamicInsert($assoc_array)
    {
        $keys = array();
        $values = array();
        foreach ($assoc_array as $key => $value) {
            $keys[] = addslashes(htmlspecialchars($key));
            if ($value == '') {
                $values[] = 'null';
            } else {
                $values[] = "'" . addslashes(htmlspecialchars($value)) . "'";
            }
        }
        return "INSERT INTO $this->table_name(" . implode(",", $keys) . ") VALUES(" . implode(",", $values) . ")";
    }

    function dynamicUpdate($assoc_array, $condition)
    {
        $keyEgalValue = array();
        foreach ($assoc_array as $key => $value) {
            if ($value == '') {
                $keyEgalValue[] = addslashes($key) . " = null";
            } else {
                $keyEgalValue[] = addslashes($key) . " = '" . addslashes($value) . "'";
            }
        }
        return "update $this->table_name set " . implode(",", $keyEgalValue) . " " . $condition;
    }
}
