<?php
require_once dirname(__FILE__)."/../../../DBConnect/SafeString.php";
require_once dirname(__FILE__)."/../../../DBConnect/User.php";
require_once dirname(__FILE__)."/../../../DBConnect/Payment.php";
header('Content-Type: text/html; charset=utf8');

if (!isset($_POST['name']) || !isset($_POST['lastName']) || !isset($_POST['email']) ||
		!isset($_POST['password']) || !isset($_POST['phone']) || !isset($_POST['device']))
{
	die(json_encode(array(
			"estado"=>"error",
			"clave"=>"valores",
			"explicacion"=>"Faltan valores"
	)));
} 


try
{
  $name = SafeString::safe($_POST['name']);
  $lastName = SafeString::safe($_POST['lastName']);
  $email = SafeString::safe($_POST['email']);
  $password = SafeString::safe($_POST['password']);
  $phone = SafeString::safe($_POST['phone']);
  $device = SafeString::safe($_POST['device']);
  $device = SafeString::safe($_POST['device']);
  $image_name = "profile_image.jpg";
  $user  = new User();
  $userInfo = $user->addUser($name, $lastName, $email, $password,$phone);
  if(isset($_POST['encoded_string']))
  {
    uploadImage($userInfo['idCliente']);
    $user->saveImage($userInfo['idCliente'], $image_name);
  }
  $userInfo = $user->sendLogIn($email,$password);
  $conektaId = Payment::createUser( $name, $lastName, $email, $phone);
  $user->saveConektaId($userInfo["idCliente"], $conektaId);
  $user->saveDevice($userInfo["idCliente"],$device);
  echo json_encode(array(
  		"estado"=>"ok",
  		"usuario"=>$userInfo
  ));
} 
catch(errorWithDatabaseException $e)
{
	echo json_encode(array(
			"estado"=>"error",
			"clave"=>"esconocido",
			"explicacion"=>$e->getMessage()
	));
} 
catch(errorCreatingUserPaymentException $e)
{
	echo json_encode(array(
			"estado"=>"error",
			"clave"=>"pago",
			"explicacion"=>$e->getMessage(),
			"usuario"=>$userInfo
	));
} 
catch (Exception $e) {
	echo json_encode(array(
			"estado"=>"error",
			"clave"=>"desconocido",
			"explicacion"=>$e->getMessage()
	));
}

function uploadImage($idClient){
  $encoded_string = $_POST['encoded_string'];
  $encoded_string = str_replace('data:image/jpg;base64,', '', $encoded_string);
  $encoded_string = str_replace(' ', '+', $encoded_string);
  $image_name = "profile_image.jpg";
  $decoded_string = base64_decode($encoded_string);
  $directory = dirname(__FILE__).'/../../../images/users/'.$idClient;
  $oldmask = umask(0);
  if(!is_dir($directory)) {
    mkdir($directory, 0777);
  }
  $path = dirname(__FILE__).'/../../../images/users/'.$idClient.'/'.$image_name;
  
  file_put_contents($path,$decoded_string);
  umask($oldmask);
}
?>