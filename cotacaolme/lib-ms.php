<?php
function ylog($text) {
	if($GLOBALS["ylog"]) echo "". date('Y-m-d H:i:s') . " " . $text . "\r\n";
}

function getDefaultConnection() {
	if($GLOBALS["env"]=="YVY") {
		$servername = "10.10.10.28";
		$username = "CD_16_lme";
		$password = '7v77m3@Yvy$LME';
		$dbname = "CD_166465_lme";
		
		$servername = "f5nv42607714192.db.42607714.0a8.hostedresource.net:3308";
		$username = "f5nv42607714192";
		$password = "c!lcxO|/2|Mf";
		$dbname = "f5nv42607714192";
		
	} else {
		$servername = "localhost";
		$username = "yvyreciclagem";
		$password = 'yvy2018';
		$dbname = "cd_166465_lme";
	}

	ylog("new mysqli(".$servername.",".$username.",".$password.",".$dbname.")");
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	return $conn;
}


function getAllCotacaoDates($conn, $closeConn, $obj, $numParams) {
	// $sql = "SELECT dataCotacao, WEEK(dataCotacao,1) weekNumber, YEAR(dataCotacao) year, MONTH(dataCotacao) month, COUNT(*) cotacoes FROM cotacao_metal WHERE dataCotacao>='" . $obj[0] ."' AND dataCotacao<='" . $obj[1] . "' AND preferencial=1 GROUP BY dataCotacao ORDER BY dataCotacao";
	$sql=
		"SELECT dataCotacao, WEEK(dataCotacao,1) weekNumber, YEAR(dataCotacao) year, MONTH(dataCotacao) month, COUNT(*) cotacoes " 
		. " FROM (SELECT DISTINCT dataCotacao, preferencial FROM "
		. " (SELECT dataCotacao, preferencial FROM cotacao_metal WHERE dataCotacao>='" . $obj[0] ."' AND dataCotacao<='" . $obj[1] ."' AND preferencial=1 "
		. " UNION ALL "
		. " SELECT dataFeriado dataCotacao, 1 preferencial FROM feriado WHERE dataFeriado>='" . $obj[0] ."' AND dataFeriado<='" . $obj[1] ."') t) t2 "
		. " WHERE preferencial=1 GROUP BY dataCotacao ORDER BY dataCotacao ";
	
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}

function getAllCotacaoMoedaDates($conn, $closeConn, $obj) {
	$sql = "SELECT dataCotacao, WEEK(dataCotacao,1) weekNumber, YEAR(dataCotacao) year, MONTH(dataCotacao) month, COUNT(*) cotacoes FROM cotacao_moeda WHERE dataCotacao>='" . $obj[0] ."' AND dataCotacao<='" . $obj[1] . "' AND preferencial=1 GROUP BY dataCotacao ORDER BY dataCotacao";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}

function getCotacaoMoedaByTipo($conn, $closeConn, $obj) {
	$sql="SELECT * FROM cotacao_moeda WHERE "
			." dataCotacao='".$obj["dataCotacao"]."' AND moeda='".$obj["moeda"]."' AND tipoBoletim='".$obj["tipoBoletim"]."'";
	$result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
	return $result;
}

function mySqlRunQuery($query, $conn, $closeConn) {
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
		$result->close();
		return false;
	}
	if($closeConn) $conn->close();
}


