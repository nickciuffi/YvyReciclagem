//(c) BendInfo LME Revisao Maio/2019 A.Silveira
function valuefield_onkeydown(e) {
	var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
    var charStr = String.fromCharCode(charCode);
    console.log(charStr+"; "+charCode);
	return ("0123456789,".indexOf(charStr)!=-1 || charCode==193 || charCode<=48 || charCode==188 || charCode==86 || charCode==110 || (charCode>=96 && charCode<=105));
}
function valuefield_onkeyup(e) {
	return true;
}
function valuefield_onfocus(e) {
	e.target.select();
}
function valuefield_onchange(e) {
	console.log(parseFloat(e.target.value.replace(",",".")));
	if(parseFloat(e.target.value.replace(",",".")) <= 0.0) {
		alert("Valor inválido.");		
	}
}


function datefield_onfocus(e) {
	e.target.select();
}
function datefield_onkeyup(e) {
	var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
    var charStr = String.fromCharCode(charCode);
    if((e.target.value.length==2 || e.target.value.length==5) && charStr!="/" && !charCode<=48) {
    	e.target.value+="/";
    }
	console.log(event.keyCode);
	return ("0123456789".indexOf(String.fromCharCode(event.keyCode))!=-1);
}

function datefield_onkeydown(e) {
	var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
    var charStr = String.fromCharCode(charCode);
	return ("0123456789/".indexOf(charStr)!=-1 || charCode==193 || charCode<=48 || charCode==86|| (charCode>=96 && charCode<=105));
}

function datefield_onchange(e) {
	var dp=e.target.value.split("/");
	var dv=new Date(dp[2],parseInt(dp[1])-1,dp[0]);
	console.log("getDay(): "+dv.getDay());
	console.log(e.target.value.length+"; "+dp.length+"; "+parseInt(dp[2])+"; "+dv.getFullYear());
	if(e.target.value.length<10 || dp.length<3 || parseInt(dp[2])<1900 || dv.getFullYear()<1900) {
		alert("Data inválida.");
		e.target.focus();
	}
	if((dv.getDay()==0 || dv.getDay()==6) && !lmerevConf.dateFields[e.target.seq].allowNonWork) {
		alert("Data não é dia útil.");
		e.target.select(); e.target.focus();
	}
	console.log(lmerevConf.dateFields[e.target.seq].allowFuture);
	if(dv>=Date.now() && !lmerevConf.dateFields[e.target.seq].allowFuture) {
		alert("Data é futura.");
		e.target.focus();
	}
	var dateFields=lmerevConf["dateFields"];
	var df=dateFields[e.target.seq];
	if(df && df.func) { df.func(); }
}
var lmerevConf=null;

function lmerev_init(conf) {
	lmerevConf=conf;
	var dateFields=lmerevConf["dateFields"]; 
	for (var i=0;i<dateFields.length;i++) {
		document.getElementById(dateFields[i].id).seq=i; //tag attribute
		document.getElementById(dateFields[i].id).onkeyup=datefield_onkeyup;
		document.getElementById(dateFields[i].id).onkeydown=datefield_onkeydown;
		document.getElementById(dateFields[i].id).onchange=datefield_onchange;
		document.getElementById(dateFields[i].id).onfocus=datefield_onfocus;
	}
	var valueFields=lmerevConf["valueFields"];
	for (var i=0;i<valueFields.length;i++) {
		document.getElementById(valueFields[i].id).seq=i; //tag attribute
		document.getElementById(valueFields[i].id).onkeyup=valuefield_onkeyup;
		document.getElementById(valueFields[i].id).onkeydown=valuefield_onkeydown;
		document.getElementById(valueFields[i].id).onchange=valuefield_onchange;
		document.getElementById(valueFields[i].id).onfocus=valuefield_onfocus;
	}
	document.getElementById("mCodigoMetal").onchange=function(){ lr_queryMetalByDate(); }
	document.getElementById("bFeriadoIncluir").onclick=function(){ lr_saveFeriado(); }
	document.getElementById("bFeriadoExcluir").onclick=function(){ lr_deleteFeriado(); }
}


