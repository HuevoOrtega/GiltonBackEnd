<?php
require_once dirname(__FILE__)."/../../../../DBConnect/SafeString.php";
require_once dirname(__FILE__)."/../../../../DBConnect/Car.php";
require_once dirname(__FILE__)."/../../../../DBConnect/User.php";
header('Content-Type: text/html; charset=utf8');

if (!isset($_POST['vehiculoId']) || !isset($_POST['color']) || !isset($_POST['placas'])
	 || !isset($_POST['marca'])  || !isset($_POST['token']))
{
	die(json_encode(array(
			"estado"=>"error",
			"clave"=>"valores",
			"explicacion"=>"Faltan valores"
	)));
} 

try
{
  $vehiculoId = SafeString::safe($_POST['vehiculoId']);
  $token = SafeString::safe($_POST['token']);
  $color = SafeString::safe($_POST['color']);
  $placas = SafeString::safe($_POST['placas']);
  $marca = SafeString::safe($_POST['marca']);
  $user = new User();
  $infoUser = $user->userHasToken($token);
  $car  = new Car();
  $carId = $car->addCar($vehiculoId,$infoUser['idCliente'],$color,$placas,$marca);
  echo json_encode(array(
  		"estado"=>"ok",
  		"id" => $carId
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
?>