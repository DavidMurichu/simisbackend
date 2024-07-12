<?php
$servername = "localhost";
$username = "simis";
$password = "secret1234";
$dbname = "SIMIS";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getReferencingTables($conn, $database, $table) {
    $sql = "SELECT TABLE_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE REFERENCED_TABLE_SCHEMA = '$database'
            AND REFERENCED_TABLE_NAME = '$table'";

    $result = $conn->query($sql);
    $tables = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tables[] = $row['TABLE_NAME'];
        }
    }
    return $tables;
}

function dropTableRecursively($conn, $database, $table, &$deletedTables) {
    $referencingTables = getReferencingTables($conn, $database, $table);

    foreach ($referencingTables as $referencingTable) {
        dropTableRecursively($conn, $database, $referencingTable, $deletedTables);
    }

    $sql = "DROP TABLE IF EXISTS `$database`.`$table`";
    if ($conn->query($sql) === TRUE) {
        $deletedTables[] = $table;
    } else {
        echo "Error deleting table $table: " . $conn->error . "\n";
    }
}

function deleteTableWithConstraints($conn, $database, $table) {
    $deletedTables = [];
    dropTableRecursively($conn, $database, $table, $deletedTables);
    return $deletedTables;
}

// Usage example
$tableToDelete = 'sch_payment_terms';
$deletedTables = deleteTableWithConstraints($conn, $dbname, $tableToDelete);

// Save deleted tables to a file
$filename = 'deleted_tables.txt';
file_put_contents($filename, implode("\n", $deletedTables));

echo "Deleted tables:\n";
foreach ($deletedTables as $deletedTable) {
    echo $deletedTable . "\n";
}

$conn->close();
?>
