
<?php include("session.inc");?>
<?php include("headerhtml.inc");?>
<?php
	$PAGINA=1128;


	$LIST=@$_GET["LIST"];
	$NEO=@$_GET["NEO"];
	$ACT=@$_GET["ACT"];
	
	if ($NEO=="" and $ACT=="") {
		 $LIST=1;
	}
?>

<?php if ($LIST<>1) {?>
<script language="JavaScript">
function validaingreso(theForm){
	
		if (theForm.DESCRIP_ES.value == ""){
			alert("COMPLETE EL CAMPO REQUERIDO.");
			theForm.DESCRIP_ES.focus();
			return false;
	}

		if (theForm.DESCRIP_EN.value == ""){
			alert("COMPLETE EL CAMPO REQUERIDO.");
			theForm.DESCRIP_EN.focus();
			return false;
	}

		if (theForm.POSICION.value == ""){
			alert("COMPLETE EL CAMPO REQUERIDO.");
			theForm.POSICION.focus();
			return false;
	}


} //validaingreso(theForm)

function validaAgregar(theForm){
	
		if (theForm.ID_INDICAT.value == 99){
			alert("COMPLETE EL CAMPO REQUERIDO.");
			theForm.ID_INDICAT.focus();
			return false;
	}

		if (theForm.ID_INDICATOPC.value == 0){
			alert("COMPLETE EL CAMPO REQUERIDO.");
			theForm.ID_INDICATOPC.focus();
			return false;
	}

} //validaAgregar(theForm)


</script>
<?php }?>
</head>

<body>

