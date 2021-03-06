<?php
require_once dirname(__FILE__)."/../../../DBConnect/SafeString.php";
require_once dirname(__FILE__)."/../../../DBConnect/Cleaner.php";
require_once dirname(__FILE__)."/../../../DBConnect/Service.php";
header('Content-Type: text/html; charset=utf8');

if (!isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['device']))
{
	die(json_encode(array(
			"estado"=>"error",
			"clave"=>"valores",
			"explicacion"=>"Faltan valores"
	)));
}

try
{
  $email = SafeString::safe($_POST['email']);
  $password = SafeString::safe($_POST['password']);
  $device = SafeString::safe($_POST['device']);
  $cleaner  = new Cleaner();
  $service  = new Service();
  
  $cleanerInfo = $cleaner->sendLogIn($email, $password);
  $cleanerId = $cleanerInfo['idLavador'];
  $cleaner->saveDevice($cleanerId,$device);
  $review = $service->readReviewForCleaner($cleanerId);
  $servicesHistory = $service->getHistory($cleanerId,2);
  echo json_encode(array(
  		"estado"=>"ok",
  		"lavador"=>$cleanerInfo,
  		"historial"=>$servicesHistory,
  		"calificacion"=>$review
  ));
} 
catch(cleanerNotFoundException $e)
{
	echo json_encode(array(
			"estado"=>"error",
			"clave"=>"lavador",
			"explicacion"=>$e->getMessage()
	));
} 
catch(errorWithDatabaseException $e)
{
	echo json_encode(array(
			"estado"=>"error",
			"clave"=>"db",
			"explicacion"=>$e->getMessage()
	));
} 
?>