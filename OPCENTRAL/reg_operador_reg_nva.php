<?php

$REGNVA=$_POST["REGNVA"];
$REGNVAUOP=$_POST["REGNVAUOP"];
$NIVEL_AUT=$_POST["NIVEL_AUT"];
$ID_OPERADOR=$_POST["ID_OPERADOR"];
$ID_ENHSEC=$_POST["ID_ENHSEC"];

if ($REGNVAUOP<>"") {
		//NIVEL DE AUTORIZACION (0-99)
		$CONSULTA2="UPDATE OP_OPERADOR SET NIVEL_AUT=".$NIVEL_AUT.", IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_OPERADOR=".$ID_OPERADOR;
		$RS2 = sqlsrv_query($conn, $CONSULTA2);
		//oci_execute($RS2);
		
		header("Location: reg_operador.php?ACT=".$ID_OPERADOR."&MSJE=5&ACT_NVA=1");
}

if ($REGNVA<>"") {
	if ($ID_ENHSEC<=14) {
			//VERIFICAR SI OPERADOR ESTA REGISTRADO PREVIAMENTE PARA NVA<=104
			$CONSULTA="SELECT * FROM OP_OPERANVA WHERE ID_OPERADOR=".$ID_OPERADOR;
			$RS = sqlsrv_query($conn, $CONSULTA);
			//oci_execute($RS);
			if ($row = sqlsrv_fetch_array($RS)) {
						//ACTUALIZA REGISTROS PARA LA ID_ENHSEC
						$CONSULTA1="SELECT * FROM OP_NVLAUTO WHERE ID_ENHSEC=".$ID_ENHSEC." ORDER BY POS_NVL ASC";
						$RS1 = sqlsrv_query($conn, $CONSULTA1);
						//oci_execute($RS1);
						while ($row = sqlsrv_fetch_array($RS1)) {
								$ID_NVLAUTO=$row['ID_NVLAUTO'];
								$VERIFICA=$_POST["NVA".$ID_NVLAUTO];
								if($VERIFICA=="Y"){ $VALOR_YN="Y";} else {$VALOR_YN="N";}
								
								$SQLUPD="UPDATE OP_OPERANVA SET  VALUE='".$VALOR_YN."', IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_NVLAUTO=".$ID_NVLAUTO." AND ID_OPERADOR=".$ID_OPERADOR;
								$RSUPD = sqlsrv_query($conn, $SQLUPD);
								//oci_execute($RSUPD);
								
								//PARA VALOR_YN="N" HAY QUE VERIFICAR LAS DEPENDENCIAS Y RE-SETEAR LOS DEPENDIENTES
								if($VALOR_YN=="N"){
									
											//QUIEN DEPENDE DE ID_NVLAUTO (VER EN OP_ENHSEC A 3 NIVELES)
											$SQLDNV1="SELECT ID_ENHSEC FROM OP_ENHSEC WHERE MNVL_DEP=".$ID_NVLAUTO; //YA PASO A "N"
											$RSNV1 = sqlsrv_query($conn, $SQLDNV1);
											//oci_execute($RSNV1);
											if ($rowDnv1 = sqlsrv_fetch_array($RSNV1)) {
													$DEP_NV1 = $rowDnv1['ID_ENHSEC']; //DEPENDIENTE NV1
													$SQLD1="SELECT ID_NVLAUTO FROM OP_NVLAUTO WHERE ID_ENHSEC=".$DEP_NV1;
													$RSD1 = sqlsrv_query($conn, $SQLD1);
													//oci_execute($RSD1);
													while ($rowD1 = sqlsrv_fetch_array($RSD1)) {
															$ID_NVLAUTO1 = $rowD1['ID_NVLAUTO']; //DEPENDIENTE NV1
															//PASAR A "N" LOS PRIMEROS DEPENDIENTES
															$SQLPN1="UPDATE OP_OPERANVA SET  VALUE='N', IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_NVLAUTO=".$ID_NVLAUTO1." AND ID_OPERADOR=".$ID_OPERADOR;
															$RSPN1 = sqlsrv_query($conn, $SQLPN1);
															//oci_execute($RSPN1);
															$SQLDNV2="SELECT ID_ENHSEC FROM OP_ENHSEC WHERE MNVL_DEP=".$ID_NVLAUTO1; //PASAR A "N"
															$RSNV2 = sqlsrv_query($conn, $SQLDNV2);
															//oci_execute($RSNV2);
															if ($rowDnv2 = sqlsrv_fetch_array($RSNV2)) {
																	$DEP_NV2 = $rowDnv2['ID_ENHSEC']; //DEPENDIENTE NV2
																	$SQLD2="SELECT ID_NVLAUTO FROM OP_NVLAUTO WHERE ID_ENHSEC=".$DEP_NV2;
																	$RSD2 = sqlsrv_query($conn, $SQLD2);
																	//oci_execute($RSD2);
																	while ($rowD2 = sqlsrv_fetch_array($RSD2)) {
																			$ID_NVLAUTO2 = $rowD2['ID_NVLAUTO']; //DEPENDIENTE NV3
																			//PASAR A "N" LOS SEGUNDOS DEPENDIENTES
																			$SQLPN2="UPDATE OP_OPERANVA SET  VALUE='N', IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_NVLAUTO=".$ID_NVLAUTO2." AND ID_OPERADOR=".$ID_OPERADOR;
																			$RSPN2 = sqlsrv_query($conn, $SQLPN2);
																			//oci_execute($RSPN2);
																	}
															}
													}
											}
								
								}
										
								//GRABAR NUMEROS DE GRUPO Y USUARIO
								if($ID_NVLAUTO==1000){
										$CONSULTA2="UPDATE OP_OPERADOR SET NVA_GRUPO=".$VERIFICA.", IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_OPERADOR=".$ID_OPERADOR;
										$RS2 = sqlsrv_query($conn, $CONSULTA2);
										//oci_execute($RS2);
								}
								if($ID_NVLAUTO==1001){
										$CONSULTA2="UPDATE OP_OPERADOR SET NVA_USUARIO=".$VERIFICA.", IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_OPERADOR=".$ID_OPERADOR;
										$RS2 = sqlsrv_query($conn, $CONSULTA2);
										//oci_execute($RS2);
								}
						}
			} else {
						//REGISTRA ID_ENHSEC POR DEFAULT
						for ($i = 1; $i <= 104; $i++) {
							$CONSULTA2="INSERT INTO OP_OPERANVA (ID_OPERADOR, ID_NVLAUTO, IDREG) VALUES (".$ID_OPERADOR.", ".$i.", ".$SESIDUSU.")";
							$RS2 = sqlsrv_query($conn, $CONSULTA2);
							//oci_execute($RS2);
						}
						
						$CONSULTA1="SELECT * FROM OP_NVLAUTO WHERE ID_ENHSEC=".$ID_ENHSEC." ORDER BY POS_NVL ASC";
						$RS1 = sqlsrv_query($conn, $CONSULTA1);
						//oci_execute($RS1);
						while ($row = sqlsrv_fetch_array($RS1)) {
								$ID_NVLAUTO=$row['ID_NVLAUTO'];
								$VERIFICA=$_POST["NVA".$ID_NVLAUTO];
								if($VERIFICA=="Y"){ $VALOR_YN="Y";} else {$VALOR_YN="N";}
								$CONSULTA2="UPDATE OP_OPERANVA SET VALUE='".$VALOR_YN."', IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_NVLAUTO=".$ID_NVLAUTO." AND ID_OPERADOR=".$ID_OPERADOR;
								$RS2 = sqlsrv_query($conn, $CONSULTA2);
								//oci_execute($RS2);
								//GRABAR NUMEROS DE GRUPO Y USUARIO
								if($ID_NVLAUTO==1000){
										$CONSULTA2="UPDATE OP_OPERADOR SET NVA_GRUPO=".$VERIFICA.", IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_OPERADOR=".$ID_OPERADOR;
										$RS2 = sqlsrv_query($conn, $CONSULTA2);
										//oci_execute($RS2);
								}
								if($ID_NVLAUTO==1001){
										$CONSULTA2="UPDATE OP_OPERADOR SET NVA_USUARIO=".$VERIFICA.", IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_OPERADOR=".$ID_OPERADOR;
										$RS2 = sqlsrv_query($conn, $CONSULTA2);
										//oci_execute($RS2);
								}
						}
			}
	} //$ID_ENHSEC<=14
	if ($ID_ENHSEC==15) {
			//VERIFICAR SI OPERADOR ESTA REGISTRADO PREVIAMENTE PARA IDNHSEC=15 USUARIO DEFINIDO
			$CONSULTA="SELECT * FROM OP_OPERAUDF WHERE ID_OPERADOR=".$ID_OPERADOR;
			$RS = sqlsrv_query($conn, $CONSULTA);
			//oci_execute($RS);
			if ($row = sqlsrv_fetch_array($RS)) {
						//ACTUALIZA REGISTROS PARA LA ID_ENHSEC
						$CONSULTA1="SELECT * FROM OP_NVLAUTO WHERE ID_ENHSEC=".$ID_ENHSEC." ORDER BY POS_NVL ASC";
						$RS1 = sqlsrv_query($conn, $CONSULTA1);
						//oci_execute($RS1);
						while ($row = sqlsrv_fetch_array($RS1)) {
								$ID_NVLAUTO=$row['ID_NVLAUTO'];
								$VERIFICA=$_POST["NVA".$ID_NVLAUTO];
								if($VERIFICA=="Y"){ $VALOR_YN="Y";} else {$VALOR_YN="N";}
								$CONSULTA2="UPDATE OP_OPERAUDF SET  VALUE='".$VALOR_YN."', IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_NVLAUTO=".$ID_NVLAUTO." AND ID_OPERADOR=".$ID_OPERADOR;
								$RS2 = sqlsrv_query($conn, $CONSULTA2);
								//oci_execute($RS2);
						}
			} else {
						//REGISTRA ID_ENHSEC POR DEFAULT
						for ($i = 1002; $i <= 1009; $i++) {
							$CONSULTA2="INSERT INTO OP_OPERAUDF (ID_OPERADOR, ID_NVLAUTO, IDREG) VALUES (".$ID_OPERADOR.", ".$i.", ".$SESIDUSU.")";
							$RS2 = sqlsrv_query($conn, $CONSULTA2);
							//oci_execute($RS2);
						}
						
						$CONSULTA1="SELECT * FROM OP_NVLAUTO WHERE ID_ENHSEC=".$ID_ENHSEC." ORDER BY POS_NVL ASC";
						$RS1 = sqlsrv_query($conn, $CONSULTA1);
						//oci_execute($RS1);
						while ($row = sqlsrv_fetch_array($RS1)) {
								$ID_NVLAUTO=$row['ID_NVLAUTO'];
								$VERIFICA=$_POST["NVA".$ID_NVLAUTO];
								if($VERIFICA=="Y"){ $VALOR_YN="Y";} else {$VALOR_YN="N";}
								$CONSULTA2="UPDATE OP_OPERAUDF SET VALUE='".$VALOR_YN."', IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_NVLAUTO=".$ID_NVLAUTO." AND ID_OPERADOR=".$ID_OPERADOR;
								$RS2 = sqlsrv_query($conn, $CONSULTA2);
								//oci_execute($RS2);
						}
			}
	} //ID_ENHSEC=15

	header("Location: reg_operador.php?ACT=".$ID_OPERADOR."&MSJE=5&ACT_NVA=1");
}// FIN REGNVA


?>