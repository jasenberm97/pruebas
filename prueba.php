<?php 
	include("session.inc");
	include_once '../libs/csrf/csrfprotector.php';
?>
<?php

//csrfProtector::init(); 
$__time = microtime(true);

$MAC= '';
$serverIp= gethostbyname($_SERVER['SERVER_NAME']);
$clientIp= $_SERVER['REMOTE_ADDR'];
if($serverIp==$clientIp){
	$MAC= exec('getmac');
	$MAC= strtok($MAC, ' '); 
}
else{
	$arp=`arp -a $clientIp`;
	$lines=explode("\n", $arp);
	foreach($lines as $line){
		$cols=preg_split('/\s+/', trim($line));
		if ($cols[0]==$clientIp){
			$MAC=$cols[1];
		}
	}
}
$CONSULTA="SELECT count(*) existen FROM MAC_ADDRESS_DEVOLUCIONES WHERE FL_SET=1 and MAC_ADDRESS in ('".$MAC."')";
$RS = sqlsrv_query($arts_conn, $CONSULTA);
$cantidad= 0;
if($row = sqlsrv_fetch_array($RS)) {
	$cantidad= $row['existen'];
}
$cantidad=1;
?>
<?php include("headerhtml.inc");?>
<!-- fjjf modal loading -->
<link rel="stylesheet" href="../css/jquery.loadingModal.min.css">
<script language="javascript" src="../js/jquery.loadingModal.min.js"></script>
<?php
//REV.20170913

	$PAGINA=1144;
	$NOMENU=0;

	//require_once "FuncionCache.php";
  require_once "FuncionCache.php";
	 noCache();
// codigo php
	$MSJE=$_GET["MSJE"];
	$MSJETOTALDEV=$_GET["MSJETD"];
	$LIST=$_GET["LIST"];
	$IDUSU=($_SESSION["ARMS_IDUSU"]);