function lr_queryMetalByDate() {
	var dateField=document.getElementById("mDataCotacao");
	var metalField=document.getElementById("mCodigoMetal");
	
	document.forms["fMetal"].dataCotacao.value=dateField.value;
	document.forms["fMetal"].codigoMetal.value=metalField.value;
	if(document.forms["fMetal"].dataCotacao.value=="") {
		/*alert("Preencha a data da cotação");*/ return;
	}
	if(document.forms["fMetal"].codigoMetal.value=="") {
		/*alert("Selecione um dos metais.");*/ return;
	}
	oForm=$("fMetal");
	aFields=["dataCotacao", "codigoMetal"];
	ffCallback=lr_queryMetalByDate_callback;
	sendForm("/cotacaolme/get_admin.php?a=qmetal", ""/*stPOSTString*/, oForm, aFields, ffCallback, false/*blArrayJs*/);
}

function lr_queryMetalByDate_callback(ret, retText) {
	var message=ret["message"];	if(message && message!=null) { alert(message); }
	var cotacoes=ret["cotacoes"];
	if(cotacoes && cotacoes!=null) {
		console.log("cotacoes.length:"+cotacoes.length);
		var tb=document.getElementById("tbMetais"); tb.innerHTML=""; //reset
		for(i=0;cotacoes[""+i]!=null;i++) {
			var row=tb.insertRow(tb.rows.length);
			var cell=null;
			cell=row.insertCell(row.cells.length);
			cell.innerHTML=cotacoes[""+i].preferencial==1?"<span class=\"tcmV\">[V]</span>":"&nbsp;";
			cell=row.insertCell(row.cells.length);
			cell.innerText=formatDate(cotacoes[""+i].dataCotacao);
			cell=row.insertCell(row.cells.length);
			cell.innerText=cotacoes[""+i].codigoMetal;
			cell=row.insertCell(row.cells.length);
			cell.innerText=formatNumber(cotacoes[""+i].cotacaoDolar,2);
			cell=row.insertCell(row.cells.length);
			cell.innerHTML=cotacoes[""+i].fonte;
			cell=row.insertCell(row.cells.length);
			if(cotacoes[""+i].fonte=="Yvy") {
				cell.innerHTML="<a href=\"#\" onclick=\"javascript:lr_deleteMetalById("+cotacoes[""+i].cotacaoId+");\">Excluir</a>";
			} else {
				cell.innerHTML="&nbsp;";
			}

		}
	}
}

function lr_queryDolarByDate() {
	console_log("lr_queryDolarByDate");
	var dateField=document.getElementById("dDataCotacao");

	document.forms["fDolar"].dataCotacao.value=dateField.value;
	document.forms["fDolar"].moeda.value="USD";
	
	oForm=$("fDolar");
	aFields=["dataCotacao"];
	ffCallback=lr_queryDolarByDate_callback;
	sendForm("/cotacaolme/get_admin.php?a=qdolar", ""/*stPOSTString*/, oForm, aFields, ffCallback, false/*blArrayJs*/);
}
function lr_queryDolarByDate_callback(ret, retText) {
	var message=ret["message"]; if(message && message!=null) { alert(message); }
	var cotacoes=ret["cotacoes"];
	if(cotacoes && cotacoes!=null) {
		console.log("cotacoes.length:"+cotacoes.length);
		var tb=document.getElementById("tbDolar"); tb.innerHTML=""; //reset

		for(i=0;cotacoes[""+i]!=null;i++) {
			var row=tb.insertRow(tb.rows.length);
			var cell=null;
			cell=row.insertCell(row.cells.length);
			cell.innerHTML=cotacoes[""+i].preferencial==1?"<span class=\"tcmV\">[V]</span>":"&nbsp;";
			cell=row.insertCell(row.cells.length);
			cell.innerText=formatDate(cotacoes[""+i].dataCotacao);
			cell=row.insertCell(row.cells.length);
			cell.innerText=cotacoes[""+i].moeda;
			cell=row.insertCell(row.cells.length);
			cell.innerText=formatNumber(cotacoes[""+i].cotacaoVenda,4);
			cell=row.insertCell(row.cells.length);
			cell.innerHTML=cotacoes[""+i].fonte
				+"<img class=\"tcmI\" src=\"detail.jpg\" title=\""+cotacoes[""+i].tipoBoletim+"\" xalt=\""+cotacoes[""+i].tipoBoletim+"\"/>";
			cell=row.insertCell(row.cells.length);
			if(cotacoes[""+i].fonte=="Yvy") {
				cell.innerHTML="<a href=\"#\" onclick=\"javascript:lr_deleteDolarById("+cotacoes[""+i].cotacaoId+");\">Excluir</a>";
			} else {
				cell.innerHTML="&nbsp;";
			}
		}
	}
}

