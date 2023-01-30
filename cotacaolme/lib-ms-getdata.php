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
            . " WHERE preferencial=1 AND dataCotacao>='".$obj[0]."' AND dataCotacao<='".$obj[1]."' "
                ." GROUP BY WEEK(dataCotacao,1), codigoMetal";
                $result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
                return $result;
}
//..........................................................................
function getCotacaoMetalMediaMes($conn, $closeConn, $obj) {
    ylog(""); ylog("getCotacaoMetalMediaMes: start..........................");
    $sql = "SELECT YEAR(dataCotacao) year, MONTH(dataCotacao) month, codigoMetal, COUNT(*) cotacoes, AVG(cotacaoDolar) mediaCotacao "
        ." FROM cotacao_metal "
            ." WHERE preferencial=1 AND dataCotacao>='".$obj[0]."' AND dataCotacao<='".$obj[1]."' "
                ." GROUP BY YEAR(dataCotacao), MONTH(dataCotacao), codigoMetal";
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
            . " WHERE preferencial=1 AND dataCotacao>='".$obj[0]."' AND dataCotacao<='".$obj[1]."' "
                ." GROUP BY WEEK(dataCotacao,1), moeda";
                $result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
                return $result;
}
//..........................................................................
function getCotacaoMoedaMediaMes($conn, $closeConn, $obj) {
    ylog(""); ylog("getCotacaoMoedaMediaMes: start..........................");
    $sql = "SELECT YEAR(dataCotacao) year, MONTH(dataCotacao) month, moeda, COUNT(*) cotacoes, AVG(cotacaoCompra) mediaCompra, AVG(cotacaoVenda) mediaVenda "
        ." FROM cotacao_moeda "
            ." WHERE preferencial=1 AND dataCotacao>='".$obj[0]."' AND dataCotacao<='".$obj[1]."' "
                ." GROUP BY YEAR(dataCotacao), MONTH(dataCotacao), moeda";
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
    $sql = "SELECT MAX(dataCotacao) previousDataCotacao FROM cotacao_metal WHERE preferencial=1 AND dataCotacao<'" . $obj[0] . "'";
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


// New functions: comparativo duas datas ABS20181206
function getNextDataCotacaoMetal($conn, $closeConn, $obj) {
    ylog(""); ylog("getNextDataCotacaoMetal: start..........................");
    $sql = "SELECT MIN(dataCotacao) dataCotacao FROM cotacao_metal "
        . " WHERE dataCotacao >=  '" . $obj[0] . "' "
            . " AND preferencial=1 "
                . " ";
    $result=mySqlRunQuery($sql, $conn, $closeConn, $obj);
    return $result;
}
//.........................................................................



?>

