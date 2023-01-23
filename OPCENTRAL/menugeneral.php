<?php 
	$INGRESAR=0;
	$CONSULTA="SELECT IDACC FROM US_PERFACC WHERE IDPERFIL=".$SESIDPERFIL;
	$RS = sqlsrv_query($maestra, $CONSULTA);
	//oci_execute($RS);
	while ($row = sqlsrv_fetch_array($RS)){
		$IDACC_V = $row['IDACC'];
		if ((int)$IDACC_V==(int)$PAGINA) { 
			$INGRESAR=1;
		}
	}	
	if ($INGRESAR=0) {
			$CONSULTA="SELECT IDACC FROM US_PERFACC WHERE IDPERFIL=".$SESIDPERFIL." AND INGRESO=1";
			$RS = sqlsrv_query($maestra, $CONSULTA);
			//oci_execute($RS);
			if ($row = sqlsrv_fetch_array($RS)) {
				$IDACC4 = $row['IDACC'];
			}	
			$CONSULTA="SELECT ENLACE FROM US_ACCESO WHERE IDACC=".$IDACC4;
			$RS = sqlsrv_query($maestra, $CONSULTA);
			//oci_execute($RS);
			if ($row = sqlsrv_fetch_array($RS)) {
				$ENLACE4 = $row['ENLACE'];
			}	
			header("Location: ".$ENLACE4);
	}
	$LSUP=@$_GET["LSUP"];
	if ($LSUP=="") {
		$LSUP=$CTP;
	}
	$LINF=@$_GET["LINF"];
	if ($LINF=="") {
		$LINF=1;
	}
	$MSJE=@$_GET["MSJE"];
?>
<div style="width:200px; overflow:visible; background-color:#FFF"><table width="100%">
<?php 
$CONSULTA="SELECT * FROM US_ACCESO WHERE IDACC IN(SELECT IDACC FROM US_PERFACC WHERE IDPERFIL=".$SESIDPERFIL.") ORDER BY TIPO ASC, NOMBRE ASC";
$RS = sqlsrv_query($maestra, $CONSULTA);
//oci_execute($RS);
$TIPO2=0;
$CUENTA_M=0;
while ($row = sqlsrv_fetch_array($RS)){
		$TIPO =$row['TIPO'];
						if ($TIPO==1) {
							$ELTIPO="Gesti&oacute;n"; }
						if ($TIPO==2) {
							$ELTIPO="Operaci&oacute;n"; }
						if ($TIPO==3) {
							$ELTIPO="Par&aacute;metros"; }
						if ($TIPO==4) {
							$ELTIPO="Mantenedores"; }
						if ($TIPO==5) {
							$ELTIPO="Webmaster"; }
		$IDACC = $row['IDACC'];
		$ENLACE = $row['ENLACE'];
		$NOMBRE_ENLACE = $row['NOMBRE'];
		 if ($TIPO2<>$TIPO) {
			$TIPO2=$TIPO;
			if ($CUENTA_M>0) {
?>
<tr><td><img src="../images/separador.png" width="100%" height="2" /></td></tr>
<?php } } if ((int)$PAGINA<>(int)$IDACC) { ?>
<tr><td onmouseover='this.style.background="#F1F1F1"' onmouseout='this.style.background="#FFFFFF"' class="menu" onclick="pagina('<?php echo $ENLACE?>');"><a href="<?php echo $ENLACE?>"><?php echo $NOMBRE_ENLACE?></a></td></tr>
<?php } else {?>
<tr><td class="menuActivo" onclick="pagina('<?php echo $ENLACE?>');"><a href="<?php echo $ENLACE?>"><?php echo $NOMBRE_ENLACE?></a></td></tr>
<?php } $CUENTA_M=$CUENTA_M+1;}?>
<tr><td align="center" valign="top" style="padding:20px 10px 40px 10px; background:url(<?=$LogoMnGeneral?>) no-repeat center center"><img src="../images/transpa.png" width="100px" height="120px"  /></td></tr>
</table></div><img src="../images/Transpa.png" alt="" width="200px" height="1px" />