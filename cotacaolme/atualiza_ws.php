<?php
require "./env.php";
require "./lib-ms.php";
require "./lib-ws.php";

ini_set('max_execution_time', 300); //300 seconds = 5 minutes
//.........................
$GLOBALS["ylog"]=false;
if(isset($_GET["ylog"])) $GLOBALS["ylog"]=$_GET["ylog"]=="true"?true:false;
//.........................
$conn=null;
{ 
	// page main
	ylog("<pre>");
	// Inicializando modo e filtros ....................
	$devmode=""; if(isset($_GET["devmode"])) { $devmode=$_GET["devmode"]; }
	$start=""; 
	if(isset($_GET["start"])) { 
		ylog("strpos=" . strpos($_GET["start"],"/",0));
		if(strpos($_GET["start"],"/")) {
			$start=date_create_from_format('d/m/Y',$_GET["start"]);
		} else {
			$start=date_create_from_format('Y-m-d',$_GET["start"]);
		}
		ylog("1 start is ".date_format($start,"d/m/Y"));
	}
	$end=""; 
	if(isset($_GET["end"])) {
		if(strpos($_GET["end"],"/")) {
			$end=date_create_from_format('d/m/Y',$_GET["end"]);
		} else {
			$end=date_create_from_format('Y-m-d',$_GET["end"]);
		}
		ylog("2 end is ".date_format($end,"d/m/Y"));
	}

	if($end=="") {
		$end=date_create_from_format('Y-m-d',date("Y-m-d")); 
		ylog("3 end assumed is " . date_format($end,'d/m/Y'));
	} 
	if($start=="") {
		$start = date_create_from_format('Y-m-d', date("Y-m-d", strtotime("-5 day", strtotime(date("Y-m-d")))) );
		ylog("4 start assumed is ". date_format($start,'d/m/Y'));
	}
	
	//echo "<pre style=\"font-size:8pt;\">";

	//phpinfo();
	if($devmode=="true") { showPhpDependencies(); }

	$metals=["AL", "ZI", "PB", "TN", "CU", "NI" ];
	//$metals=[];

	$conn = getDefaultConnection();

	$mslog=[];
	foreach( $metals as $metal ) { 
		ylog("Metal ".$metal.": quandlcom ".date_format($start,'Y-m-d')." - ".date_format($end,'Y-m-d'));
		$jsonObject=quandlcom_getValues( $metal, date_format($start,'Y-m-d'), date_format($end,'Y-m-d') );
		//var_dump($jsonObject);
		foreach( $jsonObject as $line ) {
			ylog("Loop metal x quandlcom line");
			$mslog[]=[ "dataCotacao"=>$line[0], "codigoMetal"=>$metal, "fonte"=>"LME",
			"cotacaoDolar"=>$line[1], "preferencial"=>1 ];
			saveCotacaoMetalPreferencial($conn, false, [ "dataCotacao"=>$line[0], "codigoMetal"=>$metal, "fonte"=>"LME", 
				"cotacaoDolar"=>$line[1], "preferencial"=>1 ] );
		}
	}


  { //dolar
    ylog(".");
    $moeda="USD";
    $current=$start;
    $uslog=[];
    while ($current<=$end) {
	    $dolar=bcbdolar_getValues($moeda, date_format($current,"m-d-Y"));
	    //var_dump($dolar);
	    foreach($dolar as $cotacao) {
	      $cotacao->dataCotacao=date_format($current, 'Y-m-d');
	      $preferencial=0;
	      if(strpos($cotacao->tipoBoletim,"Fech")) { $preferencial=0; }
	      if(strpos($cotacao->tipoBoletim,"Abert")) { $preferencial=0; }
	      $obj=["dataCotacao"=>$cotacao->dataCotacao, "moeda"=>$moeda, 
	      	"paridadeCompra"=>$cotacao->paridadeCompra, "paridadeVenda"=>$cotacao->paridadeVenda, 
			"cotacaoCompra"=>$cotacao->cotacaoCompra, "cotacaoVenda"=>$cotacao->cotacaoVenda, 
			"tipoBoletim"=>$cotacao->tipoBoletim, "dataHoraCotacao"=>$cotacao->dataHoraCotacao,
			"fonte"=>"BC", "preferencial"=>$preferencial
			];
	      $uslog[]=$obj;
	      
	      $transpResult=getCotacaoMoedaByTipo($conn,false,["dataCotacao"=>$cotacao->dataCotacao, "moeda"=>"USD", "tipoBoletim"=>"Transporte"]);
	      $transp=null;
	      foreach($transpResult as $line) { $transp=$line; }
	      if($transp && $transp["preferencial"]==1) {
	      	// do nothing
	      	//no! do it too...
	        saveCotacaoMoedaPreferencial($conn, false, $obj);
	          
	      } else { 
	      	saveCotacaoMoedaPreferencial($conn, false, $obj);
	      }
	      $transpResult->close();
	    }
	    $current=date_create_from_format('Y-m-d', date("Y-m-d", 
	    		strtotime("+1 day", strtotime(date_format($current,"Y-m-d")))
	    	));
	    ylog( "current is now ".date_format($current,"Y-m-d") );
    }
    ylog(".");
    
    //transporte fechamento data posterior
    
    //consulta todas as datas de cotação de metais
    ylog("Starting to transport...");
    $allDates=getAllCotacaoMoedaDates($conn, false, [date_format($start,"Y-m-d"),date_format($end,"Y-m-d")]);
    $fechto=null; $previousFechto=null; $transp=null;
    foreach($allDates as $oneDate) {
		$previousFechto=$fechto;    	
	    // loop para cada data obtém fechamento
	    $fechtoResult=getCotacaoMoedaByTipo($conn,false,["dataCotacao"=>$oneDate["dataCotacao"], "moeda"=>"USD", "tipoBoletim"=>"Fechamento PTAX"]);
	    $fechto=null;
	    foreach($fechtoResult as $line) { $fechto=$line; }
	    ylog("Fechto dumping");
	    //var_dump($fechto);
	    $fechtoResult->close();
	    
	    $transpResult=getCotacaoMoedaByTipo($conn,false,["dataCotacao"=>$oneDate["dataCotacao"], "moeda"=>"USD", "tipoBoletim"=>"Transporte"]);
	    $transp=null;
    	foreach($transpResult as $line) { $transp=$line; }
	    ylog("Transp dumping");
	    //if($transp) var_dump($transp);
	    $transpResult->close();
	    
	    if($previousFechto) {
	    	$previousFechto["dataCotacao"]=$oneDate["dataCotacao"];
	    	$previousFechto["tipoBoletim"]="Transporte";
	    	$previousFechto["preferencial"]=1;
	    	ylog("Transportando: novo fechamento ajustado");
	    	//var_dump($previousFechto);
	    	saveCotacaoMoedaPreferencial($conn, false, $previousFechto);
	    }
	    // loop transporta para o dia posterior a cotação de fechamento
	    
	    
    }
    $allDates->close();
  }
  
	//echo "</pre>";
	ylog("</pre>");
	
	
  closeConnection($conn);
  //.........................
  $finalData=array("status"=>"OK", "mslog"=>$mslog, "uslog"=>$uslog );
  echo json_encode($finalData, JSON_FORCE_OBJECT);
  //.........................
}

