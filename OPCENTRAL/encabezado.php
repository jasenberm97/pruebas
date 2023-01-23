<?php

	$CC=@$_GET["CC"];

	if ($CC==1) { 

			$CLAVE_OLD=$_POST["CLAVE_OLD"];

			$CLAVE_NEO=$_POST["CLAVE_NEO"];

			//VALIDA CLAVE_OLD ENVIADA

			$CONSULTA="SELECT * FROM US_USUARIOS WHERE IDUSU=".$SESIDUSU." AND CLAVE='".$CLAVE_OLD."' ";

			$RS = sqlsrv_query($conn, $CONSULTA);

			//oci_execute($RS);

			if ($row = sqlsrv_fetch_array($RS)) {

				//CAMBIAR CLAVE

							$CONSULTA2="UPDATE US_USUARIOS SET CLAVE='".$CLAVE_NEO."', IDREG=".$SESIDUSU.", FECHA=convert(datetime,GETDATE(), 121) WHERE IDUSU=".$SESIDUSU;

							$RS2 = sqlsrv_query($conn, $CONSULTA2);

							//oci_execute($RS2);

							//REGISTRO DE MODIFICACION

									$CONSULTA2="SELECT MAX(COD_EVENTO) AS MCOD_EVENTO FROM LG_EVENTO";

									$RS2 = sqlsrv_query($conn, $CONSULTA2);

									//oci_execute($RS2);

									if ($row = sqlsrv_fetch_array($RS2)) {

											$COD_EVENTO=$row['MCOD_EVENTO']+1;

										} else {

											$COD_EVENTO=1;

									}

									$SQLOG="INSERT INTO LG_EVENTO (COD_TIPO_EVENTO, FECHA, HORA, IP_CLIENTE, COD_USUARIO, IDACC, IDSISTEMA, IDPERFIL) VALUES ";

									$SQLOG=$SQLOG."( 3, convert(datetime,GETDATE(), 121), '".$TIMESRV."', '".$IP_CLIENTE."', ".$SESIDUSU.", 1103, ".$SESIDSISTEMA.", ".$SESIDPERFIL.")";

									$RSL = sqlsrv_query($conn, $SQLOG);

									//oci_execute($RSL);																	

							echo "<script language='javascript'>"; 

							echo "alert('SE HA MODIFICADO CON \xc9XITO LA CONTRASE\xD1A DE USUARIO');"; 

							echo "</script>"; 				

			} else {

				//MENSAJE DE ERROR

				echo "<script language='javascript'>"; 

				echo "alert('LA CLAVE ORIGINAL NO CORRESPONDE, VUELVA A INTENTARLO');"; 

				echo "</script>"; 				

			}

	}

?>

<table  id="encabezado"width="100%">

    <tr><td align="left" width="156px">

    </td><td align="left" nowrap="nowrap">

        <p style="text-align:left; margin:0; padding:0">Alliances Retail </p>

        <p style="text-align:left; margin:0; padding:0">Management Suite</p>

    </td> <td align="center"><?php include ("../menu_estado.php"); ?></td>

    <td align="right">

        <ul id="MenuUser"><li><a href="#">en sesi&oacute;n: <?php echo $GLBENSESION ?></a>

        <ul><li><p style="margin-top:10px; margin-right:10px;"><span style="font-weight:600"><?php echo $GLBENSESION ?></span> en sesi&oacute;n con perfil <span style="font-weight:600"><?php echo $GLBELPERFIL ?></span> para <span style="font-weight:600"><?php echo $GLBELCLIENTE ?></span></p></li>

        <li>

            <?php if($SESMSIS==1){ ?>

            		<a href="../msistemas.php" class="enlaceClave">Sistemas</a>

            <?php } ?>

            <a href="#" class="enlaceClave" onclick="ActivarVentana();">Cambiar clave</a>

            <a href="../hastaluego.php" class="enlaceSesion">Cerrar sesi&oacute;n</a>

        </li></ul></li></ul>

    </td></tr>

</table>

	<script>$(document).ready(function() {var posicion = $("#Ventana").offset();}); </script>

    <div id="Ventana" style="display:none"><div id="Ventana-contenedor" style="width:420; height:280; margin-left: -210px; top:75px">        

        <span style="position:absolute; top:0; right:20px;">

        <img src="../images/ICO_Close.png" border="0" onClick="CerrarVentana();" title="Cerrar ventana">

        </span>

        

        <h3> Cambiar mi Clave de Usuario</h3>

        <script>                        

        function validaClave(theForm){if (theForm.CLAVE_OLD.value == ""){alert("COMPLETE EL CAMPO REQUERIDO.");theForm.CLAVE_OLD.focus();return false;}password = theForm.CLAVE_NEO.value;if (password.length < 5 || password.length > 20) {alert("LA CLAVE DEBE CONTENER ENTRE 5 Y 20 CARACTERES");theForm.CLAVE_NEO.focus();return false;} else {for (i=0;i<password.length;i++) {c = password.substring(i,i+1);if (c == " ") {alert("LA CLAVE NO DEBE CONTENER ESPACIOS EN BLANCO");theForm.CLAVE_NEO.focus();return false;}if (c == "'") {alert("COMPLETE EL CAMPO REQUERIDO.");theForm.CLAVE_NEO.focus();return false;}}}if (theForm.CLAVE_VAL.value != theForm.CLAVE_NEO.value){alert("LAS CLAVES DEBEN SER IGUALES.");theForm.CLAVE_VAL.focus();return false;}} //validaClave(theForm)

        </script>                        

        <?php  

        $ELDOMINIO = $_SERVER['HTTP_HOST'];  

        $LADIRECCION = $_SERVER['PHP_SELF'];  

        $LAURLACTUAL = "http://" . "$ELDOMINIO" . "$LADIRECCION" . "?CC=1";  

        ?>

        <table id="forma-registro">

            <form action="<?php echo $LAURLACTUAL ?>" method="post" name="formact" onSubmit="return validaClave(this)">

            <tr><td>

            <label for="CLAVE_OLD">Contrase&ntilde;a original</label>

            </td><td>

            <input name="CLAVE_OLD" type="password" size="30" maxlength="20">

            </td></tr><tr><td>

            <label for="CLAVE_NEO">Nueva Contrase&ntilde;a</label>

            </td><td>

            <input name="CLAVE_NEO" type="password" size="30" maxlength="20">

            </td></tr><tr><td>

            <label for="CLAVE_VAL">Repetir Nueva Contrase&ntilde;a</label>

            </td><td>

            <input name="CLAVE_VAL" type="password" size="30" maxlength="20">

            </td></tr><tr><td></td><td>

            <input name="CAMBIARCLAVE" type="submit" value="Cambiar mi Clave de Usuario">

            </td></tr>

            </form>

        </table>

    </div></div>