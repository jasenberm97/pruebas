<?php include("conecta.inc"); ?>
<?php
		
		if (!isset($_SESSION['ARMS_IDUSU'])) {
			header("Location: ../finsesion.php");
			}
		
		$SESMSIS=$_SESSION['ARMS_MSIS'];
		$SESIDSISTEMA=$_SESSION['ARMS_SIST'];
		$SESIDUSU=$_SESSION['ARMS_IDUSU'];
		$SESIDPERFIL=$_SESSION['ARMS_IDPERFIL'];
		$SESPWM=$_SESSION['ARMS_PWM'];
		$SESPUBLICA=$_SESSION['ARMS_PUB'];

		$CONSULTA="SELECT NOMBRE FROM US_USUARIOS WHERE IDUSU=".$SESIDUSU;
		$RS = sqlsrv_query($maestra, $CONSULTA);
		//oci_execute($RS);
		if ($row = sqlsrv_fetch_array($RS)) {
			$GLBENSESION = $row['NOMBRE'];
		}

	if ($SESIDPERFIL != "") {
		$CONSULTA="SELECT NOMBRE FROM US_PERFIL WHERE IDPERFIL=".$SESIDPERFIL;
		$RS = sqlsrv_query($maestra, $CONSULTA);
		//oci_execute($RS);
		if ($row = sqlsrv_fetch_array($RS)) {
			$GLBELPERFIL = $row['NOMBRE'];
		}
	}
	
?>
