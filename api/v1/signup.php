<?php 
require_once '../../core/loader.php';

if(!isset($_POST['name']) || !isset($_POST['family']) || !isset($_POST['email']) || !isset($_POST['phoneNumber']) ||
 !isset($_POST['password'])){
     output(['status' => 'Error' , 'code' => 400 , 'msg' => 'لطفا تمامی فیلد ها را پر کنید']);
}

$name        = $_POST['name'];
$family      = $_POST['family'];
$email       = $_POST['email'];
$phoneNumber = $_POST['phoneNumber'];
$password    = $_POST['password'];


if(getCountUserByEmail($connection , $email) >= 1) {
    output(['status' => 'failed' , 'code' => 400 , 'msg' => 'شما قبلا ثبت نام کرده اید!']);
} else {
    $passwordHashed = password_hash($password , PASSWORD_DEFAULT);
    $shell = $connection->prepare('INSERT INTO t_user (name , family , email , phone , password) VALUES (:name , :family , :email , :phoneNumber , :password)');
    $shell->bindParam(':name' , $name);
    $shell->bindParam(':family' , $family);
    $shell->bindParam(':email' , $email);
    $shell->bindParam(':phoneNumber' , $phoneNumber);
    $shell->bindParam(':password' , $passwordHashed);
    try {
        $result = $shell->execute();
        if($result == 1){
            output(['status' => 'success' , 'code' => 200, 'msg' => 'شما با موفقیت ثبت نام شده اید']);
        } else {
            output(['status' => 'Error' , 'code' => 400, 'msg' => 'مشکلی در ثبت نام شما رخ داده است!']);
        }
    } catch(Exception $e) {
        ouput(['status' => 'Error' , 'code' => 400, 'msg' => 'مشکلی در ثبت نام شما رخ داده است!']);
    }
}


function getCountUserByEmail($connection , $email){
 return sizeOf(getUserByEmail($connection , $email));   
}

function getUserByEmail($connection , $email): array{
    $shell = $connection->prepare("SELECT * FROM t_user WHERE email=:email");
    $shell->bindParam(':email' , $email);
    $shell->execute();
    return $shell->fetchAll(PDO::FETCH_ASSOC);
}