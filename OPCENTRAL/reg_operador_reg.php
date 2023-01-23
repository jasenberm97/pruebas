<?php include("session.inc");?>
<?php
//REV.20170316

//FUNCIONES DE REGISTRO Y BLOQUEO DE USUARIOS: NOMBRE, CUENTA, CLAVE, CC_OPERADOR
function REG_USUARIO ($Tienda, $UsuSuite, $NombUsu, $CcOpera, $UsuReg, $Conecta, $IdModOpera, $CodSap) {
		$NombUsu=trim($NombUsu);
		$CcOpera=intval($CcOpera);
		$SQLF="INSERT INTO US_USUARIOS (NOMBRE, CUENTA, CLAVE, CC_OPERADOR, IDREG, FL_PASS, COD_SAP) ";
		$SQLF=$SQLF." VALUES ('".$NombUsu."', '".$CcOpera."', '".GeneraPass()."', '".$CcOpera."', ".$UsuReg.", 1, '".$CodSap."') ";
		$RSF = sqlsrv_query($Conecta, $SQLF);
		$SQLF="SELECT IDENT_CURRENT ('US_USUARIOS') AS MIDUSU";
		$RSF = sqlsrv_query($Conecta, $SQLF);
		if ($rowF = sqlsrv_fetch_array($RSF)) {
				$IDUSU=$rowF['MIDUSU'];
		}
		$SQLF="INSERT INTO US_USUTND (IDUSU, COD_TIENDA, IDREG) ";
		$SQLF=$SQLF." VALUES (".$IDUSU.", ".$Tienda.", ".$UsuReg.")";
		$RSF = sqlsrv_query($Conecta, $SQLF);
		if($UsuSuite==1){ 
				if($IdModOpera==10){$ROLACE=" AND IDPERFIL>=900 AND IDPERFIL<1000 ";} //GERENTE
				if($IdModOpera==20){$ROLACE=" AND IDPERFIL>=800 AND IDPERFIL<900 ";} //SUPERVISOR
				if($IdModOpera==11){$ROLACE=" AND IDPERFIL>=700 AND IDPERFIL<800 ";} //SECRETARIA
				if($IdModOpera==12){$ROLACE=" AND IDPERFIL>=600 AND IDPERFIL<700 ";} //BODEGA
				//$SQLF="SELECT * FROM US_PERFIL WHERE FL_PRED=1 AND FL_SET=1".$ROLACE." ORDER BY IDPERFIL ASC";
				$SQLF="SELECT * FROM US_PERFIL WHERE FL_PRED=1 ".$ROLACE." ORDER BY IDPERFIL ASC";
				$RSF = sqlsrv_query($Conecta, $SQLF);
				while ($rowF = sqlsrv_fetch_array($RSF)) {
						$IDPERFIL=$rowF['IDPERFIL'];
						$IDSISTEMA=$rowF['IDSISTEMA'];
						$SQLF2="INSERT INTO US_USUPERF (IDUSU, IDPERFIL, IDSISTEMA) ";
						$SQLF2=$SQLF2." VALUES (".$IDUSU.", ".$IDPERFIL.", ".$IDSISTEMA.")";
						$RSF2 = sqlsrv_query($Conecta, $SQLF2);
				}
		}
} //FIN FUNCION REGISTRO DE USUARIO
function ACT_USUARIO ($Tienda, $UsuSuite, $NombUsu, $CcOpera, $CcOperaOld, $Estado, $UsuReg, $Conecta, $Bloquea, $IdModOpera, $CodSap) {
				//VERIFICAR: SE ENCUENTRA PREVIAMENTE REGISTRADO
				$NombUsu=trim($NombUsu);
				$CcOpera=intval($CcOpera);
				$CcOperaOld=intval($CcOperaOld);
						$SQLOP2="SELECT * FROM MN_TIENDA WHERE DES_CLAVE=".$Tienda;
						$RSOP2 = sqlsrv_query($Conecta, $SQLOP2);
						if ($rowOP2 = sqlsrv_fetch_array($RSOP2)) {
								$COD_TIENDA=$rowOP2['COD_TIENDA'];
						}
				$SQLFU="SELECT * FROM US_USUARIOS WHERE CC_OPERADOR=".$CcOperaOld;
				$RSFU = sqlsrv_query($Conecta, $SQLFU);
				if ($rowFU = sqlsrv_fetch_array($RSFU)) {
						$IDUSU=$rowFU['IDUSU'];
						//ACTUALIZA USUARIO
						if($Bloquea==1 and $Estado==0){
								$SQLF="UPDATE US_USUARIOS SET CC_OPERADOR=".$CcOpera.", ESTADO=".$Estado.", NOMBRE='".$NombUsu."', CUENTA='".$CcOpera."', CLAVE='".GeneraPass()."', FL_PASS=0, IDREG=".$UsuReg.", FECHA=convert(datetime,GETDATE(), 121) WHERE IDUSU=".$IDUSU;
						} else {
								$SQLF="UPDATE US_USUARIOS SET CC_OPERADOR=".$CcOpera.", ESTADO=".$Estado.", NOMBRE='".$NombUsu."', CUENTA='".$CcOpera."', CLAVE='".GeneraPass()."', FL_PASS=1, IDREG=".$UsuReg.", FECHA=convert(datetime,GETDATE(), 121) WHERE IDUSU=".$IDUSU;
						}
						$RSF = sqlsrv_query($Conecta, $SQLF);
						//ACTUALIZAR TIENDA ASOCIADA
						$SQLF="DELETE FROM US_USUTND WHERE IDUSU=".$IDUSU;
						$RSF = sqlsrv_query($Conecta, $SQLF);
						$SQLF="INSERT INTO US_USUTND (IDUSU, COD_TIENDA, IDREG) ";
						$SQLF=$SQLF." VALUES (".$IDUSU.", ".$COD_TIENDA.", ".$UsuReg.")";
						$RSF = sqlsrv_query($Conecta, $SQLF);
						if($UsuSuite==0){ 
								$SQLF2="DELETE FROM US_USUPERF  WHERE IDUSU=".$IDUSU;
								$RSF2 = sqlsrv_query($Conecta, $SQLF2);
								$SQLF2="UPDATE US_USUARIOS SET ESTADO=0, FL_PASS=0, CLAVE='".GeneraPass()."' WHERE IDUSU=".$IDUSU;
								$RSF2 = sqlsrv_query($Conecta, $SQLF2);
						} else {
								if($IdModOpera==10){$ROLACE=" AND IDPERFIL>=900 AND IDPERFIL<1000 ";} //GERENTE
								if($IdModOpera==20){$ROLACE=" AND IDPERFIL>=800 AND IDPERFIL<900 ";} //SUPERVISOR
								if($IdModOpera==11){$ROLACE=" AND IDPERFIL>=700 AND IDPERFIL<800 ";} //SECRETARIA
								if($IdModOpera==12){$ROLACE=" AND IDPERFIL>=600 AND IDPERFIL<700 ";} //BODEGA
								$REGISTRAUSUPERF=1;
								$SQLF="SELECT * FROM US_USUPERF WHERE IDUSU=".$IDUSU;
								$RSF = sqlsrv_query($Conecta, $SQLF);
								if ($rowF = sqlsrv_fetch_array($RSF)) {
										$REGISTRAUSUPERF=0;
										$SQLF="DELETE FROM US_USUPERF WHERE IDUSU=".$IDUSU;
										$RSF = sqlsrv_query($Conecta, $SQLF);
										if($Estado != 0){
												//$SQLF="SELECT * FROM US_PERFIL WHERE FL_PRED=1 AND FL_SET=1 ".$ROLACE." ORDER BY IDPERFIL ASC";
												$SQLF="SELECT * FROM US_PERFIL WHERE FL_PRED=1 ".$ROLACE." ORDER BY IDPERFIL ASC";
												$RSF = sqlsrv_query($Conecta, $SQLF);
												while ($rowF = sqlsrv_fetch_array($RSF)) {
														$IDPERFIL=$rowF['IDPERFIL'];
														$IDSISTEMA=$rowF['IDSISTEMA'];
														$SQLF2="INSERT INTO US_USUPERF (IDUSU, IDPERFIL, IDSISTEMA) VALUES (".$IDUSU.", ".$IDPERFIL.", ".$IDSISTEMA.")";
														$RSF2 = sqlsrv_query($Conecta, $SQLF2);
												}
										}
								}
								if($REGISTRAUSUPERF==1){
									$SQLF="DELETE FROM US_USUPERF WHERE IDUSU=".$IDUSU;
									$RSF = sqlsrv_query($Conecta, $SQLF);
									if($Estado != 0){
										//$SQLF="SELECT * FROM US_PERFIL WHERE FL_PRED=1 AND FL_SET=1 ".$ROLACE." ORDER BY IDPERFIL ASC";
										$SQLF="SELECT * FROM US_PERFIL WHERE FL_PRED=1 ".$ROLACE." ORDER BY IDPERFIL ASC";
										$RSF = sqlsrv_query($Conecta, $SQLF);
										while ($rowF = sqlsrv_fetch_array($RSF)) {
												$IDPERFIL=$rowF['IDPERFIL'];
												$IDSISTEMA=$rowF['IDSISTEMA'];
												$SQLF2="INSERT INTO US_USUPERF (IDUSU, IDPERFIL, IDSISTEMA) VALUES (".$IDUSU.", ".$IDPERFIL.", ".$IDSISTEMA.")";
												$RSF2 = sqlsrv_query($Conecta, $SQLF2);
										}
									}
								}
						}
				} else {
						//REGISTRAR USUARIO Y ASIGNAR PERFILES
						$SQLF="INSERT INTO US_USUARIOS (NOMBRE, CUENTA, CLAVE, CC_OPERADOR, IDREG, ESTADO, FL_PASS, COD_SAP) ";
						$SQLF=$SQLF." VALUES ('".$NombUsu."', '".$CcOpera."',  '".GeneraPass()."', ".$CcOpera.",  ".$UsuReg.", ".$Estado.", 1, '".$CodSap."') ";
						$RSF = sqlsrv_query($Conecta, $SQLF);
						$SQLF="SELECT IDENT_CURRENT ('US_USUARIOS') AS MIDUSU";
						$RSF = sqlsrv_query($Conecta, $SQLF);
						if ($rowF = sqlsrv_fetch_array($RSF)) {
								$IDUSU=$rowF['MIDUSU'];
						}
						//ASOCIA TIENDA EN SUITE CENTRAL
						$SQLF="INSERT INTO US_USUTND (IDUSU, COD_TIENDA, IDREG) ";
						$SQLF=$SQLF." VALUES (".$IDUSU.", ".$COD_TIENDA.", ".$UsuReg.")";
						$RSF = sqlsrv_query($Conecta, $SQLF);
						//ASOCIA PERFILES PREDETERMINADOS
						if($UsuSuite==1){ 
								if($IdModOpera==10){$ROLACE=" AND IDPERFIL>=900 AND IDPERFIL<1000 ";} //GERENTE
								if($IdModOpera==20){$ROLACE=" AND IDPERFIL>=800 AND IDPERFIL<900 ";} //SUPERVISOR
								if($IdModOpera==11){$ROLACE=" AND IDPERFIL>=700 AND IDPERFIL<800 ";} //SECRETARIA
								if($IdModOpera==12){$ROLACE=" AND IDPERFIL>=600 AND IDPERFIL<700 ";} //BODEGA
								$SQLF="DELETE FROM US_USUPERF WHERE IDUSU=".$IDUSU;
								$RSF = sqlsrv_query($Conecta, $SQLF);
								//$SQLF="SELECT * FROM US_PERFIL WHERE FL_PRED=1  AND FL_SET=1 ".$ROLACE." ORDER BY IDPERFIL ASC";
								$SQLF="SELECT * FROM US_PERFIL WHERE FL_PRED=1 AND ".$ROLACE." ORDER BY IDPERFIL ASC";
								$RSF = sqlsrv_query($Conecta, $SQLF);
								while ($rowF = sqlsrv_fetch_array($RSF)) {
										$IDPERFIL=$rowF['IDPERFIL'];
										$IDSISTEMA=$rowF['IDSISTEMA'];
										$SQLF2="INSERT INTO US_USUPERF (IDUSU, IDPERFIL, IDSISTEMA) VALUES (".$IDUSU.", ".$IDPERFIL.", ".$IDSISTEMA.")";
										$RSF2 = sqlsrv_query($Conecta, $SQLF2);
								}
						}
				} //FIN VERIFICAR QUE NO SE ENCUENTRE PREVIAMENTE REGISTRADO
}//FIN FUNCION ACTUALIZACION DE USUARIO


