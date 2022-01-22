<?php 
require_once '../../core/loader.php';

if(!isset($_SERVER["HTTP_TOKEN"])) {
    output(['status' => 'Error' , 'code' => 400 , 'msg' => 'شما اجازه دسترسی به این بخش را ندارید .']);
}

if(!isset($_POST['title']) || !isset($_POST['text']) || !isset($_POST['type']) 
|| !isset($_POST['course']) || !isset($_POST['to_user']) || !isset($_POST['question_id']) ) { 
    output(['status' => 'Error' , 'code' => 400 , 'msg' => 'لطفا تمامی فیلد ها را پر کنید']);
}

$token = $_SERVER['HTTP_TOKEN'];
$title = $_POST['title'];
$text  = $_POST['text'] ;
$questionId = $_POST['question_id'];
$toUser   = $_POST['to_user'];
$date     = jdate('o/m/d H:i');
$typeAnswer     = $_POST['type'];
$typeCourse  = $_POST['course'];
$fromUser = -1;
$answered = 1;
$returned = 0;


try {
    global $connection;
    $command = $connection->prepare('SELECT userId , role  FROM t_user WHERE token=:token');
    $command->bindParam('token' , $token);
    $command->execute();
    $result = $command->fetchAll();
    if(count($result) < 1) {
        output(['status' => 'Error' , 'code' => 404 , 'msg' => 'حساب شما صحیح نمیباشد']);
    } else {
        $sqlCommand = $connection->prepare('UPDATE t_question SET answered=1 WHERE questionId=:questionId');
        $sqlCommand->bindParam('questionId' , $questionId);
        $sqlCommand->execute();

        $command = $connection->prepare('INSERT INTO t_question (fromUser  , toUser ,title , text , type , date , course , answered , returned) VALUES (:fromUser  , :toUser , :title , :text , :type , :date , :course , :answered , :returned)');
        $command->bindParam('fromUser' , $fromUser);
        $command->bindParam('toUser' , $toUser);
        $command->bindParam('title' , $title);
        $command->bindParam('text' , $text);
        $command->bindParam('type' , $typeAnswer);
        $command->bindParam('date' , $date);
        $command->bindParam('course' , $typeCourse);
        $command->bindParam('answered' , $answered);
        $command->bindParam('returned' , $returned);
        if($command->execute()){
            output(['status' => 'success' , 'code' => 200 , 'msg' => 'جواب شما با موفقیت ارسال شد.']);
        } else {
            output(['status' => 'Error' , 'code' => 500 , 'msg' => 'خطایی در برقراری ارتباط وجود دارد لطفا بعدا امتحان کنید.']);
        }
    }
} catch(Exception $e) {
    echo $e;
    exit();
    output(['status' => 'Error' , 'code' => 500 , 'msg' => 'خطایی در برقراری ارتباط وجود دارد لطفا بعدا امتحان کنید.']);
}