<?php
require "./env.php";
require("./security_check.php");
require("./lib-ms.php");

//.........................
if(isset($_GET["ylog"])) $ylog=true; else $ylog=false;
$action=$_GET["a"];
//qmetal, qdolar, qferiado, sferiado, dferiado
if(isset($_GET["dataCotacao"])) $pDataCotacao=$_GET["dataCotacao"];
	else if(isset($_POST["dataCotacao"])) $pDataCotacao=$_POST["dataCotacao"];
if(isset($_GET["codigoMetal"])) $pCodigoMetal=$_GET["codigoMetal"];
	else if(isset($_POST["codigoMetal"])) $pCodigoMetal=$_POST["codigoMetal"];
if(isset($_GET["dataFeriado"])) $pDFeriado=$_GET["dataFeriado"];
	else if(isset($_POST["dataFeriado"])) $pDFeriado=$_POST["dataFeriado"];
if(isset($_GET["delete"])) $pDelete=$_GET["delete"];
	else if(isset($_POST["delete"])) $pDelete=$_POST["delete"];
if(isset($_GET["cotacaoId"])) $pCotacaoId=$_GET["cotacaoId"];
	else if(isset($_POST["cotacaoId"])) $pCotacaoId=$_POST["cotacaoId"];
//.........................

//.........................
header('Content-Type: application/json');
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
//.........................

$aCotacoes=[]; $message="";

if($action=="qdolar") {
	//.........................
	$conn=getDefaultConnection(); $closeConn=true;
	$obj=array("dataCotacao"=>$pDataCotacao, "moeda"=>"USD");
	
	$res=getCotacaoDolarByDate($conn, $closeConn, $obj);
	if($res) {
		foreach($res as $el) { 
			$aCotacoes[]=array("cotacaoId"=>$el["cotacaoId"], "moeda"=>$el["moeda"],"dataCotacao"=>$el["dataCotacao"],
				 "cotacaoVenda"=>$el["cotacaoVenda"], "fonte"=>$el["fonte"], "tipoBoletim"=>$el["tipoBoletim"], 
				 "preferencial"=>$el["preferencial"]);
		}
		$res->close();
	}
	//closeConnection($conn);
	//.........................
}
if($action=="qmetal") {
	//.........................
	$conn=getDefaultConnection(); $closeConn=true;
	$obj=array("dataCotacao"=>$pDataCotacao, "codigoMetal"=>$pCodigoMetal);
	
	$res=getCotacaoMetalByDate($conn, $closeConn, $obj);
	if($res) {
		foreach($res as $el) { 
			$aCotacoes[]=array("cotacaoId"=>$el["cotacaoId"], "codigoMetal"=>$el["codigoMetal"],"dataCotacao"=>$el["dataCotacao"],
				 "cotacaoDolar"=>$el["cotacaoDolar"], "fonte"=>$el["fonte"],  
				 "preferencial"=>$el["preferencial"]);
		}
		$res->close();
	}
	//closeConnection($conn);
	//.........................
}
if($action=="qferiado") {
	//.........................
	$conn=getDefaultConnection(); $closeConn=true;
	$obj=array("dataFeriado"=>$pDFeriado);
	$res=getFeriadoByDate($conn, $closeConn, $obj);
	$message="A data " . $pDFeriado . " NÃO está marcada como feriado.";
	$allowInsert=1; $allowDelete=0;
	if($res) {
		foreach($res as $el) {
			$message="A data " . convertMySqlToDate("".$el["dataFeriado"]) . " está marcada como feriado.";
			$allowInsert=0; $allowDelete=1;
		}
	}
	//.........................
}
if($action=="sferiado") {
	//.........................
	$conn=getDefaultConnection(); $closeConn=true;
	$obj=array("dFeriado"=>$pDFeriado);
	if($pDelete=="1") {
		$res=deleteFeriado($conn, $closeConn, $obj);
	} else {
		$res=saveFeriado($conn, $closeConn, $obj);
	}
	//.........................
}

if($action=="dmetal") {
	//.........................
	$conn=getDefaultConnection(); $closeConn=true;
	$obj=array("cotacaoId"=>$pCotacaoId);
	$res=deleteCotacaoMetal($conn, $closeConn, $obj);
	//.........................
}

if($action=="dmoeda") {
	//.........................
	$conn=getDefaultConnection(); $closeConn=true;
	$obj=array("cotacaoId"=>$pCotacaoId);
	$res=deleteCotacaoMoeda($conn, $closeConn, $obj);
	//.........................
}

?>

<?php
if($action=="qferiado") {
	//.........................
	$finalData=array("action"=>$action, "cotacoes"=>$aCotacoes, "message"=>$message, "allowInsert"=>$allowInsert, "allowDelete"=>$allowDelete);
	echo json_encode($finalData, JSON_FORCE_OBJECT);
	//.........................
} else {
	//.........................
	$finalData=array("action"=>$action, "cotacoes"=>$aCotacoes, "message"=>$message);
	echo json_encode($finalData, JSON_FORCE_OBJECT);
	//.........................
}

?>

<?php

function convertDateToMySql($dt) {
	$d=explode("/",$dt);
	//var_dump($d);
	return $d[2] . "-" . $d[1] . "-" . $d[0];
}

function convertMySqlToDate($dt) {
	$d=explode("-",$dt);
	//var_dump($d);
	return $d[2] . "/" . $d[1] . "/" . $d[0];
}


function getCotacaoDolarByDate($conn, $closeConn, $obj) {
	$sql="SELECT * FROM cotacao_moeda WHERE moeda='" . ($obj["moeda"]) . "' "
		. " AND dataCotacao='" . convertDateToMySql($obj["dataCotacao"]) . "' "
		. " AND (tipoBoletim NOT LIKE 'Inter%' AND tipoBoletim NOT LIKE 'Abertura%') "
		. " ORDER BY preferencial DESC, dataHoraCotacao ASC ";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}

function getCotacaoMetalByDate($conn, $closeConn, $obj) {
	$sql="SELECT * FROM cotacao_metal WHERE codigoMetal='" . ($obj["codigoMetal"]) . "' "
		. " AND dataCotacao='" . convertDateToMySql($obj["dataCotacao"]) . "' "
		. " ORDER BY dataCotacao ASC, preferencial DESC";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}

function saveFeriado($conn, $closeConn, $obj) {
	$sql="INSERT INTO feriado (dataFeriado) VALUES ('" . convertDateToMySql($obj["dFeriado"]) . "')";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return;
}

function deleteFeriado($conn, $closeConn, $obj) {
	$sql="DELETE FROM feriado WHERE dataFeriado='" . convertDateToMySql($obj["dFeriado"]) . "'";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return;
}

function getFeriadoByDate($conn, $closeConn, $obj) {
	//var_dump($obj);
	$sql="SELECT * FROM feriado WHERE dataFeriado='" . convertDateToMySql($obj["dataFeriado"]) . "' ";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}

function deleteCotacaoMetal($conn, $closeConn, $obj) {
	$sql="DELETE FROM cotacao_metal WHERE cotacaoId=" . $obj["cotacaoId"];
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return;
}

function deleteCotacaoMoeda($conn, $closeConn, $obj) {
	$sql="DELETE FROM cotacao_moeda WHERE cotacaoId=" . $obj["cotacaoId"];
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return;
}

?>