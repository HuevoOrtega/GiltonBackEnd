<?php
include dirname(__FILE__)."/../../../DBConnect/SafeString.php";
include dirname(__FILE__)."/../../../DBConnect/Service.php";

if (!isset($_POST['idCliente']) || !isset($_POST['clientType']))
  die(json_encode(array("Satus"=>"ERROR missing values")));
  
$clientId = SafeString::safe($_POST['idCliente']);
$clientType = SafeString::safe($_POST['clientType']);
try{
  $service  = new Service();
  $servicesHistory = $service->getHistory($clientId,$clientType);
  echo json_encode(array("Status"=>"OK","History"=>$servicesHistory));
} catch(errorWithDatabaseException $e)
{
  echo $e->getMessage();
  echo json_encode(array("Status"=>"ERROR DB"));
}
?>