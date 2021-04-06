<?php defined("BASEPATH") or exit("No direct script access allowed");

if (!function_exists("getAllColumnNameAndDefaultValue")) {
    /**
     * Get all columns name and their default value of given table
     *
     * @param CI_db $dbInstance
     * @param string $tableName
     * @param array $excludeColumn
     * @return array $result
     */
    function getAllColumnNameAndDefaultValue($dbInstance, $tableName, $excludedColumn = array())
    {
        $dbInstance->select("COLUMN_NAME, COLUMN_DEFAULT");
        $dbInstance->where("TABLE_NAME", $tableName);
        empty($excludedColumn) || $dbInstance->where_not_in("COLUMN_NAME", $excludedColumn);
        $result = array();
        foreach ($dbInstance->get("INFORMATION_SCHEMA.COLUMNS")->result() as $row) {
            if (strtolower($row->COLUMN_DEFAULT) === "null") {
                $row->COLUMN_DEFAULT = null;
            }
            $result[$row->COLUMN_NAME] = $row->COLUMN_DEFAULT;
        }
        return $result;
    }
}
