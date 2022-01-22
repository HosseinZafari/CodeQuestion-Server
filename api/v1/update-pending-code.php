<?php 
require_once '../../core/loader.php';


if(!isset($_SERVER['HTTP_TOKEN'])){
    output(['status' => 'Error' , 'code' => 400 , 'msg' => 'شما اجازه دسترسی به این بخش را ندارید .']);
}

if(!isset($_POST['codeId'])){
    output(['status' => 'Error' , 'code' => 403 , 'msg' => 'شما هیچ پارامتری را وارد نکردید']);
}

$codeId= $_POST['codeId']; 
$token = $_SERVER['HTTP_TOKEN'];

global $connection; 
$sqlCommand = $connection->prepare("SELECT userId,role,point FROM t_user WHERE  token=:token");
$sqlCommand->bindParam('token' , $token);
$sqlCommand->execute();

$userAdmin = $sqlCommand->fetchAll();
if(count($userAdmin) < 1) {
    output(['status' => 'Error' , 'code' => 404 , 'msg' => 'حساب شما صحیح نمیباشد']);
} else {
    $user = $userAdmin[0];

    if($user['role'] == "admin") {
        $sqlCommand = $connection->prepare("UPDATE t_code SET publish=1 WHERE codeId=:codeId");
        $sqlCommand->bindParam('codeId' , $codeId);
        $sqlCommand->execute();

        if($sqlCommand->rowCount() == 0) {
            output(['status' => 'Error' , 'code' => 404 , 'msg' => ' ای دی کد شما صحیح نیست .']);
        } else {

            $sqlCommand = $connection->prepare("SELECT t_code.userId FROM t_code WHERE codeId=:codeId");
            $sqlCommand->bindParam('codeId' , $codeId);
            $sqlCommand->execute();
            $userId  = $sqlCommand->fetchAll()[0]["userId"];

            $sqlCommand = $connection->prepare("SELECT t_user.point FROM t_user WHERE userId=:userId");
            $sqlCommand->bindParam('userId' , $userId);
            $sqlCommand->execute();
            $userPoint  = $sqlCommand->fetchAll()[0]["point"];


            $newPoint = $userPoint + 25;
            $sqlCommand = $connection->prepare("UPDATE t_user SET point=:point WHERE userId=:userId");
            $sqlCommand->bindParam('point' , $newPoint);
            $sqlCommand->bindParam('userId' , $userId);
            $sqlCommand->execute();


            if($sqlCommand->rowCount() > 0) {
                output(['status' => 'success' , 'code' => 200 , 'msg' => 'این کد با موفقیت بروزرسانی شد.']);
            } else {
                output(['status' => 'Error' , 'code' => 404 , 'msg' => ' نتوانستیم امتیاز شما را محاسبه کنیم .']);
            }
        }
    } else {
        output(['status' => 'Error' , 'code' => 404 , 'msg' => 'شما دسترسی لازم برای این کار را ندارید .']);
    }
}

