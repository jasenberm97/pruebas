<?php
		session_start();
		
		//OBTENGO DATA MODULO
		$BDUSER = $_SESSION['ARMS_BDUSER'];
		$BDPASSWORD = $_SESSION['ARMS_BDPASS'];
		$BDIP=$_SESSION['ARMS_BDIP'];
		$BDNM=$_SESSION['ARMS_BDNM'];
		$SESIDSUITEACE=$_SESSION['ARMS_IDSUITE'];
		$BDINS = $_SESSION['ARMS_BDINS'];
		
		//CONEXION BD MODULO
		$serverName = $BDIP;
        $connectionInfo = array( "Database"=>$BDNM, "UID"=>$BDUSER, "PWD"=>$BDPASSWORD);
        $conn = sqlsrv_connect( $serverName, $connectionInfo);
		
		//OBTENGO DATA MAESTRA
		$M_BDUSER = $_SESSION['ARMS_MA_BDUSER'];
		$M_BDPASSWORD = $_SESSION['ARMS_MA_BDPASS'];
		$M_BDIP=$_SESSION['ARMS_MA_BDIP'];
		$M_BDINS=$_SESSION['ARMS_MA_BDINS'];
		$M_BDNM = $_SESSION['ARMS_MA_BDNM'];
		
		//CONEXION MAESTRA
		$serverName = $M_BDIP;
        $connectionMaster = array( "Database"=>$M_BDNM, "UID"=>$M_BDUSER, "PWD"=>$M_BDPASSWORD);
        $maestra = sqlsrv_connect( $serverName, $connectionMaster);

		//CONEXION ARTS//MODIFICAR PARA COMPARTIR TRANSVERSALMENTE
		$IDSIS_ARTS=1103;

		//OBTENER DATA CONEXION ARTS
		$SQLSIS="SELECT * FROM US_SISTEMA WHERE IDSISTEMA=".$IDSIS_ARTS;
		$RSSIS = sqlsrv_query($maestra,$SQLSIS); 
		if ($RWSIS = sqlsrv_fetch_array($RSSIS)){
			$BDIPSIS = $RWSIS['BDIP'];
			$BDNMSIS = trim($RWSIS['BDNM']);
			$BDUSSIS = trim($RWSIS['BDUS']);
			$BDPSSIS = trim($RWSIS['BDPS']);
		}
		$serverName = $BDIPSIS;
        $connectionArts = array( "Database"=>$BDNMSIS, "UID"=>$BDUSSIS, "PWD"=>$BDPSSIS );
        $arts_conn = sqlsrv_connect( $serverName, $connectionArts);

		//CONEXION EYES//MODIFICAR PARA COMPARTIR TRANSVERSALMENTE
        $IDSIS_EYES=1109;

        //OBTENER DATA CONEXION EYES
        $SQLSIS="SELECT * FROM US_SISTEMA WHERE IDSISTEMA=".$IDSIS_EYES;
        $RSSIS = sqlsrv_query($maestra,$SQLSIS);

        if ($RWSIS = sqlsrv_fetch_array($RSSIS)){
            $BDIPSIS = $RWSIS['BDIP'];
            $BDNMSIS = $RWSIS['BDNM'];
            $BDUSSIS = $RWSIS['BDUS'];
            $BDPSSIS = $RWSIS['BDPS'];
        }
        $serverName = $BDIP;
       $connectionEyes = array( "Database"=>$BDNMSIS, "UID"=>$BDUSSIS, "PWD"=>$BDPSSIS);
       $eyes_conn = sqlsrv_connect( $serverName, $connectionEyes);
	   

?>
<?php include("../paramts.php"); ?>
