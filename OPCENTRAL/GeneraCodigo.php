<?php include("session.inc");?>

<?php
		$PrinterSel=$_GET["PrinterSel"];
		$ID_OPERADOR=$_GET["IdOpera"];
		
		$FESTADO=@$_GET["FESTADO"];
		$BOPERA=trim(strtoupper(@$_GET["BOPERA"]));
		$BOPCION=@$_GET["BOPCION"];
		
		$SQL="SELECT * FROM OP_OPERADOR WHERE ID_OPERADOR=".$ID_OPERADOR;
		$RS = sqlsrv_query($conn, $SQL);
		if ($row = sqlsrv_fetch_array($RS)) {
				$NOMB_ACE=$row['NOMB_ACE'];
				$CC_OPERADOR=$row['CC_OPERADOR'];
				$DES_CLAVE=$row['COD_TIENDA'];
				$NOMB_OPERA=substr($NOMB_ACE."                     ", 0, 20);
		}
		//VERIFICAR SI OPERADOR YA CUENTA CON CODIGO
		$SQL="SELECT TOP 1 ESTADO FROM OP_CODSUPER WHERE ID_OPERADOR=$ID_OPERADOR ORDER BY ID_CODSUPER DESC";
		$RS = sqlsrv_query($conn, $SQL);
		if ($row = sqlsrv_fetch_array($RS)) {
				$ESTADO=$row['ESTADO'];
		}
		//LEYENDO ESTADOS

						//SI ESTADO ES 4, TIENE CODIGO ACTIVO, ENTONCES CAMBIAR ESTADO Y GENERAR NUEVO CÓDIGO
						if($ESTADO==4){
								$SQL="UPDATE OP_CODSUPER SET ESTADO=5 WHERE ID_CODSUPER=".$MAXCOD;
								$RS = sqlsrv_query($conn, $SQL);
						}
						//SI ESTADO ES 4, TIENE CODIGO CADUCO, GENERAR NUEVO CÓDIGO
								//GENERAR CODE128
										$PREFIJO=$PREFCODESUPER;
										//RELLENAR CUENTA
										$CUENTA=str_pad($CC_OPERADOR, 9, "0", STR_PAD_LEFT);
										//PASAR A UN ARREGLO
										$ARR_CUENTA = str_split($CUENTA);
										$LACUENTA = $ARR_CUENTA[6].$ARR_CUENTA[4].$ARR_CUENTA[2].$ARR_CUENTA[0].$ARR_CUENTA[7].$ARR_CUENTA[5].$ARR_CUENTA[3].$ARR_CUENTA[1].$ARR_CUENTA[8];
										if($SMO==1){ //SEGURIDAD MEJORADA
											$CLAVE0=substr(str_shuffle("1234567890"), 0, 1);
											$CLAVE1=substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1);
											$CLAVE2=substr(str_shuffle("1234567890"), 0, 1);
											$CLAVE3=substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1);
											$CLAVE4=substr(str_shuffle("1234567890"), 0, 1);
											$CLAVE5=substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1);
											$CLAVE6=substr(str_shuffle("1234567890"), 0, 1);
											$CLAVE7=substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1);
											$REGCLAVE = $CLAVE0.$CLAVE1.$CLAVE2.$CLAVE3.$CLAVE4.$CLAVE5.$CLAVE6.$CLAVE7;
											$CODCLAVE = $CLAVE2.$CLAVE5.$CLAVE0.$CLAVE3.$CLAVE6.$CLAVE1.$CLAVE4.$CLAVE7;
										} else {
											$CLAVE0=substr(str_shuffle("1234567890"), 0, 1);
											$CLAVE1=substr(str_shuffle("1234567890"), 0, 1);
											$CLAVE2=substr(str_shuffle("1234567890"), 0, 1);
											$CLAVE3=substr(str_shuffle("1234567890"), 0, 1);
											$CLAVE4=substr(str_shuffle("1234567890"), 0, 1);
											$CLAVE5=substr(str_shuffle("1234567890"), 0, 1);
											$CLAVE6=substr(str_shuffle("1234567890"), 0, 1);
											$CLAVE7=substr(str_shuffle("1234567890"), 0, 1);
											$REGCLAVE = $CLAVE0.$CLAVE1.$CLAVE2.$CLAVE3.$CLAVE4.$CLAVE5.$CLAVE6.$CLAVE7;
											$CODCLAVE = $CLAVE2.$CLAVE5.$CLAVE0.$CLAVE3.$CLAVE6.$CLAVE1.$CLAVE4.$CLAVE7;
										}
										$CODE128=$PREFCODESUPER.$LACUENTA.$CODCLAVE;
										
								$ESTADO=1;
								$SQL="INSERT INTO OP_CODSUPER (ID_OPERADOR, CODE128, CLAVE, ESTADO, IDREG) VALUES (".$ID_OPERADOR.", '".$CODE128."', '".$REGCLAVE."', ".$ESTADO.", ".$SESIDUSU.")";
								$RS = sqlsrv_query($conn, $SQL);
						
								//GENERAR ARCHIVO
								$EXTEN="000".$DES_CLAVE;
								$EXTEN=substr($EXTEN, -3); 

								$NOM_ARCHIVO="SUP".date("YmdHis").".".$EXTEN;

								$LN_PRINT="";
								//PRIMERA LINEA: IP DE IMPRESORA
								$SQL="SELECT DIR_IP FROM FLJ_PRINTER WHERE DEF_PRT=1 AND COD_TIENDA=".$DES_CLAVE;
								$RS = sqlsrv_query($eyes_conn, $SQL);
								if ($row = sqlsrv_fetch_array($RS)) {
										$DIR_IP=$row['DIR_IP'];
								}
								$LN_PRINT = $PrinterSel."\r\n";

											$LN_PRINT = $LN_PRINT.$CODE128;
											 $open = fopen("_arc_prt/".$NOM_ARCHIVO, "w+");
											 fwrite($open, $LN_PRINT.$NOMB_OPERA);
											 fclose($open);

								$local_file="_arc_prt/".$NOM_ARCHIVO;
								copy($local_file, $DIR_FLJ."IN/".$NOM_ARCHIVO);