$INGRESAR=$_POST["INGRESAR"];
$NEO=$_POST["NEO"];

if ($INGRESAR<>"") {
	$COD_SAP=$_POST['COD_SAP'];
		if ($NEO==1) { //INGRESA NUEVO OPERADOR
				$CC_OPERADOR=$_POST["CC_OPERADOR"];
				$NOMBRE=strtoupper(SINCOMILLAS($_POST["NOMBRE"]));
				$APELLIDO_P=strtoupper(SINCOMILLAS($_POST["APELLIDO_P"]));
				$APELLIDO_M=strtoupper(SINCOMILLAS($_POST["APELLIDO_M"]));
				$DIA_NAC=$_POST["DIA_NAC"];
				$MES_NAC=$_POST["MES_NAC"];
				$ANO_NAC=$_POST["ANO_NAC"];
						$NOMB_ACE=strtoupper($_POST["NOMB_ACE"]);
						$NOMB_ACE=substr($NOMB_ACE, 0, 20);
						$NOMB_ACE=str_replace( "_", " ", $NOMB_ACE); 
				$DES_CLAVE=$_POST["COD_TIENDA"]; //TIENDA
				$ID_MODOPERA=$_POST["ID_MODOPERA"];
						$SQLMOD="SELECT US_SUITE FROM OP_MODOPERA WHERE ID_MODOPERA=".$ID_MODOPERA;
						$RSMOD = sqlsrv_query($conn, $SQLMOD);
						if ($rowMOD = sqlsrv_fetch_array($RSMOD)) {
								$US_SUITE=$rowMOD['US_SUITE'];
						}
				$INICIALES_OP=$_POST["INI_ACE"];
		
				$SQLOP="SELECT * FROM OP_OPERADOR WHERE CC_OPERADOR=".$CC_OPERADOR;
				$RS = sqlsrv_query($conn, $SQLOP);
				if ($row = sqlsrv_fetch_array($RS)) {
					header("Location: reg_operador.php?NEO=1&MSJE=2&CC=".$CC_OPERADOR."&C1=".$NOMBRE."&C2=".$APELLIDO_P."&C3=".$APELLIDO_M."&C4=".$DIA_NAC."&C5=".$MES_NAC."&C6=".$ANO_NAC."&C7=".$NOMB_ACE."&C8=".$COD_NEGOCIO."&C9=".$COD_TIENDA);
				} else {
							
					$FECHA_NAC=$ANO_NAC."/".$MES_NAC."/".$DIA_NAC;
					$SQLOP2="SELECT * FROM MN_TIENDA WHERE DES_CLAVE=".$DES_CLAVE;
					$RS2 = sqlsrv_query($maestra, $SQLOP2);
					if ($row2 = sqlsrv_fetch_array($RS2)) {
							$COD_TIENDA=$row2['COD_TIENDA'];
							$FL_LCL_SRVR=$row2['FL_LCL_SRVR']; //LOCAL/CENTRAL
							$IP_TIENDA=$row2['IP'];
					}
					
					$SQLOP2="INSERT INTO OP_OPERADOR (CC_OPERADOR, NOMBRE, APELLIDO_P, APELLIDO_M, FECHA_NAC, NOMB_ACE,COD_TIENDA, IDREG,INICIALES_OP) ";
					$SQLOP2=$SQLOP2." VALUES (".$CC_OPERADOR.", '".$NOMBRE."', '".$APELLIDO_P."', '".$APELLIDO_M."', convert(datetime,'".$FECHA_NAC."', 111), '".$NOMB_ACE."', ".$DES_CLAVE.", ".$SESIDUSU.",'".$INICIALES_OP."')";
					$RS2 = sqlsrv_query($conn, $SQLOP2);
					
					$SQLOP2="SELECT IDENT_CURRENT ('OP_OPERADOR') AS MIDOPERA";
					$RS2 = sqlsrv_query($conn, $SQLOP2);
					if ($row2 = sqlsrv_fetch_array($RS2)) {
							$ID_OPERADOR=$row2['MIDOPERA'];
					}

					//CENTRAL - LOCAL
					if($FL_LCL_SRVR==0){ 
						$SQLOP="UPDATE OP_OPERADOR SET IP_TIENDA='".$IP_TIENDA."' WHERE ID_OPERADOR=".$ID_OPERADOR;
						$RSOP = sqlsrv_query($conn, $SQLOP);
					}
					if($FL_LCL_SRVR==1){
									//BUSCAR IP DEL CONTROLADOR EN LOCAL
									$serverLocal = $IP_TIENDA;
									$connectionSADMINLocal = array( "Database"=>$M_BDNM, "UID"=>$M_BDUSER, "PWD"=>$M_BDPASSWORD);
									$maestra_Local = sqlsrv_connect( $serverLocal, $connectionSADMINLocal);
									if( $maestra_Local ) {
										$SQLL="SELECT * FROM MN_TIENDA WHERE DES_CLAVE=".$DES_CLAVE;
										$RSL = sqlsrv_query($maestra_Local, $SQLL);
										if ($rowLoc = sqlsrv_fetch_array($RSL)) {
											$IP_CONTROLADOR = $rowLoc['IP'];
										}
									}
									sqlsrv_close($maestra_Local);
						$SQLOP="UPDATE OP_OPERADOR SET IP_TIENDA='".$IP_CONTROLADOR."' WHERE  ID_OPERADOR=".$ID_OPERADOR;
						$RSOP = sqlsrv_query($conn, $SQLOP);
					}
							
					$SQLOP2="INSERT INTO OP_OPERAMOV (STR_ESTADO, REG_ESTADO, CC_OPERADOR, COD_TIENDA, IDREG, HORA, IP_CLIENTE) ";
					$SQLOP2=$SQLOP2." VALUES (0, 0, ".$CC_OPERADOR.", ".$DES_CLAVE.", ".$SESIDUSU.", '".$TIMESRV."', '".$IP_CLIENTE."')";
					$RS2 = sqlsrv_query($conn, $SQLOP2);

					$NOMBUSU=$NOMBRE." ".$APELLIDO_P." ".$APELLIDO_M;

					//REGISTRA USUARIO EN SUITE CENTRAL
					REG_USUARIO ($COD_TIENDA, $US_SUITE, $NOMBUSU, $CC_OPERADOR, $SESIDUSU, $maestra, $ID_MODOPERA, $COD_SAP);
					
					//REGISTRA PERFILES EN SERVER LOCAL
					//if($FL_LCL_SRVR==1 and $US_SUITE==1){ //TIENDA CON SERVER LOCAL Y PERFIL DE USUARIO SUITE
					if($FL_LCL_SRVR==1){ //TIENDA CON SERVER LOCAL Y PERFIL DE USUARIO SUITE						
									//CONEXION TIENDA
									$connectionSADMINLocal = array( "Database"=>$M_BDNM, "UID"=>$M_BDUSER, "PWD"=>$M_BDPASSWORD);
									$maestra_Local = sqlsrv_connect( $IP_TIENDA, $connectionSADMINLocal);
									REG_USUARIO ($COD_TIENDA, $US_SUITE, $NOMBUSU, $CC_OPERADOR, $SESIDUSU, $maestra_Local, $ID_MODOPERA, $COD_SAP);
					}

					// NIVELES DE AUTORIZACIÓN
					$SQL="SELECT * FROM OP_MODOPERA WHERE ID_MODOPERA=".$ID_MODOPERA;
					$RS1 = sqlsrv_query($conn, $SQL);
					while ($row1 = sqlsrv_fetch_array($RS1)) {
						$NVA_GRUPO = $row1['NVA_GRUPO'];
						$NVA_USUARIO = $row1['NVA_USUARIO'];
						$NIVEL_AUT = $row1['NIVEL_AUT'];
						$SQL2="UPDATE OP_OPERADOR SET NVA_GRUPO=".$NVA_GRUPO.", NVA_USUARIO=".$NVA_USUARIO.", NIVEL_AUT=".$NIVEL_AUT.", ID_MODOPERA=".$ID_MODOPERA." WHERE ID_OPERADOR=".$ID_OPERADOR;
						$RS2 = sqlsrv_query($conn, $SQL2);
					}
					$SQL="SELECT * FROM OP_MODNVA WHERE ID_MODOPERA=".$ID_MODOPERA." ORDER BY ID_NVLAUTO ASC";
					$RS1 = sqlsrv_query($conn, $SQL);
					while ($row1 = sqlsrv_fetch_array($RS1)) {
						$ID_NVLAUTO = $row1['ID_NVLAUTO'];
						$VALUENVA = $row1['VALUE'];
						$SQL2="INSERT INTO OP_OPERANVA (ID_OPERADOR, ID_NVLAUTO, VALUE, IDREG) ";
						$SQL2=$SQL2." VALUES (".$ID_OPERADOR.", ".$ID_NVLAUTO.", '".$VALUENVA."', ".$SESIDUSU.")";
						$RS2 = sqlsrv_query($conn, $SQL2);
					}
					$SQL="SELECT * FROM OP_MODUDF WHERE ID_MODOPERA=".$ID_MODOPERA." ORDER BY ID_NVLAUTO ASC";
					$RS1 = sqlsrv_query($conn, $SQL);
					while ($row1 = sqlsrv_fetch_array($RS1)) {
						$ID_NVLAUTO = $row1['ID_NVLAUTO'];
						$VALUEUDF = $row1['VALUE'];
						$SQL2="INSERT INTO OP_OPERAUDF (ID_OPERADOR, ID_NVLAUTO, VALUE, IDREG) ";
						$SQL2=$SQL2." VALUES (".$ID_OPERADOR.", ".$ID_NVLAUTO.", '".$VALUEUDF."', ".$SESIDUSU.")";
						$RS2 = sqlsrv_query($conn, $SQL2);
					}
					// EL REGISTRO DE AUTORIZACIÓN
					$SQL="SELECT * FROM OP_MODMDA WHERE ID_MODOPERA=".$ID_MODOPERA." ORDER BY ID_INDICAT ASC, ID_INDICATOPC ASC";
					$RS1 = sqlsrv_query($conn, $SQL);
					while ($row1 = sqlsrv_fetch_array($RS1)) {
						$ID_INDICAT = $row1['ID_INDICAT'];
						$ID_INDICATOPC = $row1['ID_INDICATOPC'];
						$VALUEMDA = $row1['VALUE'];
						$SQL2="INSERT INTO OP_OPERAMDA (ID_OPERADOR, ID_INDICAT, ID_INDICATOPC, VALUE, IDREG) ";
						$SQL2=$SQL2." VALUES (".$ID_OPERADOR.", ".$ID_INDICAT.", ".$ID_INDICATOPC.", '".$VALUEMDA."', ".$SESIDUSU.")";
						$RS2 = sqlsrv_query($conn, $SQL2);
					}

		//REGISTRO DE ALTA

			$SQLOG="INSERT INTO LG_EVENTO ( COD_TIPO_EVENTO, FECHA, HORA, IP_CLIENTE, COD_USUARIO, IDACC, IDSISTEMA, IDPERFIL) VALUES ";
			$SQLOG=$SQLOG."( 1, convert(datetime,GETDATE(), 121), '".$TIMESRV."', '".$IP_CLIENTE."', ".$SESIDUSU.", 1127, ".$SESIDSISTEMA.", ".$SESIDPERFIL.")";
			$RSL = sqlsrv_query($maestra, $SQLOG);

			header("Location: reg_operador.php?ACT=".$ID_OPERADOR."&MSJE=3");
		}


		sqlsrv_close($conn);
		sqlsrv_close($maestra);
		sqlsrv_close($maestra_Local);
		}//FIN NEO=1
		//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		if ($NEO==2) { //ACTUALIZA DATA OPERADOR
				$ID_OPERADOR=$_POST["ID_OPERADOR"];
				$CC_OPERADOR=$_POST["CC_OPERADOR"];
				$NOMBRE=strtoupper(SINCOMILLAS($_POST["NOMBRE"]));
				$APELLIDO_P=strtoupper(SINCOMILLAS($_POST["APELLIDO_P"]));
				$APELLIDO_M=strtoupper(SINCOMILLAS($_POST["APELLIDO_M"]));
				$DIA_NAC=$_POST["DIA_NAC"];
				$MES_NAC=$_POST["MES_NAC"];
				$ANO_NAC=$_POST["ANO_NAC"];
						$NOMB_ACE=strtoupper($_POST["NOMB_ACE"]);
						$NOMB_ACE=substr($NOMB_ACE, 0, 20);
						$NOMB_ACE=str_replace( "_", " ", $NOMB_ACE); 
				$INICIALES_OP=$_POST["INI_ACE"];
				$DES_CLAVE=$_POST["COD_TIENDA"];
						$SQLOP2="SELECT * FROM MN_TIENDA WHERE DES_CLAVE=".$DES_CLAVE;
						$RS2 = sqlsrv_query($maestra, $SQLOP2);
						if ($row2 = sqlsrv_fetch_array($RS2)) {
								$FL_LCL_SRVR=$row2['FL_LCL_SRVR']; //LOCAL/CENTRAL
								$IP_TIENDA=$row2['IP'];
								$IP_TIENDA_SUITE=$IP_TIENDA;
								$IP_CONTROLADOR=$IP_TIENDA;
						}
						if($FL_LCL_SRVR==1){
								//BUSCAR IP DEL CONTROLADOR EN LOCAL
								$serverLocal = $IP_TIENDA_SUITE;
								$connectionSADMINLocal = array( "Database"=>$M_BDNM, "UID"=>$M_BDUSER, "PWD"=>$M_BDPASSWORD);
								$maestra_Local = sqlsrv_connect( $serverLocal, $connectionSADMINLocal);
								if( $maestra_Local ) {
									$SQLL="SELECT * FROM MN_TIENDA WHERE DES_CLAVE=".$DES_CLAVE;
									$RSL = sqlsrv_query($maestra_Local, $SQLL);
									if ($rowLoc = sqlsrv_fetch_array($RSL)) {
										$IP_CONTROLADOR = $rowLoc['IP'];
									}
								}
								sqlsrv_close($maestra_Local);
						}			
				$REG_ESTADO=$_POST["REG_ESTADO"];
				if(empty($REG_ESTADO)){ $REG_ESTADO=0;}			
			
				$VA_MODOPERA=$_POST["VA_MODOPERA"];
				$ID_MODOPERA=$_POST["ID_MODOPERA"];
				if(empty($ID_MODOPERA)){
						$SQLOP2="SELECT * FROM OP_OPERADOR WHERE ID_OPERADOR=".$ID_OPERADOR;
						$RS2 = sqlsrv_query($conn, $SQLOP2);
						if ($row2 = sqlsrv_fetch_array($RS2)) {
								$ID_MODOPERA=$row2['ID_MODOPERA'];
						}
				}
				$SQLOP2="SELECT * FROM OP_MODOPERA WHERE ID_MODOPERA=".$ID_MODOPERA;
				$RS2 = sqlsrv_query($conn, $SQLOP2);
				if ($row2 = sqlsrv_fetch_array($RS2)) {
						$US_SUITE=$row2['US_SUITE'];
				}
		
				$SQLOP="SELECT * FROM OP_OPERADOR WHERE CC_OPERADOR=".$CC_OPERADOR." AND ID_OPERADOR<>".$ID_OPERADOR;
				$RS = sqlsrv_query($conn, $SQLOP);
				if ($row = sqlsrv_fetch_array($RS)) {
							header("Location: reg_operador.php?NEO=0&MSJE=2&ACT=".$ID_OPERADOR);
				} else {
							$FECHA_NAC=$ANO_NAC."/".$MES_NAC."/".$DIA_NAC;
							//VALIDAR PROCESAMIENTO DE OPERADOR --- STR_ESTADO=1
							$SQLOP2="SELECT * FROM OP_OPERADOR WHERE ID_OPERADOR=".$ID_OPERADOR;
							$RS2 = sqlsrv_query($conn, $SQLOP2);
							if ($row2 = sqlsrv_fetch_array($RS2)) {
									$CC_OPERADOR_OLD=$row2['CC_OPERADOR'];
									$ID_MODOPERA_OLD=$row2['ID_MODOPERA'];
									$STR_ESTADO=$row2['STR_ESTADO'];
									$REG_ESTADO_REG=$row2['REG_ESTADO'];
									if(empty($STR_ESTADO)){ $STR_ESTADO=0; }
							}
							$SQLOP2="SELECT * FROM OP_MODOPERA WHERE ID_MODOPERA=".$ID_MODOPERA_OLD;
							$RS2 = sqlsrv_query($conn, $SQLOP2);
							if ($row2 = sqlsrv_fetch_array($RS2)) {
									$US_SUITE_OLD=$row2['US_SUITE'];
							}
							if($REG_ESTADO==$REG_ESTADO_REG){
									$STR_ESTADO=4; //ACTUALIZO SÓLO DATA OPERADOR
							}
							//ACTUALIZA OPERADOR
							$SQLOP2="UPDATE OP_OPERADOR SET CC_OPERADOR=".$CC_OPERADOR.", NOMBRE='".$NOMBRE."', APELLIDO_P='".$APELLIDO_P."', APELLIDO_M='".$APELLIDO_M."', FECHA_NAC=convert(datetime,'".$FECHA_NAC."', 111), NOMB_ACE='".$NOMB_ACE."' , STR_ESTADO=".$STR_ESTADO.", REG_ESTADO=".$REG_ESTADO.", IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121),INICIALES_OP='".$INICIALES_OP."' WHERE ID_OPERADOR=".$ID_OPERADOR;
							$RS2 = sqlsrv_query($conn, $SQLOP2);
							
							//ACTUALIZA STR_ESTADO SI REG_ESTADO ES DIFERENTE
							if($REG_ESTADO <> $REG_ESTADO_REG){
									$SQLOP2="UPDATE OP_OPERADOR SET STR_ESTADO=1 WHERE ID_OPERADOR=".$ID_OPERADOR;
									$RS2 = sqlsrv_query($conn, $SQLOP2);
							}
							//VERIFICAR CAMBIO DE LOCAL
							$SQLOP2="SELECT * FROM OP_OPERADOR WHERE ID_OPERADOR=".$ID_OPERADOR;
							$RS2 = sqlsrv_query($conn, $SQLOP2);
							if ($row2 = sqlsrv_fetch_array($RS2)) {
                                    $COD_TIENDA_ACT = $row2['COD_TIENDA']; //TIENDA ACTUAL
									//TIENDA ACTUAL
									$SQL="SELECT * FROM MN_TIENDA WHERE DES_CLAVE=".$COD_TIENDA_ACT;
									$RS3 = sqlsrv_query($maestra, $SQL);
									if ($row3 = sqlsrv_fetch_array($RS3)) {
											$FL_LCL_SRVR_ACT=$row3['FL_LCL_SRVR']; //LOCAL/CENTRAL
											$IP_TIENDA_ACT=$row3['IP'];
											$IP_CONTROLADOR_ACT=$IP_TIENDA_ACT;
											if($FL_LCL_SRVR_ACT==1){
												//BUSCAR IP DEL CONTROLADOR EN LOCAL
												$serverLocal = $IP_TIENDA_ACT;
												$connectionSADMINLocal = array( "Database"=>$M_BDNM, "UID"=>$M_BDUSER, "PWD"=>$M_BDPASSWORD);
												$maestra_Local = sqlsrv_connect( $serverLocal, $connectionSADMINLocal);
												if( $maestra_Local ) {
													$SQLL="SELECT * FROM MN_TIENDA WHERE DES_CLAVE=".$COD_TIENDA_ACT;
													$RSL = sqlsrv_query($maestra_Local, $SQLL);
													if ($rowLoc = sqlsrv_fetch_array($RSL)) {
														$IP_CONTROLADOR_ACT = $rowLoc['IP'];
													}
												}
												sqlsrv_close($maestra_Local);
											}
									}
							}
							$SQLOP2="UPDATE OP_OPERADOR SET STR_ESTADO=1, COD_TIENDA=".$DES_CLAVE.", IP_TIENDA='".$IP_CONTROLADOR."', COD_TIENDA_ANT=".$COD_TIENDA_ACT." , IP_TIENDA_ANT='".$IP_CONTROLADOR_ACT."' , INICIALES_OP='".$INICIALES_OP."' WHERE ID_OPERADOR=".$ID_OPERADOR;
							$RS2 = sqlsrv_query($conn, $SQLOP2);

					//ACTUALIZA USUARIO EN CENTRAL/LOCAL
							$NOMBUSU=$NOMBRE." ".$APELLIDO_P." ".$APELLIDO_M;
							$MSJE_CLAVE=0;
							//CUANDO NO HAY CAMBIOS CENTRAL/LOCAL - SOLO VERIFICA US_SUITE
							if($DES_CLAVE != $COD_TIENDA_ACT){
									//SETEA NUEVA TIENDA
									if($FL_LCL_SRVR==1){
										$connectionSADMINLocal = array( "Database"=>$M_BDNM, "UID"=>$M_BDUSER, "PWD"=>$M_BDPASSWORD);
										$maestra_Local = sqlsrv_connect( $IP_TIENDA_SUITE, $connectionSADMINLocal);
										ACT_USUARIO ($DES_CLAVE, $US_SUITE, $NOMBUSU, $CC_OPERADOR, $CC_OPERADOR_OLD, $REG_ESTADO, $SESIDUSU, $maestra_Local, 0, $ID_MODOPERA, $COD_SAP);
									} else {
										ACT_USUARIO ($DES_CLAVE, $US_SUITE, $NOMBUSU, $CC_OPERADOR, $CC_OPERADOR_OLD, $REG_ESTADO, $SESIDUSU, $maestra, 0, $ID_MODOPERA, $COD_SAP);
									}
									//BLOQUEA EN TIENDA ACTUAL
									if($FL_LCL_SRVR_ACT==1){
										$connectionSADMINLocal = array( "Database"=>$M_BDNM, "UID"=>$M_BDUSER, "PWD"=>$M_BDPASSWORD);
										$maestra_Local = sqlsrv_connect( $IP_TIENDA_ACT, $connectionSADMINLocal);
										ACT_USUARIO ($COD_TIENDA_ACT, $US_SUITE, $NOMBUSU, $CC_OPERADOR, $CC_OPERADOR_OLD, 0, $SESIDUSU, $maestra_Local, 1, $ID_MODOPERA, $COD_SAP);
									} else {
										ACT_USUARIO ($COD_TIENDA_ACT, $US_SUITE, $NOMBUSU, $CC_OPERADOR, $CC_OPERADOR_OLD, 0, $SESIDUSU, $maestra, 1, $ID_MODOPERA, $COD_SAP);
									}
									$MSJE_CLAVE=1;
							} else { //SE QUEDA EN MISMA TIENDA
									if($FL_LCL_SRVR==1){ 
										$connectionSADMINLocal = array( "Database"=>$M_BDNM, "UID"=>$M_BDUSER, "PWD"=>$M_BDPASSWORD);
										$maestra_Local = sqlsrv_connect( $IP_TIENDA_SUITE, $connectionSADMINLocal);
										if($ID_MODOPERA != $ID_MODOPERA_OLD or $REG_ESTADO != $REG_ESTADO_REG){ //CAMBIA DE MODELO
											if($US_SUITE == 0 or $REG_ESTADO==0){
												ACT_USUARIO ($DES_CLAVE, $US_SUITE, $NOMBUSU, $CC_OPERADOR, $CC_OPERADOR_OLD, 0, $SESIDUSU, $maestra_Local, 1, $ID_MODOPERA, $COD_SAP);
												$MSJE_CLAVE=2;
											} else {
												ACT_USUARIO ($DES_CLAVE, $US_SUITE, $NOMBUSU, $CC_OPERADOR, $CC_OPERADOR_OLD, $REG_ESTADO, $SESIDUSU, $maestra_Local, 0, $ID_MODOPERA, $COD_SAP);
												$MSJE_CLAVE=1;
											}
										} else { //RECICLA PERFILES
												ACT_USUARIO ($DES_CLAVE, $US_SUITE, $NOMBUSU, $CC_OPERADOR, $CC_OPERADOR_OLD, $REG_ESTADO, $SESIDUSU, $maestra_Local, 0, $ID_MODOPERA, $COD_SAP);
												//$MSJE_CLAVE=1;
										}
									} else {
										if($ID_MODOPERA != $ID_MODOPERA_OLD or $REG_ESTADO != $REG_ESTADO_REG){
											if($US_SUITE == 0 or $REG_ESTADO==0){
												ACT_USUARIO ($DES_CLAVE, $US_SUITE, $NOMBUSU, $CC_OPERADOR, $CC_OPERADOR_OLD, 0, $SESIDUSU, $maestra, 1, $ID_MODOPERA, $COD_SAP);
												$MSJE_CLAVE=2;
											} else {
												ACT_USUARIO ($DES_CLAVE, $US_SUITE, $NOMBUSU, $CC_OPERADOR, $CC_OPERADOR_OLD, $REG_ESTADO, $SESIDUSU, $maestra, 0, $ID_MODOPERA, $COD_SAP);
												$MSJE_CLAVE=1;
											}
										}
									}
							}
					
							if($VA_MODOPERA==1){
								if($ID_MODOPERA!=0){
									//REASIGNAR MODELOS DE AUTORIZACION
											//PRIMERO NIVELES DE AUTORIZACIÓN
											$SQL="SELECT * FROM OP_MODOPERA WHERE ID_MODOPERA=".$ID_MODOPERA;
											$RS1 = sqlsrv_query($conn, $SQL);
											while ($row1 = sqlsrv_fetch_array($RS1)) {
												$NVA_GRUPO = $row1['NVA_GRUPO'];
												$NVA_USUARIO = $row1['NVA_USUARIO'];
												$NIVEL_AUT = $row1['NIVEL_AUT'];
												$SQL2="UPDATE OP_OPERADOR SET STR_ESTADO=1, NVA_GRUPO=".$NVA_GRUPO.", NVA_USUARIO=".$NVA_USUARIO.", NIVEL_AUT=".$NIVEL_AUT.", ID_MODOPERA=".$ID_MODOPERA." WHERE ID_OPERADOR=".$ID_OPERADOR;
												$RS2 = sqlsrv_query($conn, $SQL2);
											}
											$SQL2="DELETE FROM OP_OPERANVA WHERE ID_OPERADOR=".$ID_OPERADOR;
											$RS2 = sqlsrv_query($conn, $SQL2);
											$SQL="SELECT * FROM OP_MODNVA WHERE ID_MODOPERA=".$ID_MODOPERA." ORDER BY ID_NVLAUTO ASC";
											$RS1 = sqlsrv_query($conn, $SQL);
											while ($row1 = sqlsrv_fetch_array($RS1)) {
												$ID_NVLAUTO = $row1['ID_NVLAUTO'];
												$VALUENVA = $row1['VALUE'];
												$SQL2="INSERT INTO OP_OPERANVA (ID_OPERADOR, ID_NVLAUTO, VALUE, IDREG) ";
												$SQL2=$SQL2." VALUES (".$ID_OPERADOR.", ".$ID_NVLAUTO.", '".$VALUENVA."', ".$SESIDUSU.")";
												$RS2 = sqlsrv_query($conn, $SQL2);
											}
											$SQL2="DELETE FROM OP_OPERAUDF WHERE ID_OPERADOR=".$ID_OPERADOR;
											$RS2 = sqlsrv_query($conn, $SQL2);
											$SQL="SELECT * FROM OP_MODUDF WHERE ID_MODOPERA=".$ID_MODOPERA." ORDER BY ID_NVLAUTO ASC";
											$RS1 = sqlsrv_query($conn, $SQL);
											while ($row1 = sqlsrv_fetch_array($RS1)) {
												$ID_NVLAUTO = $row1['ID_NVLAUTO'];
												$VALUEUDF = $row1['VALUE'];
												$SQL2="INSERT INTO OP_OPERAUDF (ID_OPERADOR, ID_NVLAUTO, VALUE, IDREG) ";
												$SQL2=$SQL2." VALUES (".$ID_OPERADOR.", ".$ID_NVLAUTO.", '".$VALUEUDF."', ".$SESIDUSU.")";
												$RS2 = sqlsrv_query($conn, $SQL2);
											}
											//SEGUNDO EL REGISTRO DE AUTORIZACIÓN
											$SQL2="DELETE FROM OP_OPERAMDA WHERE ID_OPERADOR=".$ID_OPERADOR;
											$RS2 = sqlsrv_query($conn, $SQL2);
											$SQL="SELECT * FROM OP_MODMDA WHERE ID_MODOPERA=".$ID_MODOPERA." ORDER BY ID_INDICAT ASC, ID_INDICATOPC ASC";
											$RS1 = sqlsrv_query($conn, $SQL);
											while ($row1 = sqlsrv_fetch_array($RS1)) {
												$ID_INDICAT = $row1['ID_INDICAT'];
												$ID_INDICATOPC = $row1['ID_INDICATOPC'];
												$VALUEMDA = $row1['VALUE'];
												$SQL2="INSERT INTO OP_OPERAMDA (ID_OPERADOR, ID_INDICAT, ID_INDICATOPC, VALUE, IDREG) ";
												$SQL2=$SQL2." VALUES (".$ID_OPERADOR.", ".$ID_INDICAT.", ".$ID_INDICATOPC.", '".$VALUEMDA."', ".$SESIDUSU.")";
												$RS2 = sqlsrv_query($conn, $SQL2);
											}
								}
							}
							
							$SQLOP2="INSERT INTO OP_OPERAMOV (ID_OPERADOR, STR_ESTADO, REG_ESTADO, CC_OPERADOR, COD_NEGOCIO, COD_TIENDA, IDREG, HORA, IP_CLIENTE) ";
							$SQLOP2=$SQLOP2." VALUES (".$ID_OPERADOR.", ".$STR_ESTADO.", ".$REG_ESTADO.", ".$CC_OPERADOR.", ".$COD_NEGOCIO.", ".$DES_CLAVE.", ".$SESIDUSU.", '".$TIMESRV."', '".$IP_CLIENTE."')";
							$RS2 = sqlsrv_query($conn, $SQLOP2);

							//REGISTRO DE MODIFICACION
							$SQLOG="INSERT INTO LG_EVENTO ( COD_TIPO_EVENTO, FECHA, HORA, IP_CLIENTE, COD_USUARIO, IDACC, IDSISTEMA, IDPERFIL) VALUES ";
							$SQLOG=$SQLOG."( 3, convert(datetime,GETDATE(), 121), '".$TIMESRV."', '".$IP_CLIENTE."', ".$SESIDUSU.", 1127, ".$SESIDSISTEMA.", ".$SESIDPERFIL.")";
							$RSL = sqlsrv_query($maestra, $SQLOG);

					$SQL="UPDATE US_USUARIOS SET COD_SAP='".$COD_SAP."' WHERE CC_OPERADOR=".$CC_OPERADOR." AND ISNULL(COD_SAP,0)=0";
					$RS1 = sqlsrv_query($maestra, $SQL);
					
							if($US_SUITE==0){$MSJE_CLAVE=0;}
							header("Location: reg_operador.php?ACT=".$ID_OPERADOR."&MSJE=1&MSJE_CLAVE=".$MSJE_CLAVE);
				}
		

		sqlsrv_close($conn);
		sqlsrv_close($maestra);
		} //FIN ACTUALIZA DATA OPERADOR
} //FIN INGRESAR