<?php include("../headerregusu.php");?>
<?php include("titulo_menu.php");?>
<table width="100%" height="100%">
<tr>
<td align="right"  width="200" bgcolor="#FFFFFF"><?php include("menugeneral.php");?></td> 
<td >
<?php
					if ($MSJE==1) {
							$ELMSJ="Registro actualizado";
						} 
					if ($MSJE == 2) {
							$ELMSJ="Opci&oacute;n no disponible, verifique";
						} 
					if ($MSJE == 3) {
							$ELMSJ="Registro realizado";
					}
					if ($MSJE == 4) {
							$ELMSJ="Registro eliminado";
					}
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
                <h2><?php echo $LAPAGINA?></h2>
                
                
                <?php
				
				$CONSULTA="SELECT COUNT(*) AS CUENTA FROM OP_INDICATMNO";
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

				//$CONSULTA="SELECT * FROM (SELECT  a.*, ROWNUM rnum FROM (SELECT * FROM OP_INDICATMNO ORDER BY ID_INDICATMNO ASC) a WHERE ROWNUM <= ".$LSUP.") WHERE rnum >=  ".$LINF;
				//$RS = sqlsrv_query($conn, $CONSULTA);
				//oci_execute($RS);

                $CONSULTA= "SELECT  FROM (SELECT *,ROW_NUMBER() OVER (PARTITION BY ".$CTP." ORDER BY ID_INDICATMNO ASC) ROWNUMBER FROM OP_INDICATMNO WHERE ROWNUM  AS TABLEWITHROWNUMBER WHERE ROWNUMBER BETWEEN ".$LINF." AND ".$LSUP."";
               ?>
                <table id="Listado">
                <tr>
                    <td style="background-color:transparent; vertical-align:bottom"><span class="txtBold">Posici&oacute;n</span></td>
                    <td style="background-color:transparent; vertical-align:bottom"><span class="txtBold">Nombre</span></td>
                    <td style="background-color:transparent; vertical-align:bottom"><span class="txtBold">Name</span></td>
                    <td style="background-color:transparent; vertical-align:bottom"><span class="txtBold">Registrado por</span></td>
                </tr>
                <?php
				while ($row = sqlsrv_fetch_array($RS)){
                        $ID_INDICATMNO = $row['ID_INDICATMNO'];
                        $DESCRIP_ES = $row['DESCRIP_ES'];
                        $DESCRIP_EN = $row['DESCRIP_EN'];
                        $IDREG = $row['IDREG'];
                        $FECHA = $row['FECHA'];
						$CONSULTA2="SELECT NOMBRE FROM US_USUARIOS WHERE IDUSU=".$IDREG;
						$RS2 = sqlsrv_query($maestra, $CONSULTA2);
						//oci_execute($RS2);
						if ($row = sqlsrv_fetch_array($RS2)) {
							$QUIENFUE = $row['NOMBRE'];
						}	
						$CONSULTA2="SELECT * FROM OP_INDICATMNOOPC WHERE ID_INDICATMNO=".$ID_INDICATMNO." ORDER BY ID_INDICATOPC ASC";
						$RS2 = sqlsrv_query($conn, $CONSULTA2);
						//oci_execute($RS2);
						$OPC_DESCRIP_ES = "";
						$OPC_DESCRIP_EN="";
						while ($row = sqlsrv_fetch_array($RS2)) {
							$ID_INDICATOPC = $row['ID_INDICATOPC'];
							$CONSULTA3="SELECT * FROM OP_INDICATOPC WHERE ID_INDICATOPC=".$ID_INDICATOPC;
							$RS3 = sqlsrv_query($conn, $CONSULTA3);
							//oci_execute($RS3);
							if ($row = sqlsrv_fetch_array($RS3)) {
									$OPC_DESCRIP_ES = $OPC_DESCRIP_ES."<BR>".$row['DESCRIP_ES'];
									$OPC_DESCRIP_EN = $OPC_DESCRIP_EN."<BR>".$row['DESCRIP_EN'];
							}
						}	
               ?>
                <tr>
                    <?php if($SESPUBLICA==1) { ?>
                    <td style="background:#F5F9FC; text-align:right" onmouseover='this.style.background="#FFF"' onmouseout='this.style.background="#F5F9FC"'><a style="font-size:12pt" href="mant_menuautoriza.php?ACT=<?php echo $ID_INDICATMNO?>"><?php echo $ID_INDICATMNO?></a></td>
                    <?php } else {?>
                     <td style="background:#F5F9FC; text-align:right" ><span style="font-size:12pt"><?php echo $ID_INDICATMNO?></span></td>
                    <?php } ?>
                    <td><span style="font-size:12pt"><?php echo $DESCRIP_ES?></span><?php echo $OPC_DESCRIP_ES?></td>
                    <td><span style="font-size:12pt"><?php echo $DESCRIP_EN?></span><?php echo $OPC_DESCRIP_EN?></td>
                    <td><?php echo $QUIENFUE." el ".$FECHA?></td>
                </tr>
                <?php
				}
				?>
                <tr>
                    <td colspan="4" nowrap style="background-color:transparent">
                    <?php
                    if ($LINF>=$CTP+1) {
						$ATRAS=$LINF-$CTP;
						$FILA_ANT=$LSUP-$CTP;
                   ?>
                    <input name="ANTERIOR" type="button" value="Anterior"  onClick="pagina('mant_menuautoriza.php?LSUP=<?php echo $FILA_ANT?>&LINF=<?php echo $ATRAS?>');">
                    <?php
                    }
                    if ($LSUP<=$TOTALREG) {
						$ADELANTE=$LSUP+1;
						$FILA_POS=$LSUP+$CTP;
                   ?>
                    <input name="SIGUIENTE" type="button" value="Siguiente" onClick="pagina('mant_menuautoriza.php?LSUP=<?php echo $FILA_POS?>&LINF=<?php echo $ADELANTE?>');">
                    <?php }?>
                    <span style="vertical-align:baseline;">P&aacute;gina <?php echo $NUMPAG?> de <?php echo $NUMTPAG?></span>
                    </td>
                </tr>
                </table>
