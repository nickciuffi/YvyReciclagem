<?php
require "./env.php";
require("./lib-ms.php");
//.........................
$GLOBALS["ylog"]=false;
if(isset($_GET["ylog"])) $GLOBALS["ylog"]=$_GET["ylog"]=="true"?true:false;
//.........................

//.........................
$conn=getDefaultConnection();
$dateFrom=""; if(isset($_POST["dateFrom"])) $dateFrom=$_POST["dateFrom"];
$dateTo=""; if(isset($_POST["dateTo"])) $dateTo=$_POST["dateTo"];
if($dateFrom=="" && isset($_GET["dateFrom"])) $dateFrom=$_GET["dateFrom"];
if($dateTo=="" && isset($_GET["dateTo"])) $dateTo=$_GET["dateTo"];
$codigoMetal=""; if(isset($_POST["codigoMetal"])) $codigoMetal=$_POST["codigoMetal"];
$src=""; if(isset($_POST["src"])) $src=$_POST["src"];
//.........................

//.........................
//ylog("date create ".date_format(date_create_from_format("d/m/Y",$dateFrom),"Y-m-d"));
//ylog("strpos ".strpos($dateFrom,"/"));
if(strpos($dateFrom,"/")) {
	$dateFrom=date_format(date_create_from_format("d/m/Y",$dateFrom),"Y-m-d");
}
if(strpos($dateTo,"/")) {
	$dateTo=date_format(date_create_from_format("d/m/Y",$dateTo),"Y-m-d");
}
//.........................

//.........................
header('Content-Type: application/json');
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
//.........................

//.........................
// Todas as datas de cotação
$data=getAllCotacaoDates($conn, false, [$dateFrom,$dateTo], 999); $aAllCotacaoDatesData=[];
if($data) foreach($data as $el) { $aAllCotacaoDatesData[]=array("dataCotacao"=>$el["dataCotacao"], "weekNumber"=>$el["weekNumber"], "year"=>$el["year"], "month"=>$el["month"], "cotacoes"=>$el["cotacoes"]); }
$data->close();
//.........................
// Todos os metais
$data=getAllMetals($conn, false, [], 999); $aAllMetalsData=[];
if($data) foreach($data as $el) { $aAllMetalsData[]=array("codigoMetal"=>$el["codigoMetal"]); }
$data->close();
//.........................

//.........................
// Cotacoes Preferenciais Metais
$data=getCotacaoMetalMany($conn, false, [$dateFrom,$dateTo,1]); $aCotMetais=[];
if($data) foreach($data as $el) { $aCotMetais[]=array("codigoMetal"=>$el["codigoMetal"], "dataCotacao"=>$el["dataCotacao"], "weekNumber"=>$el["weekNumber"], "year"=>$el["year"], "month"=>$el["month"], "cotacaoDolar"=>$el["cotacaoDolar"],"fonte"=>$el["fonte"]); }
$data->close();
//.........................
// Média semana
$data=getCotacaoMetalMediaSemana($conn, false, [$dateFrom,$dateTo,1]); $aCotMetaisMediaSem=[];
if($data) foreach($data as $el) { $aCotMetaisMediaSem[]=array("codigoMetal"=>$el["codigoMetal"], "weekNumber"=>$el["weekNumber"],"cotacoes"=>$el["cotacoes"],"mediaCotacao"=>$el["mediaCotacao"]); }
$data->close();
//.........................
// Média mês
$data=getCotacaoMetalMediaMes($conn, false, [$dateFrom,$dateTo,1]); $aCotMetaisMediaMes=[];
if($data) foreach($data as $el) { $aCotMetaisMediaMes[]=array("codigoMetal"=>$el["codigoMetal"], "year"=>$el["year"], "month"=>$el["month"], "cotacoes"=>$el["cotacoes"],"mediaCotacao"=>$el["mediaCotacao"]); }
$data->close();
//.........................
// Média ano
$data=getCotacaoMetalMediaAno($conn, false, [$dateFrom,$dateTo,1]); $aCotMetaisMediaAno=[];
if($data) foreach($data as $el) { $aCotMetaisMediaAno[]=array("codigoMetal"=>$el["codigoMetal"], "year"=>$el["year"], "cotacoes"=>$el["cotacoes"],"mediaCotacao"=>$el["mediaCotacao"]); }
$data->close();
//.........................

