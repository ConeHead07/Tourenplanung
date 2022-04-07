<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 28.08.2019
 * Time: 12:59
 */

interface Model_ResourceInterface {

    public function insert(array $data);

    public function update(array $data, $id);

    public function delete($id);

    public function getName(int $id);

    public function fetchEntries($options = array());

    public function fetchEntry($id);

    public function fetchCategoryIds($id);

    public function fetchCategoriesByRow($row);

    public function getSqlSelectExprAsLabel(): string;
}