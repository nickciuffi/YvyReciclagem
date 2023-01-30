<?php 
require "./env.php";
require("./security_check.php");

require("./lib-ms.php");
?>

<?php
$start="";
$end="";
if($start=="") {
	$start = date_format(date_create_from_format('Y-m-d', date("Y-m-d", strtotime("-12 day", strtotime(date("Y-m-d")))) ), "d/m/Y");
}
if($end=="") {
	$end=date_format(date_create_from_format('Y-m-d',date("Y-m-d")), "d/m/Y"); 
} 
?>

<?php
//Save POST
 
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="pragma" content="no-cache"/>
	<script src="./js/Chart.bundle.min.js" type="text/javascript"></script>
	<script src="./js/bendinfo.ws.js" type="text/javascript"></script>
	<!-- script src="./js/bendinfo.cotacao.js" type="text/javascript"></script -->
	<script src="./js/bendinfo.cotacao.Yvy.js" type="text/javascript"></script>
	<script src="./js/bendinfo.lmerev.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="./css/bendinfo.cotacao.css">
	<script type="text/javascript">
		var gM2Mode="DAY";
	
		function clear(obj) {
			document.getElementById(obj).value="";
		}
		function hasValue(obj) {
			if(document.getElementById(obj).value=="") {
				return false;
			} else {
				return true;
			}
		}
		function salvarCotacaoDolar() {
			if(!hasValue("dDataCotacao") || !hasValue("dCotacaoVenda")) {
				alert("Por favor preencha os campos corretamente."); return;
			}
			oForm=$("saveData"); aFields=["dDataCotacao","dCotacaoVenda"];
			ffCallback=saveCotacaoDolar_Callback;
			sendForm("save_cotacao_dolar.php", /*stPOSTString*/"", oForm, aFields, ffCallback, /*blArrayJs*/ false);
		}

		function saveCotacaoDolar_Callback() {
			getData();
			alert("A cotação foi salva.");
			clear("dDataCotacao"); clear("dCotacaoVenda");
		}

		function salvarCotacaoMetal() {
			if(!hasValue("mDataCotacao") || !hasValue("mCodigoMetal") || !hasValue("mCotacaoDolar")) {
				alert("Por favor preencha os campos corretamente."); return;
			}
			oForm=$("saveData"); aFields=["mDataCotacao","mCodigoMetal","mCotacaoDolar"];
			ffCallback=saveCotacaoMetal_Callback;
			sendForm("save_cotacao_metal.php", /*stPOSTString*/"", oForm, aFields, ffCallback, /*blArrayJs*/ false);
			
		}

		function saveCotacaoMetal_Callback() {
			getData();
			alert("A cotação foi salva.");
			clear("mDataCotacao"); clear("mCodigoMetal"); clear("mCotacaoDolar");
		}

		blBuildLastCotacaoBoard=false;

		function atualizaWebServices() {
			oForm=$("saveData"); aFields=["start","end"];
			ffCallback=atualizaWebServices_Callback;
			sendForm("atualiza_ws.php?start="+document.getElementById("start").value+"&end="+document.getElementById("end").value, /*stPOSTString*/"", oForm, aFields, ffCallback, /*blArrayJs*/ false);
		}

		function atualizaWebServices_Callback() {
			getData();
			alert("Os dados foram atualizados.");
		}
		
	</script>