//.........................
// Cotações Preferenciais Dólar
$data=getCotacaoMoedaMany($conn, false, [$dateFrom,$dateTo,"USD",1]); $aCotDolar=[];
if($data) foreach($data as $el) { $aCotDolar[]=array("moeda"=>$el["moeda"], "dataCotacao"=>$el["dataCotacao"], "weekNumber"=>$el["weekNumber"], "year"=>$el["year"], "month"=>$el["month"], "cotacaoCompra"=>$el["cotacaoCompra"], "cotacaoVenda"=>$el["cotacaoVenda"], "fonte"=>$el["fonte"]); }
$data->close();
//.........................
// Média semana
$data=getCotacaoMoedaMediaSemana($conn, false, [$dateFrom,$dateTo,"USD",1]); $aCotDolarMediaSem=[];
if($data) foreach($data as $el) { $aCotDolarMediaSem[]=array("moeda"=>$el["moeda"], "weekNumber"=>$el["weekNumber"],"cotacoes"=>$el["cotacoes"],"mediaCompra"=>$el["mediaCompra"],"mediaVenda"=>$el["mediaVenda"]); }
$data->close();
//.........................
// Média mês
$data=getCotacaoMoedaMediaMes($conn, false, [$dateFrom,$dateTo,"USD",1]); $aCotDolarMediaMes=[];
if($data) foreach($data as $el) { $aCotDolarMediaMes[]=array("moeda"=>$el["moeda"], "year"=>$el["year"], "month"=>$el["month"], "cotacoes"=>$el["cotacoes"],"mediaCompra"=>$el["mediaCompra"],"mediaVenda"=>$el["mediaVenda"]); }
$data->close();
//.........................
// Média ano
$data=getCotacaoMoedaMediaAno($conn, false, [$dateFrom,$dateTo,"USD",1]); $aCotDolarMediaAno=[];
if($data) foreach($data as $el) { $aCotDolarMediaAno[]=array("moeda"=>$el["moeda"], "year"=>$el["year"], "cotacoes"=>$el["cotacoes"],"mediaCompra"=>$el["mediaCompra"],"mediaVenda"=>$el["mediaVenda"]); }
$data->close();
//.........................

//.........................
// Last Data Cotação (2)
$lastDataCotacao=null; $penultimateDataCotacao=null;
$data=getLastDataCotacao($conn, false, []); $aLastDataCotacao=[];
if($data) foreach($data as $el) { $lastDataCotacao=$el["lastDataCotacao"]; }
$data->close();

$data=getPenultimateDataCotacao($conn, false, [$lastDataCotacao]); 
if($data) foreach($data as $el) { $penultimateDataCotacao=$el["previousDataCotacao"]; }
$data->close();

$aLastDataCotacao[]=array("lastDataCotacao"=>$lastDataCotacao, "previousDataCotacao"=>$penultimateDataCotacao);
//.........................

//.........................
// Last Cotações (2)
$data=getLastCotacaoMetal($conn, false, [$lastDataCotacao, $penultimateDataCotacao]); $aLastCotacaoMetal=[];
if($data) foreach($data as $el) { $aLastCotacaoMetal[]=array("codigoMetal"=>$el["codigoMetal"],"dataCotacao"=>$el["dataCotacao"], "cotacaoDolar"=>$el["cotacaoDolar"], "fonte"=>$el["fonte"]); }
$data->close();
//.........................
$data=getLastCotacaoMoeda($conn, false, [$lastDataCotacao, $penultimateDataCotacao]); $aLastCotacaoMoeda=[];
if($data) foreach($data as $el) { $aLastCotacaoMoeda[]=array("moeda"=>$el["moeda"],"dataCotacao"=>$el["dataCotacao"], "cotacaoCompra"=>$el["cotacaoCompra"], "cotacaoVenda"=>$el["cotacaoVenda"], "fonte"=>$el["fonte"]); }
$data->close();
//.........................

//.........................
closeConnection($conn);
//.........................

