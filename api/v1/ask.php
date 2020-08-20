<?php 
require_once '../../core/loader.php';


if(!isset($_SERVER["HTTP_TOKEN"])) {
    output(['status' => 'Error' , 'code' => 400 , 'msg' => 'شما اجازه دسترسی به این بخش را ندارید .']);
}


if(!isset($_POST['title']) || !isset($_POST['text']) || !isset($_POST['type']) || !isset($_POST['course']) ) { 
    output(['status' => 'Error' , 'code' => 400 , 'msg' => 'لطفا تمامی فیلد ها را پر کنید']);
}

$title      = $_POST['title'];
$text       = $_POST['text'];
$typeAsk    = $_POST['type'];
$typeCourse = $_POST['course'];
$date  = jdate('o/m/d H:i');
$token = $_SERVER['HTTP_TOKEN'];
$toUser = null ;

try {
    global $connection;
    $command = $connection->prepare('SELECT t_user.userId , t_user.role FROM t_user WHERE token=:token');
    $command->bindParam('token' , $token);
    $command->execute();
    $result = $command->fetchAll();
    if(count($result) < 1) {
        output(['status' => 'Error' , 'code' => 404 , 'msg' => 'حساب شما صحیح نمیباشد']);
    } else {
        $validUser = $result[0];

        if($validUser['role'] == 'user'){ // This is User
            $toUser = -1; // -1 it's Admin  
        } else { // This is Admin
            // TODO 
        }

        $command = $connection->prepare('INSERT INTO t_question (fromUser  , toUser ,title , text , type , date , course) VALUES (:fromUser  , :toUser , :title , :text , :type , :date , :course)');
        $command->bindParam('fromUser' , $validUser['userId']);
        $command->bindParam('toUser' , $toUser);
        $command->bindParam('title' , $title);
        $command->bindParam('text'  , $text);
        $command->bindParam('type'  , $typeAsk);
        $command->bindParam('course' , $typeCourse);
        $command->bindParam('date' , $date);
        if($command->execute()){
            output(['status' => 'success' , 'code' => 200 , 'msg' => 'سوال شما با موفقیت ارسال شد لطفا منتظر جواب باشید.']);
        } else {
            output(['status' => 'Error' , 'code' => 500 , 'msg' => 'خطایی در برقراری ارتباط وجود دارد لطفا بعدا امتحان کنید.']);
        }
    }

} catch(Exception $e) {
    output(['status' => 'Error' , 'code' => 500 , 'msg' => 'خطایی در برقراری ارتباط وجود دارد لطفا بعدا امتحان کنید.']);
}
 