//---------------------------------------------------------------------------
// Functions

function showPhpDependencies() {
	echo "extension=openssl\n";
	echo "extension=mysqli\n";

	$w = stream_get_wrappers();
	echo 'openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "\n";
	echo 'http wrapper: ', in_array('http', $w) ? 'yes':'no', "\n";
	echo 'https wrapper: ', in_array('https', $w) ? 'yes':'no', "\n";
	echo 'wrappers: ', var_export($w);

	echo '\n';
}

// Functions
//---------------------------------------------------------------------------
// MySql Functions


function getCotacaoMetal($conn, $closeConn, $obj, $numParams) {
	$sql = "SELECT cotacaoId, dataCotacao, codigoMetal, cotacaoDolar FROM cotacao_metal WHERE dataCotacao='".$obj[0]."' AND codigoMetal='".$obj[1]."'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		if($result->num_rows==1) {
			return $result->fetch_assoc();
		} else { 
			return $result;
		}
	} else {
		echo "0 results";
		return false;
	}
	if($closeConn) $conn->close();
}

function saveCotacaoMetal($conn, $closeConn, $obj, $numParams) {
	$sql = "INSERT INTO cotacao_metal ( dataCotacao, codigoMetal, cotacaoDolar, fonte ) "
		. " VALUES ('" . $obj[0] . "', '" . $obj[1] . "', '" . $obj[2] . "','".$obj[3]."')";

	if ($conn->query($sql) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}

	if($closeConn) $conn->close();
}

function saveCotacaoMoeda($conn, $closeConn, $obj, $numParams) {
	$sql = "INSERT INTO cotacao_moeda ( moeda, dataCotacao, paridadeCompra, paridadeVenda, cotacaoCompra, cotacaoVenda, dataHoraCotacao, tipoBoletim, fonte ) "
		. " VALUES ( '" . $obj[1] . "', '" . $obj[2] . "', '" . $obj[3] . "', '" . $obj[4] . "', '" . $obj[5] . "', '" . $obj[6] . "', '" . $obj[7] . "', '" . $obj[8] . "', '" . $obj[9] . "')";

	if ($conn->query($sql) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}

	if($closeConn) $conn->close();
}

function showCotacaoMetalRow($row) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
		echo "cotacaoId: " . $row["cotacaoId"]. " - Metal: " . $row["codigoMetal"]. " Data: " . $row["dataCotacao"]. " " . $row["cotacaoDolar"]. "<br>\n";
	}
}

function closeConnection($conn) {
	if($conn) $conn->close();
	$conn=null;
}
// MySql Functions
//------------------------------------------------------------------


{
	//ylog("<pre>");
	// Testando...
	//saveCotacaoMetalPreferencial($conn, false, [ "dataCotacao"=>"2018-10-05", "codigoMetal"=>"AL", "fonte"=>"LME", "cotacaoDolar"=>4.99999 ] );
	//ylog("</pre>");
	//closeConnection($conn);
}



//versão atualiza_ws 
function mySqlRunQueryV0($query, $conn, $closeConn) {
	$sql = $query;
	ylog("mySqlRunQuery:: ".$sql."");
	$result = $conn->query($sql);
	$isObject=is_object($result);
	$isArray=is_array($result);
	ylog("isObject: ".$isObject);
	ylog("isArray: ".$isArray);
	
	//var_dump($result);
	ylog("Returning...");
	if ($result /*&& $result->num_rows > 0*/) {
		//if($result->num_rows==1) {
			//return $result->fetch_assoc();
			return $result;
		//} else {
			//return $result;
		//}
	} else {
		//echo "0 results\r\n";
		return false;
	}
	if($closeConn) $conn->close();
}

?>