<?php include("session.inc");?>
<?php


$INGRESAR=$_POST["INGRESAR"];

if ($INGRESAR<>"") {
		$POSICION=$_POST["POSICION"];
		$DESCRIP_ES=COMILLAS($_POST["DESCRIP_ES"]);
		$DESCRIP_EN=COMILLAS($_POST["DESCRIP_EN"]);
		
			$CONSULTA="SELECT DESCRIP_EN FROM OP_INDICATMNO WHERE (UPPER(DESCRIP_EN)='". strtoupper($DESCRIP_EN). "' OR UPPER(DESCRIP_ES)='". strtoupper($DESCRIP_ES). "') ";
			$RS = sqlsrv_query($conn, $CONSULTA);
			//oci_execute($RS);
			if ($row = sqlsrv_fetch_array($RS)) {
				header("Location: mant_menuautoriza.php?NEO=1&MSJE=2");
			} else {
				$CONSULTA2="SELECT MAX(ID_INDICATMNO) AS MIDIND FROM OP_INDICATMNO";
				$RS2 = sqlsrv_query($conn, $CONSULTA2);
				//oci_execute($RS2);
				if ($row = sqlsrv_fetch_array($RS2)) {
						$ID_INDICATMNO=$row['MIDIND']+1;
					} else {
						$ID_INDICATMNO=1;
				}
				$CONSULTA2="INSERT INTO OP_INDICATMNO (ID_INDICATMNO, DESCRIP_ES, DESCRIP_EN, POSICION, IDREG) ";
				$CONSULTA2=$CONSULTA2." VALUES (".$ID_INDICATMNO.", '".$DESCRIP_ES."', '".$DESCRIP_EN."', ".$POSICION.", ".$SESIDUSU.")";
				$RS2 = sqlsrv_query($conn, $CONSULTA2);
				//oci_execute($RS2);

				//REGISTRO DE ALTA
						
						$SQLOG="INSERT INTO LG_EVENTO (COD_TIPO_EVENTO, FECHA, HORA, IP_CLIENTE, COD_USUARIO, IDACC, IDSISTEMA, IDPERFIL) VALUES ";
						$SQLOG=$SQLOG."( 1, convert(datetime,GETDATE(), 121), '".$TIMESRV."', '".$IP_CLIENTE."', ".$SESIDUSU.", 1128, ".$SESIDSISTEMA.", ".$SESIDPERFIL.")";
						$RSL = sqlsrv_query($maestra, $SQLOG);
						//oci_execute($RSL);																	


				header("Location: mant_menuautoriza.php?ACT=".$ID_INDICATMNO."&MSJE=3");
		}
		sqlsrv_close($conn);
		sqlsrv_close($maestra);
}
				
				

$ACTUALIZAR=$_POST["ACTUALIZAR"];

if ($ACTUALIZAR<>"") {
		$ID_INDICATMNO=$_POST["ID_INDICATMNO"];
		$DESCRIP_ES=COMILLAS($_POST["DESCRIP_ES"]);
		$DESCRIP_EN=COMILLAS($_POST["DESCRIP_EN"]);
		
			$CONSULTA="SELECT DESCRIP_EN FROM OP_INDICATMNO WHERE (UPPER(DESCRIP_EN)='". strtoupper($DESCRIP_EN). "' OR UPPER(DESCRIP_ES)='". strtoupper($DESCRIP_ES). "') AND ID_INDICATMNO<>".$ID_INDICATMNO;
			$RS = sqlsrv_query($conn, $CONSULTA);
			//oci_execute($RS);
			if ($row = sqlsrv_fetch_array($RS)) {
				header("Location: mant_menuautoriza.php?ACT=".$ID_INDICATMNO."&MSJE=2");
			} else {
				$CONSULTA2="UPDATE OP_INDICATMNO SET DESCRIP_ES='".$DESCRIP_ES."', DESCRIP_EN='".$DESCRIP_EN."', FECHA=convert(datetime,GETDATE(), 121), IDREG=".$SESIDUSU." WHERE ID_INDICATMNO=".$ID_INDICATMNO;
				$RS2 = sqlsrv_query($conn, $CONSULTA2);
				//oci_execute($RS2);

				
				//REGISTRO DE MODIFICACION
						$S2="SELECT MAX(COD_EVENTO) AS MCOD_EVENTO FROM LG_EVENTO";
						$RS2 = sqlsrv_query($maestra, $S2);
						//oci_execute($RS2);
						if ($row = sqlsrv_fetch_array($RS2)) {
								$COD_EVENTO=$row['MCOD_EVENTO']+1;
							} else {
								$COD_EVENTO=1;
						}
						$SQLOG="INSERT INTO LG_EVENTO (COD_TIPO_EVENTO, FECHA, HORA, IP_CLIENTE, COD_USUARIO, IDACC, IDSISTEMA, IDPERFIL) VALUES ";
						$SQLOG=$SQLOG."( 3, convert(datetime,GETDATE(), 121), '".$TIMESRV."', '".$IP_CLIENTE."', ".$SESIDUSU.", 1128, ".$SESIDSISTEMA.", ".$SESIDPERFIL.")";
						$RSL = sqlsrv_query($maestra, $SQLOG);
						//oci_execute($RSL);																	
																		

				header("Location: mant_menuautoriza.php?ACT=".$ID_INDICATMNO."&MSJE=1");
		}
		sqlsrv_close($conn);
		sqlsrv_close($maestra);
}
				



