
<?php include("session.inc");?>
<?php include("headerhtml.inc");?>
<?php
	$PAGINA=1130;
	$NOMENU=1;


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


} //validaingreso(theForm)


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
$ELMSJ="Nombre no disponible, verifique";
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
				
				$CONSULTA="SELECT COUNT(*) AS CUENTA FROM OP_INDICAT";
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

				//$CONSULTA="SELECT * FROM (SELECT  a.*, ROWNUM rnum FROM (SELECT * FROM OP_INDICAT ORDER BY INDICAT ASC) a WHERE ROWNUM <= ".$LSUP.") WHERE rnum >=  ".$LINF;
				//oci_execute($RS);

                  $CONSULTA= "SELECT * FROM (SELECT *,ROW_NUMBER() OVER (PARTITION BY ".$CTP." ORDER BY INDICAT ASC) ROWNUMBER FROM OP_INDICAT) AS TABLEWITHROWNUMBER WHERE ROWNUMBER BETWEEN ".$LINF." AND ".$LSUP."";
                  $RS = sqlsrv_query($conn, $CONSULTA);

               ?>
                <table id="Listado">
                <tr>
                    <th>Indicat</span></th>
                    <th>Nombre/Bit Opciones</span></th>
                    <th>Name/Bit Options</span></th>
                    <th>Registrado por</span></th>
                </tr>
                <?php
				while ($row = sqlsrv_fetch_array($RS)){
                        $ID_INDICAT = $row['ID_INDICAT'];
                        $INDICAT = $row['INDICAT'];
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
						$CONSULTA2="SELECT * FROM OP_INDICATOPC WHERE ID_INDICAT=".$ID_INDICAT." ORDER BY BITPOS DESC";
						$RS2 = sqlsrv_query($conn, $CONSULTA2);
						//oci_execute($RS2);
						$OPC_DESCRIP_ES="";
						$OPC_DESCRIP_EN="";

						while ($row = sqlsrv_fetch_array($RS2)) {
							$OPC_BITPOS = substr("0".$row['BITPOS'], -2);
							
							$OPC_DESCRIP_ES = $OPC_DESCRIP_ES."<BR>".$OPC_BITPOS." :: ".$row['DESCRIP_ES'];
							$OPC_DESCRIP_EN = $OPC_DESCRIP_EN."<BR>".$OPC_BITPOS." :: ".$row['DESCRIP_EN'];
						}	
               ?>
                <tr>
                    <?php if($SESPUBLICA==1) { ?>
                    <td><a href="mant_autoriza.php?ACT=<?php echo $ID_INDICAT?>"><?php echo "Indicat".$INDICAT?></a></td>
                    <?php } else {?>
                     <td><span style="font-size:12pt"><?php echo "Indicat".$INDICAT?></span></td>
                    <?php } ?>
                    <td><h8><?php echo $DESCRIP_ES?></h8><?php echo $OPC_DESCRIP_ES?></td>
                    <td><span style="font-size:12pt"><?php echo $DESCRIP_EN?></span><?php echo $OPC_DESCRIP_EN?></td>
                    <td><?php echo $QUIENFUE.", ".date_format($FECHA,"d-m-Y");?></td>
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
                    <input name="ANTERIOR" type="button" value="Anterior"  onClick="pagina('mant_autoriza.php?LSUP=<?php echo $FILA_ANT?>&LINF=<?php echo $ATRAS?>');">
                    <?php
                    }
                    if ($LSUP<=$TOTALREG) {
						$ADELANTE=$LSUP+1;
						$FILA_POS=$LSUP+$CTP;
                   ?>
                    <input name="SIGUIENTE" type="button" value="Siguiente" onClick="pagina('mant_autoriza.php?LSUP=<?php echo $FILA_POS?>&LINF=<?php echo $ADELANTE?>');">
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
               
               
               
               
			<?php  if ($ACT<>"") { 
				$CONSULTA="SELECT * FROM OP_INDICAT WHERE ID_INDICAT=".$ACT;
				$RS = sqlsrv_query($conn, $CONSULTA);
				//oci_execute($RS);
				if ($row = sqlsrv_fetch_array($RS)) {
					$ID_INDICAT = $row['ID_INDICAT'];
					$DESCRIP_ES = $row['DESCRIP_ES'];
					$DESCRIP_EN = $row['DESCRIP_EN'];
                }

			$ACTBIT=@$_GET["ACTBIT"];

               ?>
                <h3>Actualizar: Indicat<?php echo $ACT?></h3>
                <table id="forma-registro">
                    <form action="mant_autoriza_reg.php" method="post" name="formact" onSubmit="return validaingreso(this)">
                    <tr>
                            <td><label for="DESCRIP_ES">Descripción en Espa&ntilde;ol </label></td>
                        <td><input name="DESCRIP_ES" type="text" size="40" maxlength="200" value="<?php echo $DESCRIP_ES?>"></td>
                    </tr>
                    <tr>
                            <td><label for="DESCRIP_EN">Descripción en Ingl&eacute;s  </label></td>
                        <td><input name="DESCRIP_EN" type="text" size="40" maxlength="200" value="<?php echo $DESCRIP_EN?>"></td>
                    </tr>
                    <tr>
                        <td><input name="ID_INDICAT" type="hidden" value="<?php echo $ID_INDICAT?>">
                        <td>
                        <input name="ACTUALIZAR" type="submit" value="Actualizar">
                        <input name="SALIR" type="button" value="Salir" onClick="javascript:pagina('mant_autoriza.php')">
                        </td>
                    </tr>
                    </form>
                </table>
                <?php  if ($ACTBIT<>"") {
					
				$CONSULTA="SELECT * FROM OP_INDICATOPC WHERE ID_INDICATOPC=".$ACTBIT;
				$RS = sqlsrv_query($conn, $CONSULTA);
				//oci_execute($RS);
				if ($row = sqlsrv_fetch_array($RS)) {
					$ID_INDICATOPC = $row['ID_INDICATOPC'];
					$DESCRIP_ES = $row['DESCRIP_ES'];
					$DESCRIP_EN = $row['DESCRIP_EN'];
                }

				?>
                <h3>Actualizar: Bit<?php echo $ACTBIT?> Indicat<?php echo $ACT?></h3>
                <table id="forma-registro">
                    <form action="mant_autoriza_reg.php" method="post" name="formact" onSubmit="return validaingreso(this)">
                    <tr>
                            <td><label for="DESCRIP_ES">Descripción en Espa&ntilde;ol </label></td>
                        <td><input name="DESCRIP_ES" type="text" size="40" maxlength="200" value="<?php echo $DESCRIP_ES?>"></td>
                    </tr>
                    <tr>
                            <td><label for="DESCRIP_EN">Descripción en Ingl&eacute;s  </label></td>
                        <td><input name="DESCRIP_EN" type="text" size="40" maxlength="200" value="<?php echo $DESCRIP_EN?>"></td>
                    </tr>
                    <tr>
                        <td>
                        <input name="ACT" type="hidden" value="<?php echo $ACT?>">
                        <input name="ACTBIT" type="hidden" value="<?php echo $ACTBIT?>">
                        <td>
                        <input name="ACTUALIZA_BIT" type="submit" value="Actualizar Bit">
                        <input name="SALIR" type="button" value="Salir" onClick="javascript:pagina('mant_autoriza.php?ACT=<?php echo $ACT?>')">
                        </td>
                    </tr>
                    </form>
                </table>
				<?php } ?>


                <h3>Listado de  Bits de Autorización para Indicat<?php echo $ACT?></h3>
                            
                            
                            <?php
                            
                            $CONSULTA="SELECT * FROM OP_INDICATOPC WHERE ID_INDICAT=".$ACT." ORDER BY BITPOS DESC ";
                            $RS = sqlsrv_query($conn, $CONSULTA);
                            //oci_execute($RS);
                           ?>
                            <table id="Listado">
                            <tr>
                                <th>Bit Pos.</span></th>
                                <th>Nombre</span></th>
                                <th>Name</span></th>
                                <th>Registrado por</span></th>
                            </tr>
                            <?php
                            while ($row = sqlsrv_fetch_array($RS)){
                                    $ID_INDICATOPC = $row['ID_INDICATOPC'];
                                    $DESCRIP_ES = $row['DESCRIP_ES'];
                                    $DESCRIP_EN = $row['DESCRIP_EN'];
                                    $BITPOS = $row['BITPOS'];
									$DES_BITPOS = substr("0".$BITPOS, -2);
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
                                <td><a href="mant_autoriza.php?ACT=<?php echo $ID_INDICAT?>&ACTBIT=<?php echo $ID_INDICATOPC?>"><?php echo $DES_BITPOS?></a></td>
                                <?php } else {?>
                                 <td><?php echo $ID_INDICAT?></td>
                                <?php } ?>
                                <td><?php echo $DESCRIP_ES?></td>
                                <td><?php echo $DESCRIP_EN?></td>
                                <td><?php echo $QUIENFUE." el ".date_format($FECHA,"d-m-Y"); ?></td>
                            </tr>
                            <?php
                            }
                            ?>
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
        </table>
</td>
</tr>
</table>
</body>
</html>

