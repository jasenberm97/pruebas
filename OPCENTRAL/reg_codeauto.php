
<?php include("session.inc"); ?>
<?php include("headerhtml.inc"); ?>
<?php
$PAGINA = 1174;
$NOMENU = 1;

function Format_nombre($nombre){
    $largo = strlen($nombre);
    if($largo == 1){
        $largo = '0000000'.$nombre;
        return $largo;
    }
     if($largo == 2){
        $largo = '000000'.$nombre;
        return $largo;
    }
     if($largo == 3){
        $largo = '00000'.$nombre;
        return $largo;
    }
     if($largo == 4){
        $largo = '0000'.$nombre;
        return $largo;
    }
     if($largo == 5){
        $largo = '000'.$nombre;
        return $largo;
    }
     if($largo == 6){
        $largo = '00'.$nombre;
        return $largo;
    }
    if($largo == 7){
        $largo = '0'.$nombre;
        return $largo;
    }
    $largo = $nombre;
    return $largo;
}


function create_file($TIENDA, $guardar, $nombre) {
    
    $nombre = Format_nombre($nombre);
        if (file_exists('../../allc_dat/in/'.$TIENDA.'/codopera/')) {
            //echo "directorio Existente";
            $fp = fopen('../../allc_dat/in/'.$TIENDA.'/codopera/'.$nombre.'.'.$TIENDA, 'w+');
            try{
				if(!$fp){
						throw new Exception('File open failed.');
					}
					fwrite($fp, $guardar);
				}
				catch(Exception $e){}
				finally{
					fclose($fp);
				}
            return "Exportacion exitosa";
        } else {
            //echo "no existe y se crea la carpeta";
			try{				
				mkdir("../../allc_dat/in/".$TIENDA."/codopera", 0777);
				$fp = fopen('../../allc_dat/in/'.$TIENDA.'/codopera/'.$nombre.'.'.$TIENDA, 'w+');
					if(!$fp){
						throw new Exception('File open failed.');
						}
					fwrite($fp, $guardar);
			}
				catch(Exception $e){}
				finally{
					fclose($fp);
				}
		}		
            
	return "Creacion de Carpetas y Exportacion exitosa";
}


/// FIN FUNCIONES

$CCOPERADOR = $_POST["CCOPERADOR"];
$DES_CLAVE = $_POST["DES_CLAVE"];
$INICIALES_OP = $_POST['INICIALES_OP'];
if (empty($CCOPERADOR) || empty($DES_CLAVE)) {
    
    $CCOPERADOR = $_GET["CCOPERADOR"];
    $DES_CLAVE = $_GET["DES_CLAVE"];
    $INICIALES_OP = $_GET["INICIALES_OP"];
    
    $flag=$INICIALES_OP.$CCOPERADOR;
    $guardar.= $flag."\r\n";
    $msj = create_file(substr("000".$DES_CLAVE,-3),$guardar,$CCOPERADOR);
    
}

$GENCODE = @$_POST["GENCODE"]; //0: NO HACE NADA, 1: GENERA NUEVO COD, 2: REINTENTA CÓDIGO
if (empty($GENCODE)) {
    $GENCODE = @$_GET["GENCODE"];
}
if (empty($GENCODE)) {
    $GENCODE = 0;
}
$IDOP = @$_POST["IDOP"];
if (empty($IDOP)) {
    $IDOP = @$_GET["IDOP"];
}
if (empty($IDOP)) {
    $IDOP = 0;
}

$APLICAF = 0;

$FILTRO_GERENTE = "";
//OBTENER TIENDA DE GERENTE
$CONSULTA = "SELECT * FROM US_USUTND WHERE IDUSU=" . $SESIDUSU . " ORDER BY COD_TIENDA DESC";
$RS = sqlsrv_query($maestra, $CONSULTA);
//oci_execute($RS);
if ($row = sqlsrv_fetch_array($RS)) {
    $COD_TIENDA = $row['COD_TIENDA'];
    $S2 = "SELECT * FROM MN_TIENDA WHERE COD_TIENDA=" . $COD_TIENDA;
    $RS2 = sqlsrv_query($maestra, $S2);
    //oci_execute($RS2);
    if ($row2 = sqlsrv_fetch_array($RS2)) {
        $DES_CLAVE = $row2['DES_CLAVE'];
    }
    $FILTRO_GERENTE = " AND ID_OPERADOR IN(SELECT ID_OPERADOR FROM OP_OPERADOR WHERE COD_TIENDA=" . $DES_CLAVE . ") ";
    $APLICAF = 1;
} else {
    //USUARIO CENTRAL
    $FILTRO_GERENTE = "";
}

