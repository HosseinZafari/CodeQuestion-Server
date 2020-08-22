<?php 
require_once '../../core/loader.php';

if(!isset($_SERVER["HTTP_TOKEN"])) {
    output(['status' => 'Error' , 'code' => 400 , 'msg' => 'شما اجازه دسترسی به این بخش را ندارید .']);
}
$token = $_SERVER['HTTP_TOKEN'];

try {
    global $connection;
    $command = $connection->prepare('SELECT t_user.userId  FROM t_user WHERE token=:token');
    $command->bindParam('token' , $token);
    $command->execute();
    $result = $command->fetchAll();
    if(count($result) < 1) {
        output(['status' => 'Error' , 'code' => 404 , 'msg' => 'حساب شما صحیح نمیباشد']);
    } else {
        $person = $result[0];
        $command = $connection->prepare("SELECT * FROM t_question WHERE toUser=:toUser OR fromUser=:fromUser");
        $command->bindParam('fromUser' , $person['userId']);
        $command->bindParam('toUser' , $person['userId']);
        $command->execute();
		
		$rows = array();
        foreach($command->fetchAll() as $oldRow){
			$isAdmin = 0;
			if($oldRow['fromUser'] == ADMIN){
				$isAdmin = 1; // admin 
			} else {
				$isAdmin = 0; // user 
			}
			$newRow = array();
			$newRow['isAdmin'] = $isAdmin;
			$newRow['title'] = $oldRow['title'];
			$newRow['text']  = $oldRow['text'];
			$newRow['type']  = $oldRow['type'];
			$newRow['course'] = $oldRow['course'];
			$newRow['date']  = $oldRow['date'];
			
			$rows[] = $newRow;
		}
		
        output(['status' => 'Success' , 'code' => 200 , 'answers' => $rows]);
    }
} catch(Exception $e){
    output(['status' => 'Error' , 'code' => 500 , 'msg' => 'خطایی در برقراری ارتباط وجود دارد لطفا بعدا امتحان کنید.']);
}