function lr_queryFeriadoByDate() {
	console_log("lr_queryFeriadoByDate");
	var dateField=document.getElementById("dFeriado");

	document.forms["fFeriado"].dataFeriado.value=dateField.value;
	
	oForm=$("fFeriado");
	aFields=["dataFeriado"];
	ffCallback=lr_queryFeriadoByDate_callback;
	sendForm("/cotacaolme/get_admin.php?a=qferiado", ""/*stPOSTString*/, oForm, aFields, ffCallback, false/*blArrayJs*/);
}
function lr_queryFeriadoByDate_callback(ret, retText) {
	var message=ret["message"]; if(message && message!=null) { 
		document.getElementById("dFeriadoMensagem").innerText=ret["message"];
	}
	document.getElementById("bFeriadoIncluir").disabled=!ret["allowInsert"];
	document.getElementById("bFeriadoExcluir").disabled=!ret["allowDelete"];
}

function lr_saveFeriado(blIsDelete) {
	var dateField=document.getElementById("dFeriado");
	
	document.forms["fFeriado"].dataFeriado.value=dateField.value;
	document.forms["fFeriado"].delete.value=blIsDelete?"1":"0";
	if(document.forms["fFeriado"].dataFeriado.value=="") {
		alert("Preencha a data do feriado."); return;
	}
	oForm=$("fFeriado");
	aFields=["dataFeriado", "delete"];
	ffCallback=lr_saveFeriado_callback;
	sendForm("/cotacaolme/get_admin.php?a=sferiado", ""/*stPOSTString*/, oForm, aFields, ffCallback, false/*blArrayJs*/);
}

function lr_saveFeriado_callback() {
	alert("Registro de feriado atualizado.");
	document.getElementById("dFeriado").value="";
	getData();
}

function lr_deleteFeriado() {
	lr_saveFeriado(true);
}

function lr_deleteDolarById(cotacaoId) {
	if(!confirm("Confirma a exclusão da cotação?\r\nATENÇÃO: a operação não poderá ser desfeita.")) {
		return;
	}
	document.forms["fDolar"].cotacaoId.value=cotacaoId;
	oForm=$("fDolar"); aFields=["cotacaoId"];
	ffCallback=lr_deleteDolarById_callback;
	sendForm("/cotacaolme/get_admin.php?a=dmoeda", ""/*stPOSTString*/, oForm, aFields, ffCallback, false/*blArrayJs*/);
}
function lr_deleteDolarById_callback() {
	var dolarDate=document.getElementById("dDataCotacao");
	var dateFields=lmerevConf["dateFields"];
	var df=dateFields[dolarDate.seq];
	if(df && df.func) { df.func(); }
	getData();
}
function lr_deleteMetalById(cotacaoId) {
	if(!confirm("Confirma a exclusão da cotação?\r\nATENÇÃO: a operação não poderá ser desfeita.")) {
		return;
	}
	document.forms["fMetal"].cotacaoId.value=cotacaoId;
	oForm=$("fMetal"); aFields=["cotacaoId"];
	ffCallback=lr_deleteMetalById_callback;
	sendForm("/cotacaolme/get_admin.php?a=dmetal", ""/*stPOSTString*/, oForm, aFields, ffCallback, false/*blArrayJs*/);
}
function lr_deleteMetalById_callback() {
	var dolarDate=document.getElementById("mDataCotacao");
	var dateFields=lmerevConf["dateFields"];
	var df=dateFields[dolarDate.seq];
	if(df && df.func) { df.func(); }
	getData();
}