$VALIDACODSAP=$_GET['VALIDACODSAP'];

if ($VALIDACODSAP<>"") {
	$OPERADOR = $_GET['OPERADOR'];
	$CONSULTA="SELECT NOMBRE FROM US_USUARIOS WHERE COD_SAP='".$VALIDACODSAP."' " . ($OPERADOR==0?"":"AND CC_OPERADOR<>".$OPERADOR);
	$RS = sqlsrv_query($maestra, $CONSULTA);
	if ($row = sqlsrv_fetch_array($RS)) {
		echo $row['NOMBRE'];
	}
	sqlsrv_close($maestra);
	return;
}

?>

<?php include("reg_operador_reg_nva.php");?>

<?php
$REGIND=$_POST["REGIND"];
$ID_OPERADOR=$_POST["ID_OPERADOR"];
$ID_INDICAT=$_POST["ID_INDICAT"];

if ($REGIND<>"" ) {
	//VERIFICAR SI OPERADOR ESTA REGISTRADO PREVIAMENTE
	$SQLOP="SELECT * FROM OP_OPERAMDA WHERE ID_OPERADOR=".$ID_OPERADOR;
	$RS = sqlsrv_query($conn, $SQLOP);
	if ($row = sqlsrv_fetch_array($RS)) {
				//ACTUALIZA REGISTROS PARA LA ID_INDICAT
				$SQLOP1="SELECT * FROM OP_INDICATOPC WHERE ID_INDICAT=".$ID_INDICAT." ORDER BY POSICION ASC";
				$RS1 = sqlsrv_query($conn, $SQLOP1);
				while ($row2 = sqlsrv_fetch_array($RS1)) {
						$ID_INDICATOPC=$row2['ID_INDICATOPC'];
						$VERIFICA=$_POST["IND".$ID_INDICATOPC];
						if($VERIFICA==1){
								$SQLOP2="UPDATE OP_OPERAMDA SET  VALUE=1, IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_INDICATOPC=".$ID_INDICATOPC." AND ID_OPERADOR=".$ID_OPERADOR;
								$RS2 = sqlsrv_query($conn, $SQLOP2);
						} else {
								$SQLOP2="UPDATE OP_OPERAMDA SET  VALUE=0, IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121)  WHERE ID_INDICATOPC=".$ID_INDICATOPC." AND ID_OPERADOR=".$ID_OPERADOR;
								$RS2 = sqlsrv_query($conn, $SQLOP2);
						}
				}
	} else {
				//REGISTRA ID_INDICAT POR DEFAULT
				
				$SQLOP1="SELECT * FROM OP_INDICATOPC ORDER BY POSICION ASC";
				$RS1 = sqlsrv_query($conn, $SQLOP1);
				while ($row = sqlsrv_fetch_array($RS1)) {
					$ID_INDICATREG=$row['ID_INDICAT'];
					$ID_INDICATOPC=$row['ID_INDICATOPC'];
					$SQLOP2="INSERT INTO OP_OPERAMDA (ID_OPERADOR, ID_INDICAT, ID_INDICATOPC, IDREG) VALUES (".$ID_OPERADOR.", ".$ID_INDICATREG.",  ".$ID_INDICATOPC.", ".$SESIDUSU.")";
					$RS2 = sqlsrv_query($conn, $SQLOP2);
				}
				
				$SQLOP1="SELECT * FROM OP_INDICATOPC WHERE ID_INDICAT=".$ID_INDICAT." ORDER BY POSICION ASC";
				$RS1 = sqlsrv_query($conn, $SQLOP1);
				while ($row = sqlsrv_fetch_array($RS1)) {
						$ID_INDICATOPC=$row['ID_INDICATOPC'];
						$VERIFICA=$_POST["IND".$ID_INDICATOPC];
						if($VERIFICA==1){
								$SQLOP2="UPDATE OP_OPERAMDA SET  VALUE=1, IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE ID_INDICATOPC=".$ID_INDICATOPC." AND ID_OPERADOR=".$ID_OPERADOR;
								$RS2 = sqlsrv_query($conn, $SQLOP2);
						}
				}
	}
	header("Location: reg_operador.php?ACT=".$ID_OPERADOR."&MSJE=5&ACT_MDA=1");
}// FIN REGIND

?>
