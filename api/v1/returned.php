<?php 
require_once '../../core/loader.php';


if(!isset($_SERVER['HTTP_TOKEN'])){
    output(['status' => 'Error' , 'code' => 400 , 'msg' => 'شما اجازه دسترسی به این بخش را ندارید .']);
}

if(!isset($_POST['returnType']) || !isset($_POST['questionId'])){
    output(['status' => 'Error' , 'code' => 403 , 'msg' => 'شما هیچ پارامتری را وارد نکردید']);
}

$typeReturn = $_POST['returnType'];
$questionId = $_POST['questionId'];
if(empty($questionId)){
    output(['status' => 'Error' , 'code' => 403 , 'msg' => 'باید ای دی سوال مربوطه را وارد نمایید.']);
}
if(!($typeReturn == 0 || $typeReturn == 1)) {
    output(['status' => 'Error' , 'code' => 403 , 'msg' => 'نوع پرسجوی شما اشتباه میباشد .']);
}

$token = $_SERVER['HTTP_TOKEN'];

global $connection; 
$sqlCommand = $connection->prepare("SELECT userId,role FROM t_user WHERE  token=:token");
$sqlCommand->bindParam('token' , $token);
$sqlCommand->execute();

$userAdmin = $sqlCommand->fetchAll();
if(count($userAdmin) < 1) {
    output(['status' => 'Error' , 'code' => 404 , 'msg' => 'حساب شما صحیح نمیباشد']);
} else {
    $user = $userAdmin[0];

    if($user['role'] == "admin") {
        $sqlCommand = $connection->prepare("UPDATE t_question SET returned=:returned WHERE questionId=:questionId");
        $sqlCommand->bindParam('returned' , $typeReturn);
        $sqlCommand->bindParam('questionId' , $questionId);
        $sqlCommand->execute();

        $count = $sqlCommand->rowCount();
        if($count == 0) {
            output(['status' => 'Error' , 'code' => 404 , 'msg' => ' ای دی سوال شما صحیح نیست .']);
        } else {
            output(['status' => 'success' , 'code' => 200 , 'msg' => 'این سوال با موفقیت بروزرسانی شد.']);
        }
    } else {
        output(['status' => 'Error' , 'code' => 404 , 'msg' => 'شما دسترسی لازم برای این کار را ندارید .']);
    }
}

