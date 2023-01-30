<?php
require "./env.php";
require("./security_check.php");
require "./lib-ms.php";
require "./lib-ws.php";
//.........................
$GLOBALS["ylog"]=false;
if(isset($_GET["ylog"])) $GLOBALS["ylog"]=$_GET["ylog"]=="true"?true:false;
//.........................

//.........................
$dataCotacao=$_POST["dDataCotacao"];
$cotacaoVenda=$_POST["dCotacaoVenda"];
$moeda="USD";
//.........................

//.........................
if(strpos($dataCotacao,"/")) {
	$dataCotacao=date_format(date_create_from_format("d/m/Y",$dataCotacao),"Y-m-d");
}
//.........................

if($cotacaoVenda!="") $cotacaoVenda=str_replace(",",".",$cotacaoVenda);

//.........................
$conn=getDefaultConnection();

$obj=["dataCotacao"=>$dataCotacao, "moeda"=>$moeda,
"paridadeCompra"=>1, "paridadeVenda"=>1,
"cotacaoCompra"=>$cotacaoVenda, "cotacaoVenda"=>$cotacaoVenda,
"tipoBoletim"=>"Yvy", "dataHoraCotacao"=>date("Y-m-d H:i:s"),
"fonte"=>"Yvy", "preferencial"=>1
];

saveCotacaoMoedaPreferencial($conn, true, $obj);

//.........................
header('Content-Type: application/json');
//.........................

//.........................
$finalData=array("status"=>"OK" );
echo json_encode($finalData, JSON_FORCE_OBJECT);
//.........................

?>