$ACTUALIZA_BIT=$_POST["ACTUALIZA_BIT_HGFTUJHGTREEEYUIOOIYGYTF"];

if ($ACTUALIZA_BIT<>"") {
		$ID_INDICATMNO=$_POST["ACT"];
		$ID_INDICATMNOOPC=$_POST["ACTBIT"];
		$DESCRIP_ES=COMILLAS($_POST["DESCRIP_ES"]);
		$DESCRIP_EN=COMILLAS($_POST["DESCRIP_EN"]);
		
			$CONSULTA="SELECT DESCRIP_EN FROM OP_INDICATMNOOPC WHERE (UPPER(DESCRIP_EN)='". strtoupper($DESCRIP_EN). "' OR UPPER(DESCRIP_ES)='". strtoupper($DESCRIP_ES). "') AND ID_INDICATMNOOPC<>".$ID_INDICATMNOOPC;
			$RS = sqlsrv_query($conn, $CONSULTA);
			//oci_execute($RS);
			if ($row = sqlsrv_fetch_array($RS)) {
				header("Location: mant_menuautoriza.php?ACT=".$ID_INDICATMNO."&ACTBIT=".$ID_INDICATMNOOPC."&MSJE=2");
			} else {
				$CONSULTA2="UPDATE OP_INDICATMNOOPC SET DESCRIP_ES='".$DESCRIP_ES."', DESCRIP_EN='".$DESCRIP_EN."', FECHA=convert(datetime,GETDATE(), 121), IDREG=".$SESIDUSU." WHERE ID_INDICATMNOOPC=".$ID_INDICATMNOOPC;
				$RS2 = sqlsrv_query($conn, $CONSULTA2);
				//oci_execute($RS2);
				
				//REGISTRO DE MODIFICACION
						$S2="SELECT MAX(COD_EVENTO) AS MCOD_EVENTO FROM LG_EVENTO";
						$RS2 = sqlsrv_query($maestra, $S2);
						//oci_execute($RS2);
						if ($row = sqlsrv_fetch_array($RS2)) {
								$COD_EVENTO=$row['MCOD_EVENTO']+1;
							} else {
								$COD_EVENTO=1;
						}
						$SQLOG="INSERT INTO LG_EVENTO (COD_TIPO_EVENTO, FECHA, HORA, IP_CLIENTE, COD_USUARIO, IDACC, IDSISTEMA, IDPERFIL) VALUES ";
						$SQLOG=$SQLOG."( 3, convert(datetime,GETDATE(), 121), '".$TIMESRV."', '".$IP_CLIENTE."', ".$SESIDUSU.", 1128, ".$SESIDSISTEMA.", ".$SESIDPERFIL.")";
						$RSL = sqlsrv_query($maestra, $SQLOG);
						//oci_execute($RSL);																	
																		
				header("Location: mant_menuautoriza.php?ACT=".$ID_INDICATMNO."&ACTBIT=".$ID_INDICATMNOOPC."&MSJE=1");
		}
		sqlsrv_close($conn);
		sqlsrv_close($maestra);
}


				

$AGREGAR=$_POST["AGREGAR"];

