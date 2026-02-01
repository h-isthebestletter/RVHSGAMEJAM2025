<?php
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    require_once 'C:\xampp_new\htdocs\RVHSGAMEJAM2025\private\rvhsgamejam_secrets.inc.php';
} else {
    require_once '../../../private/rvhsgamejam_secrets.inc.php';
}
$conn = mysqli_connect(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);
// use UTF-8 charset, if not it will cause encoding errors with JSON (iirc)
$conn->set_charset('utf8mb4');

function sqlQueryObject($conn, $stmt, $arg = null) {
    $sqlResult = $conn->execute_query($stmt, $arg);
    if (!$sqlResult) { die(); }
    if ($sqlResult === true) { return; } // statement does not return i.e. INSERT, UPDATE
    $result = $sqlResult->fetch_object();
    $sqlResult->free();
    return $result;
}

function sqlQueryAllObjects($conn, $stmt, $arg = null) {
    $sqlResult = $conn->execute_query($stmt, $arg);
    if (!$sqlResult) { die(); }
    if ($sqlResult === true) { return; } // statement does not return i.e. INSERT, UPDATE
    $result = [];
    while ($row = $sqlResult->fetch_object()) {
        $result[] = $row;
    }
    $sqlResult->free();
    return $result;
}
