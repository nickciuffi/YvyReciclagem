<?php 
require "./env.php";
require("./security_check.php"); 
?>
<?php
$conn=getDefaultConnection();
$cotacoes=getCotacaoMetalPeriodo($conn, true, ["2018-08-01","2018-09-10","AL"], 2);
?>
<script type="text/javascript">
  function $(src) {
  return document.getElementById(src);
  }

  function body_onload() {
  biLoadActiveUsers();
  }


  function biLoadActiveUsers() {
  ret = sendForm("./get_data.php",
  null, document.getElementById("form-users"), ["maxRows"], bpmprojman_usersdone);
  return false;
  }


  function bpmprojman_usersdone(ret, retText) {
  //$.modal.alert('Users done!');
  document.getElementById("sAllAccounts").innerHTML = ret.length;
  var s = 1;
  for (i = 0; i < ret.length; i++) {
	            
	            var iSample1 = document.getElementById("userSample1"); // find row to copy
	            var iSample2 = document.getElementById("userSample2"); // find row to copy
	            var iSample3 = document.getElementById("userSample3"); // find row to copy
	            var iSample4 = document.getElementById("userSample4"); // find row to copy
	            var iSample5 = document.getElementById("userSample5"); // find row to copy
	            var iSample6 = document.getElementById("userSample6"); // find row to copy
	            var iSample7 = document.getElementById("userSample7"); // find row to copy

	            var item;
	            if (s == 1) item = iSample1.cloneNode(true);
	            if (s == 2) item = iSample2.cloneNode(true);
	            if (s == 3) item = iSample3.cloneNode(true);
	            if (s == 4) item = iSample4.cloneNode(true);
	            if (s == 5) item = iSample5.cloneNode(true);
	            if (s == 6) item = iSample6.cloneNode(true);
	            if (s == 7) item = iSample7.cloneNode(true);
	            s++; if (s > 7) s = 1;

                iSample1.style.display = "none";
                iSample2.style.display = "none";
                iSample3.style.display = "none";
                iSample4.style.display = "none";
                iSample5.style.display = "none";
                iSample6.style.display = "none";
                iSample7.style.display = "none";

	            var oContainer = document.getElementById("usersActive"); // find table to append to
	            item.id = "ua" + ret[i].CD_COLLAB;

	            //document.getElementById("ne"+ret[i].CD_TASK).style.display="none";
                
	            oContainer.appendChild(item); // add new row to end of table
	            document.getElementById(item.id).innerHTML =
	                document.getElementById(item.id).innerHTML
	                .replace("{CD_COLLAB}", ret[i].CD_COLLAB)
                    .replace("{FirstName}", ret[i].FirstName)
                    .replace("{OtherNames}", ret[i].OtherNames);
	            document.getElementById("ua" + ret[i].CD_COLLAB).style.display = "";
	        }
	    }
</script>

