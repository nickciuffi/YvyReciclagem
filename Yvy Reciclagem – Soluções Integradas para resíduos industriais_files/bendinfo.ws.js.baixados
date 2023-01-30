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
                //ABS commenting userInfo2 = JSON.parse(XMLParse(xhr.responseText));
                userInfo2 = JSON.parse(xhr.responseText);
            } else {
                userInfo2 = eval( XMLParse(xhr.responseText) );
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