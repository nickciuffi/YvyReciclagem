<?php
$start="";
$end="";
if($start=="") {
	$start = date_format(date_create_from_format('Y-m-d', date("Y-m-d", strtotime("-30 day", strtotime(date("Y-m-d")))) ), "d/m/Y");
}
if($end=="") {
	$end=date_format(date_create_from_format('Y-m-d',date("Y-m-d")), "d/m/Y"); 
} 
?>
<DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="pragma" content="no-cache"/>	
	<style type="text/css">
		body{ margin: 0 auto; }
		.body-outer{ text-align: center; background-color: #FAFAFA; width:100%; display:inline-block; }
		.body-inner{ margin:0 auto; min-width:800px; width:70%; background-color: #FFFFFF; display:inline-block; margin-bottom: 30px; }
	</style>
	<script type="text/javascript">
		function body_onload() {
			getData();
			$("aCU").onclick=metal_click; 
			$("aAL").onclick=metal_click; 
			$("aZI").onclick=metal_click; 
			$("aPB").onclick=metal_click; 
			$("aTN").onclick=metal_click; 
			$("aNI").onclick=metal_click; 
			$("aUS").onclick=metal_click; 
		}

		function resetActive() {
			$("aCU").classList.remove("active");
			$("aAL").classList.remove("active");
			$("aZI").classList.remove("active");
			$("aPB").classList.remove("active");
			$("aTN").classList.remove("active");
			$("aNI").classList.remove("active");
			$("aUS").classList.remove("active");
		}

		function metal_click() {
			$("codigoMetal").value=this.attributes["datavalue"].value;
			resetActive();
			this.classList.add("active");

			calculateRows();
		}

		function getData() {
			//stWebService, stPOSTString, oForm, aFields, ffCallback, blArrayJs
			stPOSTString="";
			oForm=$("frmFilter");
			stPOSTString="";
			aFields=["dateFrom","dateTo","codigoMetal"];
			ffCallback=getData_Callback;
			blArrayJs=false;
			sendForm("get_data.php", stPOSTString, oForm, aFields, ffCallback, blArrayJs);
		}

		
		function $(id) {
			return document.getElementById(id);
		}

		var gret; var gretText; var gtable;
		var gmetais; var gdolar; var gdates; var gmetais;

		function getCotacaoMetal( metal, dataCotacao, tipo ) {
			//console.log("getCotacaoMetal: start "+metal+" + "+dataCotacao);
			var i=0; var blEnd=false;
			while(!blEnd) {
				if(tipo!=null && tipo=="Last") {
					item=gret.aLastCotacaoMetal[""+i];
				} else {
					item=gret.aCotMetais[""+i];
				}
				if(item && item!=null) {
					if(item.codigoMetal==metal && item.dataCotacao==dataCotacao) {
						return item;
					}
				} else { blEnd=true; }
				i++;
			}
			return null;
		}

		function getMediaMetal( tipo, metal, yearMonth, weekNo ) {
			console.log("getMediaMetal: start "+tipo+" "+metal+" "+yearMonth+" "+weekNo); 
			var i=0; var blEnd=false;
			while(!blEnd) {
				if(tipo=="MES") {
					item=gret.aCotMetaisMediaMes[""+i];
				} else {
					console.log("MediaSem taken");
					item=gret.aCotMetaisMediaSem[""+i];
				}
				if(item && item!=null) {
					if(item.codigoMetal==metal && (tipo=="MES" && yearMonth==parseInt(item.year)*100+parseInt(item.month) || (tipo=="SEMANA" && item.weekNumber==weekNo))) {
						return item;
					}
				} else { blEnd=true; }
				i++;
			}
			return null;
		}

		
		function getCotacaoMoeda( moeda, dataCotacao, tipo ) {
			//console.log("getCotacaoMoeda: start "+moeda+" + "+dataCotacao);
			var i=0; var blEnd=false;
			while(!blEnd) {
				if(tipo!=null && tipo=="Last") {
					item=gret.aLastCotacaoMoeda[""+i];
				} else {
					item=gret.aCotDolar[""+i];
				}
				if(item) {
					if(item.moeda==moeda && item.dataCotacao==dataCotacao) {
						return item;
					}
				} else { blEnd=true; }
				i++;
			}
			return null;
		}

		function getMediaMoeda( tipo, moeda, yearMonth, weekNo ) {
			console.log("getMediaMoeda: start "+tipo+" "+moeda+" "+yearMonth+" "+weekNo); 
			var i=0; var blEnd=false;
			while(!blEnd) {
				if(tipo=="MES") {
					item=gret.aCotDolarMediaMes[""+i];
				} else {
					console.log("MediaSem taken");
					item=gret.aCotDolarMediaSem[""+i];
				}
				if(item && item!=null) {
					if(item.moeda==moeda && (tipo=="MES" && yearMonth==parseInt(item.year)*100+parseInt(item.month) || (tipo=="SEMANA" && item.weekNumber==weekNo))) {
						return item;
					}
				} else { blEnd=true; }
				i++;
			}
			return null;
		}
		
		
		function calculateRows() {
			console.log("calculateRows");
			var i=0; var blEnd=false;

			var prevWeekNo=0; var prevYearMonth=0;
			var currWeekNo=0; var currYearMonth=0;
			var currYear=0; var currMonth=0;
			
			graphData=[]; graphLabels=[];
			var row=null;
			
			var new_tbody = document.createElement('tbody');
			var blEven=true;
			
			while(!blEnd) {
				item=gret.aAllCotacaoDates[""+i];
				if(item && item!=null) {
					dataCotacao=item.dataCotacao;
					currWeekNo=item.weekNumber;
					currYearMonth=parseInt(item.year)*100+parseInt(item.month);
					currYear=item.year; currMonth=item.month;
					console.log("item "+dataCotacao+" curr:"+currWeekNo+" "+currYearMonth);
				} else {
					currWeekNo=0;
					currYearMonth=0;
					currYear=0; currMonth=0;
				}
				if(prevWeekNo!=0 && prevWeekNo!=currWeekNo) {
					console.log("week "+prevWeekNo+" x "+currWeekNo);
					medSemUSD=getMediaMoeda("SEMANA","USD", null, prevWeekNo); if(medSemUSD==null) medSemUSD=0; 
					medSemAL=getMediaMetal("SEMANA", "AL", null, prevWeekNo); if(medSemAL==null) medSemAL=0;
					medSemCU=getMediaMetal("SEMANA", "CU", null, prevWeekNo); if(medSemCU==null) medSemCU=0;
					medSemNI=getMediaMetal("SEMANA", "NI", null, prevWeekNo); if(medSemNI==null) medSemNI=0;
					medSemPB=getMediaMetal("SEMANA", "PB", null, prevWeekNo); if(medSemPB==null) medSemPB=0;
					medSemTN=getMediaMetal("SEMANA", "TN", null, prevWeekNo); if(medSemTN==null) medSemTN=0;
					medSemZI=getMediaMetal("SEMANA", "ZI", null, prevWeekNo); if(medSemZI==null) medSemZI=0;
					
					insertTableRow( new_tbody, "tr-week-avg-line", blEven, null, ["Média Semana", 
						formatNumber(medSemUSD.mediaVenda,5), 
						formatNumber(medSemAL.mediaCotacao,2),
						formatNumber(medSemAL.mediaCotacao * medSemUSD.mediaVenda,2), 
						formatNumber(medSemCU.mediaCotacao,2),
						formatNumber(medSemCU.mediaCotacao * medSemUSD.mediaVenda,2),
						formatNumber(medSemNI.mediaCotacao,2),
						formatNumber(medSemNI.mediaCotacao * medSemUSD.mediaVenda,2),
						formatNumber(medSemPB.mediaCotacao,2),
						formatNumber(medSemPB.mediaCotacao * medSemUSD.mediaVenda,2),
						formatNumber(medSemTN.mediaCotacao,2),
						formatNumber(medSemTN.mediaCotacao * medSemUSD.mediaVenda,2),
						formatNumber(medSemZI.mediaCotacao,2),
						formatNumber(medSemZI.mediaCotacao * medSemUSD.mediaVenda,2)] );
				}
				//item=gret.aAllCotacaoDates[""+i];
				if(prevYearMonth!=0 && prevYearMonth!=currYearMonth) {
					console.log("month "+prevYearMonth+" x "+currYearMonth);
					medMesUSD=getMediaMoeda("MES","USD", prevYearMonth, null); if(medMesUSD==null) medMesUSD=0;
					medMesAL=getMediaMetal("MES", "AL", prevYearMonth, null); if(medMesAL==null) medMesAL=0;
					medMesCU=getMediaMetal("MES", "CU", prevYearMonth, null); if(medMesCU==null) medMesCU=0;
					medMesNI=getMediaMetal("MES", "NI", prevYearMonth, null); if(medMesNI==null) medMesNI=0;
					medMesPB=getMediaMetal("MES", "PB", prevYearMonth, null); if(medMesPB==null) medMesPB=0;
					medMesTN=getMediaMetal("MES", "TN", prevYearMonth, null); if(medMesTN==null) medMesTN=0;
					medMesZI=getMediaMetal("MES", "ZI", prevYearMonth, null); if(medMesZI==null) medMesZI=0;
					
					insertTableRow( new_tbody, "tr-month-avg-line", blEven, null, ["Média Mês",
						formatNumber(medMesUSD.mediaVenda,5), 
						formatNumber(medMesAL.mediaCotacao,2),
						formatNumber(medMesAL.mediaCotacao * medMesUSD.mediaVenda,2), 
						formatNumber(medMesCU.mediaCotacao,2),
						formatNumber(medMesCU.mediaCotacao * medMesUSD.mediaVenda,2),
						formatNumber(medMesNI.mediaCotacao,2),
						formatNumber(medMesNI.mediaCotacao * medMesUSD.mediaVenda,2),
						formatNumber(medMesPB.mediaCotacao,2),
						formatNumber(medMesPB.mediaCotacao * medMesUSD.mediaVenda,2),
						formatNumber(medMesTN.mediaCotacao,2),
						formatNumber(medMesTN.mediaCotacao * medMesUSD.mediaVenda,2),
						formatNumber(medMesZI.mediaCotacao,2),
						formatNumber(medMesZI.mediaCotacao * medMesUSD.mediaVenda,2) ] );
				}
				item=gret.aAllCotacaoDates[""+i];
				if(item && item!=null) {
					//dataCotacao=item.dataCotacao;
					//currWeekNo=item.weekNumber;
					//currYearMonth=item.year*100+item.month;
					dolar=getCotacaoMoeda("USD", dataCotacao);
					console.log(dolar);
					//item=gret.aAllCotacaoDates[""+i];
					mAL=getCotacaoMetal("AL", dataCotacao);
					mCU=getCotacaoMetal("CU", dataCotacao);
					mNI=getCotacaoMetal("NI", dataCotacao);
					mPB=getCotacaoMetal("PB", dataCotacao);
					mTN=getCotacaoMetal("TN", dataCotacao);
					mZI=getCotacaoMetal("ZI", dataCotacao);

					//Graph
					cotacao=null;
					if($("codigoMetal").value=="USD") { cotacao=dolar; iSelected=1; }
					if($("codigoMetal").value=="AL") { cotacao=mAL; iSelected=3; }
					if($("codigoMetal").value=="CU") { cotacao=mCU; iSelected=5; }
					if($("codigoMetal").value=="NI") { cotacao=mNI; iSelected=7; }
					if($("codigoMetal").value=="PB") { cotacao=mPB; iSelected=9; }
					if($("codigoMetal").value=="TN") { cotacao=mTN; iSelected=11; }
					if($("codigoMetal").value=="ZI") { cotacao=mZI; iSelected=13; }

					if($("codigoMetal").value=="USD") {
						if(dolar && dolar!=null) {
							if(cotacao && cotacao!=null ) { 
								graphData[graphData.length]=cotacao.cotacaoVenda;
								graphLabels[graphLabels.length]=cotacao.dataCotacao.substr(8,2);
							} else { console.log("watch for dolar null cotacao"); }
						}
					} else {
						if(cotacao && cotacao!=null) {
							graphData[graphData.length]=cotacao.cotacaoDolar;
							graphLabels[graphLabels.length]=cotacao.dataCotacao.substr(8,2);
						} else { console.log("watch for non dolar null cotacao"); }
					}

					console.log("El "+i);
					insertTableRow( new_tbody, "tr-line", blEven, iSelected, [formatDate(dataCotacao), (dolar?formatNumber(dolar.cotacaoVenda, 5):null), 
						mAL?formatNumber(mAL.cotacaoDolar,2):0, (dolar&&mAL?formatNumber(parseFloat(mAL.cotacaoDolar) * parseFloat(dolar.cotacaoVenda),2):0),
						mCU?formatNumber(mCU.cotacaoDolar,2):0, (dolar&&mCU?formatNumber(mCU.cotacaoDolar * dolar.cotacaoVenda,2):0),
						mNI?formatNumber(mNI.cotacaoDolar,2):0, (dolar&&mNI?formatNumber(mNI.cotacaoDolar * dolar.cotacaoVenda,2):0),
						mPB?formatNumber(mPB.cotacaoDolar,2):0, (dolar&&mPB?formatNumber(mPB.cotacaoDolar * dolar.cotacaoVenda,2):0),
						mTN?formatNumber(mTN.cotacaoDolar,2):0, (dolar&&mTN?formatNumber(mTN.cotacaoDolar * dolar.cotacaoVenda,2):0),
						mZI?formatNumber(mZI.cotacaoDolar,2):0, (dolar&&mZI?formatNumber(mZI.cotacaoDolar * dolar.cotacaoVenda,2):0)
					] );
					
					blEven=!blEven;
					prevWeekNo=currWeekNo;
					prevYearMonth=currYearMonth;
					
				} else { blEnd=true; }
				i++;
			}

			//insertTableRow( new_tbody, "tr-week-avg-line", blEven, iSelected, ["Média Semana",0,0,0,0,0] );
			//insertTableRow( new_tbody, "tr-month-avg-line", blEven, iSelected, ["Média Mês",0,0,0,0,0] );
			
			//Table
			$("tCotacoes_tbody").parentNode.replaceChild(new_tbody, $("tCotacoes_tbody"));
			new_tbody.id="tCotacoes_tbody";

			//Graph
			doGraph(graphData, graphLabels);
		}

		function formatNumber(num3, dec) {
			var val=parseFloat(Math.round(num3 * 10000) / 10000);
			var fmt="-";
			if(isNaN(val)) { } else { fmt=val.toFixed(dec).toString().replace(".",","); }
			return fmt;
		}

		function insertTableRow( new_tbody, rtype, blEven, iSelected, acols ) {
			row=new_tbody.insertRow();
			if(rtype!="tr-line") { row.classList.add(rtype); }
			row.classList.add(rtype); row.classList.add(blEven?"even":"odd");
			for(i=0;i<acols.length;i++) { 
				cell=row.insertCell(); cell.innerText=acols[i];
				if(iSelected!=null && (iSelected==1 && iSelected==i) || (iSelected!=1 && (iSelected==i || iSelected==i+1))) { cell.classList.add("selected"); } 
			}
		}
		
		function getData_Callback(ret, retText) {
			gret=ret;
			gretText=retText;
			
			calculateRows();
			buildLastCotacaoBoard();
		}

		function buildLastCotacaoBoard() {
			var lastDataCotacao=gret.aLastDataCotacao["0"].lastDataCotacao;
			document.getElementsByClassName("last-data-cotacao")[0].innerText=formatDate(lastDataCotacao);
			
			previousUSD=parseFloat(getCotacaoMoeda("USD",gret.aLastDataCotacao["0"].previousDataCotacao,"Last").cotacaoVenda);
			lastUSD=parseFloat(getCotacaoMoeda("USD",gret.aLastDataCotacao["0"].lastDataCotacao,"Last").cotacaoVenda);
			variationUSD=((lastUSD-previousUSD)/previousUSD*100.0);
			
			document.getElementsByClassName("td-usd-last-cotacao")[0].innerText=""+formatNumber(lastUSD,4);
			document.getElementsByClassName("usd-variacao")[0].innerText=""+formatNumber( variationUSD, 2 )+"%";
			if(variationUSD<0.0) { 
				document.getElementsByClassName("usd-variacao")[0].classList.add("negative"); 
				document.getElementsByClassName("usd-up-down")[0].classList.add("down"); 
			} else { 
				document.getElementsByClassName("usd-variacao")[0].classList.add("positive"); 
				document.getElementsByClassName("usd-up-down")[0].classList.add("up"); 
			} 

			metals=["AL","CU","NI","PB","TN","ZI"];
			for(i=0;i<metals.length;i++) {
				cotacaoMetalLast=getCotacaoMetal(metals[i],gret.aLastDataCotacao["0"].lastDataCotacao,"Last");
				cotacaoMetalPrevious=getCotacaoMetal(metals[i],gret.aLastDataCotacao["0"].previousDataCotacao,"Last");
				if(cotacaoMetalLast!=null && cotacaoMetalPrevious!=null) { 
					previousMetal=parseFloat(cotacaoMetalPrevious.cotacaoDolar);
					lastMetal=parseFloat(cotacaoMetalLast.cotacaoDolar);
					variationMetal=((lastMetal-previousMetal)/previousMetal*100.0);
					
					document.getElementsByClassName("td-"+metals[i]+"-last-cotacao")[0].innerText=""+formatNumber(lastMetal,2);
					document.getElementsByClassName(""+metals[i]+"-variacao")[0].innerText=formatNumber(variationMetal,2)+"%";
					if(variationMetal<0.0) { 
						document.getElementsByClassName(""+metals[i]+"-variacao")[0].classList.add("negative"); 
						document.getElementsByClassName(""+metals[i]+"-up-down")[0].classList.add("down"); 
					} else { 
						document.getElementsByClassName(""+metals[i]+"-variacao")[0].classList.add("positive"); 
						document.getElementsByClassName(""+metals[i]+"-up-down")[0].classList.add("up"); 
					}
				}
			}
		
		}

		function formatDate(dtTxt) {
			var de=dtTxt.split("-");
			//var d=new Date(de[2], de[1], de[0]);
			var d=new Date(de[0],parseInt(de[1])-1,de[2]);
			//alert(d);
			day=d.getDate()<10?"0"+d.getDate():d.getDate();
			month=d.getMonth()+1<10?"0"+(d.getMonth()+1):(d.getMonth()+1);
			year=""+d.getFullYear();
			return day+"/"+month+"/"+year; 
		}

		var aNew;
		var yvyPtr=0;
		var lmePtr=0;
		function assemblyTable() {
			var blWeekBrk=false; blMonthBrk=false; blEnd=false;
			var prevWeekNo=0; var prevYearMonth=0;
			aNew=[]; var i=0;
			while(!blWeekBrk && !blMonthBrk && !blEnd) {
				item=gret.aCotMetais[""+i];
				if(item) {
					if(prevWeekNo!=0 && item.weekNumber!=prevWeekNo) { 
						console.log("Week break: "+prevWeekNo+" x "+item.weekNumber);
						weekLines=getWeekBreakLines(prevWeekNo);
						console.log("Length: "+weekLines.length);
						for(j=0;j<weekLines.length;j++) {
							aNew[aNew.length]=weekLines[j];
						}
					}
				} else { blEnd=true; }
				item=gret.aCotMetais[""+i];
				if(item) {
					console.log("Check: "+prevYearMonth+" x "+item);
					if(item && prevYearMonth!=0 && (parseInt(item.year)*100)+(parseInt(item.month))!=prevYearMonth) {
						console.log("Month break: "+((parseInt(item.year)*100)+(parseInt(item.month)))+" x "+prevYearMonth);
					}
					// console.log(item.cotacaoId);
					aNew[aNew.length]=item;
					prevWeekNo=item.weekNumber;
					prevYearMonth=(parseInt(item.year)*100)+(parseInt(item.month));
				} else { blEnd=true; }
				i++;
			}
			//alert(aNew[aNew.length-1]);
			gtable={"aCotacoesMetaisLME": aNew };
		}

		var weekPtr=0;
		function getWeekBreakLines(weekNumber) {
			aLines=[]; 
			while(!blEnd) {
				console.log("aCotMetaisMediaSem["+weekPtr+"]");
				item=gret.aCotMetaisMediaSem[""+weekPtr];
				if(item) {
					if(item.weekNumber==weekNumber) {
						item.dataCotacao="Week "+weekNumber;
						item.cotacaoDolar=item.mediaCotacao;
						aLines[aLines.length]=item;
					} else { weekPtr++; break; console.log("Not same week anymore. weekPtr="+weekPtr); }
				} else { console.log("no item"); blEnd=true; } 
				weekPtr++;
				console.log("New weekPtr="+weekPtr);
			}
			return aLines;			
		}

		function getMonthBreakLines() {
		}

		var gMyChart=null;
		function doGraph(graphData, graphLabels) {
			var ctx = document.getElementById("myChart");
			if(gMyChart!=null) gMyChart.destroy();			
			gMyChart = new Chart(ctx, {
			    type: 'line',
			    data: {
			        labels: /*["Red", "Blue", "Yellow", "Green", "Purple", "Orange"]*/graphLabels,
			        datasets: [{
			            label: '',
			            data: /*[12, 19, 3, 5, 2, 3]*/graphData,
			            backgroundColor: [ /*#559955*/
			                /*'rgba(255, 99, 132, 0.2)'*/'rgba(85, 153, 85, 0.2)',
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(255, 206, 86, 0.2)',
			                'rgba(75, 192, 192, 0.2)',
			                'rgba(153, 102, 255, 0.2)',
			                'rgba(255, 159, 64, 0.2)'
			            ],
			            borderColor: [
			                /*'rgba(255,99,132,1)'*/'rgba(85, 153, 85, 1)',
			                'rgba(54, 162, 235, 1)',
			                'rgba(255, 206, 86, 1)',
			                'rgba(75, 192, 192, 1)',
			                'rgba(153, 102, 255, 1)',
			                'rgba(255, 159, 64, 1)'
			            ],
			            borderWidth: 3,
			            lineTension: 0
			        }]
			    },
			    options: {
			        scales: {
			            yAxes: [{
			                ticks: {
			                    /*beginAtZero:true*/
			                    stepSize: 10
			                }
			            }]
			        },
			        legend: { display: false }
			    }
			});
		}
	</script>
	<script type="text/javascript">
		var gM2Mode="DAY";
	</script>
	<script type="text/javascript" src="./js/bendinfo.ws.js"></script>
	<script type="text/javascript" src="./js/bendinfo.cotacao.Yvy.js"></script>
	<script type="text/javascript" src="./js/Chart.bundle.min.js"></script>
</head>

<body>
	<div class="body-outer">
		<div class="body-inner">
			<style type="text/css">
				.divFilters { width:100%; background-color: #EFEFEF; }
				.negative { color: Red; }
				.positive { color: Blue; }
				.up { content: url('./up.png'); }
				.down { content: url('./down.png'); }
			</style>
			<div class="divFilters">
				<form id="frmFilter" name="frmFilter">
					<div>Selecione o período:</div>
					<input type="hidden" class="codigoMetal" id="codigoMetal" name="codigoMetal" value="AL"/>
					<input type="text" class="dateFrom" placeholder="dd-mm-yyyy" id="dateFrom" name="dateFrom" value="<?=$start?>"/>
					<input type="text" class="dateTo" placeholder="dd-mm-yyyy" id="dateTo" name="dateTo" value="<?=$end?>"/>
					<input type="button" onclick="javascript:getData();" value="Consultar"/>
				</form>
			</div>

			<style type="text/css">
				.divStatus  { float:left; width:250px; padding-right: 15px; }
				#tFechamento{}
				table { border:1px solid black; }
				#tFechamento .tr-fech th { background-color: #666666; color: #FFFFFF; border-right: 1px solid #DDDDDD; }
				#tFechamento .tr-line td.odd { background-color: #EFEFEF; }
				#tFechamento .tr-line td.even { background-color: #EFEFEF; }
			</style>
			<div class="divStatus">
				<div class="div-fech">Posição em <span class="last-data-cotacao"></span> </div>
				<table id="tFechamento" cellpadding="0" cellspacing="0" border="0">
					<thead>
					<tr class="tr-fech">
						<th>Metal</th>
						<th>Valor</th>
						<th>Variação</th>
					</tr>
					</thead>
					<tbody>
					<tr class="tr-line odd">
						<td>Dólar</td>
						<td class="td-usd-last-cotacao"></td>
						<td><img class="usd-up-down"/><span class="usd-variacao"></span></td>
					</tr>
					<tr class="tr-line odd">
						<td>Alumínio</td>
						<td class="td-AL-last-cotacao"></td>
						<td><img class="AL-up-down"/><span class="AL-variacao"></span></td>
					</tr>
					<tr class="tr-line odd">
						<td>Cobre</td>
						<td class="td-CU-last-cotacao"></td>
						<td><img class="CU-up-down"/><span class="CU-variacao"></span></td>
					</tr>
					<tr class="tr-line odd">
						<td>Níquel</td>
						<td class="td-NI-last-cotacao"></td>
						<td><img class="NI-up-down"/><span class="NI-variacao"></span></td>
					</tr>
					<tr class="tr-line odd">
						<td>Chumbo</td>
						<td class="td-PB-last-cotacao"></td>
						<td><img class="PB-up-down"/><span class="PB-variacao"></span></td>
					</tr>
					<tr class="tr-line odd">
						<td>Estanho</td>
						<td class="td-TN-last-cotacao"></td>
						<td><img class="TN-up-down"/><span class="TN-variacao"></span></td>
					</tr>
					<tr class="tr-line odd">
						<td>Zinco</td>
						<td class="td-ZI-last-cotacao"></td>
						<td><img class="ZI-up-down"/><span class="ZI-variacao"></span></td>
					</tr>
					</tbody>
				</table>
			</div>
			
			<style type="text/css">
				.metal{ float:left; padding: 4px; margin-right:7px; background-color: #FFFFFF; cursor:pointer; font-size:10pt; font-weight:bold; }
				.active{ background-color: Green; color: #FFFFFF; }
				.divGraph { width:650px; xheight:200px; border:1px solid #555555; float:left;}
				.graph-header{ background-color: #DFDFDF; height:25px;}
				.graph-body{ padding: 10px; xheight: 300px; }
				.myChart{ width:300px; height:200px; }
				.tr-line.even td.selected{ font-weight: bold; background-color: #CCEECC!important; }
				.tr-line.odd td.selected{ font-weight: bold; background-color: #EEFFEE!important; }
			</style>
			<div class="divGraph">
				<div class="graph-header">
					<div class="metal dolar" id="aUS" datavalue="USD">Dólar</div>
					<div class="metal al active" id="aAL" datavalue="AL">Alumínio</div>
					<div class="metal cu" id="aCU" datavalue="CU">Cobre</div>
					<div class="metal ni" id="aNI" datavalue="NI">Níquel</div>
					<div class="metal pb" id="aPB" datavalue="PB">Chumbo</div>
					<div class="metal tn" id="aTN" datavalue="TN">Estanho</div>
					<div class="metal zi" id="aZI" datavalue="ZI">Zinco</div>
				</div>
				<div class="graph-body">
					<canvas id="myChart" class="myChart" xwidth="400" xheight="200"></canvas>
				</div>
			</div>
		
			<style type="text/css">
				.tr-header, .tr-header th { background-color: #555555; color: #FFFFFF;  padding: 7px; font-size: 9pt; border-right:1px solid #DDDDDD; }
				.tr-line td{ border-right:1px solid #666666; text-align: right; padding-right: 25px!important; }
				.tr-line, .tr-line.even td { background-color: #F5F5F5; color: #000000; padding: 7px;  font-size: 9pt;  }
				.tr-line, .tr-line.odd td { background-color: #FFFFFF; color: #000000;  padding: 7px; font-size: 9pt; }
				.tr-week-avg-line, .tr-week-avg-line td { background-color: #559955; color: #FFFFFF; padding: 7px; font-size: 9pt; } 
				.tr-month-avg-line, .tr-month-avg-line td { background-color: #11BB11; color: #FFFFFF; padding: 7px; font-size: 9pt; }
				#tCotacoes{ width:100%; } 
				.divTable{ width:100%; float:left; }
			</style>
			<div class="divTable">
				<table id="tCotacoes" border="0" cellspacing="0" cellpadding="0">
					<thead>
					<tr class="tr-header">
						<th>Dia</th>
						<th>Dólar</th>
						<th colspan="2">Alumínio</th>
						<th colspan="2">Cobre</th>
						<th colspan="2">Níquel</th>
						<th colspan="2">Chumbo</th>
						<th colspan="2">Estanho</th>
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
					</tbody>
				</table>
			</div>
			
		</div>
	</div>
		
	<script type="text/javascript">
		document.body.onload=body_onload();
	</script>

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
		<div class="data-atual"></div>
		<div class="last-data-cotacao-2"></div>
		<div class="comp-date-from"></div>
		<div class="comp-date-to"></div>
	</div>


</body>
</html>