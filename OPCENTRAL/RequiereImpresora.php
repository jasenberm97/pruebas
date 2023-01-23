<?php include("session.inc");?>

<?php
$IdOpera = $_GET['IdOpera'];
$FESTADO = $_GET['FESTADO'];
$BOPCION = $_GET['BOPCION'];
$BOPERA = $_GET['BOPERA'];
?>
<script>
	$('#FrmSelPrinter').submit(function (ev) {
			var Printer = $("#SelPrinter").val();
			$("#DivSelectPrinter").load('GeneraCodigo.php?PrinterSel=' + Printer + '&IdOpera=<?=$IdOpera?>&FESTADO=<?= $FESTADO?>&BOPCION=<?= $BOPCION?>&BOPERA=<?= $BOPERA?>');
			ev.preventDefault();
	});
	
	function CloseSelPrinter(){
		$("#VentanaConsulta").css('display', "none");
		return false;
	}
</script>
<div id="DivSelectPrinter" style="display: block; padding: 10px; height: auto">
		<?php
		$SqlOpera="SELECT * FROM OP_OPERADOR WHERE ID_OPERADOR=$IdOpera";
		$RsOpera = sqlsrv_query($conn, $SqlOpera);
		if ($rowOp = sqlsrv_fetch_array($RsOpera)) {
			$COD_TIENDA = $rowOp['COD_TIENDA'];
			$NombOpe = $rowOp['NOMBRE'];
			$ApellPOpe = $rowOp['APELLIDO_P'];
			$ApellMOpe = $rowOp['APELLIDO_M'];
		}
		?>
		<h3>Local <?=substr("000".$COD_TIENDA, -3)?>: Seleccione Impresora d&oacute;nde se emitir&aacute; el C&oacute;digo de Autorizaci&oacute;n del Operador:<br><?=$NombOpe." ".$ApellPOpe." ".$ApellMOpe?></h3>
		<form id="FrmSelPrinter" action="GeneraCodigo.php" method="get">
				<select style="block; width: 100%; padding: 10px" id="SelPrinter" required>
					<option value="">SELECCIONAR IMPRESORA </option>
					<?php
					$SqlPrint="SELECT * FROM FLJ_PRINTER WHERE COD_TIENDA=$COD_TIENDA AND ESTADO=1";
					$RsPrint = sqlsrv_query($eyes_conn, $SqlPrint);
					while ($rowPr = sqlsrv_fetch_array($RsPrint)) {
						$DES_PRINTER = $rowPr['DES_PRINTER'];
						$DIR_IP = $rowPr['DIR_IP'];
						$DEF_PRT = $rowPr['DEF_PRT']; if($DEF_PRT == 1){$DES_PRINTER = $DES_PRINTER." (PRED)";}
						?>
						<option value="<?=$DIR_IP?>" <?php if($DEF_PRT==1){echo "SELECTED";}?>><?=$DES_PRINTER?></option>
					<?php
					}
					?>
				</select>
				<input style="float: right; margin: 20px 3px" type="button" id="CloseWin" value="SALIR" onClick="CloseSelPrinter();">
				<input style="float: right; margin: 20px 3px" type="submit" id="BtnPrinter" value="IMPRIME C&Oacute;DIGO">
		</form>
</div>