//.........................
$finalData=array("aAllCotacaoDates"=>$aAllCotacaoDatesData, "aAllMetals"=>$aAllMetalsData, 
        "aCotMetais"=>$aCotMetais, "aCotMetaisMediaSem"=>$aCotMetaisMediaSem, "aCotMetaisMediaMes"=>$aCotMetaisMediaMes, "aCotMetaisMediaAno"=>$aCotMetaisMediaAno,
        "aCotDolar"=>$aCotDolar, "aCotDolarMediaSem"=>$aCotDolarMediaSem, "aCotDolarMediaMes"=>$aCotDolarMediaMes, "aCotDolarMediaAno"=>$aCotDolarMediaAno,
		"aLastDataCotacao"=>$aLastDataCotacao, "aLastCotacaoMetal"=>$aLastCotacaoMetal, "aLastCotacaoMoeda"=>$aLastCotacaoMoeda
);
echo json_encode($finalData, JSON_FORCE_OBJECT);
//.........................
?>



<?php

//---------------------------------------------------------------------------
// MySql Functions

function getCotacaoMetal($conn, $closeConn, $obj, $numParams) {
	$sql = "SELECT cotacaoId, dataCotacao, codigoMetal, cotacaoDolar FROM cotacao_metal WHERE dataCotacao='".$obj[0]."' AND codigoMetal='".$obj[1]."' ORDER BY codigoMetal, dataCotacao";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		if($result->num_rows==1) {
			return $result->fetch_assoc();
		} else { 
			return $result;
		}
	} else {
		ylog("0 results");
		return false;
	}
	if($closeConn) $conn->close();
}

function saveCotacaoMetal($conn, $closeConn, $obj, $numParams) {
	$sql = "INSERT INTO cotacao_metal ( dataCotacao, codigoMetal, cotacaoDolar, fonte ) "
		. " VALUES ('" . $obj[0] . "', '" . $obj[1] . "', '" . $obj[2] . "','".$obj[3]."')";

	if ($conn->query($sql) === TRUE) {
		ylog("New record created successfully");
	} else {
		ylog("Error: " . $sql . " :: " . $conn->error);
	}

	if($closeConn) $conn->close();
}

function showCotacaoMetalRow($row) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
		ylog("cotacaoId: " . $row["cotacaoId"]. " - Metal: " . $row["codigoMetal"]. " Data: " . $row["dataCotacao"]. " " . $row["cotacaoDolar"]. "");
	}
}

function closeConnection($conn) {
	$conn->close();
	$conn=null;
}
// MySql Functions
//------------------------------------------------------------------


function getCotacaoMetalPeriodo($conn, $closeConn, $obj, $numParams) {
	$sql = "SELECT cotacaoId, dataCotacao, codigoMetal, cotacaoDolar, fonte FROM cotacao_metal WHERE dataCotacao>='".$obj[0]."' AND dataCotacao<='".$obj[1]."' AND fonte='" . $obj[3] . "' ORDER BY dataCotacao, codigoMetal";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj, $numParams);
	return $result;
}

function getCotacaoMoedaPeriodo($conn, $closeConn, $obj, $numParams) {
	$sql = "SELECT cotacaoId, dataCotacao, moeda, cotacaoCompra, cotacaoVenda, fonte FROM cotacao_moeda WHERE dataCotacao>='".$obj[0]."' AND dataCotacao<='".$obj[1]."' AND fonte='" . $obj[3] . "' ORDER BY dataCotacao, dataHoraCotacao";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj, $numParams);
	return $result;
}


//--------------------------------------------------
// Tabela Completa

function getAllMetals($conn, $closeConn, $obj, $numParams) {
	$sql = "SELECT 'AL' codigoMetal UNION ALL SELECT 'CU' UNION ALL SELECT 'NI' UNION ALL SELECT 'PB' UNION ALL SELECT 'TN' UNION ALL SELECT 'ZI'";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj, $numParams);
	return $result;
}

//Old version : get_data
function mySqlRunQueryV0($query, $conn, $closeConn, $obj) {
	$sql = $query;
	ylog("mySqlRunQuery:: ".$sql."");
	$result = $conn->query($sql);
	
	if ($result && $result->num_rows > 0) {
		if($result->num_rows==1) {
			//return $result->fetch_assoc();
			return $result;
		} else {
			return $result;
		}
	} else {
		//echo "0 results\r\n";
		return false;
	}
	if($closeConn) $conn->close();
}

