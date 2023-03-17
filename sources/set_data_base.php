<?php

echo "\n\nEntrez votre identifiant à votre base de donné :\n";
$id = readline();
echo "\n\nEntrez votre le mot de passe :\n";
$password = readline();
echo "\n\nEntrez l'adresse de votre base de données :\n";
$address = readline();
echo "\n\nEntrez le port de votre base de données :\n";
$port = readline();
echo "\n\nEntrez la table de votre base de données :\n";
$dbname = readline();

$dsn = "mysql:host=$address;port=$port;dbname=$dbname";

try {
    $pdo = NEW PDO($dsn, $id, $password);
} catch (Exception $e) {
    $message = $e->getMessage();
    die("\nErreur : $message\n");
}

$app_debug = "APP_DEBUG=0";
$app_env = "APP_ENV=prod";
$database_url = "DATABASE_URL=\"mysql://$id:$password@$address:$port/$dbname?serverVersion=8&charset=utf8mb4\"";

$file_content = "$app_env\n$app_debug\n$database_url\n";
file_put_contents('.env.local', $file_content);
echo "\nLa base de données a bien etait configurer\n";

