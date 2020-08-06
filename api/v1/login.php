<?php 
require_once '../../core/loader.php';

if(!isset($_POST['email']) || !isset($_POST['password'])){
     output(['status' => 'Error' , 'code' => 402 , 'msg' => 'لطفا تمامی فیلد ها را پر کنید']);
}
 
$email       = $_POST['email'];
$password    = $_POST['password'];

$user = getUserByEmail($connection , $email);
if(sizeOf($user) == 0){
    output(['status' => 'failed' , 'code' => 404 , 'msg' => 'حسابی با این ایمیل یافت نشد']);
}

if(password_verify($password , $user[0]['password'])){
    $shell = $connection->prepare('SELECT userId , name , family , phone , image , phone , email , gender , role , point , token FROM t_user WHERE email=:email');
    $shell->bindParam(':email' , $email);

    try {
        $shell->execute();
        $result = $shell->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($result)){
            output(['status' => 'success' , 'code' => 200 , 'msg' => 'خوش آمدید' , 'user' => $result[0]]);
        } else {
            output(['status' => 'Error' , 'code' => 400, 'msg' => 'مشکلی در ورود به حساب شما رخ داده است!']);
        }
    } catch(Exception $e) {
        ouput(['status' => 'Error' , 'code' => 400, 'msg' => 'مشکلی در ورود به حساب شما رخ داده است!']);
    }
} else {
    output(['status' => 'Error' , 'code' => 401, 'msg' => 'رمز عبور شما صحیح نیست']);
}



function getCountUserByEmail($connection , $email){
 return sizeOf(getUserByEmail($connection , $email));   
}

function getFirstUser($connection , $email){
    return getUserByEmail($connection , $email)[0];
}

function getUserByEmail($connection , $email): array{
    $shell = $connection->prepare("SELECT * FROM t_user WHERE email=:email");
    $shell->bindParam(':email' , $email);
    $shell->execute();
    return $shell->fetchAll(PDO::FETCH_ASSOC);
}