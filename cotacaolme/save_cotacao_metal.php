<?php
require "./env.php";
require("./security_check.php");
require "./lib-ms.php";
require "./lib-ws.php";
//.........................
$GLOBALS["ylog"]=false;
if(isset($_GET["ylog"])) $GLOBALS["ylog"]=$_GET["ylog"]=="true"?true:false;
//.........................

$dataCotacao=$_POST["mDataCotacao"];
$codigoMetal=$_POST["mCodigoMetal"];
$cotacaoDolar=$_POST["mCotacaoDolar"];

//.........................
if(strpos($dataCotacao,"/")) {
	$dataCotacao=date_format(date_create_from_format("d/m/Y",$dataCotacao),"Y-m-d");
}
//.........................

if($cotacaoDolar!="") $cotacaoDolar=str_replace(",",".",$cotacaoDolar);

$conn=getDefaultConnection();

saveCotacaoMetalPreferencial($conn, true, [ "dataCotacao"=>$dataCotacao, "codigoMetal"=>$codigoMetal, "fonte"=>"Yvy",
"cotacaoDolar"=>$cotacaoDolar, "preferencial"=>1 ] );

//.........................
header('Content-Type: application/json');
//.........................

//.........................
$finalData=array("status"=>"OK" );
echo json_encode($finalData, JSON_FORCE_OBJECT);
//.........................

?>
