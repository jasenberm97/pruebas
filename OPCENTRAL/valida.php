<?php include("conecta.inc"); ?>

<?php

				$SALIR=1;
				$CUENTA=$_POST["CUENTA"];
				$CLAVE=$_POST["CLAVE"];
				$CONSULTA="SELECT * FROM US_USUARIOS WHERE CUENTA='".$CUENTA."' AND CLAVE='".$CLAVE."' AND ESTADO=1";
				$RS = sqlsrv_query($conn, $CONSULTA);
				//oci_execute($RS);		  
				while ($row = sqlsrv_fetch_array($RS)){
						$IDUSU=$row['IDUSU'];
						$IDPERFIL=$row['IDPERFIL'];
						$_SESSION['ARMS_IDUSU'] = $IDUSU;
						$_SESSION['ARMS_IDPERFIL'] = $IDPERFIL;
						$SALIR=2;
				}
				
			if ($SALIR==2) {
						
						$CONSULTA="SELECT WM, EDITAR FROM US_PERFIL WHERE IDPERFIL=".$IDPERFIL;
						$RS = sqlsrv_query($conn, $CONSULTA);
						//oci_execute($RS);		  
						while ($row = sqlsrv_fetch_array($RS)){
								$PWM=$row['WM'];
								$EDITAR=$row['EDITAR'];
								$_SESSION['ARMS_PWM'] = $PWM;
								$_SESSION['ARMS_PUB'] = $EDITAR;
								$_SESSION['ARMS_HORA']=time(); 
						}

						$CONSULTA="SELECT IDACC FROM US_PERFACC WHERE IDPERFIL=".$IDPERFIL." AND INGRESO=1";
						$RS = sqlsrv_query($conn, $CONSULTA);
						//oci_execute($RS);
						if ($row = sqlsrv_fetch_array($RS)) {
							$IDACC = $row['IDACC'];
							$CONSULTA2="SELECT ENLACE FROM US_ACCESO WHERE IDACC=".$IDACC;
							$RS2 = sqlsrv_query($conn, $CONSULTA2);
							//oci_execute($RS2);
							if ($row = sqlsrv_fetch_array($RS2)) {
								$ENLACE = $row['ENLACE'];
								//REGISTRO DE INICIO DE SESION
										$CONSULTA2="SELECT MAX(COD_EVENTO) AS MCOD_EVENTO FROM LG_EVENTO";
										$RS2 = sqlsrv_query($conn, $CONSULTA2);
										//oci_execute($RS2);
										if ($row = sqlsrv_fetch_array($RS2)) {
												$COD_EVENTO=$row['MCOD_EVENTO']+1;
											} else {
												$COD_EVENTO=1;
										}
										$SQLOG="INSERT INTO LG_EVENTO (COD_TIPO_EVENTO, FECHA, HORA, IP_CLIENTE, COD_USUARIO, IDACC) VALUES ";
										$SQLOG=$SQLOG."( 4, convert(datetime,GETDATE(), 121), '".$TIMESRV."', '".$IP_CLIENTE."', ".$IDUSU.", ".$IDACC.")";
										$RSL = sqlsrv_query($conn, $SQLOG);
										//oci_execute($RSL);																	
								header("Location: ".$ENLACE);
							} else {
								header("Location: ../hastaluego.php");
							}
						} else {
							header("Location: ../index.php?msj=1");
						}
						
			} else {
						header("Location: ../index.php?msj=1");
			}


		sqlsrv_close($conn);

?>