function saveCotacaoMoedaPreferencial($conn, $closeConn, $obj) {
	ylog("saveCotacaoMoedaPreferencial: início");
	// se preferencial, desmarca os demais da data/metal (update)
	ylog("preferencial ".$obj["preferencial"]);
	if($obj["preferencial"]===1) {
    	$sql="UPDATE cotacao_moeda SET preferencial=0 WHERE dataCotacao='".$obj["dataCotacao"]."' AND moeda='".$obj["moeda"]."'";
    	$result=mySqlRunQuery($sql, $conn, $closeConn);
    	if($result==1) { ylog("Update ok."); }
	}
	// verifica se existente
	$found=false; $cotacaoId=null;
	$sql="SELECT * FROM cotacao_moeda WHERE dataCotacao='".$obj["dataCotacao"]."' AND moeda='".$obj["moeda"]."' AND fonte='".$obj["fonte"]."' AND tipoBoletim='".$obj["tipoBoletim"]."' AND dataHoraCotacao='".$obj["dataHoraCotacao"]."'";
	$result=mySqlRunQuery($sql, $conn, $closeConn);
	if($result) {
		if(is_array($result)) {}
		if(is_object($result)) {
			if($result->num_rows>0) { $found=true; }
			foreach($result as $resline) {
				$cotacaoId=$resline["cotacaoId"];
			}
		}
	}
	$result->close();
	
	if(!$found) {
		// insere a cotacao preferencial (ou atualiza se existente)
		$sql="INSERT INTO cotacao_moeda(dataCotacao, moeda, paridadeCompra, paridadeVenda, cotacaoCompra, cotacaoVenda, fonte, tipoBoletim, preferencial, dataHoraCotacao) VALUES "
				."('".$obj["dataCotacao"]."', '".$obj["moeda"]."', '".$obj["paridadeCompra"]."','".$obj["paridadeVenda"]."', '".$obj["cotacaoCompra"]."', '".$obj["cotacaoVenda"]."', '".$obj["fonte"]."', '".$obj["tipoBoletim"]."', ".$obj["preferencial"].", '".$obj["dataHoraCotacao"]."')";
		$result=mySqlRunQuery($sql, $conn, $closeConn);
	} else {
		// ou
		$sql="UPDATE cotacao_moeda SET "
				." cotacaoCompra='".$obj["cotacaoCompra"]."', cotacaoVenda='".$obj["cotacaoVenda"]."', preferencial=".$obj["preferencial"]." "
						." WHERE dataCotacao='".$obj["dataCotacao"]."' AND moeda='".$obj["moeda"]."' AND fonte='".$obj["fonte"]."' AND tipoBoletim='".$obj["tipoBoletim"]."' AND dataHoraCotacao='".$obj["dataHoraCotacao"]."' ";
		$result=mySqlRunQuery($sql, $conn, $closeConn);
	}
}

function saveCotacaoMetalPreferencial($conn, $closeConn, $obj) {
	ylog("saveCotacaoMetalPreferencial: início");
	// se preferencial, desmarca os demais da data/metal (update)
	$sql="UPDATE cotacao_metal SET preferencial=0 WHERE dataCotacao='".$obj["dataCotacao"]."' AND codigoMetal='".$obj["codigoMetal"]."'";
	$result=mySqlRunQuery($sql, $conn, $closeConn);

	ylog("After update");
	//var_dump($result);
	ylog("update returned ".$result);
	if($result==1) {
		ylog("Update ok.");
	}
	ylog("Fim do update.................................................");
	// verifica se existente
	$found=false; $cotacaoId=null;
	$sql="SELECT * FROM cotacao_metal WHERE dataCotacao='".$obj["dataCotacao"]."' AND codigoMetal='".$obj["codigoMetal"]."' AND fonte='".$obj["fonte"]."'";
	$result=mySqlRunQuery($sql, $conn, $closeConn);
	if($result) {
		if(is_array($result)) {}
		if(is_object($result)) {
			ylog("vardumping result");
			//var_dump($result);
			if($result->num_rows>0) { $found=true; }
			foreach($result as $resline) {
				ylog("vardumping resline");
				//var_dump($resline);
				ylog(".");
				$cotacaoId=$resline["cotacaoId"];
			}
		}
	}
	$result->close();
	ylog("Fim do select .................................................");
	if(!$found) {
		// insere a cotacao preferencial (ou atualiza se existente)
		$sql="INSERT INTO cotacao_metal(dataCotacao, codigoMetal, cotacaoDolar, fonte, preferencial) VALUES "
				."('".$obj["dataCotacao"]."', '".$obj["codigoMetal"]."', '".$obj["cotacaoDolar"]."', '".$obj["fonte"]."', '".$obj["preferencial"]."')";
		$result=mySqlRunQuery($sql, $conn, $closeConn);
	} else {
		// ou
		$sql="UPDATE cotacao_metal SET "
				." cotacaoDolar='".$obj["cotacaoDolar"]."', preferencial='".$obj["preferencial"]."' "
						." WHERE dataCotacao='".$obj["dataCotacao"]."' AND codigoMetal='".$obj["codigoMetal"]."' AND fonte='".$obj["fonte"]."' ";
		$result=mySqlRunQuery($sql, $conn, $closeConn);
	}
}

?>