$FILTRO_ESTADO = "";
$FESTADO = @$_POST["FESTADO"];
if (empty($FESTADO)) {
    $FESTADO = @$_GET["FESTADO"];
}
if (!empty($FESTADO)) {
    if ($FESTADO != 99) {
        $FILTRO_ESTADO = " AND ID_OPERADOR IN(SELECT ID_OPERADOR FROM OP_CODSUPER WHERE ESTADO=" . $FESTADO . ") ";
        $APLICAF = 1;
    } else {
        $FILTRO_ESTADO = " AND ID_OPERADOR NOT IN(SELECT ID_OPERADOR FROM OP_CODSUPER) ";
        $APLICAF = 1;
    }
}

$FILTRO_NOMB = "";
$BOPERA = trim(strtoupper(@$_POST["BOPERA"]));
if (empty($BOPERA)) {
    $BOPERA = trim(strtoupper(@$_GET["BOPERA"]));
}
$BOPCION = @$_POST["BOPCION"];
if (empty($BOPCION)) {
    $BOPCION = @$_GET["BOPCION"];
}
if (empty($BOPCION)) {
    $BOPCION = 2;
}
if ($BOPCION == 1) {
    if ($BOPERA <> "") {
        $FILTRO_NOMB = "AND ID_OPERADOR IN(SELECT ID_OPERADOR FROM OP_OPERADOR WHERE (UPPER(RTRIM(NOMBRE)) Like '%" . strtoupper($BOPERA) . "%' OR UPPER(RTRIM(APELLIDO_P)) Like '%" . strtoupper($BOPERA) . "%' OR UPPER(RTRIM(APELLIDO_M)) Like '%" . strtoupper($BOPERA) . "%'))";
        $APLICAF = 1;
    }
}
if ($BOPCION == 2) {
    if ($BOPERA <> "") {
        $FILTRO_NOMB = " AND ID_OPERADOR IN(SELECT ID_OPERADOR FROM OP_OPERADOR WHERE CC_OPERADOR Like '%" . strtoupper($BOPERA) . "%')";
        $APLICAF = 1;
    }
}
?>

</head>

<body>

<?php include("../headerregusu.php"); ?>
<?php include("titulo_menu.php"); ?>
    <table width="100%" height="100%">
        <tr>
            <td align="right"  width="200" bgcolor="#FFFFFF"><?php include("menugeneral.php"); ?></td> 
            <td >
