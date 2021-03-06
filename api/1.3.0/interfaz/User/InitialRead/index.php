<?php
require_once dirname(__FILE__)."/../../../DBConnect/SafeString.php";
require_once dirname(__FILE__)."/../../../DBConnect/User.php";
require_once dirname(__FILE__)."/../../../DBConnect/Car.php";
require_once dirname(__FILE__)."/../../../DBConnect/Payment.php";
require_once dirname(__FILE__)."/../../../DBConnect/Service.php";
header('Content-Type: text/html; charset=utf8');

if (!isset($_POST['token']) || !isset($_POST['device']))
{
	die(json_encode(array(
			"estado"=>"error",
			"clave"=>"valores",
			"explicacion"=>"Faltan valores"
	)));
}

try
{
  $token = SafeString::safe($_POST['token']);
  $device = SafeString::safe($_POST['device']);
  $user  = new User();
  $car  = new Car();
  $service  = new Service();
  $userInfo = $user->readUserData($token);
  $clientId = $userInfo['idCliente'];
  $user->saveDevice($clientId,$device);
  $carsList = $car->getCarsList($clientId);
  $servicesHistory = $service->getHistory($clientId,1);
  echo json_encode(array(
  		"estado"=>"ok",
  		"usuario"=>$userInfo, 
  		"coches"=>$carsList, 
  		"historial"=>$servicesHistory,
  		"tarjeta" => Payment::readClient($userInfo["ConektaId"])
  ));
} catch(userNotFoundException $e)
{
	echo json_encode(array(
			"estado"=>"error",
			"clave"=>"usuario",
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
catch (noSessionFoundException $e) 
{
	echo json_encode(array(
			"estado"=>"error",
			"clave"=>"sesion",
			"explicacion"=>$e->getMessage()
	));
} 
catch (errorReadingUserPayment $e) 
{
	echo json_encode(array(
			"estado"=>"error",
			"clave"=>"pago",
			"explicacion"=>$e->getMessage()
	));
}

?>