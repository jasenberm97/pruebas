<?php include("session.inc");?>
<?php

				
$ACTUALIZAR=$_POST["ACTUALIZAR"];

if ($ACTUALIZAR<>"") {
		$ID_INDICAT=$_POST["ID_INDICAT"];
		$DESCRIP_ES=COMILLAS($_POST["DESCRIP_ES"]);
		$DESCRIP_EN=COMILLAS($_POST["DESCRIP_EN"]);
		
			$CONSULTA="SELECT DESCRIP_EN FROM OP_INDICAT WHERE (UPPER(DESCRIP_EN)='". strtoupper($DESCRIP_EN). "' OR UPPER(DESCRIP_ES)='". strtoupper($DESCRIP_ES). "') AND ID_INDICAT<>".$ID_INDICAT;
			$RS = sqlsrv_query($conn, $CONSULTA);
			//oci_execute($RS);
			if ($row = sqlsrv_fetch_array($RS)) {
				header("Location: mant_autoriza.php?ACT=".$ID_INDICAT."&MSJE=2");
			} else {
				$CONSULTA2="UPDATE OP_INDICAT SET DESCRIP_ES='".$DESCRIP_ES."', DESCRIP_EN='".$DESCRIP_EN."', FECHA=convert(datetime,GETDATE(), 121), IDREG=".$SESIDUSU." WHERE ID_INDICAT=".$ID_INDICAT;
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
						$SQLOG=$SQLOG."( 3, convert(datetime,GETDATE(), 121), '".$TIMESRV."', '".$IP_CLIENTE."', ".$SESIDUSU.", 1130, ".$SESIDSISTEMA.", ".$SESIDPERFIL.")";
						$RSL = sqlsrv_query($maestra, $SQLOG);
						//oci_execute($RSL);																	
																		

				header("Location: mant_autoriza.php?ACT=".$ID_INDICAT."&MSJE=1");
		}
		
		//sqlsrv_close($conn);
		//sqlsrv_close($maestra);
		sqlsrv_close( $conn );
		sqlsrv_close( $maestra );
}
				



$ACTUALIZA_BIT=$_POST["ACTUALIZA_BIT"];

if ($ACTUALIZA_BIT<>"") {
		$ID_INDICAT=$_POST["ACT"];
		$ID_INDICATOPC=$_POST["ACTBIT"];
		$DESCRIP_ES=COMILLAS($_POST["DESCRIP_ES"]);
		$DESCRIP_EN=COMILLAS($_POST["DESCRIP_EN"]);
		
//			$CONSULTA="SELECT DESCRIP_EN FROM OP_INDICATOPC WHERE (UPPER(DESCRIP_EN)='". strtoupper($DESCRIP_EN). "' OR UPPER(DESCRIP_ES)='". strtoupper($DESCRIP_ES). "') AND ID_INDICATOPC<>".$ID_INDICATOPC;
//			$RS = sqlsrv_query($conn, $CONSULTA);
//			//oci_execute($RS);
//			if ($row = sqlsrv_fetch_array($RS)) {
//				header("Location: mant_autoriza.php?ACT=".$ID_INDICAT."&ACTBIT=".$ID_INDICATOPC."&MSJE=2");
//			} else {
				$CONSULTA2="UPDATE OP_INDICATOPC SET DESCRIP_ES='".$DESCRIP_ES."', DESCRIP_EN='".$DESCRIP_EN."', FECHA=convert(datetime,GETDATE(), 121), IDREG=".$SESIDUSU." WHERE ID_INDICATOPC=".$ID_INDICATOPC;
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
						$SQLOG=$SQLOG."( 3, convert(datetime,GETDATE(), 121), '".$TIMESRV."', '".$IP_CLIENTE."', ".$SESIDUSU.", 1130, ".$SESIDSISTEMA.", ".$SESIDPERFIL.")";
						$RSL = sqlsrv_query($maestra, $SQLOG);
						//oci_execute($RSL);																	
																		
				header("Location: mant_autoriza.php?ACT=".$ID_INDICAT."&ACTBIT=".$ID_INDICATOPC."&MSJE=1");
//		}
		sqlsrv_close($conn);
		sqlsrv_close($maestra);
}
				
			