<?php include("session.inc");?>
<?php include("headerhtml.inc");?>
<?php
	$PAGINA=1129;
	$LIST=@$_GET["LIST"];
	$ACT=@$_GET["ACT"];
	$NOMENU=1;
	
	if ($ACT=="") {
		 $LIST=1;
	}
?>
</head>
<body >
<?php include("../headerregusu.php");?>
<?php include("titulo_menu.php");?>
<table width="100%" height="100%">
<tr>
<td align="right"  width="200" bgcolor="#FFFFFF"><?php include("menugeneral.php");?></td> 
<td >
<?php
if ($MSJE==1) {$ELMSJ="Registro actualizado";} 
if ($MSJE==5) {$ELMSJ="Registrado Nivel de Autorizaci&oacute;n";}
if ($MSJE==4) {$ELMSJ="Registro de Autorizaci&oacute;n Actualizado";}
if ($MSJE <> "") {
?>
<div id="GMessaje" onClick="QuitarGMessage();"><a href="#" onClick="QuitarGMessage();" style="color:#111111;"><?php echo $ELMSJ?></a></div>
<?php }?>
        <table width="100%">
        <tr><td>
        
                <table style="margin:10px 20px; ">
                <tr>
                <td>
                
<?php
if ($LIST==1) {
?>
                <?php
				
				$CONSULTA="SELECT COUNT(*) AS CUENTA FROM OP_MODOPERA";
				$RS = sqlsrv_query($conn, $CONSULTA);
				//oci_execute($RS);
				if ($row = sqlsrv_fetch_array($RS)) {
					$TOTALREG = $row['CUENTA'];
					$NUMTPAG = round($TOTALREG/$CTP,0);
					$RESTO=$TOTALREG%$CTP;
					$CUANTORESTO=round($RESTO/$CTP, 0);
					if($RESTO>0 and $CUANTORESTO==0) {$NUMTPAG=$NUMTPAG+1;}
					$NUMPAG = round($LSUP/$CTP,0);
					if ($NUMTPAG==0) {
						$NUMTPAG=1;
						}
				}

				//$CONSULTA="SELECT * FROM (SELECT  a.*, ROWNUM rnum FROM (SELECT * FROM OP_MODOPERA ORDER BY ID_MODOPERA ASC) a WHERE ROWNUM <= ".$LSUP.") WHERE rnum >=  ".$LINF;

				$CONSULTA= "SELECT * FROM (SELECT *,ROW_NUMBER() OVER (PARTITION BY ".$CTP." ORDER BY ID_MODOPERA ASC) ROWNUMBER FROM OP_MODOPERA) AS TABLEWITHROWNUMBER WHERE ROWNUMBER BETWEEN ".$LINF." AND ".$LSUP."";

				$RS = sqlsrv_query($conn, $CONSULTA);
				//oci_execute($RS);
               ?>
                <table id="Listado">
                <tr>
                    <th>Modelo</th>
                    <th>Estado</th>
                    <th>Registrado por</th>
                </tr>
                <?php
				while ($row = sqlsrv_fetch_array($RS)){
                        $ID_MODOPERA = $row['ID_MODOPERA'];
                        $DES_MODOPERA = $row['DES_MODOPERA'];
                        $EST_MODOPERA = $row['EST_MODOPERA'];
						if($EST_MODOPERA==0){$ELESTADO="Bloqueado";}
						if($EST_MODOPERA==1){$ELESTADO="Activo";}
                        $IDREG = $row['IDREG'];
                        $FECHA = $row['FECHA'];
						$CONSULTA2="SELECT NOMBRE FROM US_USUARIOS WHERE IDUSU=".$IDREG;
						$RS2 = sqlsrv_query($maestra, $CONSULTA2);
						//oci_execute($RS2);
						if ($row = sqlsrv_fetch_array($RS2)) {
							$QUIENFUE = $row['NOMBRE'];
						}	
               ?>
                <tr>
                    <?php if($SESPUBLICA==1) { ?>
                    <td><a href="mant_modelos.php?ACT=<?php echo $ID_MODOPERA?>"><?php echo $DES_MODOPERA?></a></td>
                    <?php } else {?>
                     <td><?php echo $DES_MODOPERA?></td>
                    <?php } ?>
                     <td><?php echo $ELESTADO?></td>
                     <td><?php echo $QUIENFUE." el ".date_format($FECHA,"d-m-Y"); ?></td>
                </tr>
                <?php
				}
				?>
                <tr>
                    <td colspan="3" nowrap style="background-color:transparent">
                    <?php
                    if ($LINF>=$CTP+1) {
						$ATRAS=$LINF-$CTP;
						$FILA_ANT=$LSUP-$CTP;
                   ?>
                    <input name="ANTERIOR" type="button" value="Anterior"  onClick="pagina('mant_modelos.php?LSUP=<?php echo $FILA_ANT?>&LINF=<?php echo $ATRAS?>');">
                    <?php
                    }
                    if ($LSUP<=$TOTALREG) {
						$ADELANTE=$LSUP+1;
						$FILA_POS=$LSUP+$CTP;
                   ?>
                    <input name="SIGUIENTE" type="button" value="Siguiente" onClick="pagina('mant_modelos.php?LSUP=<?php echo $FILA_POS?>&LINF=<?php echo $ADELANTE?>');">
                    <?php }?>
                    <span style="vertical-align:baseline;">P&aacute;gina <?php echo $NUMPAG?> de <?php echo $NUMTPAG?></span>
                    </td>
                </tr>
                </table>
<?php
		sqlsrv_close($conn);
		sqlsrv_close($maestra);}
