<?php 
// NI CU TN PB ZI AL
/*
URLs que serão utilizadas são estas a seguir (API JSON do web site quandl.com, dataset LME):
Alumínio:
https://www.quandl.com/api/v3/datasets/LME/PR_AL.json?column_index=2&start_date=2018-08-01&end_date=2018-08-10&collapse=day&transformation=none&api_key=zc6786jVE4rG3sz6N9ys
Zinco: PR_ZI
https://www.quandl.com/api/v3/datasets/LME/PR_ZI.json?column_index=2&start_date=2018-08-01&end_date=2018-08-01&collapse=day&transformation=none&api_key=zc6786jVE4rG3sz6N9ys
Chumbo: PR_PB
https://www.quandl.com/api/v3/datasets/LME/PR_PB.json?column_index=2&start_date=2018-08-01&end_date=2018-08-10&collapse=day&transformation=none&api_key=zc6786jVE4rG3sz6N9ys
Estanho: PR_TN
https://www.quandl.com/api/v3/datasets/LME/PR_TN.json?column_index=2&start_date=2018-08-01&end_date=2018-08-10&collapse=day&transformation=none&api_key=zc6786jVE4rG3sz6N9ys
Cobre: PR_CU
https://www.quandl.com/api/v3/datasets/LME/PR_CU.json?column_index=2&start_date=2018-08-01&end_date=2018-08-10&collapse=day&transformation=none&api_key=zc6786jVE4rG3sz6N9ys
Niquel: PR_NI
https://www.quandl.com/api/v3/datasets/LME/PR_NI.json?column_index=2&start_date=2018-08-01&end_date=2018-08-10&collapse=day&transformation=none&api_key=zc6786jVE4rG3sz6N9ys
*/

function quandlcom_getValues( $metal, $start, $end ) {
	//$jsonObject = json_decode(file_get_contents("http://maps.google.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&sensor=false"));
	//$jsonObject = json_decode(file_get_contents("https://www.quandl.com/api/v3/datasets/LME/PR_AL.json?column_index=2&start_date=2018-08-01&end_date=2018-08-10&collapse=day&transformation=none&api_key=zc6786jVE4rG3sz6N9ys"));

	$jsonObj = json_decode(file_get_contents(
		"https://www.quandl.com/api/v3/datasets/LME/PR_" . $metal . ".json?column_index=2&start_date=" . $start . "&end_date=" . $end . "&collapse=day&transformation=none&api_key=zc6786jVE4rG3sz6N9ys"));
	return $jsonObj->dataset->data;
}



// https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoMoedaDia(moeda=@moeda,dataCotacao=@dataCotacao)?@moeda=%27USD%27&@dataCotacao=%2701-09-2018%27&$format=json
function bcbdolar_getValues($moeda, $start) {
	ylog("bcbdolar_getValues param " . $start);
	$url="https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoMoedaDia(moeda=@moeda,dataCotacao=@dataCotacao)?@moeda=%27".$moeda."%27&@dataCotacao=%27" . $start . "%27&" . "$" . "format=json";
	
	//V0
	//$jsonObj = json_decode(file_get_contents( $url ));
	
	$ctx = stream_context_create(array( 'https' => array( 'timeout' => 60, 'header'=>'Connection: close' ) ) );
	$fcts=file_get_contents( $url, 0, $ctx );
	//ylog("fcts= ".$fcts);
	$jsonObj = json_decode($fcts);
	
	
	// echo "\n" . $url . "\n";
	return $jsonObj->value;
}

?>


