<?php
require_once '../../core/loader.php';
try {
	global $connection;
	
	$command = $connection->prepare("select * from t_course where star>0 order by priority ASC LIMIT 7");
	$command->execute();
	$result = $command->fetchAll();
	
	foreach($result as $key=>$value){
		$result[$key]['price'] =   " تومان " . number_format($value['price']) ."";
	}
	
	output(['status' => 'Success' ,	 'code' => 200 , 'courses' => $result]);

} catch (Exception $e) {
	ouput(['status' => 'Failed' , 'code' => 500 , 'msg' => 'مشکلی در گرفتن اطلاعات رخ داده لطفا بعدا امتحان کنید ']);
}