//var_dump($IDUSU);

	$DEV_TRX=$_GET["Devols"];
	$TRN_DEVOL=$_GET["TRN_DEVOL"];
	$SALSINREG=$_GET["ssr"];
	
    $_SESSION["FLAG_GIFT"]=0;
    $_SESSION["EJEC_DEV"]=0;

	IF($MSGGFT===1){
		
	}


 
	//DESACTIVAR BLOQUEO DE DOCUMENTO
	$ACT_DOC = $_POST["ACT_DOC"];
	if (empty($ACT_DOC)) { $ACT_DOC=$_GET["ACT_DOC"] ;}
	$ACT_AI_TRN = $_POST["ACT_AI_TRN"];
	if (empty($ACT_AI_TRN)) { $ACT_AI_TRN=$_GET["ACT_AI_TRN"] ;}
	if($ACT_DOC == 1)
	{
		$SQL="delete from DV_DOC_EN_PROCESOS where AI_TRN = ?"; //$SQL="delete from DV_DOC_EN_PROCESOS where AI_TRN =".$ACT_AI_TRN;
		$RS = sqlsrv_query($conn, $SQL, array($ACT_AI_TRN)); 
	}
	
		//Ref. 001 ELIMINAR REGISTROS TEMPORALES DE SESIONES INICIADAS EN PROCESO DE DEVOLUCION
	$SQL="delete from DV_DOC_EN_PROCESOS where DATEDIFF(MINUTE, FECHA_ING, GETDATE()) >=  (select top 1 cast(RTRIM(d.VAL_PARAM) as int) from [SAADMIN]..[PM_PARAM] c inner join [SAADMIN]..[PM_PARVAL] d on c.COD_PARAM = d.COD_PARAM where c.VAR_PARAM = 'TIME_OUT_DEV' and ISNUMERIC(d.VAL_PARAM) = 1) OR EXISTS(SELECT 1 FROM [ARTS_EC]..TR_RTN where ORGL_AI_TRN = AI_TRN)";
	$RS = sqlsrv_query($conn, $SQL);
	//Ref.001 fin
	
	//MODO ILIMITADA
	$ACTILIMIT=$_GET["ACTILIMIT"];
	$MODOACTILM=$_GET["MODOACTILM"];
	if(!empty($ACTILIMIT)){
		$ConsIlimitada=" DisplayConsIlimitada();";
	} else { $ConsIlimitada="";}
	//MODO ILIMITADA

	$idTrnNotaArts=$_GET["idTrnNotaArts"]; //TRN NOTA DE CREDITO EN ARTS
	$idTrnNotaArtsE=$_GET["idTrnNotaArtsE"];
	$Devolucion=$_GET["ndc"];
	

	//OBTENER ID_DEV... SI NO ENCUENTRA NOTA DE CRÉDITO...
	$SQLDEV="SELECT ORGL_INVC_NMB,RTN_NMB FROM TR_RTN WHERE ID_TRN=?"; //$SQLDEV="SELECT ORGL_INVC_NMB,RTN_NMB FROM TR_RTN WHERE ID_TRN=".$idTrnNotaArts;
	$RSDEV = sqlsrv_query($arts_conn, $SQLDEV, array($idTrnNotaArts)); //$RSDEV = sqlsrv_query($arts_conn, $SQLDEV);
			if ($rowDev = sqlsrv_fetch_array($RSDEV)) {
				$FactOrgnl= $rowDev['ORGL_INVC_NMB']; //FACTURA ORIGINAL
				$NumNota= $rowDev['RTN_NMB']; //NOTA DE CREDITO ASOCIADA
			}
			$SQLDEV="SELECT ID_TRN FROM TR_INVC WHERE INVC_NMB='".$FactOrgnl."' ";
			$RSDEV = sqlsrv_query($arts_conn, $SQLDEV);
			if ($rowDev = sqlsrv_fetch_array($RSDEV)) {
				$OrgnlIdTrn= $rowDev['ID_TRN'];
			}
	if(empty($idTrnNotaArts)){
		//VIENE DE PROCESA
		$NOTADECREDITO=$Devolucion;
		$SQL="SELECT NUM_NC, ID_TRN FROM DV_TICKET WHERE ID_ESTADO=1 AND ID_DEV=".$NOTADECREDITO;
		$RS = sqlsrv_query($conn, $SQL);
		if ($row = sqlsrv_fetch_array($RS)) {
				$NUM_NC = $row['NUM_NC'];
				$ID_TRN_NC = $row['ID_TRN'];
		}
	} else {
		$NOTADECREDITO=$idTrnNotaArts;
			$SQLDEV="SELECT ORGL_INVC_NMB,RTN_NMB,TY_RTN FROM TR_RTN WHERE ID_TRN=".$idTrnNotaArts;
			$RSDEV = sqlsrv_query($arts_conn, $SQLDEV);
			if ($rowDev = sqlsrv_fetch_array($RSDEV)) {
				$FactOrgnl= $rowDev['ORGL_INVC_NMB']; //FACTURA ORIGINAL
				$NUM_NC= $rowDev['RTN_NMB']; //NOTA DE CREDITO ASOCIADA
				$TipoDevol= $rowDev['TY_RTN']; //TIPO DEVOLUCION
			}
			$SQLDEV="SELECT ID_TRN FROM TR_INVC WHERE INVC_NMB='".$FactOrgnl."' ";
			$RSDEV = sqlsrv_query($arts_conn, $SQLDEV);
			if ($rowDev = sqlsrv_fetch_array($RSDEV)) {
				$ID_TRN_NC= $rowDev['ID_TRN'];
			}
	}

	$SQL="SELECT NUM_NC, ID_TRN FROM DV_TICKET WHERE ID_ESTADO=1 AND ID_DEV=?";
	$RS = sqlsrv_query($conn, $SQL, array($NOTADECREDITO));
	if ($row = sqlsrv_fetch_array($RS)) {
			$NUM_NC = $row['NUM_NC'];
			$ID_TRN_NC = $row['ID_TRN'];
			$TipoDevol= $rowDev['TY_DEV']; //TIPO DEVOLUCION
	}

	if(!empty($MSJETOTALDEV)){
		$SQLELMADT="SELECT ID_TRN, ID_ESTADO FROM DV_TICKET WHERE ID_DEV=?";
		$RSDT = sqlsrv_query($conn, $SQLELMADT, array($MSJETOTALDEV));
		if ($rowDT = sqlsrv_fetch_array($RSDT)) {
				$ID_TRNSSRDT = $rowDT['ID_TRN'];
				$ID_ESTSSRDT = $rowDT['ID_ESTADO'];
				if($ID_ESTSSRDT == 0){
				//ELIMINAR REGISTRO DE TRANSACCION EN MODO DEVOLUCION
				$SQLTMPDT="DELETE FROM DV_TMPITM WHERE ID_TRN=".$ID_TRNSSRDT." AND ID_DEV=".$MSJETOTALDEV ;
				$RSTMPDT = sqlsrv_query($conn,$SQLTMPDT);
				$SQLTMPDT="DELETE FROM DV_TMPTRN WHERE ID_TRN=".$ID_TRNSSRDT." AND ID_DEV=".$MSJETOTALDEV ;
				$RSTMPDT = sqlsrv_query($conn,$SQLTMPDT);
				}
		}
	
		if($ID_ESTSSRDT == 0){
			$SQLREGTDT="DELETE FROM DV_TICKET WHERE ID_ESTADO=0 AND ID_DEV=".$MSJETOTALDEV;
			$REGTDT = sqlsrv_query($conn, $SQLREGTDT);
			$sqlDT =  "EXEC SP_INSERTA_ACTUALIZA_SECUENCIAL_DEVOLUCION ?,?,?";
			$paramsDT = [
				$MSJETOTALDEV, 'devolucion borrada en reg_devol_cer', 'devolucion borrada en reg_devol_cer'
			];
			sqlsrv_query($conn, $sqlDT, $paramsDT);
			$SQLELMADT="DELETE FROM DV_ARTS WHERE ID_DEV=".$MSJETOTALDEV;
			$REGAT = sqlsrv_query($conn, $SQLELMADT);
			$SQLDVDFCDT="DELETE FROM DV_GFCD WHERE ID_DEV=".$MSJETOTALDEV;
			$REGDVDFCDT = sqlsrv_query($conn, $REGDVDFCDT);
			$SQLDVDFCDT="DELETE FROM DV_DATOS_USUARIO_TARJETA_VIRTUAL WHERE ID_DEV=".$MSJETOTALDEV;
			sqlsrv_query($conn, $SQLDVDFCDT);
		}
	
		header("Location: reg_Devol_CER.php?MSJE=3");
		// fjjf
		// se agrega este die para que no siga ejecutando
		// el resto de codigo al hacer la redireccion
		// (es necesario el die porque de lo contrario se creara un nuevo
		// registro de devolucion lo cual hará una duplicidad)
		die();
	}
	if(!empty($SALSINREG)){
			$SQLELMA="SELECT ID_TRN, ID_ESTADO FROM DV_TICKET WHERE ID_DEV=".$SALSINREG;
			$RS = sqlsrv_query($conn, $SQLELMA);
			if ($row = sqlsrv_fetch_array($RS)) {
					$ID_TRNSSR = $row['ID_TRN'];
					$ID_ESTSSR = $row['ID_ESTADO'];
					if($ID_ESTSSR == 0){
					//ELIMINAR REGISTRO DE TRANSACCION EN MODO DEVOLUCION
					$SQLTMP="DELETE FROM DV_TMPITM WHERE ID_TRN=".$ID_TRNSSR." AND ID_DEV=".$SALSINREG ;
					$RSTMP = sqlsrv_query($conn,$SQLTMP);
					$SQLTMP="DELETE FROM DV_TMPTRN WHERE ID_TRN=".$ID_TRNSSR." AND ID_DEV=".$SALSINREG ;
					$RSTMP = sqlsrv_query($conn,$SQLTMP);
					}
			}
			// fjjf
            // este codigo estaba comentado
            // se supone que deberia eliminar el dv_ticket activo
            // ya que la unica forma en que se ejecute este codigo
			// es si $SALSINREG no es vacio ($SALSINREG viene de $_GET['ssr'])
            // y esto solo sucede al hacer click en "SALIR SIN REGISTRAR"
            // o al hacer click en "Cerrar Consulta" durante la "consulta ilimitada"
            // en el archivo ConsultaIlimitada.php
            // se descomenta para que el "Salir sin registrar" funcione adecuadamente
			if($ID_ESTSSR == 0){
				$SQLREGT="DELETE FROM DV_TICKET WHERE ID_ESTADO=0 AND ID_DEV=".$SALSINREG;
				$REGT = sqlsrv_query($conn, $SQLREGT);
				$sql =  "EXEC SP_INSERTA_ACTUALIZA_SECUENCIAL_DEVOLUCION ?,?,?";
				$params = [
					$SALSINREG, 'devolucion borrada en reg_devol_cer', 'devolucion borrada en reg_devol_cer'
				];
				sqlsrv_query($conn, $sql, $params);
				$SQLELMA="DELETE FROM DV_ARTS WHERE ID_DEV=".$SALSINREG;
				$REGA = sqlsrv_query($conn, $SQLELMA);
				$SQLDVDFC="DELETE FROM DV_GFCD WHERE ID_DEV=".$SALSINREG;
				$REGDVDFC = sqlsrv_query($conn, $SQLDVDFC);
				$SQLDVDFC="DELETE FROM DV_DATOS_USUARIO_TARJETA_VIRTUAL WHERE ID_DEV=".$SALSINREG;
				sqlsrv_query($conn, $SQLDVDFC);
			}
        
		header("Location: reg_Devol_CER.php?MSJE=2");
		// fjjf
		// se agrega este die para que no siga ejecutando
		// el resto de codigo al hacer la redireccion
		// (es necesario el die porque de lo contrario se creara un nuevo
		// registro de devolucion lo cual hará una duplicidad)
		die();
	}

	if(empty($DEV_TRX)){
				//FORZAR TERMINO DE NOTA DE CREDITO DE USUARIO QUE LA HA DEJADO ABIERTA
				//Devols=1&IDT=4350&IDV=1
				$SQL="SELECT ID_DEV,ID_TRN,TY_DEV FROM DV_TICKET WHERE ID_ESTADO=0 AND ID_REG=".$SESIDUSU;
				$RS = sqlsrv_query($conn, $SQL);
				if ($row = sqlsrv_fetch_array($RS)) {
						$ID_DEV = $row['ID_DEV'];
						$ID_TRN = $row['ID_TRN'];
						$TY_DEV = $row['TY_DEV'];

						$TRN_DEVOL=0;
						$S2="SELECT COUNT(ID_DEV) AS CUENTA FROM DV_TICKET WHERE ID_TRN=".$ID_TRN;
						$RS2 = sqlsrv_query($conn,$S2);
						if ($row2 = sqlsrv_fetch_array($RS2)) {
							$REG_DEVOL = $row2['CUENTA'];
						}
						if($REG_DEVOL != 0){
							$TRN_DEVOL=2;
						}
					
						if($MSJE==="7"){
							header("Location: reg_Devol_CER.php?Devols=".$TY_DEV."&IDT=".$ID_TRN."&IDV=".$ID_DEV."&TRN_DEVOL=".$TRN_DEVOL."&MSJE=7");
						}else{
							header("Location: reg_Devol_CER.php?Devols=".$TY_DEV."&IDT=".$ID_TRN."&IDV=".$ID_DEV."&TRN_DEVOL=".$TRN_DEVOL);
						}
						
				}
						
				//Ref.001 Validar que el documento no este abierto por otro usuario
				$SQL="SELECT 1 FROM DV_DOC_EN_PROCESOS WHERE AI_TRN=".$AI_TRN." ";
				$RS = sqlsrv_query($conn, $SQL);
				if ($row = sqlsrv_fetch_array($RS)) {
					header("Location: reg_Devol_CER.php?MSJE=2#nobb");
				}
				
	}
	
	
	//DE REG_DEVOL_REGDEV
			$ID_TRN=$_POST["ID_TRN"];	
		
			if (empty($ID_TRN)) { $ID_TRN=$_GET["IDT"] ;
					$SQLDEV2="SELECT ID_REG FROM DV_TICKET WHERE ID_ESTADO=0 AND ID_TRN=".$ID_TRN." ";
				//	var_dump($SQLDEV2);
															$RSDEV2 = sqlsrv_query($conn, $SQLDEV2);
															if ($rowRegNota = sqlsrv_fetch_array($RSDEV2)) {
																$IDUSUDEVACTUAL = $rowRegNota['ID_REG'];
																//	var_dump($IDUSUDEVACTUAL);
																//	var_dump($IDUSU);
																	if($IDUSUDEVACTUAL <> $IDUSU){
																header("Location: reg_Devol_CER.php?MSJE=6#nobb");}
															}			
															}
			if (empty($ID_TRN)) { $ID_TRN=$ID_TRN_NC ;	}
		

																
			$ID_DEV=$_POST["ID_DEV"];
			if (empty($ID_DEV)) { $ID_DEV=$_GET["IDV"] ;}
			if (empty($ID_DEV)) { $ID_DEV=0;}
			if ($ID_DEV != 0){
				$SQL="SELECT NUM_NC FROM DV_TICKET WHERE ID_ESTADO=0 AND ID_REG=".$SESIDUSU;
				$RS = sqlsrv_query($conn, $SQL);
				if ($row = sqlsrv_fetch_array($RS)) {
						$NUM_NC = $row['NUM_NC'];
						if($NUM_NC==0){$NUM_NC=".........";}
				}
			}
			
						
			//var_dump($ID_TRN);

			
			
		
			$SQLD="SELECT ID_BSN_UN,ID_WS FROM TR_TRN WHERE ID_TRN=".$ID_TRN;
			$RSD = sqlsrv_query($arts_conn, $SQLD);
			if ($ROWD = sqlsrv_fetch_array($RSD)) {
				$ID_BSN_DEV = $ROWD['ID_BSN_UN'];
				$ID_WS = $ROWD['ID_WS']; //POS
			}
			$SQLD="SELECT CD_STR_RT FROM PA_STR_RTL WHERE ID_BSN_UN=".$ID_BSN_DEV;
			$RSD = sqlsrv_query($arts_conn, $SQLD);
			if ($ROWD = sqlsrv_fetch_array($RSD)) {
				$CD_STR_RT = $ROWD['CD_STR_RT']; //TIENDA
			}
			$SQLD="SELECT CD_WS FROM AS_WS WHERE ID_WS=?"; //$SQLD="SELECT CD_WS FROM AS_WS WHERE ID_WS=".$ID_WS;
			$RSD = sqlsrv_query($arts_conn, $SQLD, array($ID_WS));
			if ($ROWD = sqlsrv_fetch_array($RSD)) {
				$CD_WS = $ROWD['CD_WS']; //POS
			}
			$SQLD="SELECT COD_SRI FROM MN_TIENDA WHERE DES_CLAVE=".$CD_STR_RT;
			$RSD = sqlsrv_query($maestra, $SQLD);
			if ($ROWD = sqlsrv_fetch_array($RSD)) {
				$COD_SRI = $ROWD['COD_SRI']; //SRI TIENDA
			}
			$SQLINTER="SELECT PM_PARVAL.VAL_PARAM FROM PM_PARAM, PM_PARVAL WHERE PM_PARAM.COD_PARAM=PM_PARVAL.COD_PARAM AND PM_PARAM.VAR_PARAM='NUMPOSDEVOL' AND PM_PARVAL.DES_CLAVE=".$CD_STR_RT;
			$RS = sqlsrv_query($maestra,$SQLINTER);
			if ($row = sqlsrv_fetch_array($RS)) {
				$VAR_PMT="NUMPOSDEV_STR";
				${$VAR_PMT} = $row['VAL_PARAM']; //VARIABLES DINAMICAS
			} else {
				$SQLINTER2="SELECT PM_PARVAL.VAL_PARAM FROM PM_PARAM, PM_PARVAL WHERE PM_PARAM.COD_PARAM=PM_PARVAL.COD_PARAM AND PM_PARAM.VAR_PARAM='NUMPOSDEVOL' AND PM_PARVAL.DES_CLAVE IS NULL";
				$RS2 = sqlsrv_query($maestra,$SQLINTER2);
				if ($row2 = sqlsrv_fetch_array($RS2)) {
					$VAR_PMT="NUMPOSDEV_STR";
					${$VAR_PMT} = $row2['VAL_PARAM']; //VARIABLES DINAMICAS
				}
			}


	//DE REG_DEVOLREGDEV
	

	$ID_DEVS=$_GET["IDDV"];
	if(!empty($ID_DEVS)) { $NOMENU=1;}
								
	
	if ($LIST==""  and $DEV_TRX=="" and $NOTADECREDITO=="") {  $D_GFC=1; }
	if ($NOTADECREDITO != "") {  $LIST=1; }
	
	if($LIST==1){$MODULO="Buscar/Listar ";} else {$MODULO="Registrar ";}
	

	$FILTRO_FLAGS=" AND FL_TRG_TRN<>1 AND FL_CNCL<>1 AND FL_VD<>1 AND FL_SPN IS NULL AND ID_TRN NOT IN(SELECT ID_TRN FROM CO_REC_ELEC) AND (ID_TRN IN(SELECT ID_TRN FROM TR_INVC) OR ID_TRN IN(SELECT ID_TRN FROM CO_ILIM_DT WHERE AMNT=0))";
	//echo "<script>console.log(\"'MODOACTGFT 1: " . $MODOACTGFT . "\")</script>\n";		
	$BSC_NDC=$_POST["BSC_NDC"];
	if (empty($BSC_NDC)) { $BSC_NDC=$_GET["BSC_NDC"] ;}
		
				$VERTND_UNO = 0;
				//VERIFICAR TIENDAS ASOCIADAS A USUARIO
				$SQL="SELECT COUNT(COD_TIENDA) AS CTATND FROM US_USUTND WHERE IDUSU=".$SESIDUSU;
				$RS = sqlsrv_query($maestra, $SQL);
				if ($row = sqlsrv_fetch_array($RS)) {
					$CTATND = $row['CTATND'];
				}
				//SI CTATND==0 USUARIO CENTRAL, SELECCIONAR NEGOCIO Y LOCAL
				//SI CTATND==1 DESPLEGAR LOCAL
				//SI CTATND>1 DESPLEGAR LISTADO DE LOCALES
				if($CTATND==1){
					//OBTENER TIENDA
					$SQL="SELECT DES_CLAVE,DES_TIENDA,COD_TIENDA FROM MN_TIENDA WHERE COD_TIENDA IN(SELECT COD_TIENDA FROM US_USUTND WHERE IDUSU=".$SESIDUSU.")";
					$RS = sqlsrv_query($maestra, $SQL);
					if ($row = sqlsrv_fetch_array($RS)) {
						$DES_CLAVE = $row['DES_CLAVE'];
						$DES_CLAVE_F="000".$DES_CLAVE;
						$DES_CLAVE_F=substr($DES_CLAVE_F, -3); 
						$DES_TIENDA = $row['DES_TIENDA'];
						$LATIENDA = $DES_CLAVE_F." ".$DES_TIENDA;
						$COD_TIENDA_SEL = $row['COD_TIENDA'];
						$LATIENDA_SI = "Tienda: ".$DES_CLAVE_F." - ".$DES_TIENDA;
						//OBTENER ID_BSN_UN
						$SQL1="SELECT ID_BSN_UN FROM PA_STR_RTL WHERE CD_STR_RT=".$DES_CLAVE;
						$RS1 = sqlsrv_query($arts_conn, $SQL1);
						if ($row1 = sqlsrv_fetch_array($RS1)) {
							$ID_BSN_UN_SEL = $row1['ID_BSN_UN'];
						}
					}
				} else { //if($CTATND==1)

								$COD_TIENDA_SEL=$_POST["COD_TIENDA"];
								if(empty($COD_TIENDA_SEL)) { $COD_TIENDA_SEL=$_GET["COD_TIENDA"];}
								if(empty($COD_TIENDA_SEL)) { $COD_TIENDA_SEL=$_POST["COD_TIENDA_SI"];}
								if(empty($COD_TIENDA_SEL)) { $COD_TIENDA_SEL=$_GET["COD_TIENDA_SI"];}
								
								if(!empty($COD_TIENDA_SEL)) {
									$SQL="SELECT DES_CLAVE,DES_TIENDA FROM MN_TIENDA WHERE COD_TIENDA=".$COD_TIENDA_SEL;
									$RS = sqlsrv_query($maestra, $SQL);
									if ($row = sqlsrv_fetch_array($RS)) {
										$DES_CLAVE_SEL = $row['DES_CLAVE'];
										$DES_CLAVE_FSI="000".$DES_CLAVE_SEL;
										$DES_CLAVE_FSI=substr($DES_CLAVE_FSI, -3); 
										$DES_TIENDA_FSI = $row['DES_TIENDA'];
										$LATIENDA_SI = "Tienda: ".$DES_CLAVE_FSI." - ".$DES_TIENDA_FSI;
				
									}
									$SQL="SELECT ID_BSN_UN FROM PA_STR_RTL WHERE CD_STR_RT=".$DES_CLAVE_SEL;
									$RS = sqlsrv_query($arts_conn, $SQL);
									if ($row = sqlsrv_fetch_array($RS)) {
										$ID_BSN_UN_SEL = $row['ID_BSN_UN'];
									}
								}

				} //if($CTATND==1)

	
	$FILTRO_TIENDANC=" AND ID_BSN_UN=".$ID_BSN_UN_SEL;
	$FILTRO_TIENDA=" AND ID_BSN_UN=?";//$FILTRO_TIENDA=" AND ID_BSN_UN=".$ID_BSN_UN_SEL;
	$P_TIENDA = array($ID_BSN_UN_SEL);
	$FILTRO_TIENDAARTS=" AND T.ID_BSN_UN=?";//$FILTRO_TIENDAARTS=" AND T.ID_BSN_UN=".$ID_BSN_UN_SEL;
	$P_TIENDAARTS = array($ID_BSN_UN_SEL);
	
	if(empty($ID_BSN_UN_SEL) || $ID_BSN_UN_SEL == 0){$FILTRO_TIENDANC="";} 


	$FILTRO_NC="";
	$P_NC = [];
	$B_NNDC=$_POST["B_NNDC"];
	if (empty($B_NNDC)) { $B_NNDC=$_GET["B_NNDC"] ;}
	if (!empty($B_NNDC)) {
		$FILTRO_NC=" AND N.RTN_NMB Like ? " ;//$FILTRO_NC=" AND N.RTN_NMB Like'%".$B_NNDC."%' " ;
		$P_NC[] = "%$B_NNDC%";
	}

	$FILTRO_CLTE="";

	$BUSCAR=$_POST["BUSCAR"];
	if (empty($BUSCAR)) { $BUSCAR=rawurlencode($_GET["BUSCAR"]);}
	
	$FILTRO_TERM="";
	$P_TERM = [];
	$FTERM=$_POST["FTERM"];
	if (empty($FTERM)) { $FTERM=$_GET["FTERM"] ;}
	if (empty($FTERM)) { $FTERM=0 ;}
	if ($FTERM!=0) {
		$FILTRO_TERM=" AND ID_WS=?";//$FILTRO_TERM=" AND ID_WS=".$FTERM ;
		$P_TERM[] = $FTERM;
	}
		
	$FILTRO_OPERA="";
	$P_OPERA = [];
	$FOPERA=$_POST["FOPERA"];
	if (empty($FOPERA)) { $FOPERA=$_GET["FOPERA"] ;}
	if (empty($FOPERA)) { $FOPERA=0 ;}
	if ($FOPERA!=0) {
		$FILTRO_OPERA=" AND ID_OPR=?";//$FILTRO_OPERA=" AND ID_OPR=".$FOPERA ;
		$P_OPERA[] = $FOPERA;
	}
		
	$FILTRO_TICKET="";
	$P_TICKET = [];
	$BOPCION=$_POST["BOPCION"];
	if (empty($BOPCION)) { $BOPCION=$_GET["BOPCION"];}
	if (empty($BOPCION)) { $BOPCION=1;}
				if ($BOPCION==1) {
						$FILTRO_DGFC=" AND ID_TRN IN(SELECT ID_TRN FROM TR_INVC) ";
				} 
	$B_TICKET=rawurlencode($_POST["B_TICKET"]);
	if (empty($B_TICKET)) { $B_TICKET=rawurlencode($_GET["B_TICKET"]) ;}
	if (!empty($B_TICKET)) {
			if($D_GFC==3){
				$FILTRO_TICKET=" AND ID_TRN IN(SELECT ID_TRN FROM TR_INVC WHERE  INVC_NMB Like ?) ";//$FILTRO_TICKET=" AND ID_TRN IN(SELECT ID_TRN FROM TR_INVC WHERE  INVC_NMB Like '%".$B_TICKET."%') ";
			$P_TICKET[] = "%$B_TICKET%";
			} else {
				if ($BOPCION==1) {
					$FILTRO_TICKET=" AND ID_TRN IN(SELECT ID_TRN FROM TR_INVC WHERE INVC_NMB Like ?) ";//$FILTRO_TICKET=" AND ID_TRN IN(SELECT ID_TRN FROM TR_INVC WHERE INVC_NMB Like '%".$B_TICKET."%') ";
					$P_TICKET[] = "%$B_TICKET%";
				} 
				if ($BOPCION==2) {
					$FILTRO_TICKET=" AND AI_TRN=?";//$FILTRO_TICKET=" AND AI_TRN=".$B_TICKET;
					$P_TICKET[] = $B_TICKET;
				} 
				if ($BOPCION==3) {
					$FILTRO_TICKET=" AND ID_TRN IN(SELECT A.ID_TRN FROM TR_INVC A, CO_CPR_CER B WHERE A.ID_CPR = B.ID_CPR AND B.CD_CPR=?) ";//$FILTRO_TICKET=" AND ID_TRN IN(SELECT A.ID_TRN FROM TR_INVC A, CO_CPR_CER B WHERE A.ID_CPR = B.ID_CPR AND B.CD_CPR='".$B_TICKET."') ";
					$P_TICKET[] = $B_TICKET;
				} 
			}
	}

					//CALCULAR MINIMO Y MÁXIMO FECHA REGISTRO TICKET
					$CONSULTA2="SELECT MIN(TS_TRN_END) AS MFECHA FROM TR_TRN WHERE ID_TRN IN (SELECT ID_TRN FROM TR_RTL WHERE QU_UN_RTL_TRN>0) AND FL_VD<>1 AND FL_CNCL<>1";
					$RS2 = sqlsrv_query($arts_conn, $CONSULTA2);
					if ($row = sqlsrv_fetch_array($RS2)){
							$MIN_FECHA_EMS = $row['MFECHA'];
							
							$MIN_FECHA_EMS = date_format($MIN_FECHA_EMS, 'd/m/Y');
					}
					$CONSULTA2="SELECT MAX(TS_TRN_END) AS MFECHA FROM TR_TRN WHERE ID_TRN IN (SELECT ID_TRN FROM TR_RTL WHERE QU_UN_RTL_TRN>0) AND FL_VD<>1 AND FL_CNCL<>1";
					$RS2 = sqlsrv_query($arts_conn, $CONSULTA2);
					if ($row = sqlsrv_fetch_array($RS2)){
							$MAX_FECHA_EMS = $row['MFECHA'];
							
							$MAX_FECHA_EMS = date_format($MAX_FECHA_EMS, 'd/m/Y');
					}
					if (empty($MIN_FECHA_EMS)) { $MAX_FECHA_EMS=date('d/m/Y'); }
					if (empty($MAX_FECHA_EMS)) { $MAX_FECHA_EMS=date('d/m/Y'); }
					
					//FECHA REGISTRO TICKET DESDE
					$DIA_ED=$_POST["DIA_ED"];
					if (empty($DIA_ED)) { $DIA_ED=$_GET["DIA_ED"]; }
					if (empty($DIA_ED)) { $DIA_ED=substr($MAX_FECHA_EMS, 0, 2); }
					$MES_ED=$_POST["MES_ED"];
					if (empty($MES_ED)) { $MES_ED=$_GET["MES_ED"]; }
					if (empty($MES_ED)) { $MES_ED=substr($MAX_FECHA_EMS, 3, 2); }
					$ANO_ED=$_POST["ANO_ED"];
					if (empty($ANO_ED)) { $ANO_ED=$_GET["ANO_ED"]; }
					if (empty($ANO_ED)) { $ANO_ED='20'.substr($MAX_FECHA_EMS, -2); }
					//FECHA REGISTRO HASTA
					$DIA_EH=$_POST["DIA_EH"];
					if (empty($DIA_EH)) { $DIA_EH=$_GET["DIA_EH"]; }
					if (empty($DIA_EH)) { $DIA_EH=substr($MAX_FECHA_EMS, 0, 2); }
					$MES_EH=$_POST["MES_EH"];
					if (empty($MES_EH)) { $MES_EH=$_GET["MES_EH"]; }
					if (empty($MES_EH)) { $MES_EH=substr($MAX_FECHA_EMS, 3, 2); }
					$ANO_EH=$_POST["ANO_EH"];
					if (empty($ANO_EH)) { $ANO_EH=$_GET["ANO_EH"]; }
					if (empty($ANO_EH)) { $ANO_EH='20'.substr($MAX_FECHA_EMS, -2); }
					//CONSTRUYE FECHAS REGISTRO TICKET
					//VALIDAR FECHA_ED
					if (checkdate($MES_ED, $DIA_ED, $ANO_ED)==false) { 
						$MSJE=2 ;
						$DIA_ED=substr($MIN_FECHA_EMS, 0, 2);
						$MES_ED=substr($MIN_FECHA_EMS, 3, 2);
						$ANO_ED='20'.substr($MIN_FECHA_EMS, -2);
						$DIA_EH=substr($MAX_FECHA_EMS, 0, 2);
						$MES_EH=substr($MAX_FECHA_EMS, 3, 2);
						$ANO_EH='20'.substr($MAX_FECHA_EMS, -2);
					}
					$DIA_ED=substr('00'.$DIA_ED, -2);
					$MES_ED=substr('00'.$MES_ED, -2);
					$FECHA_ED=$DIA_ED."/".$MES_ED."/".$ANO_ED;
					
					if (checkdate($MES_EH, $DIA_EH, $ANO_EH)==false) { 
						$MSJE=3 ;
						$DIA_ED=substr($MIN_FECHA_EMS, 0, 2);
						$MES_ED=substr($MIN_FECHA_EMS, 3, 2);
						$ANO_ED='20'.substr($MIN_FECHA_EMS, -2);
						$DIA_EH=substr($MAX_FECHA_EMS, 0, 2);
						$MES_EH=substr($MAX_FECHA_EMS, 3, 2);
						$ANO_EH='20'.substr($MAX_FECHA_EMS, -2);
					}
					$DIA_EH=substr('00'.$DIA_EH, -2);
					$MES_EH=substr('00'.$MES_EH, -2);
					$FECHA_EH=$DIA_EH."/".$MES_EH."/".$ANO_EH;
					//FILTRO FECHA TICKET
					$F_FECHA=" AND (Convert(varchar(20),TS_TRN_BGN, 111) >= Convert(varchar(20), ?, 111) AND Convert(varchar(20), TS_TRN_BGN, 111) <= Convert(varchar(20), ?, 111)) ";//$F_FECHA=" AND (Convert(varchar(20),TS_TRN_BGN, 111) >= Convert(varchar(20),'".$ANO_ED."/".$MES_ED."/".$DIA_ED."', 111) AND Convert(varchar(20), TS_TRN_BGN, 111) <= Convert(varchar(20),'".$ANO_EH."/".$MES_EH."/".$DIA_EH."', 111)) "; 
					$P_FECHA = array($ANO_ED."/".$MES_ED."/".$DIA_ED, $ANO_EH."/".$MES_EH."/".$DIA_EH);


					//CALCULAR MINIMO Y MAXIMO FECHA REGISTRO NOTA DE CREDITO
					$CONSULTA2="SELECT MIN(TS_DEV) AS MFECHA FROM DV_TICKET";
					$RS2 = sqlsrv_query($conn, $CONSULTA2);
					if ($row = sqlsrv_fetch_array($RS2)){
							$MIN_FECHA_NCEMS = $row['MFECHA'];
							$MIN_FECHA_NCEMS = date_format($MIN_FECHA_NCEMS, 'd/m/Y');
					}
					$CONSULTA2="SELECT MAX(TS_DEV) AS MFECHA FROM DV_TICKET";
					$RS2 = sqlsrv_query($conn, $CONSULTA2);
					if ($row = sqlsrv_fetch_array($RS2)){
							$MAX_FECHA_NCEMS = $row['MFECHA'];
							$MAX_FECHA_NCEMS = date_format($MAX_FECHA_NCEMS, 'd/m/Y');
					}
					if (empty($MIN_FECHA_NCEMS)) { $MAX_FECHA_NCEMS=date('d/m/Y'); }
					if (empty($MAX_FECHA_NCEMS)) { $MAX_FECHA_NCEMS=date('d/m/Y'); }
					
					//FECHA REGISTRO NOTA DE CREDITO DESDE
					$DIA_NCED=$_POST["DIA_NCED"];
					if (empty($DIA_NCED)) { $DIA_NCED=$_GET["DIA_NCED"]; }
					if (empty($DIA_NCED)) { $DIA_NCED=substr($MAX_FECHA_NCEMS, 0, 2); }
					$MES_NCED=$_POST["MES_NCED"];
					if (empty($MES_NCED)) { $MES_NCED=$_GET["MES_NCED"]; }
					if (empty($MES_NCED)) { $MES_NCED=substr($MAX_FECHA_NCEMS, 3, 2); }
					$ANO_NCED=$_POST["ANO_NCED"];
					if (empty($ANO_NCED)) { $ANO_NCED=$_GET["ANO_NCED"]; }
					if (empty($ANO_NCED)) { $ANO_NCED='20'.substr($MAX_FECHA_NCEMS, -2); }
					//FECHA REGISTRO HASTA
					$DIA_NCEH=$_POST["DIA_NCEH"];
					if (empty($DIA_NCEH)) { $DIA_NCEH=$_GET["DIA_NCEH"]; }
					if (empty($DIA_NCEH)) { $DIA_NCEH=substr($MAX_FECHA_NCEMS, 0, 2); }
					$MES_NCEH=$_POST["MES_NCEH"];
					if (empty($MES_NCEH)) { $MES_NCEH=$_GET["MES_NCEH"]; }
					if (empty($MES_NCEH)) { $MES_NCEH=substr($MAX_FECHA_NCEMS, 3, 2); }
					$ANO_NCEH=$_POST["ANO_NCEH"];
					if (empty($ANO_NCEH)) { $ANO_NCEH=$_GET["ANO_NCEH"]; }
					if (empty($ANO_NCEH)) { $ANO_NCEH='20'.substr($MAX_FECHA_NCEMS, -2); }
					//CONSTRUYE FECHAS REGISTRO TICKET
					//VALIDAR FECHA_ED
					if (checkdate($MES_NCED, $DIA_NCED, $ANO_NCED)==false) { 
						$MSJE=2 ;
						$DIA_NCED=substr($MIN_FECHA_NCEMS, 0, 2);
						$MES_NCED=substr($MIN_FECHA_NCEMS, 3, 2);
						$ANO_NCED='20'.substr($MIN_FECHA_NCEMS, -2);
						$DIA_NCEH=substr($MAX_FECHA_NCEMS, 0, 2);
						$MES_NCEH=substr($MAX_FECHA_NCEMS, 3, 2);
						$ANO_NCEH='20'.substr($MAX_FECHA_NCEMS, -2);
					}
					$DIA_NCED=substr('00'.$DIA_NCED, -2);
					$MES_NCED=substr('00'.$MES_NCED, -2);
					$FECHA_NCED=$DIA_NCED."/".$MES_NCED."/".$ANO_NCED;
					
					if (checkdate($MES_NCEH, $DIA_NCEH, $ANO_NCEH)==false) { 
						$MSJE=3 ;
						$DIA_NCED=substr($MIN_FECHA_NCEMS, 0, 2);
						$MES_NCED=substr($MIN_FECHA_NCEMS, 3, 2);
						$ANO_NCED='20'.substr($MIN_FECHA_NCEMS, -2);
						$DIA_NCEH=substr($MAX_FECHA_NCEMS, 0, 2);
						$MES_NCEH=substr($MAX_FECHA_NCEMS, 3, 2);
						$ANO_NCEH='20'.substr($MAX_FECHA_NCEMS, -2);
					}
					$DIA_NCEH=substr('00'.$DIA_NCEH, -2);
					$MES_NCEH=substr('00'.$MES_NCEH, -2);
					$FECHA_NCEH=$DIA_NCEH."/".$MES_NCEH."/".$ANO_NCEH;
					//FILTRO FECHA REGISTRO

					$FNC_FECHA=" AND Convert(varchar(20),T.TS_TRN_BGN, 111) >=  Convert(varchar(20), ?, 111) AND Convert(varchar(20), T.TS_TRN_BGN, 111) <= Convert(varchar(20), ?, 111) ";//$FNC_FECHA=" AND Convert(varchar(20),T.TS_TRN_BGN, 111) >=  Convert(varchar(20),'".$ANO_NCED."/".$MES_NCED."/".$DIA_NCED."', 111) AND Convert(varchar(20), T.TS_TRN_BGN, 111) <= Convert(varchar(20),'".$ANO_NCEH."/".$MES_NCEH."/".$DIA_NCEH."', 111) ";
					$PNC_FECHA = array($ANO_NCED."/".$MES_NCED."/".$DIA_NCED, $ANO_NCEH."/".$MES_NCEH."/".$DIA_NCEH);				
	
					//Se mueve codigo de creacion archivo
					include("gen_Gft_Vtl_Card.php");																	