//..........................................................................
function getCotacaoMetalMany($conn, $closeConn, $obj) {
	ylog(""); ylog("getCotacaoMetalMany: start");
	$sql = "SELECT cotacaoId, dataCotacao, WEEK(dataCotacao,1) weekNumber, YEAR(dataCotacao) year, MONTH(dataCotacao) month, codigoMetal, cotacaoDolar, fonte, preferencial FROM cotacao_metal WHERE dataCotacao>='".$obj[0]."' AND dataCotacao<='".$obj[1]."' AND preferencial=1 ORDER BY dataCotacao, codigoMetal";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}
//..........................................................................
function getCotacaoMetalMediaSemana($conn, $closeConn, $obj) {
	ylog(""); ylog("getCotacaoMetalMediaSemana: start.........................");
	$sql = "SELECT WEEK(dataCotacao,1) weekNumber, codigoMetal, COUNT(*) cotacoes, AVG(cotacaoDolar) mediaCotacao "
		. " FROM cotacao_metal "
		. " WHERE preferencial=1 AND dataCotacao>=DATE_ADD('".$obj[0]."',INTERVAL -1 WEEK) AND dataCotacao<=DATE_ADD('".$obj[1]."', INTERVAL +1 WEEK) "
		." GROUP BY WEEK(dataCotacao,1), codigoMetal";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}
//..........................................................................
function getCotacaoMetalMediaMes($conn, $closeConn, $obj) {
	ylog(""); ylog("getCotacaoMetalMediaMes: start..........................");
	$sql = "SELECT YEAR(dataCotacao) year, MONTH(dataCotacao) month, codigoMetal, COUNT(*) cotacoes, AVG(cotacaoDolar) mediaCotacao "
		." FROM cotacao_metal "
		." WHERE preferencial=1 AND dataCotacao>= DATE_ADD('".$obj[0]."', INTERVAL -1 MONTH) AND dataCotacao<=DATE_ADD('".$obj[1]."', INTERVAL +1 MONTH) "
		." GROUP BY YEAR(dataCotacao), MONTH(dataCotacao), codigoMetal";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}
//.........................................................................
function getCotacaoMetalMediaAno($conn, $closeConn, $obj) {
    ylog(""); ylog("getCotacaoMetalMediaAno: start..........................");
    $sql = "SELECT YEAR(dataCotacao) year, codigoMetal, COUNT(*) cotacoes, AVG(cotacaoDolar) mediaCotacao "
    ." FROM cotacao_metal "
    ." WHERE preferencial=1 AND dataCotacao>=DATE_ADD('".$obj[0]."', INTERVAL -1 YEAR) AND dataCotacao<=DATE_ADD('".$obj[1]."', INTERVAL +1 YEAR) "
    ." GROUP BY YEAR(dataCotacao), codigoMetal";
    $result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
    return $result;
}
//.........................................................................

//.........................................................................
function getCotacaoMoedaMany($conn, $closeConn, $obj) {
	ylog(""); ylog("getCotacaoDolarMany: start...........................");
	$sql = "SELECT cotacaoId, dataCotacao, WEEK(dataCotacao,1) weekNumber, YEAR(dataCotacao) year, MONTH(dataCotacao) month, moeda, cotacaoCompra, cotacaoVenda, fonte, preferencial "
		. " FROM cotacao_moeda "
		. " WHERE dataCotacao>='".$obj[0]."' AND dataCotacao<='".$obj[1]."' AND moeda='" . $obj[2] . "' AND preferencial=".$obj[3]." "
		. " ORDER BY dataCotacao, dataHoraCotacao";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}
