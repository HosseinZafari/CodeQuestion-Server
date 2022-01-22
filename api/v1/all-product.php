
require_once '../../core/loader.php';
 
global $connection;
  
 try {
     $cmd = $connection->prepare('SELECT * FROM t_product');  
     $cmd->execute();
     $rows = $cmd->fetchAll();
     output(['status' => 'Success' , 'code' => 200 , 'products' => $rows , 'msg' => 'با موفقیت دریافت شد']);
 } catch(Exception $e) {
     output(['status' => 'Error' , 'code' => 500 , 'msg' => 'خطایی در سرور رخ داده لطفا بعدا امتحان کنید.']);
 }