?>
<script>
		function CheckStatusFour(IdOpera, Festado, Bopcion, Bopera){
					var dataString = 'CheckStatusFour=1&IdOpera='+IdOpera;
					$.ajax({
						type: "GET", url: "MsjeConsulta.php",
						data: dataString, 
						dataType: "json",
						success: function (response) {
							if(response === "CUATRO"){
								$("#VentanaConsulta").css('display', "none");
								window.location = 'reg_codeauto.php?MSJE=1&FESTADO='+Festado+'&BOPCION='+Bopcion+'&BOPERA='+Bopera;
							}
							if(response === "DOS"){
								$("#VentanaConsulta").css('display', "none");
								window.location = 'reg_codeauto.php?MSJE=3&FESTADO='+Festado+'&BOPCION='+Bopcion+'&BOPERA='+Bopera;
							}
						}
					});
		}
		function ResetCodAuth(IdOpera, Archivo, Festado, Bopcion, Bopera){
			<?php try{ ?>
			var dataString = 'ResetCodAuth=1&IdOpera='+IdOpera+'&Archivo='+Archivo;
					$.ajax({
						type: "GET", url: "MsjeConsulta.php",
						data: dataString, 
						dataType: "json",
						success: function (response) {
							if(response === "UNO"){
								$("#VentanaConsulta").css('display', "none");
								window.location = 'reg_codeauto.php?MSJE=3&FESTADO='+Festado+'&BOPCION='+Bopcion+'&BOPERA='+Bopera;
							}
						}
					});
			<?php }
			catch(Exception $e){
			}
			?>
		}
	

	
	
	
</script>

	<h3>Un momento por favor...<br>Generando C&oacute;digo</h3>
	<div style="display:block; margin:26px 0; width:100%; text-align:center">
		<img src="../images/Preload.GIF" />
	</div>

	<script>
	$(document).ready(function() {
				 var Limite = <?=$TEACS?>;
				 var Contador = 0;
				 var Intervalo = setInterval(function(){
						Contador++;
						if(Contador >= Limite){
							ResetCodAuth('<?=$ID_OPERADOR?>', '<?=$NOM_ARCHIVO?>', '<?=$FESTADO?>', '<?=$BOPCION?>', '<?=$BOPERA?>');
							clearInterval(Intervalo);
						} else {
							CheckStatusFour('<?=$ID_OPERADOR?>', '<?=$FESTADO?>', '<?=$BOPCION?>', '<?=$BOPERA?>');
						};
				 }, 1000);
	});
	</script>
