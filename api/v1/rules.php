<?php 

require_once '../../core/loader.php';

try {
    global $connection;
    $command = $connection->prepare('SELECT * FROM t_rule');
    $command->execute();
    $rows = $command->fetchAll();
    output(['code' => 200 ,'status' => 'success' ,  'msg' => 'لیست قوانین' , 'rules' => $rows]);
} catch(Exception $e){
    output(['code' => 500 , 'status' => 'failed' , 'msg' => 'خطایی در گرفتن اطلاعات به وجود آمده لطفا بعدا امتحان کنید']);
}

