<?php
require "./env.php";
require("./lib-ms.php");
require("./lib-ms-getdata.php");
//.........................
$GLOBALS["ylog"]=false;
if(isset($_GET["ylog"])) $GLOBALS["ylog"]=$_GET["ylog"]=="true"?true:false;
//.........................

//.........................
$conn=getDefaultConnection();
$dateFrom=""; if(isset($_POST["dateCompFrom"])) $dateFrom=$_POST["dateCompFrom"];
$dateTo=""; if(isset($_POST["dateCompTo"])) $dateTo=$_POST["dateCompTo"];
if($dateFrom=="" && isset($_GET["dateCompFrom"])) $dateFrom=$_GET["dateCompFrom"];
if($dateTo=="" && isset($_GET["dateCompTo"])) $dateTo=$_GET["dateCompTo"];
//$codigoMetal=""; if(isset($_POST["codigoMetal"])) $codigoMetal=$_POST["codigoMetal"];
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

$data=getNextDataCotacaoMetal($conn, false, [$dateFrom] );
$penultimateDataCotacao=$dateFrom;
if($data) foreach($data as $el) { $penultimateDataCotacao=$el["dataCotacao"]; ylog("dataCotacao: ".$penultimateDataCotacao); }
$data->close();


$lastDataCotacao=$dateTo;
$data=getNextDataCotacaoMetal($conn, false, [$dateTo] );
if($data) foreach($data as $el) { $lastDataCotacao=$el["dataCotacao"]; ylog("dataCotacao: ".$lastDataCotacao); }
$data->close();

$aLastDataCotacao[]=array("lastDataCotacao"=>$lastDataCotacao, "previousDataCotacao"=>$penultimateDataCotacao);

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
$finalData=array(
    "aLastDataCotacao"=>$aLastDataCotacao, "aLastCotacaoMetal"=>$aLastCotacaoMetal, "aLastCotacaoMoeda"=>$aLastCotacaoMoeda
);
echo json_encode($finalData, JSON_FORCE_OBJECT);
//.........................

?>