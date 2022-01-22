<?php 

require_once '../../core/loader.php';

try {
    global $connection;

    $command = $connection->prepare('SELECT * FROM t_course ORDER BY price ASC;');
    $command->execute();
    $rows = $command->fetchAll();
    output(['status' => 'success' , 'code' => 200 , 'courses' => $rows]);
} catch(Exception $e) {
    ouput(['status' => 'Failed' , 'code' => 500 , 'msg' => 'مشکلی در گرفتن اطلاعات رخ داده لطفا بعدا امتحان کنید ']);
}