?>
               
               
                          
               
<?php  if ($ACT<>"") { 

				$CONSULTA="SELECT * FROM OP_MODOPERA WHERE ID_MODOPERA=".$ACT;
				$RS = sqlsrv_query($conn, $CONSULTA);
				//oci_execute($RS);
				if ($row = sqlsrv_fetch_array($RS)) {
					$ID_MODOPERA = $row['ID_MODOPERA'];
					$DES_MODOPERA = $row['DES_MODOPERA'];
					$EST_MODOPERA = $row['EST_MODOPERA'];
					$NVA_GRUPO = $row['NVA_GRUPO'];
					$NVA_USUARIO = $row['NVA_USUARIO'];
					$NIVEL_AUT = $row['NIVEL_AUT'];
                }
               ?>
                <h3>Actualizar Modelo <?= $DES_MODOPERA?></h3>
				<script language="JavaScript">
                function validaingreso(theForm){
                        if (theForm.DES_MODOPERA.value == ""){
                            alert("COMPLETE EL CAMPO REQUERIDO.");
                            theForm.DES_MODOPERA.focus();
                            return false;
                    }
                } //validaingreso(theForm)
                </script>
                <table id="forma-registro">
                    <form action="mant_modelos_reg.php" method="post" name="formact" onSubmit="return validaingreso(this)">
                    <tr>
                        <td><label for="DES_MODOPERA">Nombre Modelo</label></td>
                        <td><input name="DES_MODOPERA" type="text" size="20" maxlength="200" value="<?= $DES_MODOPERA?>"></td>
                   </tr>
                   <tr>
                   		<td><label for="DES_MODOPERA">Estado</label></td>
                        <td>
                                <select name="EST_MODOPERA">
                                        <option value="0" <?php if ($EST_MODOPERA==0) { echo "SELECTED";}?>>Bloqueado</option>
                                        <option value="1" <?php if ($EST_MODOPERA==1) { echo "SELECTED";}?>>Activo</option>
                                </select>
                        </td>
                    </tr>
                    <tr>
                            <td> <input name="ID_MODOPERA" type="hidden" value="<?= $ID_MODOPERA?>"></td>
                            <td>
                                <input name="ACTUALIZAR" type="submit" value="Actualizar">
                                <input name="SALIR" type="button" value="Salir" onClick="javascript:pagina('mant_modelos.php')">
                        </td>
                    </tr>
                    </form>
                </table>


			<?php

			//IDIOMA TITULOS INDICAT
			$IDIOMA=$_POST["IDIOMA"];
			if ($IDIOMA=="ESP") {
					$_SESSION['LAN_INDICAT'] = "DESCRIP_ES";	
			}
			if ($IDIOMA=="ENG") {
					$_SESSION['LAN_INDICAT'] = "DESCRIP_EN";	
			}
			
			if (!isset($_SESSION['LAN_INDICAT'])) {
					$_SESSION['LAN_INDICAT'] = "DESCRIP_EN";	
				}
			?>

                <h3>Niveles de Autorizaci&oacute;n</h3>

                        <div style="text-align:right">
                        		<form action="mant_modelos.php?ACT=<?= $ACT?>" method="post">
                                        <input style="padding:4px 8px; margin:1px;  border-color:#666" name="IDIOMA" type="submit" value="ESP" title="espa&ntilde;ol">
                                        <input style="padding:4px 8px; margin:1px;  border-color:#666" name="IDIOMA" type="submit" value="ENG" title="english">
                                </form>
                         </div>


				<?php
				if($_SESSION['LAN_INDICAT'] == "DESCRIP_EN"){
					$DESCRIP_NVA = "AUTHORIZATION LEVEL";
				} else {
					$DESCRIP_NVA = "NIVEL DE AUTORIZACI&Oacute;N";
				}
	
				?>
                <div style="display:block; width:auto; height:34px;text-align:left; padding:10px 20px; border: 1px solid #DFDFDF;-khtml-border-radius: 4px;-moz-border-radius: 4px;-webkit-border-radius: 4px;border-radius: 4px; ">

                        <form action="mant_modelos_reg.php" method="post" name="formactNVA" id="formactNVA">
                            <label style="display:inline; float:left; font-weight:600; line-height:9pt; text-align:right; padding-top:6px"><?php echo $DESCRIP_NVA;?><br />( 0 - 9 )</label>
                            <input style="margin:4px; display:inline; float:left; text-align:right" name="NIVEL_AUT" type="text" size="3" maxlength="1"  onKeyPress="return acceptNum(event);" value="<?php echo $NIVEL_AUT?>"> 
                            <input style="display:inline; float:right; margin-left:10px" type="submit" name="REGNVAUOP" id="REGNVAUOP" value="Registrar">
                            <input type="hidden" name="ID_MODOPERA" id="ID_MODOPERA" value="<?php echo $ID_MODOPERA?>">
                        </form>

                </div>
                
                <?php
				$CONSULTA="SELECT * FROM OP_ENHSEC ORDER BY ID_ENHSEC ASC";
				$RS = sqlsrv_query($conn, $CONSULTA);
				//oci_execute($RS);
				while ($row = sqlsrv_fetch_array($RS)) {
					$ID_ENHSEC = $row['ID_ENHSEC'];
					$MNVL_DEP = $row['MNVL_DEP'];
					$DESCRIP_IND = $row[$_SESSION['LAN_INDICAT']];
					$PADD_NVL = $row['PADD_NVL'];
					$PADD_BTNVL = 20*$PADD_NVL;
					
					if($MNVL_DEP<>0){ 
							//MENSAJES DE MNVL_DEP
							$SQLMS="SELECT * FROM OP_NVLAUTO WHERE ID_NVLAUTO=".$MNVL_DEP;
							$RSMS = sqlsrv_query($conn, $SQLMS);
							//oci_execute($RSMS);
							if ($rowMs = sqlsrv_fetch_array($RSMS)) {
									$ID_OPCION=$rowMs['ID_NVLAUTO'];
									$NM_OPCION=$rowMs[$_SESSION['LAN_INDICAT']];
									$ID_MENU=$rowMs['ID_ENHSEC'];
									$SQLMN="SELECT * FROM OP_ENHSEC WHERE ID_ENHSEC=".$ID_MENU;
									$RSMN = sqlsrv_query($conn, $SQLMN);
									//oci_execute($RSMN);
									if ($rowMn = sqlsrv_fetch_array($RSMN)) {
											$NM_MENU=$rowMn[$_SESSION['LAN_INDICAT']];
									}
							}
					}
				?>
                <script>
					 function ActivarNvaAuth<?= $ID_ENHSEC?>(){
							var contenedor = document.getElementById("RegNvcAuth<?= $ID_ENHSEC?>");
							contenedor.style.display = "block";
							window.scrollTo(0,0);
							return true;
						}
						
					 function CerrarNvaAuth<?= $ID_ENHSEC?>(){
							var contenedor = document.getElementById("RegNvcAuth<?= $ID_ENHSEC?>");
							contenedor.style.display = "none";
							return true;
						}
					 
					 function AlertaNvaAuth<?= $ID_ENHSEC?>(){
							<?php if ($_SESSION['LAN_INDICAT'] == "DESCRIP_ES") { ?>
									alert("Para configurar <?php echo trim($DESCRIP_IND)?>, debe habilitar <?php echo trim($NM_OPCION)?> en el menu <?php echo trim($NM_MENU)?>");
							<?php } ?>
							<?php if ($_SESSION['LAN_INDICAT'] == "DESCRIP_EN") { ?>
									alert("To configure <?php echo trim($DESCRIP_IND)?>, you must enable <?php echo trim($NM_OPCION)?> on the <?php echo trim($NM_MENU)?> page");
							<?php } ?>
						}
						
				</script>
                
                <?php
				//VALIDAR HABILITACIÓN DE OPCIONES DE VENTANA... VIA MNVL_DEP
				$ACTIVAR_MN= "N";
				if($MNVL_DEP==0){ //NO TIENE DEPENDENCIA
						$ACTIVAR_MN= "Y";
				} else {
						//VERIFICAR SI DEPENDENCIA SE ENCUENTRA EN ESTADO "Y" EN OP_MODNVA
						$SQLDEP="SELECT VALUE FROM OP_MODNVA WHERE ID_NVLAUTO=".$MNVL_DEP." AND ID_MODOPERA=".$ID_MODOPERA;
						$RSDP = sqlsrv_query($conn, $SQLDEP);
						//oci_execute($RSDP);
						if ($rowDep = sqlsrv_fetch_array($RSDP)) {
								$ACTIVAR_MN=$rowDep['VALUE'];
						}
				}
				
				?>
                
                <input type="button" style="display:block; width:100%; text-align:left; margin:2px 0; padding-left:<?php echo $PADD_BTNVL;?>" value="<?=$DESCRIP_IND?>"  <?php if($ACTIVAR_MN=="Y"){ echo " onClick='ActivarNvaAuth".$ID_ENHSEC."();' "; } else { echo " onClick='AlertaNvaAuth".$ID_ENHSEC."();' "; }?> >

                <style>
					#RegNvcAuth<?= $ID_ENHSEC?> {position:absolute;width:100%;height:300%;margin: 0 auto;left: 0;top:0;background-image: url(../images/TranspaBlack72.png);background-repeat: repeat;background-position: left top;z-index:10000;}
					#RegNvcAuth-contenedor<?php echo $ID_ENHSEC?> {
						position:absolute;
						left: 340px;
						top:40px;
						width:auto;
						min-width:300px;
						height:auto;
						overflow:visible;
						padding:20px;
						background-color:#F1F1F1;
						-khtml-border-radius: 6px;
						-moz-border-radius: 6px;
						-webkit-border-radius: 6px;
						border-radius: 6px;
						background-image: url(../images/ARMS.png); 
						background-repeat: no-repeat; 
						background-position: 20px 10px; 
						}
					#RegNvcAuth-contenedor<?php echo $ID_ENHSEC?> h3{
						margin-top:50px;
					}
					#RegNvcAuth-contenedor<?php echo $ID_ENHSEC?> td{
						padding:4px 6px;
					}

				</style>
                <div id="RegNvcAuth<?= $ID_ENHSEC?>" style="display:none">
                    <div id="RegNvcAuth-contenedor<?= $ID_ENHSEC?>">

                            <span style="position:absolute; top:0; right:20px;">
                            <img src="../images/ICO_Close.png" border="0" onClick="CerrarNvaAuth<?php echo $ID_ENHSEC?>();" title="Cerrar ventana">
                            </span>
                            <h3>Niveles de Autorizaci&oacute;n<br><?php echo $DESCRIP_IND?></h3>
                            <table id="Listado" width="100%">
                                <form action="mant_modelos_reg.php" method="post" name="formactNVA2<?php echo $ID_ENHSEC?>" id="formactNVA2<?php echo $ID_ENHSEC?>">
                                                <tr>
                                                    <th width="10px">
                                                            <script>
                                                                function MarcarCheck<?php echo $ID_ENHSEC?>(val){ 
                                                                   for (i=0;i<document.formactNVA2<?php echo $ID_ENHSEC?>.elements.length;i++) 
                                                                      if(document.formactNVA2<?php echo $ID_ENHSEC?>.elements[i].type == "checkbox")	
                                                                        if(document.formactNVA2<?php echo $ID_ENHSEC?>.SELCHECKBOX<?php echo $ID_ENHSEC?>.checked == true) {
                                                                             document.formactNVA2<?php echo $ID_ENHSEC?>.elements[i].checked=1;
                                                                        } else {
                                                                             document.formactNVA2<?php echo $ID_ENHSEC?>.elements[i].checked=0;
                                                                        }
                                                                } 
                                                            </script>
                                                            <input name="SELCHECKBOX<?php echo $ID_ENHSEC?>" type="checkbox" value="1" onChange="MarcarCheck<?php echo $ID_ENHSEC?>(this.value)">
                                                    </th>
                                                    <th>Seleccionar todos</th>
                                                </tr>
                                            <?php
                                                    $CONSULTA1="SELECT * FROM OP_NVLAUTO WHERE ID_ENHSEC=".$ID_ENHSEC." ORDER BY POS_NVL ASC";
                                                    $RS1 = sqlsrv_query($conn, $CONSULTA1);
                                                    //oci_execute($RS1);
                                                    while ($row = sqlsrv_fetch_array($RS1)) {
                                                            $ID_NVLAUTO=$row['ID_NVLAUTO'];
                                                            $NVL_DEP=$row['NVL_DEP'];
															$DESCRIP_NVA=$row[$_SESSION['LAN_INDICAT']];
															if($ID_NVLAUTO<1000 || $ID_NVLAUTO>1001){
																//CAMBIA DE TABLA PARA ID_ENHSEC=15
																if($ID_ENHSEC==15) {$TablaOpera="OP_MODUDF"; }
																if($ID_ENHSEC<=14) {$TablaOpera="OP_MODNVA"; }
																		$CONSULTA2="SELECT VALUE FROM ".$TablaOpera." WHERE ID_MODOPERA=".$ID_MODOPERA." AND ID_NVLAUTO=".$ID_NVLAUTO;
																		$RS2 = sqlsrv_query($conn, $CONSULTA2);
																		//oci_execute($RS2);
																		if ($row2 = sqlsrv_fetch_array($RS2)) {
																				@$IND_ACTIVO = $row2['VALUE'];
																		}
																		if(@$IND_ACTIVO=="Y") {
																				$TDCOLOR=" style='background:#FFF3E0' ";
																		} else {
																				$TDCOLOR="";
																		}
															}
														if($ID_NVLAUTO<1000 || $ID_NVLAUTO>1001){
																		//VERIFICAR SI ALGUIEN DEPENDE DE MI
																		$DEPENDIENTE=0;
																		$SQLDEPNV="SELECT ID_NVLAUTO FROM OP_NVLAUTO WHERE NVL_DEP=".$ID_NVLAUTO;
																		$RSNVA = sqlsrv_query($conn, $SQLDEPNV);
																		//oci_execute($RSNVA);
																		if ($rowNva = sqlsrv_fetch_array($RSNVA)) {
																				$DEPENDIENTE = $rowNva['ID_NVLAUTO']; //DEPENDIENTE
																		}
                                                        ?>
																		<script>
                                                                        function ActivaDependiente<?php echo $ID_NVLAUTO?>() {
                                                                                    if (document.getElementById("NVA<?php echo $ID_NVLAUTO;?>").checked==true) {
                                                                                            document.getElementById("NVA<?php echo $DEPENDIENTE;?>").disabled=false;
                                                                                    }
                                                                                    if (document.getElementById("NVA<?php echo $ID_NVLAUTO;?>").checked==false) {
                                                                                            document.getElementById("NVA<?php echo $DEPENDIENTE;?>").checked=false;
                                                                                            document.getElementById("NVA<?php echo $DEPENDIENTE;?>").disabled=true;
                                                                                    }
                                                                        }
                                                                        </script>
                                                                        <tr<?=@$TDCOLOR;?>>
                                                                            <td width="10px"><input type="checkbox" id="NVA<?php echo $ID_NVLAUTO;?>" name="NVA<?php echo $ID_NVLAUTO;?>" value="Y"  <?php if($DEPENDIENTE<>0) { ?> onClick="ActivaDependiente<?php echo $ID_NVLAUTO?>();"<?php }  if(@$IND_ACTIVO=="Y") { echo "checked"; } if($NVL_DEP<>0 && @$IND_ACTIVO=="N"){ echo "disabled"; }?> ></td>
                                                                            <td><?php echo $DESCRIP_NVA;?></td>
                                                                        </tr>
                                                        <?php
														} else {
                                                        ?>
                                                            <tr<?=$TDCOLOR;?>>
                                                                <td></td>
																<?php if($ID_NVLAUTO==1000){?>
                                                                        <td><p style="margin-top:10px; width:150px; display:inline; float:left"><?php echo $DESCRIP_NVA;?></p><input style="display:inline; float:left" name="NVA<?php echo $ID_NVLAUTO;?>" type="text" size="4" maxlength="4"  onKeyPress="return acceptNum(event);" value="<?php echo $NVA_GRUPO ?>"></td>
                                                                <?php }?>
                                                                <?php if($ID_NVLAUTO==1001){?>
                                                                        <td><p style="margin-top:10px; width:150px; display:inline; float:left"><?php echo $DESCRIP_NVA;?></p><input style="display:inline; float:left" name="NVA<?php echo $ID_NVLAUTO;?>" type="text" size="4" maxlength="4"  onKeyPress="return acceptNum(event);"  value="<?php echo $NVA_USUARIO ?>"></td>
                                                                <?php }?>
                                                            </tr>
                                                        <?php
														}
                                                    }
                                            ?>
                                                    <tr>
                                                        <td colspan="2" style="border:none">
                                                                <input type="hidden" name="ID_ENHSEC" id="ID_ENHSEC" value="<?php echo $ID_ENHSEC?>">
                                                                <input type="hidden" name="ID_MODOPERA" id="ID_MODOPERA" value="<?php echo $ID_MODOPERA?>">
                                                                <input style="margin:2px" type="submit" name="REGNVA" id="REGNVA" value="Registrar">
                                                                <input style="margin:2px" type="button" name="CERRARV" id="CERRARV" value="Salir" onClick="javascript: CerrarNvaAuth<?php echo $ID_ENHSEC?>();">
                                                        </td>
                                                    </tr>
                                                    <?php
													if($ID_NVLAUTO==1000 || $ID_NVLAUTO==1001){
													?>
                                                    <tr>
                                                        <td colspan="2" style="border:none">
                                                        <?php
														if ($_SESSION['LAN_INDICAT'] === "DESCRIP_ES") {
														?>
																<p style="width:500px; max-width:500px">
                                                                <span style="font-weight:600">
                                                                Los n&uacute;meros de Grupo y Usuario identifican al operador mientras se
                                                                est&aacute; en Modalidad Mandato. Estos dos n&uacute;meros se usan para determinar
                                                                la propiedad de un archivo y el acceso permitido en Mod. Mandato.</span>
                                                                <br><br>
                                                                <span style="font-weight:600">
                                                                El Usuario 1 del Grupo 2</span>, tiene derecho de acceso como
                                                                de propietario a archivos de la aplicaci&oacute;n.
                                                                Inicialmente los derechos de propiedad son LEER, GRABAR y SUPRIMIR.
                                                                <br>
                                                                <span style="font-weight:600">El Usuario distinto de 1 del Grupo 2</span>, tiene derechos
                                                                de grupo a los archivos de la aplicaci&oacute;n.
                                                                Inicialmente los derechos de  grupo son de SOLO LECTURA (READ ONLY).
                                                                <br>
                                                                <span style="font-weight:600">Los Grupos 3 al 254, con cualquier n&uacute;mero
                                                                de usuario</span>, no tiene acceso a los archivos de la aplicaci&oacute;n.<br><br>
                                                                Si se cambia el n&uacute;mero de grupo, &eacute;ste debe ser superior o igual al del n&uacute;mero de grupo
                                                                del usuario actual.<br>
                                                                Si se cambia el n&uacute;mero de usuario, &eacute;ste debe ser superior o igual al del n&uacute;mero de
                                                                usuario del usuario actual.
                                                                </p>
														<?php
														} else {
														?>
																<p style="width:500px; max-width:500px">
                                                                <span style="font-weight:600">
                                                                Group and User number identify the operator when running in Command Mode. These two numbers are used to determine file ownership and access rights in Command Mode.</span>
                                                                <br><br>
                                                                <span style="font-weight:600">
                                                                Group 2 User 1 </span>has owner access rights to application files. Initially owner rights are (READ, WRITE, DELETE).
                                                                <br>
                                                                <span style="font-weight:600">Group 2 USER not 1 </span>has group rights for application files. Initially group rights are (READ ONLY).
                                                                <br>
                                                                <span style="font-weight:600">Groups 3 through 254 with any user </span>can not access application files.<br><br>
                                                                If the user number is changed, it must be greater then or equal to the current user's user number..
                                                                </p>
														<?php
														}	
														?>
                                                        </td>
                                                    </tr>
												<?php
                                                }	
                                                ?>
                                </form>
                             </table>
                    </div>
                </div>
                <?php } //FIN NIVEL DE AUTORIZACION?>
                
                <h3>Registros de Autorizaci&oacute;n</h3>

                <?php
				$CONSULTA="SELECT * FROM OP_INDICAT WHERE RESERVADO=0 ORDER BY ID_INDICAT ASC";
				$RS = sqlsrv_query($conn, $CONSULTA);
				//oci_execute($RS);
				while ($row = sqlsrv_fetch_array($RS)) {
					$ID_INDICAT = $row['ID_INDICAT'];
					$DESCRIP_IND = $row[$_SESSION['LAN_INDICAT']];
				?>
                <style>
					#RegRegAuth<?php echo $ID_INDICAT?> {position:absolute;width:100%;height:300%;margin: 0 auto;left: 0;top:0;background-image: url(../images/TranspaBlack72.png);background-repeat: repeat;background-position: left top;z-index:10000;}
					#RegRegAuth-contenedor<?php echo $ID_INDICAT?> {
						position:absolute;
						left: 340px;
						top:40px;
						width:auto;
						min-width:300px;
						height:auto;
						overflow:visible;
						padding:20px;
						background-color:#F1F1F1;
						-khtml-border-radius: 6px;
						-moz-border-radius: 6px;
						-webkit-border-radius: 6px;
						border-radius: 6px;
						background-image: url(../images/ARMS.png); 
						background-repeat: no-repeat; 
						background-position: 20px 10px; 
						}
					#RegRegAuth-contenedor<?php echo $ID_INDICAT?> h3{
						margin-top:50px;
					}
					#RegRegAuth-contenedor<?php echo $ID_INDICAT?> td{
						padding:4px 6px;
					}

				</style>
                <script>
					 function ActivarRegAuth<?php echo $ID_INDICAT?>(){
							var contenedor = document.getElementById("RegRegAuth<?php echo $ID_INDICAT?>");
							contenedor.style.display = "block";
							window.scrollTo(0,0);
							return true;
						}
						
					 function CerrarRegAuth<?php echo $ID_INDICAT?>(){
							var contenedor = document.getElementById("RegRegAuth<?php echo $ID_INDICAT?>");
							contenedor.style.display = "none";
							return true;
						}
				</script>
                
                <input type="button" style="display:block; width:100%; text-align:left; margin:2px 0" value="<?php echo $DESCRIP_IND?>"  onClick="ActivarRegAuth<?php echo $ID_INDICAT?>();">
                
                <div id="RegRegAuth<?php echo $ID_INDICAT?>" style="display:none">
                    <div id="RegRegAuth-contenedor<?php echo $ID_INDICAT?>">

                            <span style="position:absolute; top:0; right:20px;">
                            <img src="../images/ICO_Close.png" border="0" onClick="CerrarRegAuth<?php echo $ID_INDICAT?>();" title="Cerrar ventana">
                            </span>
                            <h3>Registros de Autorizaci&oacute;n<br><?php echo $DESCRIP_IND?></h3>
                            <table id="Listado" width="100%">
                                <form action="mant_modelos_reg.php" method="post" name="formactRA<?php echo $ID_INDICAT?>" id="formactRA<?php echo $ID_INDICAT?>">
                                                <tr>
                                                    <th width="10px">
                                                            <script>
                                                                function MarcarCheckRegAuth<?php echo $ID_INDICAT?>(val){ 
                                                                   for (i=0;i<document.formactRA<?php echo $ID_INDICAT?>.elements.length;i++) 
                                                                      if(document.formactRA<?php echo $ID_INDICAT?>.elements[i].type == "checkbox")	
                                                                        if(document.formactRA<?php echo $ID_INDICAT?>.SELCHECKBOX<?php echo $ID_INDICAT?>.checked == true) {
                                                                             document.formactRA<?php echo $ID_INDICAT?>.elements[i].checked=1;
                                                                        } else {
                                                                             document.formactRA<?php echo $ID_INDICAT?>.elements[i].checked=0;
                                                                        }
                                                                } 
                                                            </script>
                                                            <input name="SELCHECKBOX<?php echo $ID_INDICAT?>" type="checkbox" value="1" onChange="MarcarCheckRegAuth<?php echo $ID_INDICAT?>(this.value)">
                                                    </th>
                                                    <th>Seleccionar todos</th>
                                                </tr>
                                            <?php
                                                    $CONSULTA1="SELECT * FROM OP_INDICATOPC WHERE ID_INDICAT=".$ID_INDICAT." AND RESERVADO=0 ORDER BY POSICION ASC";
                                                    $RS1 = sqlsrv_query($conn, $CONSULTA1);
                                                    //oci_execute($RS1);
                                                    while ($row = sqlsrv_fetch_array($RS1)) {
                                                            $ID_INDICATOPC=$row['ID_INDICATOPC'];
															$DESCRIP_INDOPC=$row[$_SESSION['LAN_INDICAT']];
                                                            $CONSULTA2="SELECT VALUE FROM OP_MODMDA WHERE ID_MODOPERA=".$ID_MODOPERA." AND ID_INDICATOPC=".$ID_INDICATOPC;
                                                            $RS2 = sqlsrv_query($conn, $CONSULTA2);
                                                            //oci_execute($RS2);
                                                            if ($row2 = sqlsrv_fetch_array($RS2)) {
                                                                    @$IND_ACTIVO = $row2['VALUE'];
                                                            }
                                                            if(@$IND_ACTIVO==1) {
                                                                    $TDCOLOR=" style='background:#FFF3E0' ";
                                                            } else {
                                                                    $TDCOLOR="";
                                                            }
                                                        ?>
                                                            <tr<?=$TDCOLOR;?>>
                                                                <td width="10px"><input type="checkbox" id="IND<?php echo $ID_INDICATOPC;?>" name="IND<?php echo $ID_INDICATOPC;?>" value="1" <?php if($IND_ACTIVO==1) {?> checked <?php }?>></td>
                                                                <td><?php echo $DESCRIP_INDOPC;?></td>
                                                            </tr>
                                                        <?php
                                                    }
                                            ?>
                                                    <tr>
                                                        <td colspan="2" style="border:none">
                                                                <input type="hidden" name="ID_INDICAT" id="ID_INDICAT" value="<?php echo $ID_INDICAT?>">
                                                                <input type="hidden" name="ID_MODOPERA" id="ID_MODOPERA" value="<?php echo $ID_MODOPERA?>">
                                                                <input style="margin:2px" type="submit" name="REGIND" id="REGIND" value="Registrar">
                                                                <input style="margin:2px" type="button" name="CERRARV" id="CERRARV" value="Salir" onClick="javascript: CerrarRegAuth<?php echo $ID_INDICAT?>();">
                                                        </td>
                                                    </tr>
                                </form>
                             </table>
                    </div>
                </div>
                <?php }?>
                
			










<?php
		sqlsrv_close($conn);
		sqlsrv_close($maestra);}
?>
                </td>
                </tr>
                </table>
        
        
        
        </td>
        </tr>
        </table>
</td>
</tr>
</table>
</body>
</html>