<script type="text/javascript">



  //var tempXML;
  //var tempText;
  //var userInfo;
  //var jsonstr;

  var aCalls = [];

  function sendForm(stWebService, stPOSTString, oForm, aFields, ffCallback, blArrayJs) {

  //TODO ABS if !string, compose with oForm
  var stMethod="POST";

  //register each page call in aCalls (callback and responses)
  var xhr = new XMLHttpRequest();
  aCalls[aCalls.length] = [stWebService, ffCallback, null, ""];
  callIndex = aCalls.length - 1;
  xhr.id = "xhr" + callIndex;

  xhr.open(stMethod, stWebService);

  //jsonstr = JSON.stringify({ name: 'John Smith', age: 34 });
  jsonstr = stPOSTString;


    for(i=0;i<aFields.length;i++) {
    	if(jsonstr==null||jsonstr=="") {
    		jsonstr="";
    	}
    	if(jsonstr!="") { jsonstr+="&"; };
    	jsonstr+=aFields[i]+"="+oForm[aFields[i]].value;
    }

    if(!jsonstr || jsonstr==null) {
    	jsonstr="name=John Smith&age=34";
    }
  //xhr.setRequestHeader('Content-Type', 'application/json');
  //xhr.setRequestHeader('Content-Type', 'text/xml');
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  //xhr.setRequestHeader('Content-Length', jsonstr.length);

  xhr.onload = function (e) {
  if (xhr.status === 200) {
  tempText = xhr.responseText;
  tempXML = xhr.responseXML;
  //log("tempText: " + tempText);
  //log("tempXML: " + tempXML);

  var userInfo2;
  if (!blArrayJs) {
  userInfo2 = JSON.parse(XMLParse(xhr.responseText));
  } else {
  //$.modal.alert(xhr.responseText);
  userInfo2 = eval( XMLParse(xhr.responseText) );
  //alert(userInfo2);
  }

  ind = parseInt((xhr.id.replace("xhr", "")));

  if (/*fCallback*/aCalls[ind][1]) {
  //fCallback(userInfo, xhr.responseText);
  aCalls[ind][2] = userInfo2;
  aCalls[ind][3] = xhr.responseText;
  aCalls[ind][1](aCalls[ind][2], aCalls[ind][3]);
  }
  } else {
  log(xhr.status);
  $.modal.alert(jsonstr);
  }
  };
  //log("jsonstr: " + jsonstr);
  xhr.send(jsonstr);
  //xhr.send("json="+"{ name: 'John Smith', age: 34 }");
  return callIndex;
  }

  function XMLParse(xmlText) {
  //log("XMLParse: " + xmlText);
  var text, parser, xmlDoc;
  text = xmlText;
  parser = new DOMParser();
  xmlDoc = parser.parseFromString(text, "text/xml");
  ret = xmlDoc.getElementsByTagName("string")[0].childNodes[0].nodeValue;
  //log("XMLParse: end " + ret);
  return ret;
  }

  function showUsers() {
  //log("showUsers: start " + userInfo.length);
  for (i = 0; i < userInfo.length; i++) {
        log(userInfo[i].id);
        log(userInfo[i].name);
    }
}

function log(stText) {
	if(document.getElementById("taLog")==null) {
	    //alert(stText);
	    $.modal.alert(stText);
	} else {
		document.getElementById("taLog").value += stText + "\r\n";
	}
}

</script>

<style type="text/css">
  .cotacao.field{ float:left; }
  .cotacao.line{ float: none; display:inline-block; width:100%; }
</style>

<body>
<div>
  <select>
    <option value="AL">AL - Alumínio</option>
    <option value="ZI">ZI - Zinco</option>
    <option value="AL">AL - Alumínio</option>
    <option value="AL">AL - Alumínio</option>
    <option value="AL">AL - Alumínio</option>
    <option value="AL">AL - Alumínio</option>
    <option value="AL">AL - Alumínio</option>
    <option value="AL">AL - Alumínio</option>
  </select>
  <input type="text" name="start" id="start" value="01/09/2018"/>
  <input type="text" name="end" id="end" value="10/09/2018"/>
</div>

  <form method="POST" name="form-users" id="form-users">
    <input type="hidden" name="maxRows" id="maxRows" value="5"/>
  </form>

<?php
while($cotacao = $cotacoes->fetch_assoc()) {
?>
	<!-- <div>Teste<?=var_dump($cotacao)?></div> -->
  <div class="cotacao line">
    <div class="cotacao field"><input type="text" value="<?=$cotacao["dataCotacao"]?>"/></div>
    <div class="cotacao field"><input type="text" value="<?=$cotacao["codigoMetal"]?>"/></div>
    <div class="cotacao field"><input type="text" value="<?=$cotacao["cotacaoDolar"]?>"/></div>
  </div>

  
<?php
}

closeConnection($conn);
?>

  <script type="text/javascript">
    document.body.onload=body_onload;
  </script>

</body>

<?php

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

function showCotacaoMetalRow($row) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
		echo "cotacaoId: " . $row["cotacaoId"]. " - Metal: " . $row["codigoMetal"]. " Data: " . $row["dataCotacao"]. " " . $row["cotacaoDolar"]. "<br>\n";
	}
}

function closeConnection($conn) {
	$conn->close();
	$conn=null;
}
// MySql Functions
//------------------------------------------------------------------


function getCotacaoMetalPeriodo($conn, $closeConn, $obj, $numParams) {
	$sql = "SELECT cotacaoId, dataCotacao, codigoMetal, cotacaoDolar FROM cotacao_metal WHERE dataCotacao>='".$obj[0]."' AND dataCotacao<='".$obj[1]."' AND codigoMetal='".$obj[2]."'";
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


?>