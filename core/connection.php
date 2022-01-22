<?php
include_once 'loader.php';

global $options;
$host = $options['db']['host'];
$name = $options['db']['name'];
$user = $options['db']['user'];
$pass = $options['db']['pass'];

try {
  $connection = new PDO("mysql:host=$host;dbname=$name", $user, $pass , array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
  $connection->setAttribute( PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION);
  $exc = $connection->prepare("SET NAMES UTF8");
  $exc->execute();
  $connection->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE  , PDO::FETCH_ASSOC);
}catch (PDOException $exception){
  echo json_encode([
    'Error' => 500 ,
    'Message' => $exception->getMessage()
  ]);
}


