<?php 

require_once '../../core/loader.php';

if(!isset($_GET['category'])){
    output(['status' => 'Error', 'code' => '400' , 'msg' => 'شما باید یک دسته بندی انتخاب کنید']);
}

$cate = $_GET['category'];

global $connection;
if($cate == POPULAR){ // Popular Codes
    try {
        $cmd = $connection->prepare('SELECT t_code.codeId , t_code.title , t_code.text ,t_code.source , t_code.date  , t_code.point as codePoint , t_user.image , t_user.gender
		 FROM t_code JOIN t_user ON t_code.userId=t_user.userId WHERE t_code.publish=1 ORDER BY t_code.point DESC');
        $cmd->execute();
        $rows = $cmd->fetchAll();
        output(['status' => 'Success' , 'code' => 200 , 'codes' => $rows]);
    } catch(Exception $e) {
        output(['status' => 'Error' , 'code' => 500 , 'msg' => 'خطایی در سرور رخ داده لطفا بعدا امتحان کنید.']);
    }

} else {
    try {
        $cmd = $connection->prepare('SELECT t_code.codeId , t_code.title , t_code.text , t_code.source , t_code.date  , t_code.point as codePoint , t_user.image , t_user.gender
		 FROM t_code JOIN t_user ON t_code.userId=t_user.userId WHERE t_code.publish=1 ORDER BY t_code.date DESC'); // TODO check Date For Sort
        $cmd->execute();
        $rows = $cmd->fetchAll();
        output(['status' => 'Success' , 'code' => 200 , 'codes' => $rows]);
    } catch(Exception $e) {
        output(['status' => 'Error' , 'code' => 500 , 'msg' => 'خطایی در سرور رخ داده لطفا بعدا امتحان کنید.']);
    }
}
