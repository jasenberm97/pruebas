<?php include("session.inc");?>
<?php
$ACTUALIZAR=$_POST["ACTUALIZAR"];

if ($ACTUALIZAR<>"") {
		$ID_MODOPERA=$_POST["ID_MODOPERA"];
		$DES_MODOPERA=COMILLAS($_POST["DES_MODOPERA"]);
		$EST_MODOPERA=$_POST["EST_MODOPERA"];
		
				$CONSULTA2="UPDATE OP_MODOPERA SET DES_MODOPERA='".$DES_MODOPERA."', FECHA=convert(datetime,GETDATE(), 121), EST_MODOPERA=".$EST_MODOPERA." , IDREG=".$SESIDUSU." WHERE ID_MODOPERA=".$ID_MODOPERA;
				$RS2 = sqlsrv_query($conn, $CONSULTA2);
				//oci_execute($RS2);

				//REGISTRO DE MODIFICACION
						$CONSULTA2="SELECT MAX(COD_EVENTO) AS MCOD_EVENTO FROM LG_EVENTO";
						$RS2 = sqlsrv_query($maestra, $CONSULTA2);
						//oci_execute($RS2);
						if ($row = sqlsrv_fetch_array($RS2)) {
								$COD_EVENTO=$row['MCOD_EVENTO']+1;
							} else {
								$COD_EVENTO=1;
						}
						$SQLOG="INSERT INTO LG_EVENTO (COD_TIPO_EVENTO, FECHA, HORA, IP_CLIENTE, COD_USUARIO, IDACC, IDSISTEMA, IDPERFIL) VALUES ";
						$SQLOG=$SQLOG."( 3, convert(datetime,GETDATE(), 121), '".$TIMESRV."', '".$IP_CLIENTE."', ".$SESIDUSU.", 1129, ".$SESIDSISTEMA.", ".$SESIDPERFIL.")";
						$RSL = sqlsrv_query($maestra, $SQLOG);
						//oci_execute($RSL);																	

				header("Location: mant_modelos.php?ACT=".$ID_MODOPERA."&MSJE=1");

		sqlsrv_close($conn);
}
				
				

$REGNVA=$_POST["REGNVA"];
$REGNVAUOP=$_POST["REGNVAUOP"];
$NIVEL_AUT=$_POST["NIVEL_AUT"];
$ID_MODOPERA=$_POST["ID_MODOPERA"];
$ID_ENHSEC=$_POST["ID_ENHSEC"];

if ($REGNVAUOP<>"") {
		//NIVEL DE AUTORIZACION (0-99)
		$CONSULTA2="UPDATE OP_MODOPERA SET NIVEL_AUT=".$NIVEL_AUT.", IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_MODOPERA=".$ID_MODOPERA;
		$RS2 = sqlsrv_query($conn, $CONSULTA2);
		//oci_execute($RS2);
		
		header("Location: mant_modelos.php?ACT=".$ID_MODOPERA."&MSJE=5&ACT_NVA=1");
}

