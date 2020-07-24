<?php
require_once '../../core/loader.php';

$result = $connection->query("select * from t_course where star>0 order by priority ASC LIMIT 7")->fetchAll();

setJsonOutPut();
echo json_encode($result);