</head>
<body>
	<form name="frmFilter" id="frmFilter">
		<div>
			<input type="hidden" class="codigoMetal" id="codigoMetal" name="codigoMetal" value="AL"/>
			<input type="hidden" name="dateFrom" id="dateFrom" value="<?=$start?>"/>
			<input type="hidden" name="dateTo" id="dateTo" value="<?=$end?>"/>
			<input type="hidden" name="lastDataCotacao" id="lastDataCotacao" class="last-data-cotacao"/>
		</div>
	</form>
	<form name="saveData" id="saveData">
		<style type="text/css">
			.action-box{ margin:20px; padding:20px; background-color:lightyellow; float:left; border:1px solid #444444; }
		</style>
		<div class="action-box">
			<div class="tcmSTit">Cotação do Dólar:</div>
			<div><span class="tcmLbl">Data da Cotação:</span><input class="txtFld" type="text" name="dDataCotacao" id="dDataCotacao" placeholder="dd/mm/yyyy" maxlength="10"/></div>
			<div><span class="tcmLbl">Cotação (venda):</span><input class="txtFld" type="text" name="dCotacaoVenda" id="dCotacaoVenda" placeholder="9,99999" maxlength="10"/></div>
			<div><input type="button" value="Salvar" onclick="javascript:salvarCotacaoDolar();"/></div>
			<div>
				<div class="tcmTit">Cotações cadastradas para a data:</div>
				<div class="tCotCner">
					<table class="tCot" id="tCotDolar" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th>Data</th>
								<th>Moeda</th>
								<th>Cotação</th>
								<th>Origem</th>
								<th>Ações</th>
							</tr>
						</thead>
						<tbody id="tbDolar" class="tCot-tb">
						</tbody>
					</table>
				</div>
			</div>
			<div class="tcmLeg"><span>[V]</span> = Cotação vigente</div>
		</div>
		<div class="action-box">
			<div class="tcmSTit">Cotação de Metais:</div>
			<div><span class="tcmLbl">Data da Cotação:</span><input class="txtFld" type="text" name="mDataCotacao" id="mDataCotacao" placeholder="dd/mm/yyyy" maxlength="10"/></div>
			<div><span class="tcmLbl">Metal:</span><select class="txtFld" name="mCodigoMetal" id="mCodigoMetal">
				<option value="">--- Selecione ---</option>
				<option value="AL">AL-Alumínio</option>
				<option value="CU">CU-Cobre</option>
				<option value="NI">NI-Níquel</option>
				<option value="PB">PB-Chumbo</option>
				<option value="TN">TN-Estanho</option>
				<option value="ZI">ZI-Zinco</option>
			</select></div>
			<div><span class="tcmLbl">Valor da Cotação:</span><input class="txtFld" type="text" name="mCotacaoDolar" id="mCotacaoDolar" placeholder="999999,99" maxlength="10"/></div>
			<div><input type="button" value="Salvar" onclick="javascript:salvarCotacaoMetal();"/></div>
			<style type="text/css">
				.tCot thead tr th { border-bottom:1px solid black; font-size:8pt; padding-bottom:3px; }
				.tCot { padding-bottom:15px; border:none; width:100%; min-width:280px; }
				#tCotMetais thead tr th { border-bottom:1px solid black; font-size:8pt; padding-bottom:3px; }
				#tCotMetais{ padding-bottom:15px; border:none; width:100%; min-width:280px; }
				.tcmTit{ padding-top: 15px;padding-bottom:15px; }
				.tcmLeg{ font-size:8pt; color:#AAAAAA; }
				.tcmLeg span { font-weight:bold; color:Green; }
				.tcmV { font-weight:bold; color:Green; }
				.tcmLbl { min-width:115px; display:inline-block; }
				.txtFld{ width:125px; padding:3px 0px 3px 0px; margin:3px 0px 3px 0px; }
				#tCotMetais tbody tr td { font-size:9pt; }
				.tCot tbody tr td { font-size:9pt; padding:2px 0px 2px 0px; }
				.tcmSTit{ font-weight:bold; }
				.tCotCner { max-height:175px; overflow:auto; display:inline-block; width:100%; }
				.tcmFerMsg { font-size: 10pt; color:#555555; padding-top:7px; }
				.bFer { margin-right: 35px; } 
				.tcmI { margin-left:5px; }
			</style>
			<div>
				<div class="tcmTit">Cotações cadastradas para a data:</div>
				<div class="tCotCner">
					<table class="tCot" id="tCotMetais" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th>Data</th>
								<th>Metal</th>
								<th>Cotação</th>
								<th>Origem</th>
								<th>Ações</th>
							</tr>
						</thead>
						<tbody id="tbMetais">
						</tbody>
					</table>
				</div>
			</div>
			<div class="tcmLeg"><span>[V]</span> = Cotação vigente</div>
		</div>
		
		<div style="max-width:350px;display:inline-block;float:left;">
			<div class="action-box">
				<div class="tcmSTit">Atualização Web Services:</div>
				<div><span class="tcmLbl">Data Início:</span><input class="txtFld" type="text" name="start" id="start" placeholder="dd/mm/yyyy" value="<?=$start?>" maxlength="10"/></div>
				<div><span class="tcmLbl">Data Fim:</span><input class="txtFld" type="text" name="end" id="end" placeholder="dd/mm/yyyy" value="<?=$end?>" maxlength="10"/></div>
				<div><input type="button" value="Atualizar" onclick="javascript:atualizaWebServices();"/></div>
			</div>

			<!-- ABS 20190515 Melhorias -->
			<div class="action-box">
				<div class="tcmSTit">Feriados:</div>
				<div><span class="tcmLbl">Data:</span><input type="text" class="txtFld" name="dFeriado" id="dFeriado" maxlength="10" placeholder="dd/mm/yyyy"/></div>
				<div><input class="bFer" type="button" value="Incluir" id="bFeriadoIncluir"/><input class="bFer" type="button" value="Excluir" id="bFeriadoExcluir"/></div>
				<div id="dFeriadoMensagem" class="tcmFerMsg">&nbsp;</div>
			</div>

		</div>
		<div class="action-box">
			<a href="logout.php">Sair</a>
		</div>
		
		
	</form>

	<table id="tCotacoes" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr class="tr-header">
				<th>Dia</th>
				<th>Dólar</th>
				<th colspan="2">Alumínio</th>
				<th colspan="2">Chumbo</th>
				<th colspan="2">Cobre</th>
				<th colspan="2">Estanho</th>
				<th colspan="2">Níquel</th>
				<th colspan="2">Zinco</th>
			</tr>
			<tr class="tr-header">
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>US$</th>
				<th>R$</th>
				<th>US$</th>
				<th>R$</th>
				<th>US$</th>
				<th>R$</th>
				<th>US$</th>
				<th>R$</th>
				<th>US$</th>
				<th>R$</th>
				<th>US$</th>
				<th>R$</th>
			</tr>
		</thead>
		<tbody id="tCotacoes_tbody">
			<tr>
				<td></td>
				<td></td>
			</tr>
		</tbody>
	</table>
	
	<!-- lmerev -->
	<div style="display:none;">
		<div class="td-usd-last-cotacao"></div>
		<div class="usd-variacao"></div>
		<div class="usd-up-down"></div>
		<div class="td-AL-last-cotacao"></div>
		<div class="td-CU-last-cotacao"></div>
		<div class="td-NI-last-cotacao"></div>
		<div class="td-PB-last-cotacao"></div>
		<div class="td-TN-last-cotacao"></div>
		<div class="td-ZI-last-cotacao"></div>
		<div class="AL-variacao"></div>
		<div class="CU-variacao"></div>
		<div class="NI-variacao"></div>
		<div class="PB-variacao"></div>
		<div class="TN-variacao"></div>
		<div class="ZI-variacao"></div>
		<div class="AL-up-down"></div>
		<div class="CU-up-down"></div>
		<div class="NI-up-down"></div>
		<div class="PB-up-down"></div>
		<div class="TN-up-down"></div>
		<div class="ZI-up-down"></div>
	</div>
	
	<form id="fMetal" name="fMetal">
		<input type="hidden" name="cotacaoId" id="cotacaoId" value=""/>
		<input type="hidden" name="dataCotacao" id="dataCotacao" value=""/>
		<input type="hidden" name="codigoMetal" id="codigoMetal" value=""/>
	</form>
	<form id="fDolar" name="fDolar">
		<input type="hidden" name="cotacaoId" id="cotacaoId" value=""/>
		<input type="hidden" name="dataCotacao" id="dataCotacao" value=""/>
		<input type="hidden" name="moeda" id="moeda" value=""/>
	</form>
	<form id="fFeriado" name="fFeriado">
		<input type="hidden" name="dataFeriado" id="dataFeriado" value=""/>
		<input type="hidden" name="delete" id="delete" value="0"/>
	</form>
	<!-- lmerev -->	

	<script type="text/javascript">
		document.body.onload=body_onload(/*blMetalButtons*/ false);
		(function(){
			blBuildLastCotacaoBoard=false;
			blDoGraph=false;
		
			var conf={"dateFields": [ 
				{"id": "dDataCotacao", "allowFuture": false, "allowNonWork": false, "func": function(){ lr_queryDolarByDate("dDataCotacao"); } }, 
				{"id": "mDataCotacao", "allowFuture": false, "allowNonWork": false, "func": function(){ lr_queryMetalByDate("mDataCotacao"); } },
				{"id": "dFeriado", "allowFuture": true, "allowNonWork": false, "func": function(){ lr_queryFeriadoByDate("dFeriado"); }},
				{"id": "start", "allowFuture": false, "allowNonWork": true},
				{"id": "end", "allowFuture": false, "allowNonWork": true},
				 ],
				 "valueFields":[ {"id":"dCotacaoVenda"}, {"id":"mCotacaoDolar"} ] };
			lmerev_init(conf); 
		})();
	</script>


</body>
</html>