if ($REGNVA<>"") {
	if ($ID_ENHSEC<=14) {
			//VERIFICAR SI MODOPERA ESTA REGISTRADO PREVIAMENTE PARA NVA<=104
			$CONSULTA="SELECT * FROM OP_MODNVA WHERE ID_MODOPERA=".$ID_MODOPERA;
			$RS = sqlsrv_query($conn, $CONSULTA);
			//oci_execute($RS);
			if ($row = sqlsrv_fetch_array($RS)) {
						//ACTUALIZA REGISTROS PARA LA ID_ENHSEC
						$CONSULTA1="SELECT * FROM OP_NVLAUTO WHERE ID_ENHSEC=".$ID_ENHSEC." ORDER BY POS_NVL ASC";
						$RS1 = sqlsrv_query($conn, $CONSULTA1);
						//oci_execute($RS1);
						while ($row1 = sqlsrv_fetch_array($RS1)) {
								$ID_NVLAUTO=$row1['ID_NVLAUTO'];
								$VERIFICA=$_POST["NVA".$ID_NVLAUTO];
								if($VERIFICA=="Y"){ $VALOR_YN="Y";} else {$VALOR_YN="N";}
								
								$SQLUPD="UPDATE OP_MODNVA SET  VALUE='".$VALOR_YN."' WHERE ID_NVLAUTO=".$ID_NVLAUTO." AND ID_MODOPERA=".$ID_MODOPERA;
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
															$SQLPN1="UPDATE OP_MODNVA SET  VALUE='N' WHERE ID_NVLAUTO=".$ID_NVLAUTO1." AND ID_MODOPERA=".$ID_MODOPERA;
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
																			$SQLPN2="UPDATE OP_MODNVA SET  VALUE='N' WHERE ID_NVLAUTO=".$ID_NVLAUTO2." AND ID_MODOPERA=".$ID_MODOPERA;
																			$RSPN2 = sqlsrv_query($conn, $SQLPN2);
																			//oci_execute($RSPN2);
																	}
															}
													}
											}
								
								}
										
								//GRABAR NUMEROS DE GRUPO Y USUARIO
								if($ID_NVLAUTO==1000){
										$CONSULTA2="UPDATE OP_MODOPERA SET NVA_GRUPO=".$VERIFICA.", IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_MODOPERA=".$ID_MODOPERA;
										$RS2 = sqlsrv_query($conn, $CONSULTA2);
										//oci_execute($RS2);
								}
								if($ID_NVLAUTO==1001){
										$CONSULTA2="UPDATE OP_MODOPERA SET NVA_USUARIO=".$VERIFICA.", IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_MODOPERA=".$ID_MODOPERA;
										$RS2 = sqlsrv_query($conn, $CONSULTA2);
										//oci_execute($RS2);
								}
						}
			} else {
						//REGISTRA ID_ENHSEC POR DEFAULT
						for ($i = 1; $i <= 104; $i++) {
							$CONSULTA2="INSERT INTO OP_MODNVA (ID_MODOPERA, ID_NVLAUTO) VALUES (".$ID_MODOPERA.", ".$i.")";
							$RS2 = sqlsrv_query($conn, $CONSULTA2);
							//oci_execute($RS2);
						}
						
						$CONSULTA1="SELECT * FROM OP_NVLAUTO WHERE ID_ENHSEC=".$ID_ENHSEC." ORDER BY POS_NVL ASC";
						$RS1 = sqlsrv_query($conn, $CONSULTA1);
						//oci_execute($RS1);
						while ($row1 = sqlsrv_fetch_array($RS1)) {
								$ID_NVLAUTO=$row1['ID_NVLAUTO'];
								$VERIFICA=$_POST["NVA".$ID_NVLAUTO];
								if($VERIFICA=="Y"){ $VALOR_YN="Y";} else {$VALOR_YN="N";}
								$CONSULTA2="UPDATE OP_MODNVA SET VALUE='".$VALOR_YN."' WHERE ID_NVLAUTO=".$ID_NVLAUTO." AND ID_MODOPERA=".$ID_MODOPERA;
								$RS2 = sqlsrv_query($conn, $CONSULTA2);
								//oci_execute($RS2);
								//GRABAR NUMEROS DE GRUPO Y USUARIO
								if($ID_NVLAUTO==1000){
										$CONSULTA2="UPDATE OP_MODOPERA SET NVA_GRUPO=".$VERIFICA.", IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_MODOPERA=".$ID_MODOPERA;
										$RS2 = sqlsrv_query($conn, $CONSULTA2);
										//oci_execute($RS2);
								}
								if($ID_NVLAUTO==1001){
										$CONSULTA2="UPDATE OP_MODOPERA SET NVA_USUARIO=".$VERIFICA.", IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_MODOPERA=".$ID_MODOPERA;
										$RS2 = sqlsrv_query($conn, $CONSULTA2);
										//oci_execute($RS2);
								}
						}
			}
	} //$ID_ENHSEC<=14
	if ($ID_ENHSEC==15) {
			//VERIFICAR SI OPERADOR ESTA REGISTRADO PREVIAMENTE PARA IDNHSEC=15 USUARIO DEFINIDO
			$CONSULTA="SELECT * FROM OP_MODUDF WHERE ID_MODOPERA=".$ID_MODOPERA;
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
								$CONSULTA2="UPDATE OP_MODUDF SET  VALUE='".$VALOR_YN."' WHERE ID_NVLAUTO=".$ID_NVLAUTO." AND ID_MODOPERA=".$ID_MODOPERA;
								$RS2 = sqlsrv_query($conn, $CONSULTA2);
								//oci_execute($RS2);
						}
			} else {
						//REGISTRA ID_ENHSEC POR DEFAULT
						for ($i = 1002; $i <= 1009; $i++) {
							$CONSULTA2="INSERT INTO OP_MODUDF (ID_MODOPERA, ID_NVLAUTO) VALUES (".$ID_MODOPERA.", ".$i.")";
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
								$CONSULTA2="UPDATE OP_MODUDF SET VALUE='".$VALOR_YN."' WHERE ID_NVLAUTO=".$ID_NVLAUTO." AND ID_MODOPERA=".$ID_MODOPERA;
								$RS2 = sqlsrv_query($conn, $CONSULTA2);
								//oci_execute($RS2);
						}
			}
	} //ID_ENHSEC=15

	header("Location: mant_modelos.php?ACT=".$ID_MODOPERA."&MSJE=5&ACT_NVA=1");
}// FIN REGNVA









