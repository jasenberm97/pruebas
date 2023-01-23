<?php include("session.inc");?>
<?php


$ID_OPERADOR=$_GET["GENCODE"];

if ($ID_OPERADOR<>"") {
		$SQL="SELECT * FROM OP_OPERADOR WHERE ID_OPERADOR=".$ID_OPERADOR;
		$RS = sqlsrv_query($conn, $SQL);
		//oci_execute($RS);
		if ($row = sqlsrv_fetch_array($RS)) {
				$CC_OPERADOR=$row['CC_OPERADOR'];
				$DES_CLAVE=$row['COD_TIENDA'];
		}
		//VERIFICAR SI OPERADOR YA CUENTA CON CODIGO
		$SQL="SELECT MAX(ID_CODSUPER) AS MAXCOD FROM OP_CODSUPER WHERE ID_OPERADOR=".$ID_OPERADOR;
		$RS = sqlsrv_query($conn, $SQL);
		//oci_execute($RS);
		if ($row = sqlsrv_fetch_array($RS)) {
				$MAXCOD=$row['MAXCOD'];
		}
		
		$SQL="SELECT * FROM OP_CODSUPER WHERE ID_CODSUPER=".$MAXCOD;
		$RS = sqlsrv_query($conn, $SQL);
		//oci_execute($RS);
		if ($row = sqlsrv_fetch_array($RS)) {
				$ESTADO=$row['ESTADO'];
		}
		//LEYENDO ESTADOS
		//SI ESTADO ES 4, TIENE CODIGO ACTIVO, ENTONCES CAMBIAR ESTADO Y GENERAR NUEVO CÓDIGO
		if($ESTADO==4){
				$SQL="UPDATE OP_CODSUPER SET ESTADO=5 WHERE ID_CODSUPER=".$MAXCOD;
				$RS = sqlsrv_query($conn, $SQL);
				//oci_execute($RS);
		}
		//SI ESTADO ES 4, TIENE CODIGO CADUCO, GENERAR NUEVO CÓDIGO
				//GENERAR CODE128
						$PREFIJO=$PREFCODESUPER;
						//RELLENAR CUENTA
						$CUENTA=str_pad($CC_OPERADOR, 9, "0", STR_PAD_LEFT);
						//PASAR A UN ARREGLO
						$ARR_CUENTA = str_split($CUENTA);
						$LACUENTA = $ARR_CUENTA[6].$ARR_CUENTA[4].$ARR_CUENTA[2].$ARR_CUENTA[0].$ARR_CUENTA[7].$ARR_CUENTA[5].$ARR_CUENTA[3].$ARR_CUENTA[1].$ARR_CUENTA[8];
						if($SMO==1){ //SEGURIDAD MEJORADA
							$CLAVE0=substr(str_shuffle("1234567890"), 0, 1);
							$CLAVE1=substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1);
							$CLAVE2=substr(str_shuffle("1234567890"), 0, 1);
							$CLAVE3=substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1);
							$CLAVE4=substr(str_shuffle("1234567890"), 0, 1);
							$CLAVE5=substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1);
							$CLAVE6=substr(str_shuffle("1234567890"), 0, 1);
							$CLAVE7=substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1);
							$REGCLAVE = $CLAVE0.$CLAVE1.$CLAVE2.$CLAVE3.$CLAVE4.$CLAVE5.$CLAVE6.$CLAVE7;
							$CODCLAVE = $CLAVE2.$CLAVE5.$CLAVE0.$CLAVE3.$CLAVE6.$CLAVE1.$CLAVE4.$CLAVE7;
						} else {
							$CLAVE0=substr(str_shuffle("1234567890"), 0, 1);
							$CLAVE1=substr(str_shuffle("1234567890"), 0, 1);
							$CLAVE2=substr(str_shuffle("1234567890"), 0, 1);
							$CLAVE3=substr(str_shuffle("1234567890"), 0, 1);
							$CLAVE4=substr(str_shuffle("1234567890"), 0, 1);
							$CLAVE5=substr(str_shuffle("1234567890"), 0, 1);
							$CLAVE6=substr(str_shuffle("1234567890"), 0, 1);
							$CLAVE7=substr(str_shuffle("1234567890"), 0, 1);
							$REGCLAVE = $CLAVE0.$CLAVE1.$CLAVE2.$CLAVE3.$CLAVE4.$CLAVE5.$CLAVE6.$CLAVE7;
							$CODCLAVE = $CLAVE2.$CLAVE5.$CLAVE0.$CLAVE3.$CLAVE6.$CLAVE1.$CLAVE4.$CLAVE7;
						}
						$CODE128=$PREFCODESUPER.$LACUENTA.$CODCLAVE;
						
				$SQL="INSERT INTO OP_CODSUPER (ID_OPERADOR, CODE128, CLAVE, ESTADO, IDREG) VALUES (".$ID_OPERADOR.", '".$CODE128."', '".$REGCLAVE."', 1, ".$SESIDUSU.")";
				$RS = sqlsrv_query($conn, $SQL);
				//oci_execute($RS);
		
		//GENERAR ARCHIVO
		$EXTEN="000".$DES_CLAVE;
		$EXTEN=substr($EXTEN, -3); 
		
		$NOM_ARCHIVO="SUP".date("YmdHis").".".$EXTEN;
		
					$LN_PRINT=$CODE128;
					 $open = fopen("_arc_prt/".$NOM_ARCHIVO, "w+");
					 fwrite($open, $LN_PRINT);
					 fclose($open);

		$local_file="_arc_prt/".$NOM_ARCHIVO;
		copy($local_file, $DIR_FLJ."IN/".$NOM_ARCHIVO);
		
		header("Location: reg_codeauto.php");
}
?>