<?php
if ($MSJE == 1) { $ELMSJ = "C&oacute;digo de Autorizaci&oacute;n emitido y activado"; }
if ($MSJE == 2) { $ELMSJ = "No se encontraron coincidencias"; }
if ($MSJE == 3) { $ELMSJ = "No ha habido respuesta para el registro del C&oacute;digo de Autorizaci&oacute;n, por favor reintente"; }
if ($MSJE <> "") {
    ?>
                    <div id="GMessaje" onClick="QuitarGMessage();"><a href="#" onClick="QuitarGMessage();" style="color:#111111;"><?php echo $ELMSJ ?></a></div>
<?php } ?>



                <table width="100%">
                    <tr><td>
                            <h2><?php echo $LAPAGINA ?></h2>

                            <table width="100%" id="Filtro">
                                <tr>
                                    <td>
                                        <form action="reg_codeauto.php" method="post" name="frmbuscar" id="frmbuscar">

                                            <!-- 1: PROCESAR, 2: FALLO, 3: DISPONIBLE, 4 ACTIVO, 5: EXPIRADO  -->
                                            <select style="clear:left" name="FESTADO" onChange="document.forms.frmbuscar.submit();">
                                                <option value="">Estado</option>
                                                <option value="99" <?php if ($FESTADO == 99) {  echo "SELECTED"; } ?>> SIN C&Oacute;DIGO</option>
                                                <option value="4" <?php if ($FESTADO == 4) { echo "SELECTED"; } ?>> C&Oacute;DIGO ACTIVO</option>
                                                <option value="5" <?php if ($FESTADO == 5) { echo "SELECTED"; } ?>> C&Oacute;DIGO EXPIRADO</option>
                                                <option value="2" <?php if ($FESTADO == 2) {  echo "SELECTED"; } ?>>REINTENTAR C&Oacute;DIGO</option>
                                            </select>

                                            <input name="BOPERA" type="text" id="BOPERA" size="12" value="<?php echo htmlspecialchars($BOPERA, ENT_QUOTES); ?>">
                                            <input type="radio" name="BOPCION" value="2" <?php if ($BOPCION == 2) { ?> checked <?php } ?>>
                                            <label for="BOPCION2">Cuenta</label>
                                            <input type="radio" name="BOPCION" value="1"  <?php if ($BOPCION == 1) { ?> checked <?php } ?>>
                                            <label for="BOPCION1">Nombre</label>
                                            <input name="BUSCAR" type="submit" id="BUSCAR" value="Buscar">
                                            <input name="LIMPIAR" type="button" id="LIMPIAR" value="Limpiar" onClick="javascript:pagina('reg_codeauto.php')">

                                        </form>
                                    </td>
                                </tr>
                            </table>

                            <table style="margin:10px 20px; ">
                                <tr>
                                    <td>
                                            <?php
											//SI TIENDA ES CENTRAL NO DEBE ELEGIR LOS DE LAS TIENDAS LOCALES
											if($_SESSION['SUITEARMS']==0){
												//OBTENER TIENDAS LOCALES
												$FiltroStore="";
												$SQLStore="SELECT * FROM MN_TIENDA WHERE IND_ACTIVO=1 AND FL_LCL_SRVR=1";
												$RSStore = sqlsrv_query($maestra, $SQLStore);
												while ($rowStore = sqlsrv_fetch_array($RSStore)) {
													$DES_CLAVE = $rowStore['DES_CLAVE'];
													$FiltroStore=$FiltroStore." AND COD_TIENDA<>".$DES_CLAVE;
												}
												
												$SCRIPT_CONTADOR="SELECT COUNT(ID_OPERADOR) AS CUENTA FROM OP_OPERADOR WHERE ID_MODOPERA=20 AND REG_ESTADO=1 " . $FILTRO_GERENTE . $FILTRO_ESTADO . $FILTRO_NOMB.$FiltroStore;
												$SCRIPT_LISTA="SELECT * FROM (SELECT *,ROW_NUMBER() OVER (PARTITION BY " . $CTP . " ORDER BY ID_MODOPERA DESC, NOMBRE ASC) ROWNUMBER FROM OP_OPERADOR WHERE ID_MODOPERA=20 AND REG_ESTADO=1 " . $FILTRO_GERENTE . $FILTRO_ESTADO . $FILTRO_NOMB.$FiltroStore.") AS TABLEWITHROWNUMBER WHERE ROWNUMBER BETWEEN " . $LINF . " AND " . $LSUP;
											} else {
												$SCRIPT_CONTADOR="SELECT COUNT(ID_OPERADOR) AS CUENTA FROM OP_OPERADOR WHERE ID_MODOPERA=20 AND REG_ESTADO=1 " . $FILTRO_GERENTE . $FILTRO_ESTADO . $FILTRO_NOMB;
												$SCRIPT_LISTA="SELECT * FROM (SELECT *,ROW_NUMBER() OVER (PARTITION BY " . $CTP . " ORDER BY ID_MODOPERA DESC, NOMBRE ASC) ROWNUMBER FROM OP_OPERADOR WHERE ID_MODOPERA=20 AND REG_ESTADO=1 " . $FILTRO_GERENTE . $FILTRO_ESTADO . $FILTRO_NOMB.") AS TABLEWITHROWNUMBER WHERE ROWNUMBER BETWEEN " . $LINF . " AND " . $LSUP;
											}
											$RS = sqlsrv_query($conn, $SCRIPT_CONTADOR);
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
										
										if($TOTALREG>0){
                                            $RS = sqlsrv_query($conn, $SCRIPT_LISTA);
                                            ?>
                                        <table id="Listado">
                                            <tr>
                                                <th>Nombre Operador<br>Tipo Operador</th>
                                                <th>Negocio/ Tienda</th>
                                                <th>Nombre de Sistema<br>Cuenta Sistema</th>
                                                <th>Estado</th>
                                                <th>C&oacute;digo Autorizaci&oacute;n</th>
                                            </tr>
                                            <?php
                                            while ($row = sqlsrv_fetch_array($RS)) {

                                                $ID_OPERADOR = $row['ID_OPERADOR'];
                                                $NOMBRE = $row['NOMBRE'];
                                                $APELLIDO_P = $row['APELLIDO_P'];
                                                $APELLIDO_M = $row['APELLIDO_M'];
                                                $INICIALES_OP = $row["INICIALES_OP"];
                                                $OPERADOR = $NOMBRE . " " . $APELLIDO_P . " " . $APELLIDO_M;
                                                $CC_OPERADOR = $row['CC_OPERADOR'];
                                                $CCOPERA = $row['CC_OPERADOR'];

                                                $NOMB_ACE = $row['NOMB_ACE'];

                                                $ID_MODOPERA = $row['ID_MODOPERA'];
                                                $S2 = "SELECT * FROM OP_MODOPERA WHERE ID_MODOPERA=" . $ID_MODOPERA;
                                                $RS2 = sqlsrv_query($conn, $S2);
                                                if ($row2 = sqlsrv_fetch_array($RS2)) {
                                                    $DES_MODOPERA = $row2['DES_MODOPERA'];
                                                } else {
                                                    $DES_MODOPERA = "NO ASIGNADO";
                                                }

                                                $DES_CLAVE = $row['COD_TIENDA'];
                                                $COD_NEGOCIO = $row['COD_NEGOCIO'];

                                                $S2 = "SELECT * FROM MN_TIENDA WHERE DES_CLAVE=" . $DES_CLAVE;
                                                $RS2 = sqlsrv_query($maestra, $S2);
                                                if ($row2 = sqlsrv_fetch_array($RS2)) {
                                                    $DES_TIENDA = $row2['DES_TIENDA'];
                                                    $FLTDES_CLAVE = $row2['DES_CLAVE'];
                                                    $FLTDES_CLAVE = substr("000" . $FLTDES_CLAVE, -3);
                                                }
                                                $S2 = "SELECT DES_NEGOCIO FROM MN_NEGOCIO WHERE COD_NEGOCIO=" . $COD_NEGOCIO;
                                                $RS2 = sqlsrv_query($maestra, $S2);
                                                if ($row2 = sqlsrv_fetch_array($RS2)) {
                                                    $DES_NEGOCIO = $row2['DES_NEGOCIO'];
                                                }

                                                $ESTADO = 0;
                                                $ESTADOCOD = "SIN  C&Oacute;DIGO";
                                                $REG_CODSUPER = 0;
                                                $S2 = "SELECT COUNT(ID_CODSUPER) AS CTACODSUPER FROM OP_CODSUPER WHERE ID_OPERADOR=" . $ID_OPERADOR;
                                                $RS2 = sqlsrv_query($conn, $S2);
                                                if ($row2 = sqlsrv_fetch_array($RS2)) {
                                                    $REG_CODSUPER = $row2['CTACODSUPER'];
                                                }
                                                if ($REG_CODSUPER >= 1) {
                                                    $SQL = "SELECT MAX(ID_CODSUPER) AS MAXCOD FROM OP_CODSUPER WHERE ID_OPERADOR=" . $ID_OPERADOR;
                                                    $RS3 = sqlsrv_query($conn, $SQL);
                                                    if ($row3 = sqlsrv_fetch_array($RS3)) {
                                                        $MAXCOD = $row3['MAXCOD'];
                                                    }
                                                    $SQL = "SELECT * FROM OP_CODSUPER WHERE ID_CODSUPER=" . $MAXCOD;
                                                    $RS4 = sqlsrv_query($conn, $SQL);
                                                    if ($row4 = sqlsrv_fetch_array($RS4)) {
                                                        $ESTADO = $row4['ESTADO'];
                                                    }
                                                    //<!-- 1: PROCESAR, 2: FALLO, 3: DISPONIBLE, 4 ACTIVO, 5: EXPIRADO  -->
                                                    if ($ESTADO == 2) {
                                                        $ESTADOCOD = "REINTENTAR<BR>C&Oacute;DIGO";
                                                    }
                                                    if ($ESTADO == 4) {
                                                        $ESTADOCOD = "C&Oacute;DIGO<BR>ACTIVO";
                                                    }
                                                    if ($ESTADO == 5) {
                                                        $ESTADOCOD = "C&Oacute;DIGO<BR>EXPIRADO";
                                                    }
                                                }

                                                if ($BOPCION == 1) {
                                                    $OPERADOR = str_replace(strtoupper($BOPERA), '<span style="background-color:#FFF9C4;">' . strtoupper($BOPERA) . '</span>', strtoupper($OPERADOR));
                                                }

                                                if ($BOPCION == 2) {
                                                    $CC_OPERADOR = str_replace($BOPERA, '<span style="background-color:#FFF9C4;">' . $BOPERA . '</span>', $CC_OPERADOR);
                                                }
                                                ?>
                                                <script>
                                                    function paginaPrint(pagina) {
                                                        var aceptaEntrar = window.confirm("VERIFIQUE EL ESTADO DE LA IMPRESORA, PRESIONE ACEPTAR PARA CONTINUAR");
                                                        if (aceptaEntrar) {
                                                            parent.location.href = pagina;
                                                        } else {
                                                            return false;
                                                        }
                                                    }
                                                </script>
                                                <tr>
                                                    <td><span style="font-weight:600; font-size:12pt"><?php echo htmlspecialchars($OPERADOR, ENT_QUOTES);?></span><br><?php echo htmlspecialchars($DES_MODOPERA, ENT_QUOTES); ?></td>
                                                    <td><?php echo $DES_NEGOCIO ?><BR><?php echo htmlspecialchars($FLTDES_CLAVE . " - " . $DES_TIENDA, ENT_QUOTES); ?></td>
                                                    <td><?php echo htmlspecialchars($NOMB_ACE, ENT_QUOTES); ?><br><span style="font-size:14pt"><?php echo htmlspecialchars($CC_OPERADOR, ENT_QUOTES); ?></span></td>
                                                    <td><?= $ESTADOCOD ?></td>
                                                    <td>
                                                        <!-- //1: PROCESAR, 2: FALLO, 3: DISPONIBLE, 4 ACTIVO, 5: EXPIRADO -->
                                                        <?php if ($ESTADO == 0) { ?>
                                                            <input type="button" name="GENCODOP" value="Generar C&oacute;digo" onClick="pagina('reg_codeauto.php?GENCODE=1&IDOP=<?= htmlspecialchars($ID_OPERADOR, ENT_QUOTES);?>&FESTADO=<?= htmlspecialchars($FESTADO, ENT_QUOTES);?>&BOPCION=<?= htmlspecialchars($BOPCION, ENT_QUOTES);?>&BOPERA=<?= htmlspecialchars($BOPERA, ENT_QUOTES);?>&DES_CLAVE=<?= htmlspecialchars($FLTDES_CLAVE, ENT_QUOTES);?>&INICIALES_OP=<?= htmlspecialchars($INICIALES_OP, ENT_QUOTES);?>&CCOPERADOR=<?= htmlspecialchars($CCOPERA, ENT_QUOTES);?>');">
                                                        <?php } ?>
                                                        <?php if ($ESTADO == 2) { ?>
                                                            <input type="button" name="GENCODOP" value="Reintentar" onClick="pagina('reg_codeauto.php?GENCODE=1&IDOP=<?= htmlspecialchars($ID_OPERADOR, ENT_QUOTES);?>&FESTADO=<?= htmlspecialchars($FESTADO, ENT_QUOTES);?>&BOPCION=<?= htmlspecialchars($BOPCION, ENT_QUOTES); ?>&BOPERA=<?= htmlspecialchars($BOPERA, ENT_QUOTES);?>&DES_CLAVE=<?= htmlspecialchars($FLTDES_CLAVE, ENT_QUOTES); ?>&INICIALES_OP=<?= htmlspecialchars($INICIALES_OP, ENT_QUOTES); ?>&CCOPERADOR=<?= htmlspecialchars($CCOPERA, ENT_QUOTES); ?>');">
                                                        <?php } ?>
                                                        <?php if ($ESTADO == 4) { ?>
                                                            <input type="button" name="GENCODOP" value="Nuevo  C&oacute;digo" onClick="pagina('reg_codeauto.php?GENCODE=1&IDOP=<?= htmlspecialchars($ID_OPERADOR, ENT_QUOTES);?>&FESTADO=<?= htmlspecialchars($FESTADO, ENT_QUOTES); ?>&BOPCION=<?= htmlspecialchars($BOPCION, ENT_QUOTES); ?>&BOPERA=<?= htmlspecialchars($BOPERA, ENT_QUOTES);?>&DES_CLAVE=<?= htmlspecialchars($FLTDES_CLAVE, ENT_QUOTES); ?>&INICIALES_OP=<?= htmlspecialchars($INICIALES_OP, ENT_QUOTES);?>&CCOPERADOR=<?= htmlspecialchars($CCOPERA, ENT_QUOTES);?>');">
                                                        <?php } ?>
                                                        <?php if ($ESTADO == 5) { ?>
                                                            <input type="button" name="GENCODOP" value="Nuevo  C&oacute;digo" onClick="pagina('reg_codeauto.php?GENCODE=1&IDOP=<?= htmlspecialchars($ID_OPERADOR, ENT_QUOTES);?>&FESTADO=<?= htmlspecialchars($FESTADO, ENT_QUOTES);?>&BOPCION=<?= htmlspecialchars($BOPCION, ENT_QUOTES); ?>&BOPERA=<?= htmlspecialchars($BOPERA, ENT_QUOTES);?>&DES_CLAVE=<?= htmlspecialchars($FLTDES_CLAVE, ENT_QUOTES);?>&INICIALES_OP=<?= htmlspecialchars($INICIALES_OP, ENT_QUOTES);?>&CCOPERADOR=<?= htmlspecialchars($CCOPERA, ENT_QUOTES);?>');">
                                                        <?php } ?>
                                                    </td>
                                                </tr>
										<?php
									}
									?>
                                            <tr>
                                                <td colspan="5" nowrap style="background-color:transparent">
									<?php
									if ($LINF >= $CTP + 1) {
										$ATRAS = $LINF - $CTP;
										$FILA_ANT = $LSUP - $CTP;
										?>
                                                        <input name="ANTERIOR" type="button" value="Anterior"  onClick="pagina('reg_codeauto.php?LSUP=<?php echo htmlspecialchars($FILA_ANT, ENT_QUOTES);?>&LINF=<?php echo htmlspecialchars($ATRAS, ENT_QUOTES);?>&FESTADO=<?= htmlspecialchars($FESTADO, ENT_QUOTES);?>&BOPCION=<?= htmlspecialchars($BOPCION, ENT_QUOTES);?>&BOPERA=<?= htmlspecialchars($BOPERA, ENT_QUOTES);?>');">
										<?php
									}
									if ($LSUP <= $TOTALREG) {
										$ADELANTE = $LSUP + 1;
										$FILA_POS = $LSUP + $CTP;
										?>
                                                        <input name="SIGUIENTE" type="button" value="Siguiente" onClick="pagina('reg_codeauto.php?LSUP=<?php echo htmlspecialchars($FILA_POS, ENT_QUOTES);?>&LINF=<?php echo htmlspecialchars($ADELANTE, ENT_QUOTES);?>&FESTADO=<?= htmlspecialchars($FESTADO, ENT_QUOTES);?>&BOPCION=<?= htmlspecialchars($BOPCION, ENT_QUOTES);?>&BOPERA=<?= htmlspecialchars($BOPERA, ENT_QUOTES);?>');">
									<?php } ?>
                                                    <span style="vertical-align:baseline;">P&aacute;gina <?php echo $NUMPAG ?> de <?php echo $NUMTPAG ?></span>
                                                </td>
                                            </tr>
                                        </table>
<?php
										} else {
											//TOTALREG=0
											echo "<h4> No se registra emisi&oacute;n de C&oacute;digos de Autorizaci&oacute;n </h4>";
										}
sqlsrv_close($conn);
sqlsrv_close($maestra);
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


<?php if ($GENCODE == 1) {?>
	<script>
		$(document).ready(function(){
			$("#VentanaConsulta").css('display', "block");
			$("#VentanaConsulta-contenedor").load('RequiereImpresora.php?IdOpera=<?= htmlspecialchars($IDOP, ENT_QUOTES);?>&FESTADO=<?= htmlspecialchars($FESTADO, ENT_QUOTES);?>&BOPCION=<?= htmlspecialchars($BOPCION, ENT_QUOTES);?>&BOPERA=<?= htmlspecialchars($BOPERA, ENT_QUOTES);?>');
		});
	</script>
        <div id="VentanaConsulta" style="display:none">
            <div id="VentanaConsulta-contenedor" style="width: 400px; margin-left: -200px; min-height: 200px">
            </div>
        </div>
<?php
}
?>



</body>
</html>

