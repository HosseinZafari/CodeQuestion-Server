<?php
require_once '../../core/loader.php';

$result = $connection->query("
        select t_code.codeId , t_code.title ,t_code.text , t_code.source , 
        t_code.date , t_code.point AS 'codePoint', t_user.name , t_user.family , t_user.image , t_user.point AS 'userPoint'
        FROM t_code 
        JOIN t_user 
        ON t_code.userId=t_user.userId ORDER BY t_code.point DESC")->fetchAll();

setJsonOutPut();
echo json_encode($result);
