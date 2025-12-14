<?php
$host = "db.fr-pari1.bengt.wasmernet.com";
$dbname = "dbLPmdYroL9h4AHd4XY2D8CW";
$username = "f358599c75ae8000ff78c60d765d";
$password = "0693f358-599d-7320-8000-84dfb959a00c";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8; port=10272",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed.");
}