$REGIND=$_POST["REGIND"];

if ($REGIND<>"" ) {
	$ID_MODOPERA=$_POST["ID_MODOPERA"];
	$ID_INDICAT=$_POST["ID_INDICAT"];
	//VERIFICAR SI MODELO ESTA REGISTRADO PREVIAMENTE
	$CONSULTA="SELECT * FROM OP_MODMDA WHERE ID_MODOPERA=".$ID_MODOPERA;
	$RS = sqlsrv_query($conn, $CONSULTA);
	//oci_execute($RS);
	if ($row = sqlsrv_fetch_array($RS)) {
				//ACTUALIZA REGISTROS PARA LA ID_INDICAT
				$CONSULTA1="SELECT * FROM OP_INDICATOPC WHERE ID_INDICAT=".$ID_INDICAT." ORDER BY POSICION ASC";
				$RS1 = sqlsrv_query($conn, $CONSULTA1);
				//oci_execute($RS1);
				while ($row = sqlsrv_fetch_array($RS1)) {
						$ID_INDICATOPC=$row['ID_INDICATOPC'];
						$VERIFICA=$_POST["IND".$ID_INDICATOPC];
						if($VERIFICA==1){
								$CONSULTA2="UPDATE OP_MODMDA SET  VALUE=1 WHERE ID_INDICATOPC=".$ID_INDICATOPC." AND ID_MODOPERA=".$ID_MODOPERA;
								$RS2 = sqlsrv_query($conn, $CONSULTA2);
								//oci_execute($RS2);
						} else {
								$CONSULTA2="UPDATE OP_MODMDA SET  VALUE=0 WHERE ID_INDICATOPC=".$ID_INDICATOPC." AND ID_MODOPERA=".$ID_MODOPERA;
								$RS2 = sqlsrv_query($conn, $CONSULTA2);
								//oci_execute($RS2);
						}
				}
	} else {
				//REGISTRA ID_INDICAT POR DEFAULT
				
				$CONSULTA1="SELECT * FROM OP_INDICATOPC ORDER BY POSICION ASC";
				$RS1 = sqlsrv_query($conn, $CONSULTA1);
				//oci_execute($RS1);
				while ($row = sqlsrv_fetch_array($RS1)) {
					$ID_INDICATREG=$row['ID_INDICAT'];
					$ID_INDICATOPC=$row['ID_INDICATOPC'];
					$CONSULTA2="INSERT INTO OP_MODMDA (ID_MODOPERA, ID_INDICAT, ID_INDICATOPC) VALUES (".$ID_MODOPERA.", ".$ID_INDICATREG.",  ".$ID_INDICATOPC.")";
					$RS2 = sqlsrv_query($conn, $CONSULTA2);
					//oci_execute($RS2);
				}
				
				$CONSULTA1="SELECT * FROM OP_INDICATOPC WHERE ID_INDICAT=".$ID_INDICAT." ORDER BY POSICION ASC";
				$RS1 = sqlsrv_query($conn, $CONSULTA1);
				//oci_execute($RS1);
				while ($row = sqlsrv_fetch_array($RS1)) {
						$ID_INDICATOPC=$row['ID_INDICATOPC'];
						$VERIFICA=$_POST["IND".$ID_INDICATOPC];
						if($VERIFICA==1){
								$CONSULTA2="UPDATE OP_MODMDA SET  VALUE=1 WHERE ID_INDICATOPC=".$ID_INDICATOPC." AND ID_MODOPERA=".$ID_MODOPERA;
								$RS2 = sqlsrv_query($conn, $CONSULTA2);
								//oci_execute($RS2);
						}
				}
	}
	header("Location: mant_modelos.php?ACT=".$ID_MODOPERA."&MSJE=4");
}
?>