//..........................................................................
function getCotacaoMoedaMediaSemana($conn, $closeConn, $obj) {
	ylog(""); ylog("getCotacaoMoedaMediaSemana: start.........................");
	$sql = "SELECT WEEK(dataCotacao,1) weekNumber, moeda, COUNT(*) cotacoes, AVG(cotacaoCompra) mediaCompra, AVG(cotacaoVenda) mediaVenda "
		. " FROM cotacao_moeda "
		. " WHERE preferencial=1 AND dataCotacao>=DATE_ADD('".$obj[0]."', INTERVAL -1 WEEK) AND dataCotacao<=DATE_ADD('".$obj[1]."', INTERVAL +1 WEEK) "
		." GROUP BY WEEK(dataCotacao,1), moeda";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}
//..........................................................................
function getCotacaoMoedaMediaMes($conn, $closeConn, $obj) {
	ylog(""); ylog("getCotacaoMoedaMediaMes: start..........................");
	$sql = "SELECT YEAR(dataCotacao) year, MONTH(dataCotacao) month, moeda, COUNT(*) cotacoes, AVG(cotacaoCompra) mediaCompra, AVG(cotacaoVenda) mediaVenda "
		." FROM cotacao_moeda "
		." WHERE preferencial=1 AND dataCotacao>=DATE_ADD('".$obj[0]."', INTERVAL -1 MONTH) AND dataCotacao<=DATE_ADD('".$obj[1]."', INTERVAL +1 MONTH) "
		." GROUP BY YEAR(dataCotacao), MONTH(dataCotacao), moeda";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}
//.........................................................................
function getCotacaoMoedaMediaAno($conn, $closeConn, $obj) {
    ylog(""); ylog("getCotacaoMoedaMediaAno: start..........................");
    $sql = "SELECT YEAR(dataCotacao) year, moeda, COUNT(*) cotacoes, AVG(cotacaoCompra) mediaCompra, AVG(cotacaoVenda) mediaVenda "
		    ." FROM cotacao_moeda "
	    ." WHERE preferencial=1 AND dataCotacao>=DATE_ADD('".$obj[0]."', INTERVAL -1 YEAR) AND dataCotacao<=DATE_ADD('".$obj[1]."', INTERVAL +1 YEAR) "
		    ." GROUP BY YEAR(dataCotacao), moeda";
		    $result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
		    return $result;
}
//.........................................................................

//.........................................................................
function getLastDataCotacaoV0($conn, $closeConn, $obj) {
	ylog(""); ylog("getLastDataCotacao: start..........................");
	$sql = "SELECT "
		. " (SELECT MAX(dataCotacao) dataCotacao FROM cotacao_metal WHERE preferencial=1) lastDataCotacao, "
		. " (SELECT MAX(dataCotacao) dataCotacao FROM cotacao_metal WHERE dataCotacao< "
		. " 	 (SELECT MAX(dataCotacao) dataCotacao FROM cotacao_metal WHERE preferencial=1)) previousDataCotacao ";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}
//.........................................................................

//.........................................................................
function getLastDataCotacao($conn, $closeConn, $obj) {
	ylog(""); ylog("getLastDataCotacao: start..........................");
	$sql = "SELECT MAX(dataCotacao) lastDataCotacao FROM cotacao_metal WHERE preferencial=1";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}
//.........................................................................

//.........................................................................
function getPenultimateDataCotacao($conn, $closeConn, $obj) {
	ylog(""); ylog("getLastDataCotacao: start..........................");
	$sql = "SELECT MAX(dataCotacao) previousDataCotacao FROM cotacao_metal WHERE dataCotacao<'" . $obj[0] . "'";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}
//.........................................................................

//.........................................................................
function getLastCotacaoMetal($conn, $closeConn, $obj) {
	ylog(""); ylog("getLastCotacaoMetal: start..........................");
	$sql = "SELECT * FROM cotacao_metal "
		. " WHERE dataCotacao IN ( '" . $obj[0] . "', '" . $obj[1] . "')"
		. " AND preferencial=1 "
		. " ORDER BY codigoMetal";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}
//.........................................................................
function getLastCotacaoMoeda($conn, $closeConn, $obj) {
	ylog(""); ylog("getLastCotacaoMoeda: start..........................");
	$sql = "SELECT * FROM cotacao_moeda "
		. " WHERE dataCotacao IN (  '" . $obj[0] . "', '" . $obj[1] . "') " 
		. " AND preferencial=1 " 
		. " ORDER BY moeda, dataCotacao";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}
//.........................................................................


?>

