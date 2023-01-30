// (c) BendInfo Bend Informatica SP Brasil - Amauri Silveira - 2018
var blBuildLastCotacaoBoard=true;
var blDoGraph=true;

function body_onload(blMetalButtons) {
		console_log("body_onload");
			getData();
			if(blMetalButtons) {
				$("aCU").onclick=metal_click; 
				$("aAL").onclick=metal_click; 
				$("aZI").onclick=metal_click; 
				$("aPB").onclick=metal_click; 
				$("aTN").onclick=metal_click; 
				$("aNI").onclick=metal_click; 
				$("aUS").onclick=metal_click;
			}
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

			calculateRows(blDoGraph);
		}

		function getData() {
			console_log("getData");
			//stWebService, stPOSTString, oForm, aFields, ffCallback, blArrayJs
			stPOSTString="";
			oForm=$("frmFilter");
			stPOSTString="";
			aFields=["dateFrom","dateTo","codigoMetal"];
			ffCallback=getData_Callback;
			blArrayJs=false;
			sendForm("/cotacaolme/get_data.php", stPOSTString, oForm, aFields, ffCallback, blArrayJs);
		}

		
		function $(id) {
			return document.getElementById(id);
		}

		var gret; var gretText; var gtable;
		var gmetais; var gdolar; var gdates; var gmetais;

		function getCotacaoMetal( metal, dataCotacao, tipo ) {
			//console_log("getCotacaoMetal: start "+metal+" + "+dataCotacao);
			var i=0; var blEnd=false; var item;
			while(!blEnd) {
				if(tipo!=null && tipo=="Last") {
					item=gret["aLastCotacaoMetal"][""+i];
				} else {
					item=gret["aCotMetais"][""+i];
				}
				if(item && item!=null) {
					if(item["codigoMetal"]==metal && item["dataCotacao"]==dataCotacao) {
						return item;
					}
				} else { blEnd=true; }
				i++;
			}
			return null;
		}

		function getMediaMetal( tipo, metal, yearMonth, weekNo ) {
			//console_log("getMediaMetal: start "+tipo+" "+metal+" "+yearMonth+" "+weekNo); 
			var i=0; var blEnd=false; var item;
			while(!blEnd) {
				if(tipo=="ANO") {
					item=gret["aCotMetaisMediaAno"][""+i];
				} else if(tipo=="MES") {
					item=gret["aCotMetaisMediaMes"][""+i];
				} else {
					//console.log("MediaSem taken");
					item=gret["aCotMetaisMediaSem"][""+i];
				}
				if(item && item!=null) {
					if(item["codigoMetal"]==metal && ((tipo=="ANO" && yearMonth==parseInt(item["year"]))|| tipo=="MES" && yearMonth==parseInt(item["year"])*100+parseInt(item["month"]) || (tipo=="SEMANA" && item["weekNumber"]==weekNo))) {
						return item;
					}
				} else { blEnd=true; }
				i++;
			}
			return null;
		}

		
		function getCotacaoMoeda( moeda, dataCotacao, tipo ) {
			//console.log("getCotacaoMoeda: start "+moeda+" + "+dataCotacao);
			var i=0; var blEnd=false; var item;
			while(!blEnd) {
				if(tipo!=null && tipo=="Last") {
					//ABS 20190225 does not work in IE
					//item=gret.aLastCotacaoMoeda[""+i];
					item=gret["aLastCotacaoMoeda"][""+i];
				} else {
					//ABS 20190225 does not work in IE
					//item=gret.aCotDolar[""+i];
					item=gret["aCotDolar"][""+i];
				}
				if(item) {
					//ABS 20190225 does not work in IE
					//if(item.moeda==moeda && item.dataCotacao==dataCotacao) {
					if(item["moeda"]==moeda && item["dataCotacao"]==dataCotacao) {
						return item;
					}
				} else { blEnd=true; }
				i++;
			}
			return null;
		}

		function getMediaMoeda( tipo, moeda, yearMonth, weekNo ) {
			//console.log("getMediaMoeda: start "+tipo+" "+moeda+" "+yearMonth+" "+weekNo); 
			var i=0; var blEnd=false; var item;
			while(!blEnd) {
				if(tipo=="ANO") {
					item=gret["aCotDolarMediaAno"][""+i];
				} else if(tipo=="MES") {
					//ABS 20190225 does not work in IE
					//item=gret.aCotDolarMediaMes[""+i];
					item=gret["aCotDolarMediaMes"][""+i];
				} else {
					//console.log("MediaSem taken");
					//ABS 20190225 does not work in IE
					//item=gret.aCotDolarMediaSem[""+i];
					item=gret["aCotDolarMediaSem"][""+i];
				}
				if(item && item!=null) {
					if(item["moeda"]==moeda && ((tipo=="ANO" && yearMonth==parseInt(item["year"])) || tipo=="MES" && yearMonth==parseInt(item["year"])*100+parseInt(item["month"]) || (tipo=="SEMANA" && item["weekNumber"]==weekNo))) {
						return item;
					}
				} else { blEnd=true; }
				i++;
			}
			return null;
		}
		
		
		function calculateRows(blDoGraph) {
			console_log("calculateRows");
			//console.log("calculateRows");
			var i=0; var blEnd=false; var item;

			var item;
			var prevWeekNo=0; var prevYearMonth=0; var prevYear=0;
			var currWeekNo=0; var currYearMonth=0;
			var currYear=0; var currMonth=0;
			
			graphData=[]; graphLabels=[];
			var row=null;
			
			var new_tbody = document.createElement('tbody');
			var blEven=true;
			
			var medSemUSD; var medSemAL; var medSemCU; var medSemNI; var medSemPB; var medSemTN; var medSemZI;
			var medMesUSD; var medMesAL; var medMesCU; var medMesNI; var medMesPB; var medMesTN; var medMesZI;
			var medAnoUSD; var medAnoAL; var medAnoCU; var medAnoNI; var medAnoPB; var medAnoTN; var medAnoZI;
			var dolar; var mAL; var mCU; var mNI; var mPB; var mTN; var mZI;
			//return;
			while(!blEnd) {
				console_log("while "+i);
				item=gret["aAllCotacaoDates"][""+i];
				console_log(item);
				if(item && item!=null) {
					console_log("item.dataCotacao "+item["dataCotacao"]);
					dataCotacao=item["dataCotacao"];
					currWeekNo=item["weekNumber"];
					currYearMonth=parseInt(item["year"])*100+parseInt(item["month"]);
					currYear=item["year"]; currMonth=item["month"];
					console_log("item "+dataCotacao+" curr:"+currWeekNo+" "+currYearMonth);
				} else {
					currWeekNo=0;
					currYearMonth=0;
					currYear=0; currMonth=0;
				}

				if(prevWeekNo!=0 && prevWeekNo!=currWeekNo) {
					if(gM2Mode=="DAY" || gM2Mode=="WEEK") {
						//console.log("week "+prevWeekNo+" x "+currWeekNo);
						medSemUSD=getMediaMoeda("SEMANA","USD", null, prevWeekNo); if(medSemUSD==null) medSemUSD=0; 
						medSemAL=getMediaMetal("SEMANA", "AL", null, prevWeekNo); if(medSemAL==null) medSemAL=0;
						medSemCU=getMediaMetal("SEMANA", "CU", null, prevWeekNo); if(medSemCU==null) medSemCU=0;
						medSemNI=getMediaMetal("SEMANA", "NI", null, prevWeekNo); if(medSemNI==null) medSemNI=0;
						medSemPB=getMediaMetal("SEMANA", "PB", null, prevWeekNo); if(medSemPB==null) medSemPB=0;
						medSemTN=getMediaMetal("SEMANA", "TN", null, prevWeekNo); if(medSemTN==null) medSemTN=0;
						medSemZI=getMediaMetal("SEMANA", "ZI", null, prevWeekNo); if(medSemZI==null) medSemZI=0;
						
						console.log("med "+medSemUSD);
						console.log(medSemUSD);
					
						insertTableRow( new_tbody, "tr-week-avg-line", blEven, null, ["M.S. ("+prevWeekNo+")", 
							formatNumber(medSemUSD["mediaVenda"],4), 
							formatNumber(medSemAL["mediaCotacao"],2),
							formatNumber(medSemAL["mediaCotacao"] * medSemUSD["mediaVenda"],2), 
							formatNumber(medSemPB["mediaCotacao"],2),
							formatNumber(medSemPB["mediaCotacao"] * medSemUSD["mediaVenda"],2),
							formatNumber(medSemCU["mediaCotacao"],2),
							formatNumber(medSemCU["mediaCotacao"] * medSemUSD["mediaVenda"],2),
							formatNumber(medSemTN["mediaCotacao"],2),
							formatNumber(medSemTN["mediaCotacao"] * medSemUSD["mediaVenda"],2),
							formatNumber(medSemNI["mediaCotacao"],2),
							formatNumber(medSemNI["mediaCotacao"] * medSemUSD["mediaVenda"],2),
							formatNumber(medSemZI["mediaCotacao"],2),
							formatNumber(medSemZI["mediaCotacao"] * medSemUSD["mediaVenda"],2)] );
					}
					console_log("DAY inserted row");

				}
				//item=gret.aAllCotacaoDates[""+i];
				if(prevYearMonth!=0 && prevYearMonth!=currYearMonth) {
					if(gM2Mode!="YEAR") {
						console_log("month "+prevYearMonth+" x "+currYearMonth);
						medMesUSD=getMediaMoeda("MES","USD", prevYearMonth, null); if(medMesUSD==null) medMesUSD=0;
						medMesAL=getMediaMetal("MES", "AL", prevYearMonth, null); if(medMesAL==null) medMesAL=0;
						medMesCU=getMediaMetal("MES", "CU", prevYearMonth, null); if(medMesCU==null) medMesCU=0;
						medMesNI=getMediaMetal("MES", "NI", prevYearMonth, null); if(medMesNI==null) medMesNI=0;
						medMesPB=getMediaMetal("MES", "PB", prevYearMonth, null); if(medMesPB==null) medMesPB=0;
						medMesTN=getMediaMetal("MES", "TN", prevYearMonth, null); if(medMesTN==null) medMesTN=0;
						medMesZI=getMediaMetal("MES", "ZI", prevYearMonth, null); if(medMesZI==null) medMesZI=0;
						
						insertTableRow( new_tbody, "tr-month-avg-line", blEven, null, ["M.M. ("+(""+prevYearMonth).substr(4,2)+"/"+(""+prevYearMonth).substr(2,2)+")",
							formatNumber(medMesUSD["mediaVenda"],4), 
							formatNumber(medMesAL["mediaCotacao"],2),
							formatNumber(medMesAL["mediaCotacao"] * medMesUSD["mediaVenda"],2), 
							formatNumber(medMesPB["mediaCotacao"],2),
							formatNumber(medMesPB["mediaCotacao"] * medMesUSD["mediaVenda"],2),
							formatNumber(medMesCU["mediaCotacao"],2),
							formatNumber(medMesCU["mediaCotacao"] * medMesUSD["mediaVenda"],2),
							formatNumber(medMesTN["mediaCotacao"],2),
							formatNumber(medMesTN["mediaCotacao"] * medMesUSD["mediaVenda"],2),
							formatNumber(medMesNI["mediaCotacao"],2),
							formatNumber(medMesNI["mediaCotacao"] * medMesUSD["mediaVenda"],2),
							formatNumber(medMesZI["mediaCotacao"],2),
							formatNumber(medMesZI["mediaCotacao"] * medMesUSD["mediaVenda"],2) ] );
						console_log("ABS inserted row month");
					}
				}
				if(prevYear!=0 && prevYear!=currYear) {
					console_log("year "+prevYear+" x "+currYear);
					medAnoUSD=getMediaMoeda("ANO","USD", prevYear, null); if(medAnoUSD==null) medAnoUSD=0;
					medAnoAL=getMediaMetal("ANO", "AL", prevYear, null); if(medAnoAL==null) medAnoAL=0;
					medAnoCU=getMediaMetal("ANO", "CU", prevYear, null); if(medAnoCU==null) medAnoCU=0;
					medAnoNI=getMediaMetal("ANO", "NI", prevYear, null); if(medAnoNI==null) medAnoNI=0;
					medAnoPB=getMediaMetal("ANO", "PB", prevYear, null); if(medAnoPB==null) medAnoPB=0;
					medAnoTN=getMediaMetal("ANO", "TN", prevYear, null); if(medAnoTN==null) medAnoTN=0;
					medAnoZI=getMediaMetal("ANO", "ZI", prevYear, null); if(medAnoZI==null) medAnoZI=0;
					
					insertTableRow( new_tbody, "tr-month-avg-line", blEven, null, ["M.A. ("+(""+prevYear)+")",
						formatNumber(medAnoUSD["mediaVenda"],4), 
						formatNumber(medAnoAL["mediaCotacao"],2),
						formatNumber(medAnoAL["mediaCotacao"] * medAnoUSD["mediaVenda"],2), 
						formatNumber(medAnoPB["mediaCotacao"],2),
						formatNumber(medAnoPB["mediaCotacao"] * medAnoUSD["mediaVenda"],2),
						formatNumber(medAnoCU["mediaCotacao"],2),
						formatNumber(medAnoCU["mediaCotacao"] * medAnoUSD["mediaVenda"],2),
						formatNumber(medAnoTN["mediaCotacao"],2),
						formatNumber(medAnoTN["mediaCotacao"] * medAnoUSD["mediaVenda"],2),
						formatNumber(medAnoNI["mediaCotacao"],2),
						formatNumber(medAnoNI["mediaCotacao"] * medAnoUSD["mediaVenda"],2),
						formatNumber(medAnoZI["mediaCotacao"],2),
						formatNumber(medAnoZI["mediaCotacao"] * medAnoUSD["mediaVenda"],2) ] );
					console_log("ABS inserted row month");
				}
				item=gret["aAllCotacaoDates"][""+i];
				if(item && item!=null) {
					//dataCotacao=item.dataCotacao;
					//currWeekNo=item.weekNumber;
					//currYearMonth=item.year*100+item.month;
					dolar=getCotacaoMoeda("USD", dataCotacao);
					//console.log(dolar);
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
								graphData[graphData.length]=cotacao["cotacaoVenda"];
								graphLabels[graphLabels.length]=cotacao["dataCotacao"].substr(8,2)+"/"+cotacao["dataCotacao"].substr(5,2);
							} else { /*console.log("watch for dolar null cotacao");*/ }
						}
					} else {
						if(cotacao && cotacao!=null) {
							graphData[graphData.length]=cotacao["cotacaoDolar"];
							graphLabels[graphLabels.length]=cotacao["dataCotacao"].substr(8,2)+"/"+cotacao["dataCotacao"].substr(5,2);
						} else { /*console.log("watch for non dolar null cotacao");*/ }
					}

					//console.log("El "+i);
					if(gM2Mode=="DAY") {
						insertTableRow( new_tbody, "tr-line", blEven, iSelected, [formatDate(dataCotacao), (dolar?formatNumber(dolar["cotacaoVenda"], 4):null), 
							mAL?formatNumber(mAL["cotacaoDolar"],2):0, (dolar&&mAL?formatNumber(parseFloat(mAL["cotacaoDolar"]) * parseFloat(dolar["cotacaoVenda"]),2):0),
							mPB?formatNumber(mPB["cotacaoDolar"],2):0, (dolar&&mPB?formatNumber(mPB["cotacaoDolar"] * dolar["cotacaoVenda"],2):0),
							mCU?formatNumber(mCU["cotacaoDolar"],2):0, (dolar&&mCU?formatNumber(mCU["cotacaoDolar"] * dolar["cotacaoVenda"],2):0),
							mTN?formatNumber(mTN["cotacaoDolar"],2):0, (dolar&&mTN?formatNumber(mTN["cotacaoDolar"] * dolar["cotacaoVenda"],2):0),
							mNI?formatNumber(mNI["cotacaoDolar"],2):0, (dolar&&mNI?formatNumber(mNI["cotacaoDolar"] * dolar["cotacaoVenda"],2):0),
							mZI?formatNumber(mZI["cotacaoDolar"],2):0, (dolar&&mZI?formatNumber(mZI["cotacaoDolar"] * dolar["cotacaoVenda"],2):0)
						] );
					}
					
					blEven=!blEven;
					prevWeekNo=currWeekNo;
					prevYearMonth=currYearMonth;
					prevYear=currYear;
					
				} else { blEnd=true; }
				i++;
				//if(i>100) blEnd=true; //TODO: resolver!
			}

			//insertTableRow( new_tbody, "tr-week-avg-line", blEven, iSelected, ["Média Semana",0,0,0,0,0] );
			//insertTableRow( new_tbody, "tr-month-avg-line", blEven, iSelected, ["Média Mês",0,0,0,0,0] );
			
			//Table
			$("tCotacoes_tbody").parentNode.replaceChild(new_tbody, $("tCotacoes_tbody"));
			new_tbody.id="tCotacoes_tbody";

			//Graph
			if(blDoGraph) {
				doGraph(graphData, graphLabels);
			}
		}

		function formatNumber(num3, dec) {
			//var previous=parseFloat(Math.round(num3 * 10000) / 10000).toFixed(dec).toString().replace(".",",");
			var fmtd="";
			var val=parseFloat(Math.round(num3 * 10000) / 10000);
			if(isNaN(val) || val==0.0) return "-";
			if(dec==2) {
				fmtd=val.toLocaleString(['pt','en'],{minimumFractionDigits    : 2, maximumFractionDigits    : 2});
			} else {
				fmtd=val.toFixed(dec).toString().replace(".",",");
			}
			return fmtd;
		}

		function insertTableRow( new_tbody, rtype, blEven, iSelected, acols ) {
			var bl3=true;
			row=new_tbody.insertRow(new_tbody.rows.length);
			if(rtype!="tr-line") { row.classList.add(rtype); }
			row.classList.add(rtype); row.classList.add(blEven?"even":"odd");
			row.classList.add(blEven?"m2-treven":"m2-trodd");
			for(i=0;i<acols.length;i++) { 
				var firstCell=null;
				cell=row.insertCell(i); cell.innerText=acols[i];
				if(i==0) {
					cell.classList.add("m2-td1");
					if(acols[i]==(new Date()).toLocaleDateString("pt")) {
						cell.innerText="Hoje";
						row.classList.add("m2-today");
					}
				} else if(i==1) {
					firstCell=cell;
					cell.classList.add("m2-td2");
					if(cell.innerText=="" || cell.innerText=="0,00000"  || cell.innerText=="null") {
						row.classList.add("m2-feriado");
						cell.innerText="Feriado";
					}
				} else {
					if(bl3) {
						cell.classList.add("m2-td3");
						//console.log("cell.innerText "+i+" "+cell.innerText);
						if(cell.innerText=="0" || cell.innerText=="0,00") { cell.innerText="-"; if(firstCell) firstCell.innerText="Feriado"; else cell.innerText="Feriado"; }
						bl3=false;
					} else {
						if(i>12) {
							cell.classList.add("m2-td4f");
							//console.log("cell.innerText "+i+" "+cell.innerText);
							if(cell.innerText=="0" || cell.innerText=="0,00") { cell.innerText="Feriado"; if(firstCell) firstCell.innerText="Feriado"; }
						} else {
							cell.classList.add("m2-td4");
							//console.log("cell.innerText "+i+" "+cell.innerText);
							if(cell.innerText=="0" || cell.innerText=="0,00") { cell.innerText="Feriado"; if(firstCell) firstCell.innerText="Feriado"; }
						}
						bl3=true;
					}
				}
				
				if(iSelected!=null && (iSelected==1 && iSelected==i) || (iSelected!=1 && (iSelected==i || iSelected==i+1))) { cell.classList.add("selected"); } 
			}
		}
		
		var compCalendarSet=false;
		
		var blUpdateAppLME=false;
		
		var blCalculateRows=true;
		function getData_Callback(ret, retText) {
			console_log("getData_Callback");

			gret=ret;
			gretText=retText;
			
			if(blCalculateRows) calculateRows(blDoGraph);
			if(blBuildLastCotacaoBoard) {
				buildLastCotacaoBoard();
			}
			
			if(blBuildLastCotacaoBoard) {
				if(!compCalendarSet) {
					 try {
					  const myPicker2 = new Lightpick({
					        field: document.getElementById('dateCompFrom'),
					        secondField: document.getElementById('dateCompTo'),
					    	repick: true,
					    	startDate: backToIsoDate(document.getElementById('dateCompFrom').value),
					    	endDate: backToIsoDate(document.getElementById('dateCompTo').value),
					    	firstDay: 0 /*Sunday*/,
					    	lang: 'pt'
					  });
					 } catch(e) {
						 console.log(e);
					 }
					  compCalendarSet=true;
				}
			}
			if(blUpdateAppLME) {
				updateAppLME();
			}
			
			//After 1st time do not touch LastCotacoes board
			blBuildLastCotacaoBoard=false;

		}

		function buildLastCotacaoBoard() {
			console_log("buildLastCotacaoBoard");
			console_log("bl1");
			var lastDataCotacao=gret["aLastDataCotacao"]["0"]["lastDataCotacao"];
			console_log(lastDataCotacao);
			var previousDataCotacao=gret["aLastDataCotacao"]["0"]["previousDataCotacao"];
			document.getElementsByClassName("last-data-cotacao")[0].innerText=formatDate(lastDataCotacao);
			document.getElementsByClassName("last-data-cotacao-2")[0].innerText=formatDate(lastDataCotacao);
			var now=new Date();
			document.getElementsByClassName("data-atual")[0].innerText=formatDate(""+now.getFullYear()+"-"+(now.getMonth()+1)+"-"+now.getDate());
			// ABS20181206 Comparativo
			document.getElementsByClassName("comp-date-from")[0].value=formatDate(previousDataCotacao);
			document.getElementsByClassName("comp-date-to")[0].value=formatDate(lastDataCotacao);
			
			console_log("bl2");
			
			previousUSD=0; lastUSD=0; variationUSD=0;
			//console_log("bl2.1 "+gret);
			//ABS 20190225 old version (does not work in IE)
			/*
			if(getCotacaoMoeda("USD",gret.aLastDataCotacao["0"].previousDataCotacao,"Last") && getCotacaoMoeda("USD",gret.aLastDataCotacao["0"].lastDataCotacao,"Last")) {
				previousUSD=parseFloat(getCotacaoMoeda("USD",gret.aLastDataCotacao["0"].previousDataCotacao,"Last").cotacaoVenda);
				lastUSD=parseFloat(getCotacaoMoeda("USD",gret.aLastDataCotacao["0"].lastDataCotacao,"Last").cotacaoVenda);
				variationUSD=((lastUSD-previousUSD)/previousUSD*100.0);
			}
			*/
			console_log(getCotacaoMoeda("USD",gret["aLastDataCotacao"]["0"]["previousDataCotacao"],"Last"));
			//console_log(getCotacaoMoeda("USD",gret["aLastDataCotacao"]["0"]["previousDataCotacao"],"Last")["cotacaoVenda"]);
			if(getCotacaoMoeda("USD",gret["aLastDataCotacao"]["0"]["previousDataCotacao"],"Last") && getCotacaoMoeda("USD",gret["aLastDataCotacao"]["0"]["lastDataCotacao"],"Last")) {
				previousUSD=parseFloat(getCotacaoMoeda("USD",gret["aLastDataCotacao"]["0"]["previousDataCotacao"],"Last")["cotacaoVenda"]);
				lastUSD=parseFloat(getCotacaoMoeda("USD",gret["aLastDataCotacao"]["0"]["lastDataCotacao"],"Last")["cotacaoVenda"]);
				variationUSD=((lastUSD-previousUSD)/previousUSD*100.0);
			}
			console_log("bl3 "+previousUSD+" "+lastUSD+" "+variationUSD);
			
			document.getElementsByClassName("td-usd-last-cotacao")[0].innerText=""+formatNumber(lastUSD,4);
			document.getElementsByClassName("usd-variacao")[0].innerText=""+formatNumber( variationUSD, 2 )+"%";
			document.getElementsByClassName("usd-up-down")[0].classList.remove("down"); 
			document.getElementsByClassName("usd-variacao")[0].classList.remove("negative"); 
			document.getElementsByClassName("usd-up-down")[0].classList.remove("up"); 
			document.getElementsByClassName("usd-variacao")[0].classList.remove("positive"); 
			if(variationUSD<0.0) { 
				document.getElementsByClassName("usd-variacao")[0].classList.add("negative"); 
				document.getElementsByClassName("usd-up-down")[0].classList.add("down"); 
				document.getElementsByClassName("usd-up-down")[0].src="/cotacaolme/down.png";
			} else { 
				document.getElementsByClassName("usd-variacao")[0].classList.add("positive"); 
				document.getElementsByClassName("usd-up-down")[0].classList.add("up"); 
				document.getElementsByClassName("usd-up-down")[0].src="/cotacaolme/up.png";
			} 
			console_log("bl4");

			var cotacaoMetalLast; var cotacaoMetalPrevious;
			metals=["AL","CU","NI","PB","TN","ZI"];
			for(i=0;i<metals.length;i++) {
				cotacaoMetalLast=getCotacaoMetal(metals[i],gret["aLastDataCotacao"]["0"]["lastDataCotacao"],"Last");
				cotacaoMetalPrevious=getCotacaoMetal(metals[i],gret["aLastDataCotacao"]["0"]["previousDataCotacao"],"Last");
				if(cotacaoMetalLast!=null && cotacaoMetalPrevious!=null) { 
					previousMetal=parseFloat(cotacaoMetalPrevious["cotacaoDolar"]);
					lastMetal=parseFloat(cotacaoMetalLast["cotacaoDolar"]);
					variationMetal=((lastMetal-previousMetal)/previousMetal*100.0);
					console_log("bl5 "+previousMetal+ " "+lastMetal+" "+variationMetal);
					
					document.getElementsByClassName("td-"+metals[i]+"-last-cotacao")[0].innerText=""+formatNumber(lastMetal,2);
					document.getElementsByClassName(""+metals[i]+"-variacao")[0].innerText=formatNumber(variationMetal,2)+"%";

					document.getElementsByClassName(""+metals[i]+"-up-down")[0].classList.remove("down");
					document.getElementsByClassName(""+metals[i]+"-up-down")[0].classList.remove("up");
					document.getElementsByClassName(""+metals[i]+"-variacao")[0].classList.remove("negative"); 
					document.getElementsByClassName(""+metals[i]+"-variacao")[0].classList.remove("positive"); 
					if(variationMetal<0.0) { 
						document.getElementsByClassName(""+metals[i]+"-variacao")[0].classList.add("negative"); 
						document.getElementsByClassName(""+metals[i]+"-up-down")[0].classList.add("down");
						document.getElementsByClassName(""+metals[i]+"-up-down")[0].src="/cotacaolme/down.png";
					} else { 
						document.getElementsByClassName(""+metals[i]+"-variacao")[0].classList.add("positive"); 
						document.getElementsByClassName(""+metals[i]+"-up-down")[0].classList.add("up"); 
						document.getElementsByClassName(""+metals[i]+"-up-down")[0].src="/cotacaolme/up.png";
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
						//console.log("Week break: "+prevWeekNo+" x "+item.weekNumber);
						weekLines=getWeekBreakLines(prevWeekNo);
						//console.log("Length: "+weekLines.length);
						for(j=0;j<weekLines.length;j++) {
							aNew[aNew.length]=weekLines[j];
						}
					}
				} else { blEnd=true; }
				item=gret.aCotMetais[""+i];
				if(item) {
					//console.log("Check: "+prevYearMonth+" x "+item);
					if(item && prevYearMonth!=0 && (parseInt(item.year)*100)+(parseInt(item.month))!=prevYearMonth) {
						//console.log("Month break: "+((parseInt(item.year)*100)+(parseInt(item.month)))+" x "+prevYearMonth);
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
				//console.log("aCotMetaisMediaSem["+weekPtr+"]");
				item=gret.aCotMetaisMediaSem[""+weekPtr];
				if(item) {
					if(item.weekNumber==weekNumber) {
						item.dataCotacao="Week "+weekNumber;
						item.cotacaoDolar=item.mediaCotacao;
						aLines[aLines.length]=item;
					} else { weekPtr++; break; /*console.log("Not same week anymore. weekPtr="+weekPtr);*/ }
				} else { /*console.log("no item");*/ blEnd=true; } 
				weekPtr++;
				//console.log("New weekPtr="+weekPtr);
			}
			return aLines;			
		}

		function getMonthBreakLines() {
		}

		var gMyChart=null;
		function doGraph(graphData, graphLabels) {
			var ctx = document.getElementById("myChart");
			if(gMyChart!=null) gMyChart.destroy();	
			min=Math.min.apply(null,graphData);
			max=Math.max.apply(null,graphData);
			if(min>10.0) {
				step=Math.round((max-min*10.0))/100.0;
			} else {
				step=Math.round((max-min))/10.0;
			}
			gMyChart = new Chart(ctx, {
			    type: 'line',
			    data: {
			        labels: /*["Red", "Blue", "Yellow", "Green", "Purple", "Orange"]*/graphLabels,
			        datasets: [{
			            label: '',
			            data: /*[12, 19, 3, 5, 2, 3]*/graphData,
			            backgroundColor: /*#559955*/
			                /*'rgba(255, 99, 132, 0.2)'*/'rgba(85, 153, 85, 0.2)'/*,
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(255, 206, 86, 0.2)',
			                'rgba(75, 192, 192, 0.2)',
			                'rgba(153, 102, 255, 0.2)',
			                'rgba(255, 159, 64, 0.2)'*/
			            ,
			            borderColor: 
			                /*'rgba(255,99,132,1)'*/'rgba(85, 153, 85, 1)'/*,
			                'rgba(54, 162, 235, 1)',
			                'rgba(255, 206, 86, 1)',
			                'rgba(75, 192, 192, 1)',
			                'rgba(153, 102, 255, 1)',
			                'rgba(255, 159, 64, 1)'*/
			            ,
			            borderWidth: 1, /*3*/
			            lineTension: 0,
			            pointRadius: 1, /*2*/
			            fill: true
			        }]
			    },
			    options: {
			        scales: {
			            yAxes: [{
			                ticks: {
			                    /*beginAtZero:true*/
			                    stepSize: step /*,
			                    maxTicksLimit: 10*/
			                }
			            }]
			        },
			        legend: { display: false },
			        animation: { duration: 0 }
			    }
			});
		}

			
		//ABS20181206 Comparativo
		function getComparativo() {
			//stWebService, stPOSTString, oForm, aFields, ffCallback, blArrayJs
			stPOSTString="";
			oForm=$("frmComp");
			stPOSTString="";
			aFields=["dateCompFrom","dateCompTo"];
			ffCallback=getComparativo_Callback;
			blArrayJs=false;
			sendForm("/cotacaolme/get_comparativo.php", stPOSTString, oForm, aFields, ffCallback, blArrayJs);
		}

		var gcomp=null;
		var gcompText=null;
		function getComparativo_Callback(ret, retText) {
			gcomp=ret;
			gcompText=retText;
			
			gret.aLastDataCotacao=gcomp.aLastDataCotacao;
			gret.aLastCotacaoMoeda=gcomp.aLastCotacaoMoeda;
			gret.aLastCotacaoMetal=gcomp.aLastCotacaoMetal;
			
			var lastDataCotacao=gret.aLastDataCotacao["0"].lastDataCotacao;
			var previousDataCotacao=gret.aLastDataCotacao["0"].previousDataCotacao;

			if(lastDataCotacao=="" || lastDataCotacao==null || previousDataCotacao=="" || previousDataCotacao==null) {
				alert("ATENÇÃO! Para o comparativo, selecione duas datas que possuam cotações.");
				return;
			}
			//calculateRows(blDoGraph);
			//if(blBuildLastCotacaoBoard) {
				buildLastCotacaoBoard();
			//}
			
		}
		
		
		function backToIsoDate(stDate) {
			var dp=stDate.split('/');
			return ""+dp[2]+"-"+dp[1]+"-"+dp[0];
		} 

		function console_log(text) {
			if(document.getElementById("taLog")) {
				document.getElementById("taLog").value+=""+(new Date()).getMinutes()+"."+(new Date()).getMilliseconds()+" : "+text+"\r\n";
			}
		}
		