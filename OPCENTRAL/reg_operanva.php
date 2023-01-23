			<?php
			if($ACT_NVA==1){ //NIVEL DE AUTORIZACION

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

                <p class="speech" style="color:<?php echo $TDESTADO?>"><?php echo $ELESTADO?></p>
                <h3>Data Operador: <?php echo $NOMBRE." ".$APELLIDO_P." ".$APELLIDO_M?><br>Niveles de Autorizaci&oacute;n</h3>

                        <div style="text-align:right">
                        		<form action="reg_operador.php?ACT=<?php echo $ACT?>&ACT_NVA=1" method="post">
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
				$CONSULTA="SELECT NIVEL_AUT FROM OP_OPERADOR WHERE ID_OPERADOR=".$ID_OPERADOR;
				$RS = sqlsrv_query($conn, $CONSULTA);
				//oci_execute($RS);
				while ($row = sqlsrv_fetch_array($RS)) {
					$NIVEL_AUT = $row['NIVEL_AUT'];
				}
	
				?>
                <div style="display:block; width:auto; height:34px;text-align:left; padding:10px 20px; border: 1px solid #DFDFDF;-khtml-border-radius: 4px;-moz-border-radius: 4px;-webkit-border-radius: 4px;border-radius: 4px; ">

                        <form action="reg_operador_reg.php" method="post" name="formactNVA" id="formactNVA">
                            <label style="display:inline; float:left; font-weight:600; line-height:9pt; text-align:right; padding-top:6px"><?php echo $DESCRIP_NVA;?><br />( 0 - 9 )</label>
                            <input style="margin:4px; display:inline; float:left; text-align:right" name="NIVEL_AUT" type="text" size="3" maxlength="1"  onKeyPress="return acceptNum(event);" value="<?php echo $NIVEL_AUT?>"> 
                            <input style="display:inline; float:right; margin-left:10px" type="submit" name="REGNVAUOP" id="REGNVAUOP" value="Registrar">
                            <input type="hidden" name="ID_OPERADOR" id="ID_OPERADOR" value="<?php echo $ID_OPERADOR?>">
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
					 function ActivarNvaAuth<?php echo $ID_ENHSEC?>(){
							var contenedor = document.getElementById("RegNvcAuth<?php echo $ID_ENHSEC?>");
							contenedor.style.display = "block";
							window.scrollTo(0,0);
							return true;
						}
						
					 function CerrarNvaAuth<?php echo $ID_ENHSEC?>(){
							var contenedor = document.getElementById("RegNvcAuth<?php echo $ID_ENHSEC?>");
							contenedor.style.display = "none";
							return true;
						}
					 
					 function AlertaNvaAuth<?php echo $ID_ENHSEC?>(){
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
						//VERIFICAR SI DEPENDENCIA SE ENCUENTRA EN ESTADO "Y" EN OP_OPERANVA
						$SQLDEP="SELECT VALUE FROM OP_OPERANVA WHERE ID_NVLAUTO=".$MNVL_DEP." AND ID_OPERADOR=".$ID_OPERADOR;
						$RSDP = sqlsrv_query($conn, $SQLDEP);
						//oci_execute($RSDP);
						if ($rowDep = sqlsrv_fetch_array($RSDP)) {
								$ACTIVAR_MN=$rowDep['VALUE'];
						}
				}
				
				?>
                
                <input type="button" style="display:block; width:100%; text-align:left; margin:2px 0; padding-left:<?php echo $PADD_BTNVL;?>" value="<?php echo utf8_decode($DESCRIP_IND) ?>"  <?php if($ACTIVAR_MN=="Y"){ echo " onClick='ActivarNvaAuth".$ID_ENHSEC."();' "; } else { echo " onClick='AlertaNvaAuth".$ID_ENHSEC."();' "; } ?>>

                <style>
					#RegNvcAuth<?php echo $ID_ENHSEC?> {position:absolute;width:100%;height:300%;margin: 0 auto;left: 0;top:0;background-image: url(../images/TranspaBlack72.png);background-repeat: repeat;background-position: left top;z-index:10000;}
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
                <div id="RegNvcAuth<?php echo $ID_ENHSEC?>" style="display:none">
                    <div id="RegNvcAuth-contenedor<?php echo $ID_ENHSEC ?>">

                            <span style="position:absolute; top:0; right:20px;">
                            <img src="../images/ICO_Close.png" border="0" onClick="CerrarNvaAuth<?php echo $ID_ENHSEC?>();" title="Cerrar ventana">
                            </span>
                            <h3>Niveles de Autorizaci&oacute;n<br><?php echo $DESCRIP_IND?></h3>
                            <table id="Listado" width="100%">
                                <form action="reg_operador_reg.php" method="post" name="formact<?php echo $ID_ENHSEC?>" id="formact<?php echo $ID_ENHSEC?>">
                                                <tr>
                                                    <th width="10px">
                                                            <script>
                                                                function MarcarCheck<?php echo $ID_ENHSEC?>(val){ 
                                                                   for (i=0;i<document.formact<?php echo $ID_ENHSEC?>.elements.length;i++) 
                                                                      if(document.formact<?php echo $ID_ENHSEC?>.elements[i].type == "checkbox")	
                                                                        if(document.formact<?php echo $ID_ENHSEC?>.SELCHECKBOX<?php echo $ID_ENHSEC?>.checked == true) {
                                                                             document.formact<?php echo $ID_ENHSEC?>.elements[i].checked=1;
                                                                        } else {
                                                                             document.formact<?php echo $ID_ENHSEC?>.elements[i].checked=0;
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
																if($ID_ENHSEC==15) {$TablaOpera="OP_OPERAUDF"; }
																if($ID_ENHSEC<=14) {$TablaOpera="OP_OPERANVA"; }
																		$CONSULTA2="SELECT VALUE FROM ".$TablaOpera." WHERE ID_OPERADOR=".$ID_OPERADOR." AND ID_NVLAUTO=".$ID_NVLAUTO;
																		$RS2 = sqlsrv_query($conn, $CONSULTA2);
																		//oci_execute($RS2);
																		if ($row2 = sqlsrv_fetch_array($RS2)) {
																				$IND_ACTIVO = $row2['VALUE'];
																		}
																		if($IND_ACTIVO=="Y") {
																				$TDCOLOR=" style='background:#FFF3E0' ";
																		} else {
																				$TDCOLOR="";
																		}
															} else {
																		$CONSULTA2="SELECT NVA_GRUPO, NVA_USUARIO FROM OP_OPERADOR WHERE ID_OPERADOR=".$ID_OPERADOR;
																		$RS2 = sqlsrv_query($conn, $CONSULTA2);
																		//oci_execute($RS2);
																		if ($row2 = sqlsrv_fetch_array($RS2)) {
																				$NVA_GRUPO = $row2['NVA_GRUPO'];
																				$NVA_USUARIO = $row2['NVA_USUARIO'];
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
                                                                        <tr<?=$TDCOLOR;?>>
                                                                            <td width="10px"><input type="checkbox" id="NVA<?php echo $ID_NVLAUTO;?>" name="NVA<?php echo $ID_NVLAUTO;?>" value="Y"  <?php if($DEPENDIENTE<>0) { ?> onClick="ActivaDependiente<?php echo $ID_NVLAUTO?>();"<?php }  if($IND_ACTIVO=="Y") { echo "checked"; } if($NVL_DEP<>0 && $IND_ACTIVO=="N"){ echo "disabled"; }?> ></td>
                                                                            <td><?php echo utf8_decode($DESCRIP_NVA);?></td>
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
                                                                <input type="hidden" name="ID_OPERADOR" id="ID_OPERADOR" value="<?php echo $ID_OPERADOR?>">
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
														}	else {
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
                <?php }?>
                
                
                
			<?php
            } //FIN NIVEL DE AUTORIZACION
			
			
