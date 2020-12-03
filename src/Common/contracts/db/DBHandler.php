<?php

namespace cmever\AWS\Common\contracts\db;

interface DBHandler
{
    /**
     * create a new row
     * @param string $table
     * @param array $data
     * @return bool
     */
    public function create(string $table, array $data): bool;

    /**
     * update row
     * @param string $table
     * @param array $where
     * @param array $data
     * @return int
     */
    public function update(string $table, array $where, array $data): int;

    /**
     * increment row data
     * @param string $table
     * @param array $where
     * @param string $field
     * @param $num
     * @return int
     */
    public function incr(string $table, array $where, string $field, $num): int;

    /**
     * decrement row data
     * @param string $table
     * @param string $where
     * @param string $field
     * @param $num
     * @return int
     */
    public function decr(string $table, array $where, string $field, $num): int;

    /**
     * delete row
     * @param string $table
     * @param string $where
     * @return int
     */
    public function delete(string $table, array $where): int;
}