if ($AGREGAR<>"") {
		$ID_INDICATOPC=$_POST["ID_INDICATOPC"];
		$ID_INDICAT=$_POST["ID_INDICAT"];
		$ID_INDICATMNO=$_POST["ID_INDICATMNO"];
		
			$CONSULTA="SELECT * FROM OP_INDICATMNOOPC WHERE ID_INDICATMNO=".$ID_INDICATMNO." AND ID_INDICATOPC=".$ID_INDICATOPC;
			$RS = sqlsrv_query($conn, $CONSULTA);
			//oci_execute($RS);
			if ($row = sqlsrv_fetch_array($RS)) {
				header("Location: mant_menuautoriza.php?ACT=".$ID_INDICATMNO."&MSJE=2");
			} else {
				$CONSULTA2="INSERT INTO OP_INDICATMNOOPC (ID_INDICATMNO, ID_INDICATOPC, IDREG) ";
				$CONSULTA2=$CONSULTA2." VALUES (".$ID_INDICATMNO.",".$ID_INDICATOPC.", ".$SESIDUSU.")";
				$RS2 = sqlsrv_query($conn, $CONSULTA2);
				//oci_execute($RS2);

				//REGISTRO DE ALTA
						$CONSULTA2="SELECT MAX(COD_EVENTO) AS MCOD_EVENTO FROM LG_EVENTO";
						$RS2 = sqlsrv_query($maestra, $CONSULTA2);
						//oci_execute($RS2);
						if ($row = sqlsrv_fetch_array($RS2)) {
								$COD_EVENTO=$row['MCOD_EVENTO']+1;
							} else {
								$COD_EVENTO=1;
						}
						$SQLOG="INSERT INTO LG_EVENTO (COD_TIPO_EVENTO, FECHA, HORA, IP_CLIENTE, COD_USUARIO, IDACC, IDSISTEMA, IDPERFIL) VALUES ";
						$SQLOG=$SQLOG."( 1, convert(datetime,GETDATE(), 121), '".$TIMESRV."', '".$IP_CLIENTE."', ".$SESIDUSU.", 1128, ".$SESIDSISTEMA.", ".$SESIDPERFIL.")";
						$RSL = sqlsrv_query($maestra, $SQLOG);
						//oci_execute($RSL);																	


				header("Location: mant_menuautoriza.php?ACT=".$ID_INDICATMNO."&MSJE=3");
		}
		sqlsrv_close($conn);
		sqlsrv_close($maestra);
}
				
				


$RETIRAR=@$_GET["RETIRAR"];

if ($RETIRAR<>"") {
		$ID_INDICATOPC=@$_GET["ID_INDICATOPC"];
		$ID_INDICATMNO=@$_GET["ID_INDICATMNO"];
		
				$CONSULTA2="DELETE FROM OP_INDICATMNOOPC WHERE ID_INDICATOPC=".$ID_INDICATOPC." AND ID_INDICATMNO=".$ID_INDICATMNO;
				$RS2 = sqlsrv_query($conn, $CONSULTA2);
				//oci_execute($RS2);

				//REGISTRO DE BAJA
						$CONSULTA2="SELECT MAX(COD_EVENTO) AS MCOD_EVENTO FROM LG_EVENTO";
						$RS2 = sqlsrv_query($maestra, $CONSULTA2);
						//oci_execute($RS2);
						if ($row = sqlsrv_fetch_array($RS2)) {
								$COD_EVENTO=$row['MCOD_EVENTO']+1;
							} else {
								$COD_EVENTO=1;
						}
						$SQLOG="INSERT INTO LG_EVENTO (COD_TIPO_EVENTO, FECHA, HORA, IP_CLIENTE, COD_USUARIO, IDACC, IDSISTEMA, IDPERFIL) VALUES ";
						$SQLOG=$SQLOG."( 2, convert(datetime,GETDATE(), 121), '".$TIMESRV."', '".$IP_CLIENTE."', ".$SESIDUSU.", 1128, ".$SESIDSISTEMA.", ".$SESIDPERFIL.")";
						$RSL = sqlsrv_query($maestra, $SQLOG);
						//oci_execute($RSL);																	


				header("Location: mant_menuautoriza.php?ACT=".$ID_INDICATMNO."&MSJE=4");

		sqlsrv_close($conn);
		sqlsrv_close($maestra);
}
				
				


?>