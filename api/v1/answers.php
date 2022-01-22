<?php 
require_once '../../core/loader.php';


if(!isset($_SERVER['HTTP_TOKEN'])){
    output(['status' => 'Error' , 'code' => 400 , 'msg' => 'شما اجازه دسترسی به این بخش را ندارید .']);
}
$token = $_SERVER['HTTP_TOKEN'];

if(!isset($_GET['page'])){
    $page  = 1;
} else {
    $page  = $_GET['page'];
}



try {
    
    global $connection;
    $res = $connection->prepare('SELECT questionId FROM  t_question');
    $res->execute();
    
    $limit = 5;
    $full_item   = $res->rowCount();
    $length_page = ceil($full_item / $limit );
    $start = ( $page - 1 ) * $limit;
    
    $command = $connection->prepare('SELECT userId , role  FROM t_user WHERE token=:token');
    $command->bindParam('token' , $token);
    $command->execute();
    $result = $command->fetchAll();
    if(count($result) < 1) {
        output(['status' => 'Error' , 'code' => 404 , 'msg' => 'حساب شما صحیح نمیباشد']);
    } else {
        $person = $result[0];
        if($person['role'] == "user") {
            $command = $connection->prepare("SELECT * FROM t_question WHERE toUser=:toUser OR fromUser=:fromUser LIMIT $limit OFFSET $start");
            $command->bindParam('fromUser' , $person['userId']);
            $command->bindParam('toUser' , $person['userId']);
            $command->execute();
            
            $rows = array();
            foreach($command->fetchAll() as $oldRow){
                $isAdmin = $oldRow['fromUser'] == ADMIN ? 1 : 0;
                $newRow = array();
                $newRow['questionId'] = $oldRow['questionId'];
                $newRow['isAdmin']    = $isAdmin;
                $newRow['title']      = $oldRow['title'];
                $newRow['fromUser']   = $oldRow['fromUser'];
                $newRow['toUser']     = $oldRow['toUser'];
                $newRow['text']       = $oldRow['text'];
                $newRow['type']       = $oldRow['type'];
                $newRow['course']     = $oldRow['course'];
                $newRow['date']       = $oldRow['date'];
                $newRow['returned']   = $oldRow['returned'] == true;
				$newRow['answered']   = $oldRow['answered'] == true;

                $rows[] = $newRow;
            }
            
            output(['status' => 'Success' , 'code' => 200 , 'answers' => $rows]);
        } else {
            $command = $connection->prepare(
                "SELECT t_question.* , t_user.name , t_user.family
                 FROM t_question 
                 JOIN t_user ON t_question.fromUser = t_user.userId
                 WHERE toUser=:admin
                 AND returned=:returnedParam
                 AND answered=:answeredParam
                 ORDER BY t_question.questionId DESC
                 LIMIT $limit OFFSET $start

            ");

            $valueAdmin = ADMIN;
            $valueZero = 0;
            $command->bindParam('admin' , $valueAdmin);
            $command->bindParam('returnedParam' , $valueZero);
            $command->bindParam('answeredParam' , $valueZero);
            $command->execute();

            $rows = array();
            foreach($command->fetchAll() as $oldRow) {
                $isAdmin = $oldRow['fromUser'] == ADMIN ? 1 : 0;
                $newRow = array();
                $newRow['questionId'] = $oldRow['questionId'];
                $newRow['isAdmin']    = $isAdmin;
                $newRow['fromUser']   = $oldRow['fromUser'];
                $newRow['toUser']     = $oldRow['toUser'];
                $newRow['title']      = $oldRow['title'];
                $newRow['text']       = $oldRow['text'];
                $newRow['type']       = $oldRow['type'];
                $newRow['course']     = $oldRow['course'];
                $newRow['date']       = $oldRow['date'];
                $newRow['name']       = $oldRow['name'];
                $newRow['family']     = $oldRow['family'];
                $newRow['answered']   = $oldRow['answered'] == true;
                $newRow['returned']   = $oldRow['returned'] == true;

                $rows[] = $newRow;
            }
            output(['status' => 'Success' , 'code' => 200 , 'answers' => $rows]);
        }
		
    }
} catch(Exception $e){
    output(['status' => 'Error' , 'code' => 500 , 'msg' => 'خطایی در برقراری ارتباط وجود دارد لطفا بعدا امتحان کنید.' , 'error' => $e]);
}
