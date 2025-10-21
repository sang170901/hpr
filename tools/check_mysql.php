<?php
// Diagnostic script to check MySQL tables and row counts
// Usage: c:\xampp\php\php.exe tools\check_mysql.php

require_once __DIR__ . '/../inc/db_frontend.php';

function safePrint($label, $val) {
    echo str_pad($label . ':', 30) . " $val\n";
}

try {
    $pdo = getFrontendPDO();
    echo "Connected to database OK\n\n";

    // List tables
    $tables = [];
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }

    if (empty($tables)) {
        echo "No tables found in database.\n";
        exit(0);
    }

    echo "Found " . count($tables) . " tables:\n";
    foreach ($tables as $t) echo " - $t\n";
    echo "\n";

    // For each table, get row count and attempt to find created_at or updated_at
    foreach ($tables as $t) {
        try {
            $c = $pdo->query("SELECT COUNT(*) AS cnt FROM `" . $t . "`")->fetch(PDO::FETCH_ASSOC)['cnt'];
        } catch (Exception $e) {
            $c = 'ERROR';
        }

        // try max(created_at) and max(updated_at)
        $latest = [];
        foreach (['created_at','updated_at','created','updated'] as $col) {
            try {
                $r = $pdo->query("SELECT MAX(`$col`) AS m FROM `" . $t . "`")->fetch(PDO::FETCH_ASSOC);
                if ($r && $r['m']) $latest[$col] = $r['m'];
            } catch (Exception $e) {
                // ignore
            }
        }

        echo str_pad($t, 30) . " rows=" . $c;
        if (!empty($latest)) {
            echo "  latest: ";
            $parts = [];
            foreach ($latest as $k=>$v) $parts[] = "$k=$v";
            echo implode(', ', $parts);
        }
        echo "\n";
    }

    // Quick checks for key tables
    echo "\nQuick checks:\n";
    $keyTables = ['suppliers','products','categories','users'];
    foreach ($keyTables as $kt) {
        if (in_array($kt, $tables)) {
            $cnt = $pdo->query("SELECT COUNT(*) AS c FROM `$kt`")->fetch(PDO::FETCH_ASSOC)['c'];
            safePrint("Table $kt rows", $cnt);
        } else {
            safePrint("Table $kt exists", 'no');
        }
    }

    echo "\nIf you expect specific data that's missing, tell me which table or example record and I can dig deeper.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(2);
}

?>
