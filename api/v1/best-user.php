<?php
require_once '../../core/loader.php';

$result = $connection->query("select userId , name , family , image ,gender ,point from t_user order by point DESC")->fetchAll();

setJsonOutPut();
echo json_encode($result);
