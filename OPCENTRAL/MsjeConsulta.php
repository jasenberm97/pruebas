<?php include("session.inc");?>

<?php
//ResetCodAuth
if(isset($_GET['ResetCodAuth'])){
	$IdOpera=$_GET['IdOpera'];
	$NOM_ARCHIVO=$_GET['Archivo'];

	//PASO DEL TIEMPO DE ESPERA
	$SQL="SELECT MAX(ID_CODSUPER) AS MAXCOD FROM OP_CODSUPER WHERE ID_OPERADOR=".$IdOpera;
	$RS = sqlsrv_query($conn, $SQL);
	if ($row = sqlsrv_fetch_array($RS)) {
			$MAXCOD=$row['MAXCOD'];
	}
	$SQL="UPDATE OP_CODSUPER SET ESTADO=2 WHERE ID_CODSUPER=".$MAXCOD;
	$RS = sqlsrv_query($conn, $SQL);
	if(file_exists($DIR_FLJ."IN/".$NOM_ARCHIVO)){
		unlink($DIR_FLJ."IN/".$NOM_ARCHIVO);
	}
	
	header('Content-Type: application/json');
	echo json_encode("UNO");

}


//CheckStatusFour
if(isset($_GET['CheckStatusFour'])){
	$IdOpera=$_GET['IdOpera'];
	$SQL="SELECT TOP 1 ESTADO FROM OP_CODSUPER WHERE ID_OPERADOR=$IdOpera ORDER BY ID_CODSUPER DESC";
	$RS = sqlsrv_query($conn, $SQL);
	if ($row = sqlsrv_fetch_array($RS)) {
			$ESTADO=$row['ESTADO'];
	}
	if($ESTADO == 4){
		header('Content-Type: application/json');
		echo json_encode("CUATRO");
	}
	if($ESTADO == 2){
		header('Content-Type: application/json');
		echo json_encode("DOS");
	}
}
?>

