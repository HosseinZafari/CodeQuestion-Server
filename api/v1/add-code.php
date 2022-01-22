<?php 

require_once '../../core/loader.php';

if(!isset($_SERVER["HTTP_TOKEN"])) {
    output(['status' => 'Error' , 'code' => 400 , 'msg' => 'شما اجازه دسترسی به این بخش را ندارید .']);
}

if(!isset($_POST['title']) || !isset($_POST['text']) || !isset($_POST['source'])) { 
    output(['status' => 'Error' , 'code' => 400 , 'msg' => 'لطفا تمامی فیلد ها را پر کنید']);
}

$token  = $_SERVER['HTTP_TOKEN'];
$title  = $_POST['title'];
$text   = $_POST['text'];
$source = $_POST['source'];
$date   = jdate('o/m/d H:i');

try {
    global $connection;
    $command = $connection->prepare('SELECT t_user.userId FROM t_user WHERE token=:token');
    $command->bindParam('token' , $token);
    $command->execute();
    $result = $command->fetchAll();
    
    if(count($result) < 1) {
        output(['status' => 'Error' , 'code' => 404 , 'msg' => 'حساب شما صحیح نمیباشد']);
    } else {
        $userId = $result[0]['userId'];
        $cmd = $connection->prepare('INSERT INTO t_code(userId , title , text , source , date) VALUES (:userId ,:title , :text , :source , :date)');
        $cmd->bindParam('userId' , $userId);
        $cmd->bindParam('title'  , $title);
        $cmd->bindParam('text'   , $text);
        $cmd->bindParam('source' , $source);
        $cmd->bindParam('date'   , $date);
        $cmd->execute();
        output(['status' => 'Success' , 'code' => 200 , 'msg' => 'کد شما با موفقیت ثبت شد']);
    }
    
} catch(Exception $e) {
    echo $e;
    output(['status' => 'Error' , 'code' => 500 , 'msg' => 'خطایی به وجود آمده بعدا امتحان کنید']);
}