<?php
		sqlsrv_close($conn);
		sqlsrv_close($maestra);
}
?>
               
               
               
               
<?php  if ($NEO<>"") { ?>
                <h2>Nuevo registro</h2>
                <table id="forma-registro">
                    <form action="mant_menuautoriza_reg.php" method="post" name="formact" onSubmit="return validaingreso(this)">
                    <tr>
                            <td><label for="DESCRIP_ES">Descripción en Espa&ntilde;ol </label></td>
                        <td><input name="DESCRIP_ES" type="text" size="40" maxlength="200"></td>
                    </tr>
                    <tr>
                            <td><label for="DESCRIP_EN">Descripción en Ingl&eacute;s  </label></td>
                        <td><input name="DESCRIP_EN" type="text" size="40" maxlength="200"></td>
                    </tr>
                    <tr>
                            <td><label for="POSICION">Posici&oacute;n en el Men&uacute;  </label></td>
                        <td><input name="POSICION" type="text" size="2" maxlength="2"  onKeyPress="return acceptNum(event);"></td>
                    </tr>
                    <tr>
                        <td>
                        <td>
                        <input name="INGRESAR" type="submit" value="Registrar">
                        <input name="SALIR" type="button" value="Salir" onClick="javascript:pagina('mant_menuautoriza.php')">
                        </td>
                    </tr>
                    </form>
                </table>

<?php
		sqlsrv_close($conn);
		sqlsrv_close($maestra);
}
?>
               
               
               
			<?php  if ($ACT<>"") { 
				$CONSULTA="SELECT * FROM OP_INDICATMNO WHERE ID_INDICATMNO=".$ACT;
				$RS = sqlsrv_query($conn, $CONSULTA);
				//oci_execute($RS);
				if ($row = sqlsrv_fetch_array($RS)) {
					$ID_INDICATMNO = $row['ID_INDICATMNO'];
					$DESCRIP_ES = $row['DESCRIP_ES'];
					$DESCRIP_EN = $row['DESCRIP_EN'];
					$OPC_MENU = $DESCRIP_ES."/".$DESCRIP_EN;
                }

			$ACTOPC=@$_GET["ACTOPC"];

               ?>
                <h2>Actualizar Menú<BR><?php echo $OPC_MENU?></h2>
                <table id="forma-registro">
                    <form action="mant_menuautoriza_reg.php" method="post" name="formact" onSubmit="return validaingreso(this)">
                    <tr>
                            <td><label for="DESCRIP_ES">Descripción en Espa&ntilde;ol </label></td>
                        <td><input name="DESCRIP_ES" type="text" size="40" maxlength="200" value="<?php echo $DESCRIP_ES?>"></td>
                    </tr>
                    <tr>
                            <td><label for="DESCRIP_EN">Descripción en Ingl&eacute;s  </label></td>
                        <td><input name="DESCRIP_EN" type="text" size="40" maxlength="200" value="<?php echo $DESCRIP_EN?>"></td>
                    </tr>
                    <tr>
                        <td><input name="ID_INDICATMNO" type="hidden" value="<?php echo $ID_INDICATMNO?>">
                        <td>
                        <input name="ACTUALIZAR" type="submit" value="Actualizar">
                        <input name="SALIR" type="button" value="Salir" onClick="javascript:pagina('mant_menuautoriza.php')">
                        </td>
                    </tr>
                    </form>
                </table>
                            <h2 style="margin-top:20px">Listado de  Opciones de Autorización para Menú<BR><?php echo $OPC_MENU?></h2>
                            
                            
                            <?php
                            
                            $CONSULTA="SELECT * FROM OP_INDICATMNOOPC WHERE ID_INDICATMNO=".$ACT." ORDER BY ID_INDICATOPC DESC ";
                            $RS = sqlsrv_query($conn, $CONSULTA);
                            //oci_execute($RS);
                           ?>
                            <table id="Listado">
                            <tr>
                                <td style="background-color:transparent; vertical-align:bottom"><span class="txtBold">Opción de Autorización</span></td>
                                <td style="background-color:transparent; vertical-align:bottom"><span class="txtBold">Indicat</span></td>
                                <td colspan="2" style="background-color:transparent; vertical-align:bottom"><span class="txtBold">Registrado por</span></td>
                            </tr>
                            <?php
                            while ($row = sqlsrv_fetch_array($RS)){
                                    $ID_INDICATOPC = $row['ID_INDICATOPC'];
												$CONSULTA2="SELECT * FROM OP_INDICATOPC WHERE ID_INDICATOPC=".$ID_INDICATOPC;
												$RS2 = sqlsrv_query($conn, $CONSULTA2);
												//oci_execute($RS2);
												if ($row = sqlsrv_fetch_array($RS2)) {
													$OPC_DESCRIP_ES = $row['DESCRIP_ES'];
													$OPC_DESCRIP_EN = $row['DESCRIP_EN'];
													$ID_INDICAT = $row['ID_INDICAT'];
													$OPCION_AUTORIZA=$OPC_DESCRIP_ES."<BR>".$OPC_DESCRIP_EN;
												}	
												$CONSULTA2="SELECT * FROM OP_INDICAT WHERE ID_INDICAT=".$ID_INDICAT;
												$RS2 = sqlsrv_query($conn, $CONSULTA2);
												//oci_execute($RS2);
												if ($row = sqlsrv_fetch_array($RS2)) {
													$IND_DESCRIP_ES = $row['DESCRIP_ES'];
													$IND_DESCRIP_EN = $row['DESCRIP_EN'];
													$INDICAT_AUTORIZA=$IND_DESCRIP_ES."<BR>".$IND_DESCRIP_EN;
												}	
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
                                <td><?php echo $OPCION_AUTORIZA?></td>
                                <td><?php echo $INDICAT_AUTORIZA?></td>
                                <td><?php echo $QUIENFUE." el ".$FECHA?></td>
                                <td><input type="button" value="Retirar" onClick="pagina('mant_menuautoriza_reg.php?RETIRAR=1&ID_INDICATOPC=<?php echo $ID_INDICATOPC;?>&ID_INDICATMNO=<?php echo $ACT;?>');"></td>
                            </tr>
                            <?php
                            }
                            ?>
                            </table>

                            <h3 style="margin-top:20px">Agregar  Opci&oacute;n de Autorización para Menú<BR><?php echo $OPC_MENU?></h3>

                        <table id="forma-registro">
                            <form action="mant_menuautoriza_reg.php" method="post" name="forming" onSubmit="return validaAgregar(this)">
                            <tr>
                                    <td><label for="ID_INDICAT">Indicat </label></td>
                                <td><select name="ID_INDICAT"  onChange="Carga_OpcionIndicat(this.value)">
                            <option value="99">SELECCIONAR</option>
                            <?php
                                $S1="SELECT * FROM OP_INDICAT ORDER BY ID_INDICAT ASC";
                                $RS1 = sqlsrv_query($conn, $S1);
                                //oci_execute($RS1);
                                while ($row = sqlsrv_fetch_array($RS1)) {
                                    $ID_INDICAT = $row['ID_INDICAT'];
                                    $DESCRIP_ES = $row['DESCRIP_ES'];
                                    $DESCRIP_EN = $row['DESCRIP_EN'];
                            ?>
                            <option value="<?php echo $ID_INDICAT?>"><?php echo $DESCRIP_ES."/".$DESCRIP_EN?></option>
                            <?php
                                }
                            ?>
                            </select></td>
                            </tr>
                            <tr>
                                    <td><label for="ID_INDICATOPC">Opci&oacute;n de Autorizaci&oacute;n  </label></td>
                                <td><select id="ID_INDICATOPC" name="ID_INDICATOPC">
                           		<option value="0">SELECCIONAR</option>
                            </select></td>
                            </tr>
                            <tr>
                                <td><input name="ID_INDICATMNO" type="hidden" value="<?php echo $ID_INDICATMNO?>">
                                <td>
                                <input name="AGREGAR" type="submit" value="Agregar">
                                <input name="SALIR" type="button" value="Salir" onClick="javascript:pagina('mant_menuautoriza.php')">
                                </td>
                            </tr>
                            </form>
                        </table>
<?php
		sqlsrv_close($conn);
		sqlsrv_close($maestra);
}
?>
                </td>
                </tr>
                </table>
        
        
        
        </td>
        </tr>
        <tr>
        <td>
        <iframe name="frmHIDEN" width="0%" height="0" frameborder="0" align="top" src="" framespacing="0" marginheight="0" marginwidth="0">
        </iframe>
        </td>
        </tr>  
        </table>
</td>
</tr>
</table>
</body>
</html>

