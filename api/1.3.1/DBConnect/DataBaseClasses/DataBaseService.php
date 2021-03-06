<?php
require_once dirname(__FILE__)."/DataBase.php";
class DataBaseService extends DataBase {
  
  const QUERY_READ_ALL_SERVICES = "SELECT * FROM Servicio;";
	const QUERY_READ_PRICE = "SELECT Precio
	FROM Precio_Servicio
	WHERE idVehiculo = '%s'
	AND idServicio = '%s' 
	AND idRegion = %s
	;";
	const REVISAR_CLIENTE_BLOQUEADO = "SELECT * FROM Cliente WHERE idCliente = '%s' AND block = 1;";
	const QUERY_INSERT_SERVICE = "INSERT INTO Servicio_Pedido (FechaPedido, Direccion, Latitud, Longitud, Precio, idServicio,
	idCliente, idVehiculo, idVehiculoFavorito, metodoDePago, precioAPagar, idRegion)
	VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s','%s', %s, %s)
	;";
	const QUERY_UPDATE_SERVICE_ACCEPTED_CHECK_OPEN = "SELECT * FROM Servicio_Pedido
	WHERE idServicioPedido = '%s' AND idLavador IS NULL AND idStatus != 6 
	;";
	const QUERY_UPDATE_SERVICE_ACCEPTED_CHECK_AVAILABLE_PRODUCTS = "SELECT * FROM Servicio_Pedido
	LEFT JOIN Servicio ON
	Servicio.idServicio = Servicio_Pedido.idServicio
	LEFT JOIN Vehiculo ON
	Vehiculo.idVehiculo = Servicio_Pedido.idVehiculo
	LEFT JOIN Vehiculo_Usa_Producto ON
	Vehiculo_Usa_Producto.idVehiculo = Vehiculo.idVehiculo AND
    Vehiculo_Usa_Producto.idServicio = Servicio.idServicio
	LEFT JOIN Producto ON
	Producto.idProducto = Vehiculo_Usa_Producto.idProducto
	LEFT JOIN Lavador_Tiene_Producto ON
	Lavador_Tiene_Producto.idProducto = Producto.idProducto
	LEFT JOIN Lavador ON
	Lavador.idLavador = Lavador_Tiene_Producto.idLavador
	WHERE (Lavador_Tiene_Producto.Cantidad - Vehiculo_Usa_Producto.CantidadConsumida) < 0
 	AND Vehiculo_Usa_Producto.CantidadConsumida > 0 
	AND Lavador.idLavador = '%s'
	AND Servicio_Pedido.idServicioPedido = '%s'
	;";
	const QUERY_UPDATE_SERVICE_ACCEPTED = "UPDATE Servicio_Pedido SET idLavador = '%s'
	WHERE idServicioPedido = '%s';
	";
	const QUERY_UPDATE_START_TIME = "UPDATE Servicio_Pedido SET FechaEmpezado = '%s' WHERE idServicioPedido = '%s';";
	const QUERY_UPDATE_ACCEPT_TIME = "UPDATE Servicio_Pedido SET FechaAceptado = '%s' WHERE idServicioPedido = '%s';";
	const QUERY_UPDATE_SERVICE = "UPDATE Servicio_Pedido SET idStatus = '%s' WHERE idServicioPedido = '%s';";
  const QUERY_READ_SERVICE =
		"SELECT Servicio_Pedido.idServicioPedido AS id, Status.Status as status, Lavador.Nombre AS nombreLavador, 
		Vehiculo.Nombre AS coche, Servicio.Servicio AS servicio,Lavador.idLavador AS idLavador, 
  		Lavador.Latitud AS latitudLavador, Lavador.Longitud AS longitudLavador, 
		Servicio_Pedido.precio AS precio, Servicio_Pedido.Latitud AS latitud, Servicio_Pedido.Longitud AS longitud, 
  		Tiempo_Servicio.TiempoEstimado AS tiempoEstimado,
		Servicio.Descripcion AS descripcion, Servicio_Pedido.FechaEmpezado AS fechaEmpezado, 
  		Servicio_Pedido.FechaEmpezado + INTERVAL tiempoEstimado MINUTE AS horaFinalEstimada,
		Servicio_Pedido.Calificacion AS Calificacion, Cliente.Nombre AS nombreCliente, Cliente.Telefono AS telCliente, 
  		Servicio_Pedido.idTransaccion AS idTransaccion, Cliente.idCliente AS idCliente, Servicio_Pedido.fechaAceptado AS fechaAceptado,
		Servicio_Pedido.metodoDePago AS pago, Servicio_Pedido.precioAPagar AS precioAPagar,  
  		Vehiculos_Favoritos.Color AS Color, Vehiculos_Favoritos.Placas AS Placas, Vehiculos_Favoritos.Marca AS Marca,
  		Cliente.ConektaId
		FROM Servicio_Pedido
		LEFT JOIN Status ON
		Servicio_Pedido.idStatus = Status.idStatus
		LEFT JOIN Vehiculo ON
		Servicio_Pedido.idVehiculo = Vehiculo.idVehiculo
		LEFT JOIN Servicio ON
		Servicio_Pedido.idServicio = Servicio.idServicio
		LEFT JOIN Cliente ON
		Servicio_Pedido.idCliente = Cliente.idCliente
		LEFT JOIN Lavador ON
		Servicio_Pedido.idLavador = Lavador.idLavador 
		LEFT JOIN Tiempo_Servicio ON
		Vehiculo.idVehiculo = Tiempo_Servicio.idVehiculo AND Tiempo_Servicio.idServicio = Servicio.idServicio
  		LEFT JOIN Vehiculos_Favoritos ON
  		Vehiculos_Favoritos.idVehiculoFavorito = Servicio_Pedido.idVehiculoFavorito
		WHERE Servicio_Pedido.idServicioPedido = '%s'
		;";
		
		const QUERY_READ_ACTIVE_SERVICE_FOR_CLEANER =
		"SELECT Status.Status as status, Lavador.Nombre AS nombreLavador, Lavador.idLavador AS idLavador, Lavador.Latitud AS latitudLavador, 
				Lavador.Longitud AS longitudLavador,
		Vehiculo.Nombre AS coche, Servicio.Servicio AS servicio, Servicio_Pedido.idServicioPedido AS id,
		Servicio_Pedido.precio AS precio, Servicio_Pedido.Latitud AS latitud, Servicio_Pedido.Longitud AS longitud,
		Servicio.Descripcion AS descripcion, Tiempo_Servicio.TiempoEstimado AS tiempoEstimado,
		Servicio_Pedido.FechaEmpezado + INTERVAL tiempoEstimado MINUTE AS horaFinalEstimada, 
		Servicio_Pedido.metodoDePago AS pago, Servicio_Pedido.precioAPagar AS precioAPagar, 
		Cliente.Nombre AS nombreCliente, Cliente.Telefono AS telCliente,    
		Vehiculos_Favoritos.Color AS Color, Vehiculos_Favoritos.Placas AS Placas, Vehiculos_Favoritos.Marca AS Marca
		FROM Servicio_Pedido
		LEFT JOIN Status ON
		Servicio_Pedido.idStatus = Status.idStatus
		LEFT JOIN Vehiculo ON
		Servicio_Pedido.idVehiculo = Vehiculo.idVehiculo
		LEFT JOIN Vehiculos_Favoritos ON
		Vehiculos_Favoritos.idVehiculoFavorito = Servicio_Pedido.idVehiculoFavorito
		LEFT JOIN Servicio ON
		Servicio_Pedido.idServicio = Servicio.idServicio
		LEFT JOIN Cliente ON
		Servicio_Pedido.idCliente = Cliente.idCliente
		LEFT JOIN Sesion_Cliente ON
		Sesion_Cliente.idCliente = Cliente.idCliente
		LEFT JOIN Lavador ON
		Servicio_Pedido.idLavador = Lavador.idLavador
		LEFT JOIN Sesion_Lavador ON
		Sesion_Lavador.idLavador = Lavador.idLavador
		LEFT JOIN Tiempo_Servicio ON
		Vehiculo.idVehiculo = Tiempo_Servicio.idVehiculo AND Tiempo_Servicio.idServicio = Servicio.idServicio
		WHERE Sesion_Lavador.Token = '%s'
		AND Servicio_Pedido.idStatus != 5 
		AND Servicio_Pedido.idStatus != 6
		;";
		//TODO: Cambiar query para que en la seccion de WHERE si salgan los que estan a 5 min de acabar
		const QUERY_READ_CLEANERS_LOCATION = "(SELECT 0 AS ocupado, idLavador, Nombre, PrimerApellido, Latitud, Longitud,
		( 6371 * acos( cos( radians('%s') ) * cos( radians( Latitud ) ) * cos( radians( Longitud ) - radians('%s') ) +
		sin( radians('%s') ) * sin( radians( Latitud ) ) ) ) AS distance
		FROM Lavador 
		WHERE idLavador NOT IN 
				(
				SELECT idLavador FROM Servicio_Pedido WHERE idStatus != 5 AND idStatus != 6 AND idLavador IS NOT NULL
				)
        HAVING distance < '%s'
        )
        UNION 
        (SELECT 1 AS ocupado, idLavador, Nombre, PrimerApellido, Latitud, Longitud,
		( 6371 * acos( cos( radians('%s') ) * cos( radians( Latitud ) ) * cos( radians( Longitud ) - radians('%s') ) +
		sin( radians('%s') ) * sin( radians( Latitud ) ) ) ) AS distance
		FROM Lavador 
		WHERE idLavador IN 
				(
				SELECT idLavador FROM Servicio_Pedido WHERE idStatus != 5 AND idStatus != 6 AND idLavador IS NOT NULL
				)
        HAVING distance < '%s'
        )
		ORDER BY distance
		;";
		
		const QUERY_READ_SERVICES_LOCATION = "SELECT *,
		( 6371 * acos( cos( radians('%s') ) * cos( radians( Latitud ) ) * cos( radians( Longitud ) - radians('%s') ) +
		sin( radians('%s') ) * sin( radians( Latitud ) ) ) ) AS distance
		FROM Servicio_Pedido HAVING distance < '%s' 
		AND idLavador IS NULL 
		AND Servicio_Pedido.idStatus != 6 
		ORDER BY distance
		;";
		const QUERY_UPDATE_CLEANER_PRODUCTS = "UPDATE Servicio_Pedido
		LEFT JOIN Vehiculo ON
		Servicio_Pedido.idVehiculo = Vehiculo.idVehiculo
		LEFT JOIN Servicio ON
		Servicio_Pedido.idServicio = Servicio.idServicio 
		LEFT JOIN Vehiculo_Usa_Producto ON
		Vehiculo_Usa_Producto.idVehiculo = Vehiculo.idVehiculo AND
		Vehiculo_Usa_Producto.idServicio = Servicio.idServicio
		LEFT JOIN Producto ON
		Producto.idProducto = Vehiculo_Usa_Producto.idProducto
		LEFT JOIN Lavador_Tiene_Producto ON
		Lavador_Tiene_Producto.idProducto = Producto.idProducto AND Lavador_Tiene_Producto.idLavador = Servicio_Pedido.idLavador
		SET Lavador_Tiene_Producto.Cantidad = Lavador_Tiene_Producto.Cantidad - Vehiculo_Usa_Producto.CantidadConsumida
		WHERE Servicio_Pedido.idServicioPedido = '%s'
		;";
		
		const QUERY_READ_ACTIVE_SERVICE_FOR_USER =
		"SELECT Status.Status as status, Lavador.Nombre AS nombreLavador, Lavador.idLavador AS idLavador, Lavador.Latitud AS latitudLavador, Lavador.Longitud AS longitudLavador,
		Vehiculo.Nombre AS coche, Servicio.Servicio AS servicio, Servicio_Pedido.idServicioPedido AS id,
		Servicio_Pedido.precio AS precio, Servicio_Pedido.Latitud AS latitud, Servicio_Pedido.Longitud AS longitud,
		Servicio.Descripcion AS descripcion, Tiempo_Servicio.TiempoEstimado AS tiempoEstimado,
		Servicio_Pedido.FechaEmpezado + INTERVAL tiempoEstimado MINUTE AS horaFinalEstimada, Servicio_Pedido.metodoDePago AS pago, 
		Cliente.Nombre AS nombreCliente, Cliente.Telefono AS telCliente, Servicio_Pedido.idTransaccion AS idTransaccion, Servicio_Pedido.metodoDePago AS pago, Servicio_Pedido.precioAPagar AS precioAPagar, 
		Vehiculos_Favoritos.Color AS Color, Vehiculos_Favoritos.Placas AS Placas, Vehiculos_Favoritos.Marca AS Marca
		FROM Servicio_Pedido
		LEFT JOIN Status ON
		Servicio_Pedido.idStatus = Status.idStatus
		LEFT JOIN Vehiculo ON
		Servicio_Pedido.idVehiculo = Vehiculo.idVehiculo
		LEFT JOIN Vehiculos_Favoritos ON
		Vehiculos_Favoritos.idVehiculoFavorito = Servicio_Pedido.idVehiculoFavorito
		LEFT JOIN Servicio ON
		Servicio_Pedido.idServicio = Servicio.idServicio
		LEFT JOIN Cliente ON
		Servicio_Pedido.idCliente = Cliente.idCliente
		LEFT JOIN Sesion_Cliente ON
		Sesion_Cliente.idCliente = Cliente.idCliente
		LEFT JOIN Lavador ON
		Servicio_Pedido.idLavador = Lavador.idLavador
		LEFT JOIN Sesion_Lavador ON
		Sesion_Lavador.idLavador = Lavador.idLavador
		LEFT JOIN Tiempo_Servicio ON
		Vehiculo.idVehiculo = Tiempo_Servicio.idVehiculo AND Tiempo_Servicio.idServicio = Servicio.idServicio
		WHERE Sesion_Cliente.Token = '%s'
		AND Servicio_Pedido.idStatus != 5 
		AND Servicio_Pedido.idStatus != 6
		;";
		
		const QUERY_SELECT_SERVICES_FOR_USER = "SELECT Servicio_Pedido.idServicioPedido AS id, Status.Status as status, Lavador.Nombre AS nombreLavador, 
		Vehiculo.Nombre AS coche, Servicio.Servicio AS servicio,Lavador.idLavador AS idLavador, Lavador.Latitud AS latitudLavador, Lavador.Longitud AS longitudLavador, 
		Servicio_Pedido.precio AS precio, Servicio_Pedido.Latitud AS latitud, Servicio_Pedido.Longitud AS longitud, Tiempo_Servicio.TiempoEstimado AS tiempoEstimado,
		Servicio.Descripcion AS descripcion, Servicio_Pedido.FechaEmpezado AS fechaEmpezado, Servicio_Pedido.FechaEmpezado + INTERVAL tiempoEstimado MINUTE AS horaFinalEstimada,
		Servicio_Pedido.Calificacion AS Calificacion, Cliente.Nombre AS nombreCliente, Cliente.Telefono AS telCliente, 
		Servicio_Pedido.FechaAceptado AS fechaAceptado, Servicio_Pedido.metodoDePago, Servicio_Pedido.precioAPagar AS precioAPagar, 
		Vehiculos_Favoritos.Color AS Color, Vehiculos_Favoritos.Placas AS Placas, Vehiculos_Favoritos.Marca AS Marca
		FROM Servicio_Pedido
		LEFT JOIN Status ON
		Servicio_Pedido.idStatus = Status.idStatus
		LEFT JOIN Vehiculo ON
		Servicio_Pedido.idVehiculo = Vehiculo.idVehiculo
		LEFT JOIN Vehiculos_Favoritos ON
		Vehiculos_Favoritos.idVehiculoFavorito = Servicio_Pedido.idVehiculoFavorito
		LEFT JOIN Servicio ON
		Servicio_Pedido.idServicio = Servicio.idServicio
		LEFT JOIN Cliente ON
		Servicio_Pedido.idCliente = Cliente.idCliente
		LEFT JOIN Lavador ON
		Servicio_Pedido.idLavador = Lavador.idLavador 
		LEFT JOIN Tiempo_Servicio ON
		Vehiculo.idVehiculo = Tiempo_Servicio.idVehiculo AND Tiempo_Servicio.idServicio = Servicio.idServicio
		WHERE Cliente.idCliente = '%s'
		AND Servicio_Pedido.idStatus != '6'
		ORDER BY fechaEmpezado IS NULL DESC, fechaEmpezado DESC
		LIMIT 12
		;";
		
		const QUERY_SELECT_SERVICES_FOR_CLEANER = "SELECT Servicio_Pedido.idServicioPedido AS id, Status.Status as status, Lavador.Nombre AS nombreLavador, 
		Vehiculo.Nombre AS coche, Servicio.Servicio AS servicio,Lavador.idLavador AS idLavador, Lavador.Latitud AS latitudLavador, Lavador.Longitud AS longitudLavador, 
		Servicio_Pedido.precio AS precio, Servicio_Pedido.Latitud AS latitud, Servicio_Pedido.Longitud AS longitud, Tiempo_Servicio.TiempoEstimado AS tiempoEstimado,
		Servicio.Descripcion AS descripcion, Servicio_Pedido.FechaEmpezado AS fechaEmpezado, Servicio_Pedido.FechaEmpezado + INTERVAL tiempoEstimado MINUTE AS horaFinalEstimada,
		Servicio_Pedido.Calificacion AS Calificacion, Servicio_Pedido.metodoDePago,  Servicio_Pedido.precioAPagar AS precioAPagar, 
		Cliente.Nombre AS nombreCliente, Cliente.Telefono AS telCliente,
		Vehiculos_Favoritos.Color AS Color, Vehiculos_Favoritos.Placas AS Placas, Vehiculos_Favoritos.Marca AS Marca
		FROM Servicio_Pedido
		LEFT JOIN Status ON
		Servicio_Pedido.idStatus = Status.idStatus
		LEFT JOIN Vehiculo ON
		Servicio_Pedido.idVehiculo = Vehiculo.idVehiculo
		LEFT JOIN Vehiculos_Favoritos ON
		Vehiculos_Favoritos.idVehiculoFavorito = Servicio_Pedido.idVehiculoFavorito
		LEFT JOIN Servicio ON
		Servicio_Pedido.idServicio = Servicio.idServicio
		LEFT JOIN Cliente ON
		Servicio_Pedido.idCliente = Cliente.idCliente
		LEFT JOIN Lavador ON
		Servicio_Pedido.idLavador = Lavador.idLavador 
		LEFT JOIN Tiempo_Servicio ON
		Vehiculo.idVehiculo = Tiempo_Servicio.idVehiculo AND Tiempo_Servicio.idServicio = Servicio.idServicio
		WHERE Lavador.idLavador = '%s'
		AND Servicio_Pedido.idStatus != '6'
		ORDER BY fechaEmpezado IS NULL DESC, fechaEmpezado DESC
		LIMIT 50
		;";
		
		const QUERY_UPDATE_SERVICE_REVIEW = "UPDATE Servicio_Pedido SET Calificacion = '%d' WHERE idServicioPedido = '%d';";
		
		const QUERY_READ_CLEANER_REVIEWS = "SELECT ROUND(AVG(Calificacion),1) AS Calificacion FROM Servicio_Pedido WHERE idLavador = '%d';";
		const QUERY_UPDATE_TRANSACTION_ID = "UPDATE Servicio_Pedido SET idTransaccion = '%s'
		WHERE idServicioPedido = '%s';";
		const QUERY_READ_PUSH_NOTIFICATION_TOKEN = "
		SELECT Cliente.pushNotificationToken AS pushNotificationTokenCliente, Cliente.dispositivo AS clientDevice, 
				Lavador.pushNotificationToken AS pushNotificationTokenLavador, Lavador.dispositivo AS cleanerDevice
		From Cliente
		LEFT JOIN Servicio_Pedido ON
		Servicio_Pedido.idCliente = Cliente.idCliente
		LEFT JOIN Lavador ON
		Lavador.idLavador = Servicio_Pedido.idLavador 
		WHERE Servicio_Pedido.idServicioPedido = '%s'
		;";
		
		const QUERY_GET_USER_ID = "SELECT idCliente FROM Servicio_Pedido WHERE idServicioPedido = '%s';";
		const QUERY_GET_CLEANER_LOCATION = "SELECT idLavador, Latitud, Longitud FROM Lavador WHERE idLavador = '%s'";
		const QUERY_BLOCK_USER = "UPDATE Cliente SET block = 1 WHERE idCliente = '%s'";
		const GUARDAR_TRANSACCION_EFECTIVO = "UPDATE Servicio_Pedido SET idTransaccion = '%s' WHERE idServicioPedido = %s";

		const LEER_PRECIOS = "SELECT Vehiculo.idVehiculo, Vehiculo.Nombre, Servicio.idServicio, Servicio.Servicio, 
			Precio_Servicio.Precio, Region.id FROM Region 
			LEFT JOIN Precio_Servicio 
			ON Region.id = Precio_Servicio.idRegion 
			RIGHT JOIN Vehiculo 
			ON Vehiculo.idVehiculo = Precio_Servicio.idVehiculo 
			RIGHT JOIN Servicio 
			ON Servicio.idServicio = Precio_Servicio.idServicio 
			RIGHT JOIN (
				SELECT id FROM Region 
				WHERE (latitudInicio < %s AND latitudFinal > %s) AND (longitudInicio < %s AND longitudFinal > %s) 
				ORDER BY prioridad ASC LIMIT 1) d 
			ON d.id = Region.id";

		public function readCleanerLocation($cleanerId){
				$query = sprintf(DataBaseService::QUERY_GET_CLEANER_LOCATION,$cleanerId);
				$result = $this->ejecutarQuery($query);
				$line = $result->fetch_assoc();
				return $line;
		}
		
	function getUserId($idService)
	{
		$query = sprintf(DataBaseService::QUERY_GET_USER_ID,$idService);
		$result = $this->ejecutarQuery($query);
		$line = $result->fetch_assoc();
    return $line['idCliente'];
	}
		
		public function readPushNotificationToken($serviceId){
				$query = sprintf(DataBaseService::QUERY_READ_PUSH_NOTIFICATION_TOKEN,$serviceId);
				$result = $this->ejecutarQuery($query);
				
				return $result;
		}
	
	public function updateTransactionId($serviceId,$transactionId){
		$query = sprintf(DataBaseService::QUERY_UPDATE_TRANSACTION_ID,$transactionId,$serviceId);
		$this->ejecutarQuery($query);
	}
	
	public function blockUser($idClient)
	{
		$query = sprintf(DataBaseService::QUERY_BLOCK_USER,$idClient);
		$result = $this->ejecutarQuery($query);
		
			return $result;
	}
	
	public function readCleanersLocation($pointLatitud, $pointLongitud, $distance)
	{
	    $query = sprintf(DataBaseService::QUERY_READ_CLEANERS_LOCATION,$pointLatitud, $pointLongitud, $pointLatitud, $distance,$pointLatitud, $pointLongitud, $pointLatitud, $distance);
		$result = $this->ejecutarQuery($query);
        return $result;
	}
	
	public function readReviewForCleaner($cleanerId)
	{
		$query = sprintf(DataBaseService::QUERY_READ_CLEANER_REVIEWS,$cleanerId);
		$result = $this->ejecutarQuery($query);
		
    return $result;
	}
	
	public function readServicesLocation($pointLatitud, $pointLongitud, $distance)
	{
		$query = sprintf(DataBaseService::QUERY_READ_SERVICES_LOCATION,$pointLatitud, $pointLongitud, $pointLatitud, $distance);
		$result = $this->ejecutarQuery($query);
		
    return $result;
	}
	
	function usuarioEstaBloqueado($idCliente) {
		$query = sprintf(DataBaseService::REVISAR_CLIENTE_BLOQUEADO,$idCliente);
		$resultado = $this->ejecutarQuery($query);
		return $this->resultadoTieneValores($resultado);
	}
  
	public function insertService($fecha,$direccion, $latitud,$longitud,$idServicio,$idCliente,$idCoche, $idCocheFavorito, $metodoDePago, $precio, $precioAPagar, $idRegion)
	{
		$query = sprintf(DataBaseService::QUERY_INSERT_SERVICE,$fecha,$direccion,$latitud,$longitud,$precio,
				$idServicio,$idCliente,$idCoche, $idCocheFavorito, $metodoDePago, $precioAPagar, $idRegion);
		$this->ejecutarQuery($query);
		return $this->mysqli->insert_id;
	}
	
	
	public function calculatePrice($idCoche, $idServicio ,$idRegion)
	{
		$query = sprintf(DataBaseService::QUERY_READ_PRICE,$idCoche, $idServicio, $idRegion);
		$result = $this->ejecutarQuery($query);
		
		if($result->num_rows === 0)
			throw new errorWithDatabaseException("Price could not be calculated");
		
		$priceForCarAndService = $result->fetch_assoc();
		return $priceForCarAndService['Precio'];
	}
	
  public function readAllServices()
  {
    $query = sprintf(DataBaseService::QUERY_READ_ALL_SERVICES);
    $result = $this->ejecutarQuery($query);
		
    return $result;
  }
	
	public function readAllServicesType()
	{
		$query = sprintf(DataBaseService::QUERY_READ_ALL_SERVICES_TYPE);
		$result = $this->ejecutarQuery($query);
		
    return $result;
	}
  
  public function readService($serviceId)
  {
    $query = sprintf(DataBaseService::QUERY_READ_SERVICE,$serviceId);
    $result = $this->ejecutarQuery($query);
		
		if($result->num_rows === 0)
			throw new serviceNotFoundException("Service not found");
		
    return $result;
  }
	
	public function readActiveServiceForUser($token)
  {
    $query = sprintf(DataBaseService::QUERY_READ_ACTIVE_SERVICE_FOR_USER,$token);
    $result = $this->ejecutarQuery($query);
		
    return $result;
  }
	
	public function readActiveServiceForCleaner($token)
  {
    $query = sprintf(DataBaseService::QUERY_READ_ACTIVE_SERVICE_FOR_CLEANER,$token);
    $result = $this->ejecutarQuery($query);
		
    return $result;
  }
	
	public function readServicesHistoryForUser($clientId)
  {
    $query = sprintf(DataBaseService::QUERY_SELECT_SERVICES_FOR_USER,$clientId);
    $result = $this->ejecutarQuery($query);
		
    return $result;
  }
	
	public function readServicesHistoryForCleaner($clientId)
  {
    $query = sprintf(DataBaseService::QUERY_SELECT_SERVICES_FOR_CLEANER,$clientId);
    $result = $this->ejecutarQuery($query);
		
    return $result;
  }
	public function updateAcceptTimeService ( $serviceId, $fecha ){
		$query = sprintf(DataBaseService::QUERY_UPDATE_ACCEPT_TIME,$fecha,$serviceId);
		$this->ejecutarQuery($query);
	}
	
	public function updateStartTimeService($serviceId, $fecha)
	{
		$query = sprintf(DataBaseService::QUERY_UPDATE_START_TIME,$fecha,$serviceId);
		$this->ejecutarQuery($query);
	}
	
	public function updateReview($serviceId,$rating){
		$query = sprintf(DataBaseService::QUERY_UPDATE_SERVICE_REVIEW,$rating,$serviceId);
		$this->ejecutarQuery($query);
	}
	
	public function updateService($serviceId, $statusId)
	{
		$query = sprintf(DataBaseService::QUERY_UPDATE_SERVICE,$statusId,$serviceId);
		$this->ejecutarQuery($query);
	}
	
	public function updateServiceAccepted($serviceId,$cleanerId)
	{
		$this->checkIfServiceCanBeAccepted($serviceId,$cleanerId);
		
		$query = sprintf(DataBaseService::QUERY_UPDATE_SERVICE_ACCEPTED,$cleanerId,$serviceId);
		$this->ejecutarQuery($query);
	}
	public function checkIfServiceCanBeAccepted($serviceId,$cleanerId)
	{
		$query = sprintf(DataBaseService::QUERY_UPDATE_SERVICE_ACCEPTED_CHECK_OPEN,$serviceId);
		$result = $this->ejecutarQuery($query);
		
		if($result->num_rows === 0)
			throw new serviceTakenException("Service taken");
		
		$query = sprintf(DataBaseService::QUERY_UPDATE_SERVICE_ACCEPTED_CHECK_AVAILABLE_PRODUCTS, $cleanerId, $serviceId);
		$result = $this->ejecutarQuery($query);
		
		if($result->num_rows != 0)
			throw new insufficientProductException("Not Enough Products");
	}
	
	public function removeCleanerProducts($serviceId)
	{
		$query = sprintf(DataBaseService::QUERY_UPDATE_CLEANER_PRODUCTS,$serviceId);
		$this->ejecutarQuery($query);
	}
	
	function generarYGuardarCodigoDePagoPorEfectivo($serviceId)
	{
		$idTransaccion = "efe_".md5 (uniqid(mt_rand(), true));
		$query = sprintf(DataBaseService::GUARDAR_TRANSACCION_EFECTIVO, $idTransaccion, $serviceId);
		$this->ejecutarQuery($query);
	}
	
	public function readProductsForService($serviceId,$cleanerId)
	{
		$query = sprintf(DataBaseService::QUERY_UPDATE_SERVICE_ACCEPTED_CHECK_OPEN,$serviceId);
		$result = $this->ejecutarQuery($query);
		
		if($result->num_rows === 0)
			throw new serviceTakenException("Service taken");
		
		$query = sprintf(DataBaseService::QUERY_UPDATE_SERVICE_ACCEPTED,$cleanerId,$serviceId);
		$this->ejecutarQuery($query);
	}

	function leerPrecios($latitud, $longitud)
	{
		$query = sprintf(DataBaseService::LEER_PRECIOS,$latitud, $latitud, $longitud, $longitud);
		$result = $this->ejecutarQuery($query);
		return $result;
	}
  
}
?>