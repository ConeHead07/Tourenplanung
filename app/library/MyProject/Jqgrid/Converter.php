<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 19.09.2018
 * Time: 14:10
 */

class MyProject_Jqgrid_Converter
{

    public static function rowsToGridResult(array $rows, int $total = 0, int $offset = 0, int $limit = 0): array {

        $numRows = count($rows);

        if (!$total) $total = $numRows;
        if (!$limit) $limit = $numRows;

        $totalPages = ceil($total / $limit);
        $currPage = floor(($offset + $limit) / $limit);

        return [
            'total' => $totalPages,
            'page' => $currPage,
            'records' => $total,
            'rows' => $rows,
        ];
    }
}