?>


</head>
<body onLoad="nobackbutton(); <?=$ConsIlimitada?>">

<?php include("../headerregusu.php");?>
<?php include("titulo_menu.php");?>
<table width="100%" height="100%">
<tr>
<?php if($NOMENU!=1){ ?>
        <td align="right"  width="200" bgcolor="#FFFFFF"><?php include("menugeneral.php");?></td> 
<?php } ?>        
<td <?php if($NOMENU==1){ ?> style="padding-left:10px"<?php } ?> >
<?php
if ($MSJE==1) {$ELMSJ="Debe seleccionar al menos un Art&iacute;culo en Devoluci&oacute;n";} 
if ($MSJE==2) {$ELMSJ="Se ha cancelado el registro de Devoluci&oacute;n";}
if ($MSJE==3) {$ELMSJ="Ha ocurrido un evento inesperado en el proceso de Devoluci&oacute;n, por favor vuelva a intentar.";} 
if ($MSJE==4) {$ELMSJ="Ha ocurrido un evento inesperado en el proceso de Devoluci&oacute;n, por favor contacte a Soporte.";} 
if ($MSJE==5) {$ELMSJ="Se ha registrado la Devoluci&oacute;n del Ticket";} 
if ($MSJE==6) {$ELMSJ="Esta factura ya esta en proceso de Devoluci&oacute;n.";} 
if ($MSJE==7) {$ELMSJ="Lectura de tarjeta incorrecta, por favor intente nuevamente.";} 
if ($MSJE <> "") {
?>
<div id="GMessaje" onClick="QuitarGMessage();"><a href="#" onClick="QuitarGMessage();" style="color:#111111;"><?=($ELMSJ==''?$MSJE:$ELMSJ)?></a></div>
<?php }?>
        <table width="100%">
        <tr><td>
              <?php if($LIST==1){ ?>
				<h2>
				<?=$MODULO." ".$LAPAGINA?>
				<?php
                if(!empty($ID_DEV) or !empty($NOTADECREDITO) ){
						//GENERAR NÚMERO DE NOTA DE CREDITO
						//LLL-PPP-CCCCCCCCCCCC
						//OBTENER LOCAL SRI
						if($TipoDevol<>80){
								$NOTA = substr("000".$COD_SRI, -3).substr("000".$NUMPOSDEV_STR, -3).substr("000000000".$NUM_NC, -9);
						} else {
								$NOTA = "";
						}
						echo " ".htmlspecialchars($NOTA);
                }?>
              </h2>
              <table width="100%" id="Filtro">
                <form action="reg_Devol_CER.php?BSC_NDC=1&LIST=1" method="post" name="forming" id="forming">
                  <tr>
                    <td>
                      <?php
                                            $VERTND_UNO = 0;
                                            //VERIFICAR TIENDAS ASOCIADAS A USUARIO
                                            $SQL="SELECT COUNT(COD_TIENDA) AS CTATND FROM US_USUTND WHERE IDUSU=".$SESIDUSU;
                                            $RS = sqlsrv_query($maestra, $SQL);
                                            if ($row = sqlsrv_fetch_array($RS)) {
                                                $CTATND = $row['CTATND'];
                                            }
                                            //SI CTATND==0 USUARIO CENTRAL, SELECCIONAR NEGOCIO Y LOCAL
                                            //SI CTATND==1 DESPLEGAR LOCAL
                                            //SI CTATND>1 DESPLEGAR LISTADO DE LOCALES
                                            if($CTATND==1){
                                                //OBTENER TIENDA
                                                $SQL="SELECT DES_CLAVE,DES_TIENDA,COD_TIENDA FROM MN_TIENDA WHERE COD_TIENDA IN(SELECT COD_TIENDA FROM US_USUTND WHERE IDUSU=".$SESIDUSU.")";
                                                $RS = sqlsrv_query($maestra, $SQL);
                                                if ($row = sqlsrv_fetch_array($RS)) {
                                                    $DES_CLAVE = $row['DES_CLAVE'];
                                                    $DES_CLAVE_F="000".$DES_CLAVE;
                                                    $DES_CLAVE_F=substr($DES_CLAVE_F, -3); 
                                                    $DES_TIENDA = $row['DES_TIENDA'];
                                                    $LATIENDA = $DES_CLAVE_F." ".$DES_TIENDA;
                                                    $COD_TIENDA_SEL = $row['COD_TIENDA'];
                                                    $LATIENDA_SI = "Tienda: ".$DES_CLAVE_F." - ".$DES_TIENDA;
                                                    //OBTENER ID_BSN_UN
                                                    $SQL1="SELECT ID_BSN_UN FROM PA_STR_RTL WHERE CD_STR_RT=".$DES_CLAVE;
                                                    $RS1 = sqlsrv_query($arts_conn, $SQL1);
                                                    if ($row1 = sqlsrv_fetch_array($RS1)) {
                                                        $ID_BSN_UN_SEL = $row1['ID_BSN_UN'];
                                                    }
                                                }
                                                ?>
                                                    <input type="text" style="width: auto; min-width: 250px; border:none; text-align: center; color: #FFFFFF; background: #666666" readonly value="<?=$LATIENDA; ?>">
                                                <?php
                                            }//if($CTATND==1)
                
                                            if($CTATND>1){//SELECCIONAR TIENDA
                                            $VERTND_UNO = 1;
											?>
                      <select name="COD_TIENDA" onChange="document.forms.forming.submit();">
                        <option value="0">SELECCIONAR TIENDA</option>
                        <?php 
                                                            $SQL="SELECT COD_TIENDA,DES_CLAVE,DES_TIENDA FROM MN_TIENDA WHERE COD_TIENDA IN(SELECT COD_TIENDA FROM US_USUTND WHERE IDUSU=".$SESIDUSU.") ORDER BY DES_CLAVE ASC";
                                                            $RS = sqlsrv_query($maestra, $SQL);
                                                            while ($row = sqlsrv_fetch_array($RS)) {
															$COD_TIENDA = $row['COD_TIENDA'];
															$DES_CLAVE = $row['DES_CLAVE'];
															$DES_CLAVE_F="000".$DES_CLAVE;
															$DES_CLAVE_F=substr($DES_CLAVE_F, -3); 
															$DES_TIENDA = $row['DES_TIENDA'];
															$LATIENDA = $DES_CLAVE_F." ".$DES_TIENDA;
															 ?>
																<option value="<?=$COD_TIENDA ?>"  <?php if($COD_TIENDA==$COD_TIENDA_SEL) {echo "Selected";} ?>><?=$LATIENDA ?></option>
															<?php 
                                                            }
                                                             ?>
                      </select>
                      <?php
                                            }//$CTATND>1)
                
                                            if($CTATND==0){//SELECCIONAR TIENDA
                                                    if(!empty($CD_ITM_SI) && !empty($COD_TIENDA_SEL)){
                                                                $SQL="SELECT DES_CLAVE,DES_TIENDA,COD_TIENDA FROM MN_TIENDA WHERE COD_TIENDA =".$COD_TIENDA_SEL;
                                                                $RS = sqlsrv_query($maestra, $SQL);
                                                                if ($row = sqlsrv_fetch_array($RS)) {
                                                                    $DES_CLAVE = $row['DES_CLAVE'];
                                                                    $DES_CLAVE_F="000".$DES_CLAVE;
                                                                    $DES_CLAVE_F=substr($DES_CLAVE_F, -3); 
                                                                    $DES_TIENDA = $row['DES_TIENDA'];
                                                                    $LATIENDA = $DES_CLAVE_F." ".$DES_TIENDA;
                                                                    $COD_TIENDA_SEL = $row['COD_TIENDA'];
                                                                }
                                                                ?>
                                                                    <h5><?=$LATIENDA ?></h5>
                                                                    <input type="hidden" name="COD_TIENDA" value="<?=htmlspecialchars($COD_TIENDA_SEL, ENT_QUOTES);?>">
                                                        <?php
                                                    } else {
                                                        ?>
                      <select id="COD_TIENDA" name="COD_TIENDA" onChange="document.forms.forming.submit();">
                        <option value="0">SELECCIONAR TIENDA</option>
                        <?php
                                                                                $SQL="SELECT DES_CLAVE,DES_TIENDA,COD_TIENDA FROM MN_TIENDA WHERE IND_ACTIVO=1 ORDER BY DES_CLAVE ASC";
                                                                                $RS = sqlsrv_query($maestra, $SQL);
                                                                                $VERTND=0;
                                                                                while ($row = sqlsrv_fetch_array($RS)) {
                                                                                    $NUM_TIENDA = $row['DES_CLAVE'];
                                                                                    $NUM_TIENDA_F="000".$NUM_TIENDA;
                                                                                    $NUM_TIENDA_F=substr($NUM_TIENDA_F, -3); 
                                                                                    $STRDES = $row['DES_TIENDA'];
                                                                                    $STRCOD =$row['COD_TIENDA'];
                                                                                 ?>
                                                                                        <option value="<?=$STRCOD ?>" <?php if($STRCOD==$COD_TIENDA_SEL) {echo "Selected";} ?> ><?=$NUM_TIENDA_F." - ".$STRDES ?></option>
                                                                                <?php 
                                                                                }
                                                                    ?>
                      </select>
                      <?php
                                                    }//!empty($CD_ITM_SI) && !empty($COD_TIENDA_SEL)
                                            }//if($CTATND==0)
                                        ?>

                      <label style="clear:left; margin-left:10px" for="FECHA_EM_D">Desde </label>
                      <input name="DIA_NCED" type="text" id="DIA_NCED" value="<?= htmlspecialchars($DIA_NCED, ENT_QUOTES); ?>" size="2" maxlength="2" onKeyPress="return acceptNum(event);">
                     <select name="MES_NCED"  id="MES_NCED">
                            <option value="01" <?php  if ($MES_NCED==1) { echo "SELECTED";}?>>Enero</option>
                            <option value="02" <?php  if ($MES_NCED==2) { echo "SELECTED";}?>>Febrero</option>
                            <option value="03" <?php  if ($MES_NCED==3) { echo "SELECTED";}?>>Marzo</option>
                            <option value="04" <?php  if ($MES_NCED==4) { echo "SELECTED";}?>>Abril</option>
                            <option value="05" <?php  if ($MES_NCED==5) { echo "SELECTED";}?>>Mayo</option>
                            <option value="06" <?php  if ($MES_NCED==6) { echo "SELECTED";}?>>Junio</option>
                            <option value="07" <?php  if ($MES_NCED==7) { echo "SELECTED";}?>>Julio</option>
                            <option value="08" <?php  if ($MES_NCED==8) { echo "SELECTED";}?>>Agosto</option>
                            <option value="09" <?php  if ($MES_NCED==9) { echo "SELECTED";}?>>Septiembre</option>
                            <option value="10" <?php  if ($MES_NCED==10) { echo "SELECTED";}?>>Octubre</option>
                            <option value="11" <?php  if ($MES_NCED==11) { echo "SELECTED";}?>>Noviembre</option>
                            <option value="12" <?php  if ($MES_NCED==12) { echo "SELECTED";}?>>Diciembre</option>
                       </select>
                       <input name="ANO_NCED" type="text"  id="ANO_NCED" value="<?= htmlspecialchars($ANO_NCED, ENT_QUOTES); ?>" size="4" maxlength="4">
                      <label for="FECHA_EM_H" >Hasta</label>
                      <input name="DIA_NCEH" type="text" id="DIA_NCEH" value="<?= htmlspecialchars($DIA_NCEH, ENT_QUOTES); ?>" size="2" maxlength="2" onKeyPress="return acceptNum(event);">
                      <select name="MES_NCEH"  id="MES_NCEH">
                            <option value="01" <?php  if ($MES_NCEH==1) { echo "SELECTED";}?>>Enero</option>
                            <option value="02" <?php  if ($MES_NCEH==2) { echo "SELECTED";}?>>Febrero</option>
                            <option value="03" <?php  if ($MES_NCEH==3) { echo "SELECTED";}?>>Marzo</option>
                            <option value="04" <?php  if ($MES_NCEH==4) { echo "SELECTED";}?>>Abril</option>
                            <option value="05" <?php  if ($MES_NCEH==5) { echo "SELECTED";}?>>Mayo</option>
                            <option value="06" <?php  if ($MES_NCEH==6) { echo "SELECTED";}?>>Junio</option>
                            <option value="07" <?php  if ($MES_NCEH==7) { echo "SELECTED";}?>>Julio</option>
                            <option value="08" <?php  if ($MES_NCEH==8) { echo "SELECTED";}?>>Agosto</option>
                            <option value="09" <?php  if ($MES_NCEH==9) { echo "SELECTED";}?>>Septiembre</option>
                            <option value="10" <?php  if ($MES_NCEH==10) { echo "SELECTED";}?>>Octubre</option>
                            <option value="11" <?php  if ($MES_NCEH==11) { echo "SELECTED";}?>>Noviembre</option>
                            <option value="12" <?php  if ($MES_NCEH==12) { echo "SELECTED";}?>>Diciembre</option>
                        </select>
                        <input name="ANO_NCEH" type="text"  id="ANO_NCEH" value="<?= htmlspecialchars($ANO_NCEH, ENT_QUOTES); ?>" size="4" maxlength="4" onKeyPress="return acceptNum(event);">
					
                        <input style="clear:left; text-align:right" name="B_NNDC" type="text"  id="B_NNDC" value="<?= htmlspecialchars($B_NNDC, ENT_QUOTES); ?>" size="20" maxlength="20" onKeyPress="return acceptNumGuion(event);">
                       <input name="BSC_NDC" type="submit" id="BSC_NDC" value="Buscar Nota de Cr&eacute;dito">
                       <input name="LIMPIAR" type="button" id="LIMPIAR" value="Limpiar" onClick="pagina('reg_Devol_CER.php?LIST=1');">
              </td>
                  </tr>
                </form>
              </table>
              <!-- FIN FILTRO, INICIO LISTADO -->
              <table style="margin:10px 20px; ">
                <tr>
                  <td>
                    <?php if(!empty($BSC_NDC)) { //INICIO RESULTADO BUSCAR ?>
                    <?php
					//CUENTA REGISTROS

					$P_RS = array_merge($PNC_FECHA, $P_TIENDAARTS, $P_NC);					
					$CONSULTA="SELECT COUNT(T.ID_TRN) AS CUENTA FROM TR_RTN N, TR_TRN T WHERE N.ID_TRN=T.ID_TRN ".$FNC_FECHA.$FILTRO_TIENDAARTS.$FILTRO_NC." AND T.FL_CNCL<>1"; //$CONSULTA="SELECT COUNT(T.ID_TRN) AS CUENTA FROM TR_RTN N, TR_TRN T WHERE N.ID_TRN=T.ID_TRN ".$FNC_FECHA.$FILTRO_TIENDAARTS.$FILTRO_NC." AND T.FL_CNCL<>1";
					$RS = sqlsrv_query($arts_conn, $CONSULTA, $P_RS);
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
					if($TOTALREG>=1){ //ENCONTRO AL MENOS UNO
					//CONSULTA RESULTADO BUSQUEDA
					$P_VISTA = array_merge($PNC_FECHA, $P_TIENDAARTS, $P_NC);
					array_unshift($P_VISTA, intval($CTP));
					array_push($P_VISTA, intval($LINF), intval($LSUP));
					$CONSULTA= "SELECT * FROM (SELECT T.*,N.RTN_NMB, ROW_NUMBER() OVER (PARTITION BY ? ORDER BY T.ID_TRN DESC) ROWNUMBER FROM TR_RTN N, TR_TRN T WHERE N.ID_TRN=T.ID_TRN  ".$FNC_FECHA.$FILTRO_TIENDAARTS.$FILTRO_NC." AND T.FL_CNCL<>1) AS TABLEWITHROWNUMBER WHERE ROWNUMBER BETWEEN ? AND ? ";
								$RS_VISTA = sqlsrv_query($arts_conn, $CONSULTA, $P_VISTA);
				   ?>
                    <table id="Listado">
					<tr>
							<th colspan="3" style="padding-left: 36px">T.Cont/ Nota de Cr&eacute;dito</th>
							<th>Local<br>Terminal</th>
							<th>Operador</th>
							<th style="border-left-width:3px; border-left-style:solid; border-left-color:#DFDFDF">SUBTOTAL</th>
							<th>DSCTO</th>
							<th>IMPUESTO</th>
							<th>TOTAL</th>
							<th style="border-left-width:3px; border-left-style:solid; border-left-color:#DFDFDF">Medio</th>
							<th style="text-align:right">Emisi&oacute;n</th>
					</tr>
                    <?php
					$NUM_ARTS=0;
                            while ($ROW_VISTA = sqlsrv_fetch_array($RS_VISTA)) {
								// fjjf
								// buscamos el DEV_TICKET si existe utilizando el RTN_NMB
								$TYPDEV=0;
								$ID_DEV=0;
								$q0 = sqlsrv_query($conn,'SELECT * FROM DV_TICKET WHERE NOTA=?',array($ROW_VISTA['RTN_NMB']));
								$_lclVarDvTicket = @sqlsrv_fetch_array($q0);
								$ID_DEV=$_lclVarDvTicket['ID_DEV'];
								$TYPDEV=$_lclVarDvTicket['TY_DEV'];
								$ID_TRN = $ROW_VISTA['ID_TRN'];
								$AI_TRN = $ROW_VISTA['AI_TRN'];
								$TS_TRN_BGN = $ROW_VISTA['TS_TRN_BGN'];
								$DC_DY_BSN = $ROW_VISTA['DC_DY_BSN'];
								$SQL = "SELECT CD_TYP_TRN_RTL FROM TR_RTL WHERE ID_TRN=".$ID_TRN;
								$RS = sqlsrv_query($arts_conn, $SQL);
								if ($row= sqlsrv_fetch_array($RS)){
									$TY_TRN_SLS = $row['CD_TYP_TRN_RTL'];
								}
								//NOTA DE CREDITO/FACTURA/TICKET
								$NUM_DOC="";
								$CLIENTE="";
								if($TY_TRN_SLS==0){$TYDOC="TK"; $COLOR_DOC="#006064";}
								if($TY_TRN_SLS==1){$TYDOC="FC"; $COLOR_DOC="#7A2A9C";}
								if($TY_TRN_SLS==2){$TYDOC="NC"; $COLOR_DOC="#E65100";}
								if($TY_TRN_SLS==1){
										$ID_CPR=0;
										$SQLF="SELECT INVC_NMB,ID_CPR,FL_CP,ID_CST,NM_CST FROM TR_INVC WHERE ID_TRN=?"; //$SQLF="SELECT INVC_NMB,ID_CPR,FL_CP,ID_CST,NM_CST FROM TR_INVC WHERE ID_TRN=".$ID_TRN;
											$RSF = sqlsrv_query($arts_conn, $SQLF, array($ID_TRN));
										if ($rowF = sqlsrv_fetch_array($RSF)) {
											$NUM_DOC = $rowF['INVC_NMB'];
											$ID_CPR = $rowF['ID_CPR'];
											$FL_CP = $rowF['FL_CP'];
											$ID_CST = $rowF['ID_CST'];
											$NM_CST = $rowF['NM_CST'];
										}

										if(empty($ID_CPR)){
											//BUSCAR EN ID_CST
											if(empty($ID_CST)){
												if(!empty($_lclVarDvTicket['CONSFINAL_TIPO_IDENT'])) {
													$CLIENTE=$_lclVarDvTicket['CONSFINAL_NOMBRE']."<BR>RUC/CED/PAS: ".$_lclVarDvTicket['CONSFINAL_IDENT'];
												} else {
													$CLIENTE="CONSUMIDOR FINAL";
												}
											} else {
												$CLIENTE=$NM_CST."<BR>RUC/CED/PAS: ".$ID_CST;
											}
										} else {
													if(!empty($ID_CST) or is_null($ID_CST)){
															if($FL_CP==0){ //CEDULA O RUC
																		$SQLF1="SELECT NOMBRE,CD_CPR FROM CO_CPR_CER WHERE ID_CPR=".$ID_CPR;
																		$RSF1 = sqlsrv_query($arts_conn, $SQLF1);
																		if ($rowF1 = sqlsrv_fetch_array($RSF1)) {
																			$NOMBRE_F = $rowF1['NOMBRE'];
																			$IDENTIFICACION_F = $rowF1['CD_CPR'];
																			$CLIENTE=$NOMBRE_F."<BR>RUC/CED/PAS: ".$IDENTIFICACION_F;
																		}
															} else { //PASAPORTE
																		$SQLF1="SELECT NOMBRE,CD_CPR FROM CO_EXT_CER WHERE ID_CPR=".$ID_CPR;
																		$RSF1 = sqlsrv_query($arts_conn, $SQLF1);
																		if ($rowF1 = sqlsrv_fetch_array($RSF1)) {
																			$NOMBRE_F = $rowF1['NOMBRE'];
																			$IDENTIFICACION_F = $rowF1['CD_CPR'];
																			$CLIENTE=$NOMBRE_F."<BR>RUC/CED/PAS: ".$IDENTIFICACION_F;
																		}
															}
													} else {
														$CLIENTE=$NM_CST."<BR>RUC/CED/PAS: ".$ID_CST;
													}
										} //if(is_null($ID_CPR) || empty($ID_CPR)

								}
                                $CONSFINAL_TIPO_IDENT="";
								$CONSFINAL_IDENT="";
								$CONSFINAL_NOMBRE="";
								if($TY_TRN_SLS==2){
									$SQL = "SELECT RTN_NMB,ORGL_INVC_NMB,CD_OPR,CD_SUP,CONSFINAL_TIPO_IDENT,CONSFINAL_IDENT,CONSFINAL_NOMBRE FROM TR_RTN WHERE ID_TRN=?"; //$SQL = "SELECT RTN_NMB,ORGL_INVC_NMB,CD_OPR,CD_SUP,CONSFINAL_TIPO_IDENT,CONSFINAL_IDENT,CONSFINAL_NOMBRE FROM TR_RTN WHERE ID_TRN=".$ID_TRN;
									$RS = sqlsrv_query($arts_conn, $SQL, array($ID_TRN));
										if ($row= sqlsrv_fetch_array($RS)) {
												$NUM_DOC = $row['RTN_NMB'];
												$ORGL_INVC_NMB= $row['ORGL_INVC_NMB'];
												$CD_EMITE= intval($row['CD_OPR']);
												$CD_GERENTE= intval($row['CD_SUP']);
												$CONSFINAL_TIPO_IDENT=$row['CONSFINAL_TIPO_IDENT'];
								                $CONSFINAL_IDENT=$row['CONSFINAL_IDENT'];
								                $CONSFINAL_NOMBRE=$row['CONSFINAL_NOMBRE'];
												$EMITE_NC="";
												if($CD_EMITE != 0){
													$SQLOP="SELECT NOMBRE,APELLIDO_P,APELLIDO_M FROM OP_OPERADOR WHERE CC_OPERADOR=".$CD_EMITE;
													$RSOP=sqlsrv_query($Opera_conn, $SQLOP);
													if ($rowOpera= sqlsrv_fetch_array($RSOP)) {
															$NOMB_CDOPR = $rowOpera['NOMBRE']." ".$rowOpera['APELLIDO_P']." ".$rowOpera['APELLIDO_M'];
													}
													$EMITE_NC="Emite: ".$CD_EMITE." ".$NOMB_CDOPR."<br>";
												}
												$AUTORIZA_NC="";
												if($CD_GERENTE != 0){
													$SQLOP="SELECT NOMBRE,APELLIDO_P,APELLIDO_M FROM OP_OPERADOR WHERE CC_OPERADOR=".$CD_GERENTE;
													$RSOP=sqlsrv_query($Opera_conn, $SQLOP);
													if ($rowOpera= sqlsrv_fetch_array($RSOP)) {
															$NOMB_CDSUP = $rowOpera['NOMBRE']." ".$rowOpera['APELLIDO_P']." ".$rowOpera['APELLIDO_M'];
													}
													$AUTORIZA_NC="Autoriza: ".$CD_GERENTE." ".$NOMB_CDSUP."<br>";
												}
												//OBTENER CLIENTE
												$ID_CPR=0;
												$SQLF="SELECT INVC_NMB,ID_CPR,FL_CP,ID_CST,NM_CST FROM TR_INVC WHERE INVC_NMB=?"; //$SQLF="SELECT INVC_NMB,ID_CPR,FL_CP,ID_CST,NM_CST FROM TR_INVC WHERE INVC_NMB='".$ORGL_INVC_NMB."'";
												$RSF = sqlsrv_query($arts_conn, $SQLF, array($ORGL_INVC_NMB));
												if ($rowF = sqlsrv_fetch_array($RSF)) {
													$INVC_NMB = $rowF['INVC_NMB'];
													$ID_CPR = $rowF['ID_CPR'];
													$FL_CP = $rowF['FL_CP'];
													$ID_CST = $rowF['ID_CST'];
													$NM_CST = $rowF['NM_CST'];
												}
                                                if (!empty($CONSFINAL_IDENT))
												{
													$NOMBRE_F = $CONSFINAL_NOMBRE;
													$IDENTIFICACION_F = $CONSFINAL_IDENT;
													$CLIENTE=$NOMBRE_F."<BR>RUC/CED/PAS: ".$IDENTIFICACION_F;
												}
												else
												{
													if(empty($ID_CPR)){
														//BUSCAR EN ID_CST
														if(empty($ID_CST) || $ID_CST=='CONSUMIDOR FINAL'){
															if(!empty($_lclVarDvTicket['CONSFINAL_TIPO_IDENT'])) {
																$CLIENTE=$_lclVarDvTicket['CONSFINAL_NOMBRE']."<BR>RUC/CED/PAS: ".$_lclVarDvTicket['CONSFINAL_IDENT'];
															} else {
																$CLIENTE="CONSUMIDOR FINAL";
															}
														} else {
															$CLIENTE=$NM_CST."<BR>RUC/CED/PAS: ".$ID_CST;
														}
													} else {
																if(!empty($ID_CST) or is_null($ID_CST)){
																		if($FL_CP==0){ //CEDULA O RUC
																					$SQLF1="SELECT NOMBRE,CD_CPR FROM CO_CPR_CER WHERE ID_CPR=".$ID_CPR;
																					$RSF1 = sqlsrv_query($arts_conn, $SQLF1);
																					if ($rowF1 = sqlsrv_fetch_array($RSF1)) {
																						$NOMBRE_F = $rowF1['NOMBRE'];
																						$IDENTIFICACION_F = $rowF1['CD_CPR'];
																						$CLIENTE=$NOMBRE_F."<BR>RUC/CED/PAS: ".$IDENTIFICACION_F;
																					}
																		} else { //PASAPORTE
																					$SQLF1="SELECT NOMBRE,CD_CPR FROM CO_EXT_CER WHERE ID_CPR=".$ID_CPR;
																					$RSF1 = sqlsrv_query($arts_conn, $SQLF1);
																					if ($rowF1 = sqlsrv_fetch_array($RSF1)) {
																						$NOMBRE_F = $rowF1['NOMBRE'];
																						$IDENTIFICACION_F = $rowF1['CD_CPR'];
																						$CLIENTE=$NOMBRE_F."<BR>RUC/CED/PAS: ".$IDENTIFICACION_F;
																					}
																		}
																} else {
																	$CLIENTE=$NM_CST."<BR>RUC/CED/PAS: ".$ID_CST;
																}
													} //if(is_null($ID_CPR) || empty($ID_CPR)
												}	
										}
								}

								//TERMINAL POS
								$ID_WS = $ROW_VISTA['ID_WS'];
								$TERMINAL = "NR";
								$S2 = "SELECT CD_WS FROM AS_WS WHERE ID_WS=?"; //$S2 = "SELECT CD_WS FROM AS_WS WHERE ID_WS=" . $ID_WS;
								$RS2 = sqlsrv_query($arts_conn, $S2, array( $ID_WS));
								if ($row2 = sqlsrv_fetch_array($RS2)) { 
									$TERMINAL = $row2['CD_WS'];
								}
								$TERMINAL_F = "000" . $TERMINAL;
								$TERMINAL_F = "T: ".substr($TERMINAL_F, -3);
								//TIENDA	
								$ID_BSN_UN = $ROW_VISTA['ID_BSN_UN'];
								$TIENDA = "NR";
								$S2 = "SELECT CD_STR_RT, INC_PRC FROM PA_STR_RTL WHERE ID_BSN_UN=" . $ID_BSN_UN;
								$RS2 = sqlsrv_query($arts_conn, $S2);
								if ($row2 = sqlsrv_fetch_array($RS2)) { 
									$CODTIENDA = $row2['CD_STR_RT']; 
								}
								$COD_TIENDA_F = "000" . $CODTIENDA;
								$COD_TIENDA_F = "L: ".substr($COD_TIENDA_F, -3);
								//OPERADOR
								$ID_OPR = $ROW_VISTA['ID_OPR'];
									$OPERADOR="";
									$NOMB_ACE="";
									$S2="SELECT CD_OPR FROM PA_OPR WHERE ID_OPR=".$ID_OPR;
									$RS2 = sqlsrv_query($arts_conn, $S2);
									if ($row2 = sqlsrv_fetch_array($RS2)) {
										$CD_OPR = $row2['CD_OPR'];
									}	
									$S2="SELECT NOMB_ACE FROM OP_OPERADOR WHERE CC_OPERADOR=".$CD_OPR;
									$RS2 = sqlsrv_query($Opera_conn, $S2);
									if ($row2 = sqlsrv_fetch_array($RS2)) {
										$NOMB_ACE = $row2['NOMB_ACE'];
									}
									if($CD_OPR==9999){$NOMB_ACE="SIST. DEVOLUCIONES";}
									if(empty($NOMB_ACE)){$NOMB_ACE="<span style='color:#cc0000'>Nombre NO registrado</span>";}
									$OPERADOR=$CD_OPR."<br>".$NOMB_ACE;


											//TR_TOT_RTL x TR_TOT_TYP
											$GROSS_POS = 0;
													$S2="SELECT MO_TOT_RTL_TRN  FROM TR_TOT_RTL WHERE ID_TR_TOT_TYP=1 AND ID_TRN=".$ID_TRN;
													$RS2 = sqlsrv_query($arts_conn,$S2);
													if ($row2 = sqlsrv_fetch_array($RS2)) {
														$GROSS_POS = $row2['MO_TOT_RTL_TRN'];
													}
											$GROSS_NEG = 0;
													$S2="SELECT MO_TOT_RTL_TRN  FROM TR_TOT_RTL WHERE ID_TR_TOT_TYP=2 AND ID_TRN=".$ID_TRN;
													$RS2 = sqlsrv_query($arts_conn,$S2);
													if ($row2 = sqlsrv_fetch_array($RS2)) {
														$GROSS_NEG = $row2['MO_TOT_RTL_TRN'];
													}
											$TR_TAX = 0;
													$S2="SELECT MO_TOT_RTL_TRN  FROM TR_TOT_RTL WHERE ID_TR_TOT_TYP=3 AND ID_TRN=".$ID_TRN;
													$RS2 = sqlsrv_query($arts_conn,$S2);
													if ($row2 = sqlsrv_fetch_array($RS2)) {
														$TR_TAX = $row2['MO_TOT_RTL_TRN'];
													}
											$DESC_POS = 0;
													$S2="SELECT MO_TOT_RTL_TRN FROM TR_TOT_RTL WHERE ID_TR_TOT_TYP=4 AND ID_TRN=".$ID_TRN;
													$RS2 = sqlsrv_query($arts_conn,$S2);
													if ($row2 = sqlsrv_fetch_array($RS2)) {
														$DESC_POS = $row2['MO_TOT_RTL_TRN'];
													}
											$DESC_NEG = 0;
													$S2="SELECT MO_TOT_RTL_TRN  FROM TR_TOT_RTL WHERE ID_TR_TOT_TYP=5 AND ID_TRN=".$ID_TRN;
													$RS2 = sqlsrv_query($arts_conn,$S2);
													if ($row2 = sqlsrv_fetch_array($RS2)) {
														$DESC_NEG = $row2['MO_TOT_RTL_TRN'];
													}
											$VECTOR_POS = 0;
													$S2="SELECT MO_TOT_RTL_TRN  FROM TR_TOT_RTL WHERE ID_TR_TOT_TYP=6 AND ID_TRN=".$ID_TRN;
													$RS2 = sqlsrv_query($arts_conn,$S2);
													if ($row2 = sqlsrv_fetch_array($RS2)) {
														$VECTOR_POS = $row2['MO_TOT_RTL_TRN'];
													}
											$VECTOR_NEG = 0;
													$S2="SELECT MO_TOT_RTL_TRN FROM TR_TOT_RTL WHERE ID_TR_TOT_TYP=7 AND ID_TRN=".$ID_TRN;
													$RS2 = sqlsrv_query($arts_conn,$S2);
													if ($row2 = sqlsrv_fetch_array($RS2)) {
														$VECTOR_NEG = $row2['MO_TOT_RTL_TRN'];
													}

											if($TY_TRN_SLS==1 or $TY_TRN_SLS==0){
													$ORGL_INVC_NMB_F="";
													$TR_SUBT = $GROSS_POS - ($GROSS_NEG + $TR_TAX) ;
													$PS_DSCTO = $DESC_POS - $DESC_NEG; //DESCUENTOS (INCLUIDOS EN GROSS_NEG --> RETIRAR DE SUBTOTAL)
													$PS_VECTOR = $VECTOR_POS - $VECTOR_NEG; //PROMOCIONES (NO INCLUIDOS EN GROSS_NEG)
													$TR_DSCTO = $PS_DSCTO + $PS_VECTOR; //TOTAL DESCUENTOS
													$TR_TOTAL=($TR_SUBT - $TR_DSCTO)  + $TR_TAX;
											}

											if($TY_TRN_SLS==2){
													$ORGL_INVC_NMB_F="<BR>FC: ".$ORGL_INVC_NMB;
													$TR_SUBT = $GROSS_NEG - ($GROSS_POS + $TR_TAX) ;
													$PS_DSCTO = $DESC_NEG - $DESC_POS; //DESCUENTOS (INCLUIDOS EN GROSS_NEG --> RETIRAR DE SUBTOTAL)
													$PS_VECTOR = $VECTOR_NEG - $VECTOR_POS; //PROMOCIONES (NO INCLUIDOS EN GROSS_NEG)
													$TR_DSCTO = $PS_DSCTO + $PS_VECTOR; //TOTAL DESCUENTOS
													if($TR_DSCTO<0){$TR_DSCTO = $TR_DSCTO * -1;}
													$TR_TOTAL=($TR_SUBT - $TR_DSCTO)  + $TR_TAX;
											}

											$TR_SUBT_F=$TR_SUBT/$DIVCENTS;
											$TR_SUBT_F=number_format($TR_SUBT_F, $CENTS, $GLBSDEC, $GLBSMIL);
											$TR_DSCTO_F=$TR_DSCTO/$DIVCENTS;
											$TR_DSCTO_F=number_format($TR_DSCTO_F, $CENTS, $GLBSDEC, $GLBSMIL);
											$TR_TAX_F=$TR_TAX/$DIVCENTS;
											$TR_TAX_F=number_format($TR_TAX_F, $CENTS, $GLBSDEC, $GLBSMIL);
											$TR_TOTAL_F=$TR_TOTAL/$DIVCENTS;
											$TR_TOTAL_F=number_format($TR_TOTAL_F, $CENTS, $GLBSDEC, $GLBSMIL);

											$S2="SELECT ID_CNY,TX_INC,TS_TRN_RCP FROM TR_RTL WHERE ID_TRN=".$ID_TRN; //OBTENER DATA GENERICA
											$RS2 = sqlsrv_query($arts_conn,$S2);
											if ($row2 = sqlsrv_fetch_array($RS2)) {
												$ID_CNY = $row2['ID_CNY'];
												$TAXINCL = $row2['TX_INC'];
												$TS_TRN_RCP = $row2['TS_TRN_RCP'];
											}
											//$FECHA_TICKET = date_format($TS_TRN_RCP,'d-m-Y H:i:s');
											$FECHA_TICKET = date_format($TS_TRN_BGN,'H:i:s');
											$FECHA_TICKET = date_format($TS_TRN_BGN,'d-m-Y')." ".$FECHA_TICKET;
								
										//MEDIOS DE PAGO Y SU MONTO
											$MEDIODEPAGO="";
								$MONTO_PAGO=0;
											if($TY_TRN_SLS==1 or $TY_TRN_SLS==0){
													$S2="SELECT ID_TND,ID_ACNT_TND,FL_IS_CHNG,MO_ITM_LN_TND FROM TR_LTM_TND WHERE ID_TRN=".$ID_TRN." ORDER BY AI_LN_ITM ASC"; //OBTENER DATA GENERICA
													$RS2 = sqlsrv_query($arts_conn,$S2);
													while ($row2 = sqlsrv_fetch_array($RS2)) {
														$ID_TND = $row2['ID_TND'];
														$ID_ACNT_TND = $row2['ID_ACNT_TND'];
														$FL_IS_CHNG = $row2['FL_IS_CHNG'];
														$S3="SELECT DE_TND,TY_TND FROM AS_TND WHERE ID_TND=".$ID_TND;
														$RS3 = sqlsrv_query($arts_conn, $S3);
														if ($row3 = sqlsrv_fetch_array($RS3)) {
															$DE_TND = $row3['DE_TND'];
															$TY_TND = $row3['TY_TND'];
														}
														if($TY_TND>=40 and $TY_TND<=49){
															//BUSCAR BIN Y DEFINICION DE BIN
															$CD_BIN=substr($ID_ACNT_TND,0,6);
															$S3="SELECT DES_BIN FROM PA_BIN WHERE CD_BIN=".$CD_BIN;
															$RS3 = sqlsrv_query($arts_conn, $S3);
															if ($row3 = sqlsrv_fetch_array($RS3)) {
																$DES_BIN = $row3['DES_BIN']." ".$CD_BIN;
																$DE_TND =$DES_BIN;
															}
														}
														if($TY_TND>=21 and $TY_TND<=24){
															//BUSCAR CHEQUE Y BANCO
															$S3="SELECT BNK FROM CO_CHK_DT WHERE ID_TRN=".$ID_TRN;
															$RS3 = sqlsrv_query($arts_conn, $S3);
															if ($row3 = sqlsrv_fetch_array($RS3)) {
																$ID_BANK = $row3['BNK'];
															}
															$S3="SELECT DES_BANK FROM AS_BANK WHERE ID_BANK=".$ID_BANK;
															$RS3 = sqlsrv_query($arts_conn, $S3);
															if ($row3 = sqlsrv_fetch_array($RS3)) {
																$DES_BANK = $row3['DES_BANK'];
															}
															$DE_TND ="CHEQUE ".$DES_BANK;
														}
														if($FL_IS_CHNG==1){$DE_TND="CAMBIO";}
														$MO_ITM_LN_TND = $row2['MO_ITM_LN_TND'];
														$MO_ITM_LN_TND_F=$MO_ITM_LN_TND/$DIVCENTS;
														$MO_ITM_LN_TND_F=number_format($MO_ITM_LN_TND_F, $CENTS, $GLBSDEC, $GLBSMIL);
														$MEDIODEPAGO=$MEDIODEPAGO.$DE_TND." (".$MO_ITM_LN_TND_F.")<br>";
													}
											}
											$REVERSADO="";
											if($TY_TRN_SLS==2){
													$S2="SELECT TY_RTN,ORGL_AI_TRN FROM TR_RTN WHERE ID_TRN=".$ID_TRN; //OBTENER DATA GENERICA
													$RS2 = sqlsrv_query($arts_conn,$S2);
													if ($row2 = sqlsrv_fetch_array($RS2)) {
														$TY_RTN = $row2['TY_RTN'];
														$ORGL_AI_TRN = $row2['ORGL_AI_TRN'];
													}
													if($TY_RTN<>80){
															//10: GIFTCARD, 20: EFECTIVO
															//Ref.001 INI Agrupar por el medio de cobro
															//$SNC="SELECT * FROM TR_LTM_TND_RTN WHERE ID_TRN=".$ID_TRN." ORDER BY AI_LN_ITM ASC"; //OBTENER DATA GENERICA
															$SNC="SELECT ID_TND, ID_ACNT_TND, SUM(MO_ITM_LN_TND) AS 'MO_ITM_LN_TND' FROM [ARTS_EC]..TR_LTM_TND WHERE ID_TRN = ".$ID_TRN." AND AI_LN_ITM in(SELECT AI_LN_ITM FROM [ARTS_EC]..TR_LTM_TND_RTN WHERE ID_TRN = ".$ID_TRN.") GROUP BY ID_TND, ID_ACNT_TND ORDER BY ID_TND";
															$RNC = sqlsrv_query($arts_conn,$SNC);
															while ($rowNC = sqlsrv_fetch_array($RNC)) {
																	$ID_TND = $rowNC['ID_TND'];
																	//$AI_LN_ITM_NC = $rowNC['AI_LN_ITM'];
																	$S2="SELECT R.* FROM [ARTS_EC]..TR_LTM_TND_RTN R  inner JOIN [ARTS_EC]..TR_LTM_TND T ON R.ID_TRN = T.ID_TRN AND R.AI_LN_ITM = T.AI_LN_ITM WHERE R.ID_TRN = ".$ID_TRN." AND T.ID_TND != 8 AND T.ID_TND = ".$ID_TND." ORDER BY AI_LN_ITM ASC"; //OBTENER DATA GENERICA
																	$RS2 = sqlsrv_query($arts_conn,$S2);
																	if ($row2 = sqlsrv_fetch_array($RS2)) {
																		$STS = $row2['STS'];
																	}
																	$ID_ACNT_TND = $rowNC['ID_ACNT_TND'];
																	$S3="SELECT DE_TND,TY_TND FROM AS_TND WHERE ID_TND=".$ID_TND;
																	$RS3 = sqlsrv_query($arts_conn, $S3);
																	if ($row3 = sqlsrv_fetch_array($RS3)) {
																		$DE_TND = $row3['DE_TND'];
																		$TY_TND = $row3['TY_TND'];
																	}
																	if($TY_TND==55 and ($TY_RTN==10 or $TY_RTN==41 or $TY_RTN==51 or $TY_RTN==61))
																		{$DE_TND="GIFTCARD"; $REVERSAR=0;}
																	else if($TY_TND==55 and ($TY_RTN==20 or $TY_RTN==32 or $TY_RTN==42 or $TY_RTN==52 or $TY_RTN==62)){$DE_TND="EFECTIVO"; $REVERSAR=1;}
																	else if($TY_TND==11 and $TY_RTN==99){$DE_TND="NC POR ANULACION/REVERSA";$REVERSAR=0;}
																	else if($TY_TND==11 and ($TY_RTN==10 or $TY_RTN==31 or $TY_RTN==41 or $TY_RTN==51 or $TY_RTN==61)){$DE_TND="GIFTCARD"; $REVERSAR=0;}
																	//if($TY_TND>=40 and $TY_TND<=49 or ($TY_TND==55 and $TY_RTN==30)){
																	//Ref.001 considerar el modo de devolucion por reverso + Giftcard "$TY_RTN==31"
																	else if($TY_TND==55 and ($TY_RTN==30 OR $TY_RTN==31 OR $TY_RTN==41 OR $TY_RTN==51 OR $TY_RTN==61)){$REVERSAR=1;} else{ $REVERSAR=0; }
																	if(!empty($ID_ACNT_TND) and $REVERSAR==1){
																		//BUSCAR BIN Y DEFINICION DE BIN
																		$REVERSAR=1;
																		$CD_BIN=substr($ID_ACNT_TND,0,6);
																		$S3="SELECT DES_BIN FROM PA_BIN WHERE CD_BIN=".$CD_BIN;
																		$RS3 = sqlsrv_query($arts_conn, $S3);
																		if ($row3 = sqlsrv_fetch_array($RS3)) {
																			$DES_BIN = $row3['DES_BIN']." ".$CD_BIN;
																			$DE_TND =$DES_BIN;
																		}
																	}
																	$MO_ITM_LN_TND = $rowNC['MO_ITM_LN_TND'];
																	$MO_ITM_LN_TND_F=$MO_ITM_LN_TND/$DIVCENTS;
																	$MO_ITM_LN_TND_F=number_format($MO_ITM_LN_TND_F, $CENTS, $GLBSDEC, $GLBSMIL);
																	//}
																	if($TY_TND == 62 OR $TY_TND == 51 or ($TY_TND == 55 and $TY_RTN == 20)) { $REVERSAR = 1; }
																	if($REVERSAR==1){
																			if($STS==1){$REVERSADO="<span style='font-size:6pt; border-radius:100%; -webkit-border-radius:100%; -moz-border-radius:100%; background-color:#7A2A9C; color: white; text-align:center; padding: 3px 4px;'>RE</span>";}
																			if($STS==0){$REVERSADO="<span style='font-size:6pt; border-radius:100%; -webkit-border-radius:100%; -moz-border-radius:100%; background-color:#C00; color: white; text-align:center; padding: 3px;'> NR</span>";}
																	} else{$REVERSADO="";}
																	$MEDIODEPAGO=$MEDIODEPAGO.$DE_TND." (".$MO_ITM_LN_TND_F.") ".$REVERSADO."<br>";
															}
													} else {
														$MEDIODEPAGO="DEVOLUCI&Oacute;N TICKET ILIMITADA";
														$REVERSAR=0;
														$TYDOC="DT";
														$COLOR_DOC="#F50057";
														$CLIENTE="";
														$ORGL_INVC_NMB_F="";
														$NUM_DOC="";
														$AI_TRN=$AI_TRN." (".$ORGL_AI_TRN.")";
													}

													$IDENTIFICACION = "";
													$BIN_GFCB = 0;
													$CRD_NBRB = '';
													
													$RS_BIVI = sqlsrv_query($conn,'EXEC SP_ConsInfBivi ?',array($ID_DEV));
													
													if ($ROW_BIVI = sqlsrv_fetch_array($RS_BIVI)) {
														$BIN_GFCB = $ROW_BIVI['BIN_GFC'];
														$CRD_NBRB = $ROW_BIVI['CRD_NBR'];
														
														if($BIN_GFCB!=0){
															$IDENTIFICACION = substr($CRD_NBRB, 0, 4) . str_repeat("*", strlen($CRD_NBRB) - 8) . substr($CRD_NBRB, -4); 
															
														}else{
															$MEDIODEPAGO = "BIVI"." (".$MO_ITM_LN_TND_F.") ";
															$IDENTIFICACION = "C.I. " . $CRD_NBRB;
														}
													}
											}
									?>
                      <script>
                        function Ocultar<?=$ID_TRN; ?>(){
                        var mostrar = document.getElementById("mostrar<?=$ID_TRN; ?>");
                        var ocultar = document.getElementById("ocultar<?=$ID_TRN; ?>");
                        var ver = document.getElementById("ver<?=$ID_TRN; ?>");
                        var TcktO = document.getElementById("TcktO<?=$ID_TRN; ?>");
                        var TcktM = document.getElementById("TcktM<?=$ID_TRN; ?>");
                        mostrar.style.display = "table-cell";
                        ocultar.style.display = "none";
                        TcktO.style.display = "none";
                        TcktM.style.display = "table-cell";
                        ver.style.display = "none";
                        for(j=1; j <= 11; j = j+1) {
                        var TRN = document.getElementById("TRN"+j+"<?=$ID_TRN; ?>");
                        TRN.className = "tdShow";
                        TRN.style.color = "#333";
                        <?php if ($CUENTAFILAS % 2 == 0) { ?> TRN.style.background = "#F1F1F1"; <?php } else { ?> TRN.style.background = "#F7F7F7"; <?php } ?>
                        }
                        }
                        function Mostrar<?=$ID_TRN; ?>(){
                        var mostrar = document.getElementById("mostrar<?=$ID_TRN; ?>");
                        var ocultar = document.getElementById("ocultar<?=$ID_TRN; ?>");
                        var ver = document.getElementById("ver<?=$ID_TRN; ?>");
                        var TcktO = document.getElementById("TcktO<?=$ID_TRN; ?>");
                        var TcktM = document.getElementById("TcktM<?=$ID_TRN; ?>");
                        mostrar.style.display = "none";
                        ocultar.style.display = "table-cell";
                        TcktO.style.display = "table-cell";
                        TcktM.style.display = "none";
                        ver.style.display = "table-row";
                        for(j=1; j <= 11; j = j+1) {
                        var TRN = document.getElementById("TRN"+j+"<?=$ID_TRN; ?>");
                        TRN.className = "tdHide";
                        TRN.style.color = "#FFFFFF";
                        TRN.style.background = "#8B44AA";
                        }
                        }
                      </script>
                      <tr>
                       		<td class="tdShow" id="mostrar<?=$ID_TRN; ?>" onClick="Mostrar<?=$ID_TRN; ?>();"><img src="../images/ICO_ShowM.png"></td>
											<td style="display:none" class="tdHide" id="ocultar<?=$ID_TRN; ?>" onClick="Ocultar<?=$ID_TRN; ?>();"><img src="../images/ICO_ShowB.png"></td>
                                            <td  id="TcktM<?=$ID_TRN; ?>" class="tdShow" style="text-align: right; font-size:11pt; font-weight:600; cursor:pointer; max-width: 400px" onClick="Mostrar<?=$ID_TRN; ?>();"><?=$AI_TRN." ".$TYDOC.$ID_TRN."/ ".$NUM_DOC; ?><br><span style="font-size:9pt; font-weight:400"><?=$CLIENTE.$ORGL_INVC_NMB_F?></span></td>
                                            <td nowrap id="TcktO<?=$ID_TRN; ?>" class="tdHide" style="display:none; text-align: right; font-size:11pt; font-weight:600; cursor:pointer" onClick="Ocultar<?=$ID_TRN; ?>();"><?=$AI_TRN." ".$TYDOC."/ ".$NUM_DOC; ?><br><span style="font-size:9pt; font-weight:400"><?=$CLIENTE.$ORGL_INVC_NMB_F?></span></td>

										<td class="tdShow"><p style="font-size: 12pt; font-weight: 300; border-radius:100%; -webkit-border-radius:100%; -moz-border-radius:100%; background-color:<?=$COLOR_DOC?>; color: white; text-align:center; padding: 6px; width: 22px; height: 22px"><?=$TYDOC;?></p></td>
                                            <td id="TRN1<?=$ID_TRN;?>"><?=$COD_TIENDA_F; ?><br><?=$TERMINAL_F; ?></td>
                                            <td nowrap id="TRN2<?=$ID_TRN;?>"><?=$OPERADOR; ?></td>
                                            
                                            <td id="TRN3<?=$ID_TRN;?>"  style="text-align:right;border-left-width:3px; border-left-style:solid; border-left-color:#DFDFDF"><?=$TR_SUBT_F; ?></td>
                                            <td id="TRN4<?=$ID_TRN;?>"  style="text-align:right"><?=$TR_DSCTO_F; ?></td>
                                            <td id="TRN5<?=$ID_TRN;?>"  style="text-align:right"><?=$TR_TAX_F; ?></td>
                                            <td id="TRN6<?=$ID_TRN;?>"  style="text-align:right"><?=$TR_TOTAL_F; ?></td>
                                            
                                            <td id="TRN7<?=$ID_TRN;?>"  style="border-left-width:3px; border-left-style:solid; border-left-color:#DFDFDF"><?=$MEDIODEPAGO;?>
											<b><p style="font-size: 10pt; margin-top:6px"><?=$IDENTIFICACION;?></p></b>
											<p style="font-size: 8pt; margin-top:6px"><?=$EMITE_NC.$AUTORIZA_NC?></p></td>
                                            <td id="TRN8<?=$ID_TRN;?>"  style="text-align:right"><?=$FECHA_TICKET; ?></td>
                                            <?php //if($TY_RTN<>80){ ?>
											<td>
                                  				<style>
												#Print-RACE {
													position:relative;
													float:right;
													width:50px;
													height:50px;
													background:url(../images/ICO_PrintNA.png) no-repeat center center;
													cursor:pointer;
													}
												#Print-RACE:hover {
													background:url(../images/ICO_PrintAC.png) no-repeat center center;
													}
												</style>
                                   				<div id="Print-RACE" title="Imprime Nota de Credito" onClick="pagina('reg_Devol_CER.php?idTrnNotaArts=<?=$ID_TRN?>');"><img src="../images/Transpa.png" width="100%" height="50px" border="none" /></div>
                                   			</td>
											  <td>
                                  				<style>
												#Check-DEV {
													position:relative;
													float:right;
													width:50px;
													height:50px;
													background:url(../images/comment-24.png) no-repeat center center;
													cursor:pointer;
													}
												#Check-DEV:hover {
													background:url(../images/comment-24.png) no-repeat center center;
													}
												</style>
												<?php 
													//$fecha_actual = date_format(date("d-m-Y H:i:00",time()), 'd/m/Y');
													$fecha_actual = date("d/m/Y",time());
													$ID_USU="";
													$E_COMM ="";
													$Fec_base=date_format($TS_TRN_BGN,'d/m/Y');
													echo "<script>console.log(\"'fecha_actual 7: " . $fecha_actual . "\")</script>\n";
													echo "<script>console.log(\"'Fec_base 8: " . $Fec_base . "\")</script>\n";
													$SQLCOMM="SELECT * FROM TRN_COMMENTS_DEV WHERE ID_TRN=?"; //$SQLCOMM="SELECT * FROM TRN_COMMENTS_DEV WHERE ID_TRN=".$ID_TRN;
													$RSCOMM= sqlsrv_query($arts_conn, $SQLCOMM, array($ID_TRN));
													if ($row3 = sqlsrv_fetch_array($RSCOMM)) {
														$E_COMM = $row3['COMMENT'];
													}
													echo "<script>console.log(\"'E_COMM 9: " . $E_COMM . "\")</script>\n";
													echo "<script>console.log(\"'SQLCOMM 10: " . $SQLCOMM . "\")</script>\n";
													if($fecha_actual===$Fec_base && empty($E_COMM)){
														$SQLUS="SELECT * FROM US_USUARIOS WHERE ESTADO=1 AND IDUSU=".$SESIDUSU;
														$RSUS= sqlsrv_query($maestra, $SQLUS);
														if ($row1 = sqlsrv_fetch_array($RSUS)) {
															$CC_OPERADOR = $row1['CC_OPERADOR'];
														}
														if(!empty($CC_OPERADOR)){
															$SQLOP="SELECT * FROM OP_OPERADOR WHERE REG_ESTADO=1 AND ID_MODOPERA=10 AND COD_TIENDA='".$CODTIENDA."' AND CC_OPERADOR='".$CC_OPERADOR."'";
															$RSOP= sqlsrv_query($Opera_conn, $SQLOP);
															if ($row2 = sqlsrv_fetch_array($RSOP)) {
																$ID_OPERADOR = $row2['ID_OPERADOR'];
															}
														}
														echo "<script>console.log(\"'CC_OPERADOR 9: " . $CC_OPERADOR . "\")</script>\n";
														echo "<script>console.log(\"'ID_OPERADOR 9: " . $ID_OPERADOR . "\")</script>\n";
														
														echo "<script>console.log(\"'SQLOP 10: " . $SQLOP . "\")</script>\n";
														echo "<script>console.log(\"'SQLUS 10: " . $SQLUS . "\")</script>\n";
														if(!empty($ID_OPERADOR)){
															?>
															<div id="Check-DEV" title="Ingresar Comentario" onClick="pagina('reg_Devol_CER.php?idTrnNotaArtsE=<?=$ID_TRN?>');"><img src="../images/Transpa.png" width="100%" height="50px" border="none" /></div>
														<?php
														}
													}
												?>
                                   				
                                   			</td>
                                   			<?php // }?>
                                    </tr>
									<tr id="ver<?=$ID_TRN; ?>" style="display:none">
                                    <td colspan="11" style="background-color:#FFF">
                                    <!-- DETALLE DE ITEMS EN TRX -->
                                    <?php include("reg_arts_dev.php"); ?>
                                    <!-- DETALLE DE ITEMS EN TRX -->
                                    </td>
                                    </tr>
									<?php
									$COLORTRX = "";
									$INVC_NMB = "";
									$ELTICKET = "";
									$CUENTAFILAS = $CUENTAFILAS + 1;
									$EMITE_NC="";
									$AUTORIZA_NC="";
                            }
						
                    ?>
                    <?php if(empty($B_NNDC)){?>
                    <tr>
                        <td colspan="12" nowrap style="background-color:transparent">
                        <?php
                        if ($LINF>=$CTP+1) {
                            $ATRAS=$LINF-$CTP;
                            $FILA_ANT=$LSUP-$CTP;
                       ?>
                        <input name="ANTERIOR" type="button" value="Anterior"  onClick="pagina('reg_Devol_CER.php?LIST=1&BSC_NDC=<?= htmlspecialchars($BSC_NDC, ENT_QUOTES);?>&LSUP=<?= htmlspecialchars($FILA_ANT, ENT_QUOTES);?>&LINF=<?=htmlspecialchars($ATRAS, ENT_QUOTES);?>&COD_TIENDA=<?= htmlspecialchars($COD_TIENDA_SEL, ENT_QUOTES);?>&DIA_NCED=<?= htmlspecialchars($DIA_NCED, ENT_QUOTES);?>&MES_NCED=<?= htmlspecialchars($MES_NCED, ENT_QUOTES);?>&ANO_NCED=<?= htmlspecialchars($ANO_NCED, ENT_QUOTES);?>&DIA_NCEH=<?=htmlspecialchars($DIA_NCEH, ENT_QUOTES);?>&MES_NCEH=<?=htmlspecialchars($MES_NCEH, ENT_QUOTES);?>&ANO_NCEH=<?= htmlspecialchars($ANO_NCEH, ENT_QUOTES);?>&B_TICKET=<?=htmlspecialchars($B_TICKET, ENT_QUOTES);?>&B_NNDC=<?=htmlspecialchars($B_NNDC, ENT_QUOTES);?>&B_CLTE=<?=htmlspecialchars($B_CLTE, ENT_QUOTES);?>');">
                        <?php
                        }
                        if ($LSUP<$TOTALREG) {
                            $ADELANTE=$LSUP+1;
                            $FILA_POS=$LSUP+$CTP;
							$BSC_NDC="Buscar Nota de Crédito";
                       ?>
                        <input name="SIGUIENTE" type="button" value="Siguiente" onClick="pagina('reg_Devol_CER.php?LIST=1&BSC_NDC=<?=htmlspecialchars($BSC_NDC, ENT_QUOTES);?>&LSUP=<?=htmlspecialchars($FILA_POS, ENT_QUOTES);?>&LINF=<?=htmlspecialchars($ADELANTE, ENT_QUOTES);?>&COD_TIENDA=<?=htmlspecialchars($COD_TIENDA_SEL, ENT_QUOTES);?>&DIA_NCED=<?=htmlspecialchars($DIA_NCED, ENT_QUOTES);?>&MES_NCED=<?=htmlspecialchars($MES_NCED, ENT_QUOTES);?>&ANO_NCED=<?=htmlspecialchars($ANO_NCED, ENT_QUOTES);?>&DIA_NCEH=<?=htmlspecialchars($DIA_NCEH, ENT_QUOTES);?>&MES_NCEH=<?=htmlspecialchars($MES_NCEH, ENT_QUOTES);?>&ANO_NCEH=<?=htmlspecialchars($ANO_NCEH, ENT_QUOTES);?>&B_TICKET=<?=htmlspecialchars($B_TICKET, ENT_QUOTES);?>&B_NNDC=<?= htmlspecialchars($B_NNDC, ENT_QUOTES);?>&B_CLTE=<?= htmlspecialchars($B_CLTE, ENT_QUOTES);?>');">
                        <?php }?>
                        <span style="vertical-align:baseline;">P&aacute;gina <?=$NUMPAG?> de <?=$NUMTPAG?></span>
                        </td>
                      </tr>
                      <?php } //if(empty($B_NNDC))?>
                    </table>
                    <?php
					} else {
					?>
                    <h4>No se registran coincidencias, por favor, intente nuevamente</h4>
                    <?php
					}//FIN ENCONTRO AL MENOS UNO
                    ?>
                    <?php } //FIN RESULTADO BUSCAR ?>
                    <!-- FIN RESULTADO BÚSQUEDA -->


                  </td>
                </tr>
              </table>
              <?php } //FIN LISTADO?>
			  <?php if($cantidad<=0 and $LIST!=1){?>
				<h2>Terminal no autorizada para devoluciones</h2>
			  <?php } else{if($LIST!=1){?>
				
				<h2>
					<?=$MODULO." ".$LAPAGINA?>
					<?php
					if(!empty($ID_DEV) or !empty($NOTADECREDITO) ){
							//GENERAR NÚMERO DE NOTA DE CREDITO
							//LLL-PPP-CCCCCCCCCCCC
							//OBTENER LOCAL SRI
							if($TipoDevol<>80){
									$NOTA = substr("000".$COD_SRI, -3).substr("000".$NUMPOSDEV_STR, -3).substr("000000000".$NUM_NC, -9);
							} else {
									$NOTA = "";
							}
							echo " ".$NOTA;
					}?>
				</h2>
				<?php } ?>
				<?php if(!empty($idTrnNotaArtsE)) { include('reg_Devol_CommentSupervisor.php');$D_GFC=""; }?>
              <?php if(!empty($D_GFC)) { include("reg_Devol_SEL.php"); }?>
              <?php if(!empty($DEV_TRX)) { include("reg_Devol_REGDev.php"); }?>
              <?php if(!empty($NOTADECREDITO)) { include("reg_Devol_NDC.php"); }?>
			  <?php }?>
            </td>
          </tr>
        </table>

      </td>
    </tr>
  </table>
</body>
</html>
<?php sqlsrv_close($conn);?>
