<?php 
require_once '../../core/loader.php';


$jsonRequest = file_get_contents('php://input');
$params 	 = json_decode($jsonRequest , true);
if(!isset($params['name']) || !isset($params['family']) || !isset($params['email']) || !isset($params['phoneNumber']) || !isset($params['password']) || !isset($params['gender'])){
    output(['status' => 'Error' , 'code' => 400 , 'msg' => 'لطفا تمامی فیلد ها را پر کنید']);
}

$name        = $params['name'];
$family      = $params['family'];
$email       = $params['email'];
$phoneNumber = $params['phoneNumber'];
$password    = $params['password'];
$gender      = $params['gender'];
$token = getToken($email);


if(getCountUserByEmail($connection , $email) >= 1) {
    output(['status' => 'failed' , 'code' => 400 , 'msg' => 'شما قبلا ثبت نام کرده اید!']);
} else {
    $passwordHashed = password_hash($password , PASSWORD_DEFAULT);
    $shell = $connection->prepare('INSERT INTO t_user (name , family , email , phone , password , gender , token) VALUES (:name , :family , :email , :phoneNumber , :password , :gender , :token)');
    $shell->bindParam(':name' , $name);
    $shell->bindParam(':family' , $family);
    $shell->bindParam(':email' , $email);
    $shell->bindParam(':phoneNumber' , $phoneNumber);
    $shell->bindParam(':password' , $passwordHashed);
    $shell->bindParam(':gender' , $gender);
    $shell->bindParam(':token' , $token);
    try {
        $result = $shell->execute();
        if($result == 1){
			// Create a User Object 
			$user = new stdClass();
			$user->name   = $name;
			$user->family = $family;
			$user->email = $email;
			$user->gender = $gender;
			$user->phoneNumber = $phoneNumber;
			$user->token = $token;
			
            output(['status' => 'success' , 'code' => 200, 'msg' => 'شما با موفقیت ثبت نام شده اید' , 'user' => $user] );
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