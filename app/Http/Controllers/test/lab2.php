<?php

namespace App\Http\Controllers\test;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Exception;


class LabController extends Controller
{
function getForeignTableData($tableName, $columnName, $foreignKeyValue, $foreignColumnName)
{
    // Query to retrieve foreign key information from information_schema
    $foreignKeyInfo = DB::selectOne("
        SELECT
            kcu.REFERENCED_TABLE_NAME AS foreign_table,
            kcu.REFERENCED_COLUMN_NAME AS foreign_column
        FROM
            information_schema.KEY_COLUMN_USAGE kcu
        WHERE
            kcu.TABLE_NAME = ? 
            AND kcu.COLUMN_NAME = ?
            AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
    ", [$tableName, $columnName]);

    // Check if foreign key information was found
    if (!$foreignKeyInfo) {
        throw new Exception('No foreign key found for the specified column in the given table.');
    }

    $foreignTableName = $foreignKeyInfo->foreign_table;
    $foreignTableColumnName = $foreignKeyInfo->foreign_column;

    // Define a dynamic Eloquent model for the foreign table
    $foreignModel = new class extends Model {
        protected $table;
        public $timestamps = false;
    };

    $foreignModel->setTable($foreignTableName);

    // Fetch the specified column's data from the foreign table
    $data = $foreignModel->where($foreignTableColumnName, $foreignKeyValue)->value($foreignColumnName);

    return $data;
}}

// Example usage
$tableName = 'orders';
$columnName = 'customer_id';
$foreignKeyValue = 1;
$foreignColumnName = 'name';

try {
    $data = getForeignTableData($tableName, $columnName, $foreignKeyValue, $foreignColumnName);
    echo $data;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
