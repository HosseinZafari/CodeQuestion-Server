<?php 
require_once '../../core/loader.php';

if(!isset($_SERVER['HTTP_TOKEN'])){
    output(['status' => 'Error' , 'code' => 400 , 'msg' => 'شما اجازه دسترسی به این بخش را ندارید .']);
}

if(!isset($_GET['score']) || !isset($_GET['codeId'])){
    output(['status' => 'Error' , 'code' => 400 , 'msg' => 'تمامی فیلد ها را پر کنید']);
}

$token = $_SERVER['HTTP_TOKEN'];
$score = $_GET['score'];
$codeId = $_GET['codeId'];
$oldPoint  = 0;
$newPoint  = 0;

global $connection;
$command = $connection->prepare('SELECT userId FROM t_user WHERE token=:token');
$command->bindParam('token' , $token);
$command->execute();
$userRows = $command->fetchAll();
if(count($userRows) < 1) {
    output(['status' => 'Error' , 'code' => 404 , 'msg' => 'حساب شما صحیح نمیباشد']);
}

$userId = $userRows['0']['userId'];

$command = $connection->prepare('SELECT t_code.point FROM t_code WHERE codeId=:codeId');
$command->bindParam('codeId' , $codeId);
$command->execute();
$pointRow = $command->fetchAll();
if(count($pointRow) < 1) {
    output(['status' => 'Error' , 'code' => 404 , 'msg' => 'کدی با این مشخصات پیدا نشد']);
}

$command = $connection->prepare('SELECT pointId FROM t_point WHERE codeId=:codeId AND userId=:userId');
$command->bindParam('codeId' , $codeId);
$command->bindParam('userId' , $userId);
$command->execute();
$resultPointId = $command->fetchAll();
if(count($resultPointId) > 0) {
    output(['status' => 'Error' , 'code' => 404 , 'msg' => 'شما قبلا روی این پست امتیاز داده اید']);
}

$oldPoint = $pointRow[0]['point'];


if($score == 0){
    $newPoint = $oldPoint - 1;
} else {
    $newPoint = $oldPoint + 1;
}

$command = $connection->prepare('UPDATE t_code SET point=:point WHERE t_code.codeId=:codeId');
$command->bindParam('point' , $newPoint);
$command->bindParam('codeId' , $codeId);
if($command->execute() != 1) {
    output(['status' => 'Error' , 'code' => 500 , 'msg' => 'مشکلی در سرور رخ داده لطفا بعدا امتحان کنید']);
}

$command = $connection->prepare('INSERT INTO t_point (userId , codeId) VALUES (:userId , :codeId)');
$command->bindParam('userId' , $userId);
$command->bindParam('codeId' , $codeId);
if($command->execute() != 1) {
    output(['status' => 'Error' , 'code' => 500 , 'msg' => 'مشکلی در سرور رخ داده لطفا بعدا امتحان کنید']);
}


output(['status' => 'Success' , 'code' => 200 , 'msg' => 'امتیاز شما با موفقیت ثبت شد']);
