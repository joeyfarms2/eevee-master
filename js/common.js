// JavaScript Document
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

/************ Start : Control Display ************/
function showObj(obj_name){
	$("#"+obj_name).removeClass("hidden");
}

function hideObj(obj_name){
	$("#"+obj_name).addClass("hidden");
}

function visibleObj(obj_name){
	$("#"+obj_name).css('visibility', 'visible');
}

function unvisibleObj(obj_name){
	$("#"+obj_name).css('visibility', 'hidden');
}

/************ End : Control Display ************/

/************ Start : Common fuctions ************/
function trimString(sInString) {
	sInString = sInString.replace(/^\s+/, '');
	for (var i = sInString.length - 1; i >= 0; i--) {
		if (/\S/.test(sInString.charAt(i))) {
			sInString = sInString.substring(0, i + 1);
			break;
		}
	}
	return sInString;
  // sInString = sInString.replace( /^\s+/g, "" );// strip leading
  // return sInString.replace( /\s+$/g, "" );// strip trailing
}

function padString(str, len, pad, dir){
	if (typeof(len) == "undefined") { var len = 0; }
	if (typeof(pad) == "undefined") { var pad = '0'; }
	if (typeof(dir) == "undefined") { var dir = "left"; }

	// alert(pad);
	str = str.toString();

	if (len + 1 >= str.length) {

		switch (dir){
			case 'left':
				str = Array(len + 1 - str.length).join(pad) + str;
				break;

			case 'both':
				var right = Math.ceil((padlen = len - str.length) / 2);
				var left = padlen - right;
				str = Array(left+1).join(pad) + str + Array(right+1).join(pad);
				break;

			default:
				str = str + Array(len + 1 - str.length).join(pad);
				break;
		} // switch

	}
	return str;
}

function processRedirect(url, target){
	if(url.indexOf('http') != 0 && url.indexOf('ftp') != 0){
		url = base_url+url;
	}
	if(target){
		window.open(url, target);
	}else{
		window.location = url;
	}
}

function getFullUrl(url){
	if(url.indexOf('http') != 0 && url.indexOf('ftp') != 0){
		url = base_url+url;
	}
	return url;	
}

function clearValue(obj_name){
	$("#"+obj_name).val("");
}

function clearThenHide(obj_name){
	clearValue(obj_name);
	hideObj(obj_name);
}

function convert_to_array(item_name){
	var result = new Array();
	jQuery("input[name="+item_name+"]").each(function()
	{
		if(this.checked){
			result.push(this.value);
		}	
	});
	return result;
}

function replacenl2br(str, is_xhtml) {
	var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
	str = (str + '').replace(/\r/g, '');
	return (str + '').replace(/\n/g, breakTag);
}

function nl2br (str, is_xhtml) {
	// http://kevin.vanzonneveld.net
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: Philip Peterson
	// +   improved by: Onno Marsman
	// +   improved by: Atli ??r
	// +   bugfixed by: Onno Marsman
	// +      input by: Brett Zamir (http://brett-zamir.me)
	// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: Brett Zamir (http://brett-zamir.me)
	// +   improved by: Maximusya
	// *     example 1: nl2br('Kevin\nvan\nZonneveld');
	// *     returns 1: 'Kevin<br />\nvan<br />\nZonneveld'
	// *     example 2: nl2br("\nOne\nTwo\n\nThree\n", false);
	// *     returns 2: '<br>\nOne<br>\nTwo<br>\n<br>\nThree<br>\n'
	// *     example 3: nl2br("\nOne\nTwo\n\nThree\n", true);
	// *     returns 3: '<br />\nOne<br />\nTwo<br />\n<br />\nThree<br />\n'

	var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';

	return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}

function htmlspecialchars (string, quote_style, charset, double_encode) {
	// http://kevin.vanzonneveld.net
	// +   original by: Mirek Slugen
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   bugfixed by: Nathan
	// +   bugfixed by: Arno
	// +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +    bugfixed by: Brett Zamir (http://brett-zamir.me)
	// +      input by: Ratheous
	// +      input by: Mailfaker (http://www.weedem.fr/)
	// +      reimplemented by: Brett Zamir (http://brett-zamir.me)
	// +      input by: felix
	// +    bugfixed by: Brett Zamir (http://brett-zamir.me)
	// %        note 1: charset argument not supported
	// *     example 1: htmlspecialchars("<a href='test'>Test</a>", 'ENT_QUOTES');
	// *     returns 1: '&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;'
	// *     example 2: htmlspecialchars("ab\"c'd", ['ENT_NOQUOTES', 'ENT_QUOTES']);
	// *     returns 2: 'ab"c&#039;d'
	// *     example 3: htmlspecialchars("my "&entity;" is still here", null, null, false);
	// *     returns 3: 'my &quot;&entity;&quot; is still here'

	var optTemp = 0, i = 0, noquotes= false;
	if (typeof quote_style === 'undefined' || quote_style === null) {
		quote_style = 2;
	}
	string = string.toString();
	if (double_encode !== false) { // Put this first to avoid double-encoding
		string = string.replace(/&/g, '&amp;');
	}
	string = string.replace(/</g, '&lt;').replace(/>/g, '&gt;');

	var OPTS = {
		'ENT_NOQUOTES': 0,
		'ENT_HTML_QUOTE_SINGLE' : 1,
		'ENT_HTML_QUOTE_DOUBLE' : 2,
		'ENT_COMPAT': 2,
		'ENT_QUOTES': 3,
		'ENT_IGNORE' : 4
	};
	if (quote_style === 0) {
		noquotes = true;
	}
	if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
		quote_style = [].concat(quote_style);
		for (i=0; i < quote_style.length; i++) {
			// Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
			if (OPTS[quote_style[i]] === 0) {
				noquotes = true;
			}
			else if (OPTS[quote_style[i]]) {
				optTemp = optTemp | OPTS[quote_style[i]];
			}
		}
		quote_style = optTemp;
	}
	if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
		string = string.replace(/'/g, '&#039;');
	}
	if (!noquotes) {
		string = string.replace(/"/g, '&quot;');
	}

	return string;
}

/************ End : Common fuctions ************/

/************ Start : Process fuctions ************/
function processSubmit(form_name){
	// alert(form_name);
	$("#"+form_name).validate();
	if ($("#"+form_name).valid())
	{
		$("#"+form_name).submit();
	}
	else
	{
		return false;
	}
}

function processSubmitOption(frm_name,option){
	$('#save_option').val(option);
	processSubmit(frm_name);
}

function processDelete(aid, url, name){
	clearThenHide('result-msg-box');
	get_confirm_box("Confirmation?","Confirm to delete "+name+" ?","ajaxDelete('"+aid+"', '"+url+"')");
}

function ajaxDelete(aid, url){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+url+"/ajax-delete-one/"+sid;
	$.ajax({
		type: "POST",
		url: full_url,
		data: ({aid_selected:aid }),
		dataType: "json",
		beforeSend: function(data) {
			$('#tbldata_processing').removeClass("hidden");
		},
		success: function(data){
			showSearchResult(data);
			show_result_box(data);
		}
	});
}
 
function processChangeValue(msg, url, aid, command){
	clearThenHide('result-msg-box');
	msg = msg.replace('active ', 'activate ');
	get_confirm_box("Confirmation?","Confirm to "+msg+" ?","ajaxChangeValue('"+url+"', '"+aid+"', '"+command+"')");
}

function ajaxChangeValue(url, aid, command){
	// alert(command);
	var sid = Math.floor(Math.random()*10000000000);	
	var arr = command.split("=");
	var full_url = base_url+url+"/ajax-set-value"+"/"+sid;

	$.ajax({
		type: "POST",
		url: full_url,
		data: ({aid_selected:aid, f_name:arr[0], f_value:arr[1] }),
		dataType: "json",
		beforeSend: function(data) {
			$('#tbldata_processing').removeClass("hidden");
		},
		success: function(data){
			showSearchResult();
			show_result_box(data);
		}
	});
}

function ajaxChangeLanguage(lang){
	var sid = Math.floor(Math.random()*10000000000);	
	var full_url = base_url+"ajax-change-language/"+lang+"/"+sid;
	$.ajax({
		type: "POST",
		url: full_url,
		data: ({lang:lang }),
		dataType: "json",
		beforeSend: function(data) {
			// $('#tbldata_processing').removeClass("hidden");
		},
		success: function(data){
			location.reload();
		}
	});
}

function ajaxClearSearchResult(session_name){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"ajax-clear-session/"+sid;
	$.ajax({
		type: "POST",
		url: full_url,
		data: ({session_name:session_name }),
		beforeSend: function(data) {
			$('#tbldata_processing').removeClass("hidden");
			clearSearchResult();
		},
		success: function(msg){
			showSearchResult();
		}
	});
}


/************ End : Process fuctions ************/

/************ Start : button fuctions ************/
function enableButton(obj_id, class_name_enable, class_name_disable){
	$("#"+obj_id).removeAttr('disabled');  
	if(class_name_enable != ""){	
		$("#"+obj_id).addClass(class_name_enable);  
	}
	if(class_name_disable != ""){	
		$("#"+obj_id).removeClass(class_name_disable);  
	}
}
function disableButton(obj_id, class_name_enable, class_name_disable){
	$("#"+obj_id).attr('disabled', 'disabled');  
	if(class_name_enable != ""){	
		$("#"+obj_id).removeClass(class_name_enable);  
	}
	if(class_name_disable != ""){	
		$("#"+obj_id).addClass(class_name_disable);  
	}
}

/************ End : Process fuctions ************/

/************ Start : Control checkbox when has check all ************/
function changeCheckAll(name_all, name_item,disable_flag,check_all_when_all_false){
	if(check_all_when_all_false){
		$("#"+name_all).attr('checked', true);
		$("input[name='"+name_item+"']").each(function()
		{
			this.checked = true;
		});
	}
	else{
		var status = $("#"+name_all+":checked").val();
		var checked_status = false;
		if(status != null){
			checked_status = true;
		}
		//get_alert_box("Debug",checked_status);
		$("input[name='"+name_item+"']").each(function()
		{
			this.checked = checked_status;
			if(disable_flag) this.disabled = checked_status;
		});
	}
	var total_checked = $("input[name='"+name_item+"']:checked").length;
	if ($("#btn_print_barcode").length > 0) {
		if (total_checked > 0) { $("#btn_print_barcode").html("Print PDF Barcode "+total_checked+" รายการ"); }
		else if ($("#total_record").val() > 0) { $("#btn_print_barcode").html("Print PDF Barcode "+$("#total_record").val()+" รายการ"); }
	}
	if ($("#btn_print_cat").length > 0) {
		if (total_checked > 0) { $("#btn_print_cat").html("Print PDF สันปก "+total_checked+" รายการ"); }
		else if ($("#total_record").val() > 0) { $("#btn_print_cat").html("Print PDF สันปก "+$("#total_record").val()+" รายการ"); }
	}
}

function changeCheckItem(name_all, name_item,disable_flag,check_all_when_all_false){

	var checked_status = true;
	var all_false = true;
	//get_alert_box("Debug",checked_status);
	$("input[name='"+name_item+"']").each(function()
	{
		if(!this.checked){
			checked_status = false;
		}else{
			all_false = false;
		}
	});
	// get_alert_box("Debug",checked_status);
	if( all_false && check_all_when_all_false){
		$("#"+name_all).attr('checked', true);
		$("input[name='"+name_item+"']").each(function()
		{
			this.checked = true;
		});
	}else{
		$("#"+name_all).attr('checked', checked_status);
	}
	if(disable_flag){
		$("input[name='"+name_item+"']").each(function()
		{
			this.disabled = checked_status;
		});
	}

	var total_checked = $("input[name='"+name_item+"']:checked").length;
	if ($("#btn_print_barcode").length > 0) {
		if (total_checked > 0) { $("#btn_print_barcode").html("Print PDF Barcode "+total_checked+" รายการ"); }
		else if ($("#total_record").val() > 0) { $("#btn_print_barcode").html("Print PDF Barcode "+$("#total_record").val()+" รายการ"); }
	}
	if ($("#btn_print_cat").length > 0) {
		if (total_checked > 0) { $("#btn_print_cat").html("Print PDF สันปก "+total_checked+" รายการ"); }
		else if ($("#total_record").val() > 0) { $("#btn_print_cat").html("Print PDF สันปก "+$("#total_record").val()+" รายการ"); }
	}
}

function isCheck(name_item){
	var chk = false;
	$("input[name='"+name_item+"']").each(function()
	{
		if(this.checked){
			chk = true;
		}	
	});
	return chk;
}

/************ End : Control checkbox when has check all ************/

/************ Start : Control input by keyboard ************/
/* Character code list
|		backspace	[8]			|	tab	[9]		|			enter	[13]				|			escape	[27]			|						|						|						|						|						|						|
|	!	[33]		|	"	[34]		|	#	[35]		|	$	[36]		|	%[37]		|	&	[38]		|	'	[39]		|	(	[40]		|	)	[41]		|						|						|						|						|
|	*	[42]		|	+	[43]		|	,	[44]		|	-	[45]		|	.	[46]		|	/	[47]		|						|						|						|						|						|						|						|
|	0	[48]		|	1	[49]		|	2	[50]		|	3	[51]		|	4	[52]		|	5	[53]		|	6	[54]		|	7	[55]		|	8	[56]		|	9	[57]		|						|						|						|
|	:	[58]		|	;	[59]		|	<	[60]		|	=	[61]		|	>	[62]		|	?	[63]		|	@ [64]		|						|						|						|						|						|						|
|	A	[65]		|	B	[66]		|	C	[67]		|	D	[68]		|	E	[69]		|	F	[70]		|	G	[71]		|	H	[72]		|	I	[73]		|	J	[74]		|	K	[75]		|	L	[76]		|	M	[77]		|
|	N	[78]		|	O	[79]		|	P	[80]		|	Q	[81]		|	R	[82]		|	S	[83]		|	T	[84]		|	U	[85]		|	V	[86]		|	W[87]		|	X	[88]		|	Y	[89]		|	Z	[90]		|
|	[	[91]		|	\	[92]		|	]	[93]		|	^	[94]		|	_	[95]		|						|						|						|						|						|						|						|						|
|	a	[97]		|	b	[98]		|	c	[99]		|	d	[100]		|	e	[101]		|	f	[102]		|	g	[103]		|	h	[104]		|	i	[105]		|	j	[106]		|	k	[107]		|	l	[108]		|	m[109]		|
|	n	[110]		|	o	[111]		|	p	[112]		|	q	[113]		|	r	[114]		|	s	[115]		|	t	[116]		|	u	[117]		|	v	[118]		|	w	[119]		|	x	[120]		|	y	[121]		|	z	[122]		|
|	{	[123]		|	|	[124]		|	}	[125]		|						|						|						|						|						|						|						|						|						|						|
*/
function getCharactorCode(theEvent){
	var characterCode = theEvent.keyCode ? theEvent.keyCode : theEvent.which ? theEvent.which : theEvent.charCode;
	return characterCode;
}

function isKeyDecimal(theEvent){
	var characterCode = getCharactorCode(theEvent);
	if((characterCode != 8 && characterCode != 13 && characterCode != 45 && characterCode != 46) && (characterCode < 48 || characterCode > 57)){
		if(theEvent && theEvent.which){
			theEvent.preventDefault();
			theEvent.stopPropagation();		
		}else{
			theEvent.keyCode=0;
		}
     }
}

function isKeyNumber(theEvent){
	var characterCode = getCharactorCode(theEvent);
	if((characterCode != 8 && characterCode != 13) && (characterCode < 48 || characterCode > 57)){
		if(theEvent && theEvent.which){
			theEvent.preventDefault();
			theEvent.stopPropagation();		
		}else{
			theEvent.keyCode=0;
		}
     }
}

function isKeyUsername(theEvent){
	var characterCode = getCharactorCode(theEvent);
	// alert(characterCode);
	//45 = - , 95 = _
	if((characterCode != 8 && characterCode != 13 && characterCode != 45 && characterCode != 95) && (characterCode < 48 || characterCode > 57) && (characterCode < 65 || characterCode > 90)  && (characterCode < 97 || characterCode > 122) ){
		if(theEvent && theEvent.which){
			theEvent.preventDefault();
			theEvent.stopPropagation();		
		}else{
			theEvent.keyCode=0;
		}
     }
}

function isKeyDomain(theEvent){
	var characterCode = getCharactorCode(theEvent);
	// alert(characterCode);
	//45 = - , 46 = .
	if((characterCode != 8 && characterCode != 13 && characterCode != 95 && characterCode != 45 && characterCode != 46) && (characterCode < 48 || characterCode > 57) && (characterCode < 97 || characterCode > 122) ){
		if(theEvent && theEvent.which){
			theEvent.preventDefault();
			theEvent.stopPropagation();		
		}else{
			theEvent.keyCode=0;
		}
   }
}

function isKeyUrl(theEvent){
	var characterCode = getCharactorCode(theEvent);
	// alert(characterCode);
	//45 = -
	if((characterCode != 8 && characterCode != 13 && characterCode != 95 && characterCode != 45) && (characterCode < 48 || characterCode > 57) && (characterCode < 97 || characterCode > 122) ){
		if(theEvent && theEvent.which){
			theEvent.preventDefault();
			theEvent.stopPropagation();		
		}else{
			theEvent.keyCode=0;
		}
   }
}

function isNumeric(theEvent,theValueObj){
	var code = (theEvent.keyCode)? theEvent.keyCode: theEvent.which;
	if(code == 46){
		posPoint = theValueObj.indexOf(".");
		if(posPoint > -1) (theEvent.keyCode)? theEvent.keyCode=0: theEvent.preventDefault();
	}else{
		if(code < 48 || code > 57){
			(theEvent.keyCode)? theEvent.keyCode=0:theEvent.preventDefault();
		}else{
			posPoint=theValueObj.indexOf(".");
			if(posPoint>-1){
				len=theValueObj.substr(posPoint+1,theValueObj.length).length;
				if(len==2) (theEvent.keyCode)? theEvent.keyCode=0:theEvent.preventDefault();
			}
		}
	}
}

function isInt(n) {
	return n % 1 == 0 && !isNaN(n);
}

function isWeight(theEvent, theValueObj){
	var code = (theEvent.keyCode)? theEvent.keyCode: theEvent.which;
	// alert(code);
	if(code == 45){
		t = theValueObj.charAt(0);
		if(t != ""){
			if(theEvent && theEvent.which){
				theEvent.preventDefault();
				theEvent.stopPropagation();		
			}else{
				theEvent.keyCode=0;
			}
		}
		posPoint = theValueObj.lastIndexOf("-");
		// alert(t);
		// alert(posPoint);
		// if(posPoint > -1) (theEvent.keyCode)? theEvent.keyCode=0: theEvent.preventDefault();
		// if(posPoint == -1 && t != '') (theEvent.keyCode)? theEvent.keyCode=0: theEvent.preventDefault();
	}else if(code == 46){
		posPoint = theValueObj.indexOf(".");
		if(posPoint > -1) (theEvent.keyCode)? theEvent.keyCode=0: theEvent.preventDefault();
	}else{
		if(code < 48 || code > 57){
			(theEvent.keyCode)? theEvent.keyCode=0:theEvent.preventDefault();
		}else{
			posPoint=theValueObj.indexOf(".");
			if(posPoint>-1){
				len=theValueObj.substr(posPoint+1,theValueObj.length).length;
				if(len==5) (theEvent.keyCode)? theEvent.keyCode=0:theEvent.preventDefault();
			}
		}
	}
}

function isEnter(theEvent){ 
	var characterCode = getCharactorCode(theEvent);
	//get_alert_box("Debug",characterCode);
	if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
		return true;
	}else{
		return false;
	}
}

function isEnterGoTo(theEvent,fname){ 
	var characterCode = getCharactorCode(theEvent);
	//get_alert_box("Debug",characterCode);
	//get_alert_box("Debug",fname);
	if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
		eval(fname);
		return true;
	}else{
		return false;
	}
}

function isEscapeGoto(theEvent,fname){ 
	var characterCode = getCharactorCode(theEvent);
	//alert(characterCode);
	//alert(fname);
	if(characterCode == 27){ //if generated character code is equal to ascii 27 (if escape key)
		eval(fname);
		return true;
	}else{
		return false;
	}
}

/************ End : Control input by keyboard ************/

/************ Control money input ************/
function getFloat(theValueObj){
	if(theValueObj == "" || theValueObj == "."){
		return 0.00;
	}else{
		return parseFloat(theValueObj);
	}
}

function outComma(theValueObj){
	realnumber = "";
	var valueObj = trimString(theValueObj);
	if(valueObj.length >0){
		for(i=0;i<valueObj.length;i++){
			if (valueObj.substr(i,1) != ","){
				realnumber = realnumber + valueObj.substr(i,1);
			}
		}
	}
	return realnumber;
}

function commaSplit(theValueObj,min_value,max_value) {
	var chk = false;
	var error_msg = "";
	if(max_value > 0){
		if(getFloat(returnResult) >= min_value && getFloat(returnResult) <= max_value) {
			chk = true;
		}else{
			error_msg = "This number value must between "+min_value+" - "+commaSplit(keepPoint(max_value,2),0);
		}
	}else{
		if(getFloat(returnResult) >= 0) {
			chk = true;
		}else{
			error_msg  = "This value must be number.";
		}
	}
	if(chk) {
		var txtNumber = '' + theValueObj;
		var rxSplit = new RegExp('([0-9])([0-9][0-9][0-9][,.])');
		var arrNumber = txtNumber.split('.');
		arrNumber[0] += '.';

		do {
			arrNumber[0] = arrNumber[0].replace(rxSplit, '$1,$2');
		} while (rxSplit.test(arrNumber[0]));

		if (arrNumber.length > 1) {
			return arrNumber.join('');
		}else {
			return arrNumber[0].split('.')[0];
		}
	}else{
		get_alert_box("Error",error_msg);
		return "0.00";
	}
}

function keepPoint(theValueObj,theValuePoint){
	returnResult=theValueObj;
	if(getFloat(returnResult) > 0 ) {
		theValueObj=theValueObj+"";
		posPoint=theValueObj.indexOf(".");
		if(posPoint>-1){
			len=theValueObj.substr(posPoint+1,theValueObj.length).length;
			if(len>theValuePoint){
				if (theValuePoint == 0) {
					returnResult=theValueObj.substr(0,posPoint);
				}else{
					tmp=theValueObj.substr(posPoint,theValueObj.length).substr(theValuePoint+1,1);
					if(eval(tmp)>4){
						dec = parseFloat(theValueObj.substr(posPoint+1,theValuePoint))+1;
						dec = dec + "";
						if (dec.length <= theValuePoint) {
							for (k = dec.length;k < theValuePoint;k++)
							dec = "0" + dec;
							returnResult=theValueObj.substr(0,posPoint+1) + dec;
						} else {
							returnResult=((outComma(theValueObj.substr(0,posPoint))*1)+1) + "." + dec.substr(1,theValuePoint);
						}
					}else{
						returnResult=theValueObj.substr(0,posPoint+theValuePoint+1);
					}

				}
			} else {
				for(k=len;k<theValuePoint;k++) {
					theValueObj += "0";
				}
				returnResult=theValueObj;
			}
		} else {
			if (theValuePoint > 0) {
				theValueObj += ".";
				for(k=0;k<theValuePoint;k++) {
					theValueObj += "0";
				}
			}
			returnResult=theValueObj;
		}
		
		if (theValuePoint !=0 ) {
			posPoint=returnResult.indexOf(".");
			if(posPoint<0){
				returnResult += ".";
				for(k=0;k<theValuePoint;k++) {
					returnResult += "0";
				}
			}
		}
	}
	return returnResult;
}

/************ End : Control money input ************/

/************ Start : Control Table Result ************/
function set_focus(divName){
	$('#'+divName).focus();
}

function set_order_by(val){
	$('#search_order_by').val(val);
	// get_alert_box("Debug",$('#search_order_by').val());
}

function set_page(val){
	$('#page_selected').val(val);
	// get_alert_box("Debug",$('#page_selected').val());
}

function draw_table_result(data,divName,show_i){
	// get_alert_box("Debug",data.status);
	if(data.status=="error"){
		get_alert_box("Error",data.msg);
	}else if(data.status=="warning"){
		var txt = '';
		txt += '<div class="da-form-row">';
		txt += data.msg;
		txt += '</div>';
		$('#'+divName).html(txt);
	
	}else if(data.status=="success"){
		var txt = '';
		
		txt += '<div class="row-fluid">' ;
		txt += '<div class="span6">' ;
		txt += '<div id="tbldata_length" class="dataTables_length">';
		txt += '<label>Show&nbsp;:&nbsp;';
		txt += '';
		txt += '<select id="search_record_per_page" name="search_record_per_page" size="1" onchange="showSearchResult(\''+divName+'\',\''+show_i+'\')" class="form-control">';
		
		/*
		txt += '<option value="1"';
		if(data.optional.search_record_per_page == 1){ txt += ' selected'; }
		txt += '>1</option>';

		txt += '<option value="2"';
		if(data.optional.search_record_per_page == 2){ txt += ' selected'; }
		txt += '>2</option>';
		*/
		txt += '<option value="10"';
		if(data.optional.search_record_per_page == 10){ txt += ' selected'; }
		txt += '>10</option>';

		txt += '<option value="25"';
		if(data.optional.search_record_per_page == 25){ txt += ' selected'; }
		txt += '>25</option>';

		txt += '<option value="50"';
		if(data.optional.search_record_per_page == 50){ txt += ' selected'; }
		txt += '>50</option>';

		txt += '<option value="100"';
		if(data.optional.search_record_per_page == 100){ txt += ' selected'; }
		txt += '>100</option>';

		txt += '<option value="500"';
		if(data.optional.search_record_per_page == 500){ txt += ' selected'; }
		txt += '>500</option>';

		txt += '<option value="1000"';
		if(data.optional.search_record_per_page == 1000){ txt += ' selected'; }
		txt += '>1000</option>';

		txt += '</select>';
		txt += '';
		txt += 'entries';
		txt += '</label></div>';

		txt += '</div>';
		txt += '</div>';


		
		txt += '<table cellspacing="0" cellpadding="0" border="0" id="tbldata" class="display table table-bordered table-striped dataTable">';
		txt += '<thead>';
		txt += '<tr role="row">';
		if(show_i){
			txt += '<th class="w10 hcenter">No.</th>';
		}
		
		var order_option = 'asc';
		$.each(data.header_list, function(i,item){
			if(item.sort_able == "1"){
				if(data.sorting.order_by == item.field_order){
					order_option = 'asc';
					if(data.sorting.order_by_option == 'asc'){
						order_option = 'desc';
					}
					txt += '<th class="'+item.title_class+' sorting_'+data.sorting.order_by_option+'" onclick="set_order_by(\''+item.field_order+' '+order_option+'\');showSearchResult(\''+divName+'\',\''+show_i+'\')">'+item.title_show+'</th>';
				}else{
					txt += '<th class="'+item.title_class+' sorting" onclick="set_order_by(\''+item.field_order+' asc\');showSearchResult(\''+divName+'\',\''+show_i+'\')">'+item.title_show+'</th>';
				}
			}else{
					txt += '<th class="'+item.title_class+'">'+item.title_show+'</th>';
			}
		});
		txt += '</tr>';
		txt += '</thead>';
							
		txt += '<tbody>';
		$.each(data.result, function(i,item){
			if(i%2 == 1){
				txt += '<tr class="even">';
			}else{
				txt += '<tr class="odd">';
			}
				if(show_i){
					txt += '<td class="hleft">'+ ((data.optional.search_record_per_page*(data.optional.page_selected-1))+i+1) +'.</td>';
				}
				$.each(data.header_list, function(j,sub_item){
					txt += '<td class="'+sub_item.result_class+'">'+eval( "item." + sub_item.col_show )+'</td>';
				});
			txt += '</tr>';
		});
		txt += '</tbody>';
		txt += '</table>';


		txt += '<div class="row-fluid">' ;
		txt += '<div class="span6">' ;
		txt += '<div id="tbldata_info" class="dataTables_info">';
		txt += '<b>Showing '+ (data.optional.start_record + 1) +' to '+(data.optional.start_record + data.optional.total_in_page)+' of '+data.optional.total_record+' entries</b>';
		txt += '</div>';
		txt += '</div>';
		
		txt += '<div class="span6">' ;
		txt += '<div class="dataTables_paginate paging_bootstrap pagination" id="tbldata_paginate">';
		txt += '<ul>';
		
		if(data.optional.page_selected > 1){
			txt += '<li class="first"><a class="button" onclick="set_page(\''+1+'\');showSearchResult(\''+divName+'\',\''+show_i+'\');" id="tbldata_first">First</a></li>';
			txt += '<li class="prev"><a class="button" onclick="set_page(\''+(parseInt(data.optional.page_selected)-1)+'\');showSearchResult(\''+divName+'\',\''+show_i+'\');" id="tbldata_previous">Previous</a></li>';
		}

		var page_selected = parseInt(data.optional.page_selected);
		var total_page = parseInt(data.optional.total_page);
		var end_page = page_selected + 5;
		
		if(end_page >= total_page) end_page = total_page;
		
		var start_page = page_selected-5;
		if(start_page <= 0) start_page = 1;

		for(i=start_page; i<=end_page; i++){
			if(i==data.optional.page_selected){
				txt += '<li class="active">'+'<a>'+i+'</a></li>';
			}else{
				txt += '<li>'+'<a class="button" onclick="set_page(\''+i+'\');showSearchResult(\''+divName+'\',\''+show_i+'\');">'+i+'</a></li>';
			}
		}

		if(data.optional.page_selected < data.optional.total_page){
			txt += '<li class="next"><a class="button" onclick="set_page(\''+(parseInt(data.optional.page_selected)+1)+'\');showSearchResult(\''+divName+'\',\''+show_i+'\');" id="tbldata_next">Next</a></li>';
			txt += '<li class="last"><a class="button" onclick="set_page(\''+data.optional.total_page+'\');showSearchResult(\''+divName+'\',\''+show_i+'\');" id="tbldata_last">Last</a></li>';
		}

		txt += '</ul>';
		txt += '</div>';
		txt += '</div>';
		txt += '</div>';
		
		
		// get_alert_box("Debug",txt);
		$('#'+divName).html(txt);
		// set_tooltip();
		/* Tooltips */
		
		// if($.fn.tipsy) {
			// var gravity = ['n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw'];
			// for(var i in gravity)
				// $(".da-tooltip-"+gravity[i]).tipsy({gravity: gravity[i]});
				
			// $('input[title], select[title], textarea[title], a[title], div[title], span[title], img[title]').tipsy({trigger: 'focus', gravity: 'w'});
		// }
		
		$("#tbldata_processing").each(function() {
			// $(this).progressbar({
			// 	value:Math.floor(100)
			// });
		});
		

		/*
		if($("#tbldata").width() > $("#tbldata_wrapper").width()){
			alert('bbb');
			if($.fn.tinyscrollbar) {
				$(".da-panel.scrollable .da-panel-content").each(function() {
					var height = $(this).height(), 
						o = 
						$(this)
							.children().wrapAll('<div class="overview"></div>')
						.end()
							.children().wrapAll('<div class="viewport"></div>')
						.end()
							.find('.viewport').css('height', height)
						.end()
							.append('<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>')
						.tinyscrollbar({axis: 'x'});
					
					$(window).resize(function() {
						o.tinyscrollbar_update();
					});
				});
			}
		}
		*/
	}
	$('#tbldata_processing').addClass("hidden");
}

function set_order_by_popup(val, popup_name){
	$('#search_order_by_'+popup_name).val(val);
	// get_alert_box("Debug",$('#search_order_by').val());
}

function set_page_popup(val, popup_name){
	$('#page_selected_'+popup_name).val(val);
	// get_alert_box("Debug",$('#page_selected').val());
}

function draw_popup_result(data,divName,show_i,function_name,popup_name){
	// get_alert_box("Debug",data.status);
	if(data.status=="error"){
		get_alert_box("Error",data.msg);
	}else if(data.status=="warning"){
		var txt = '';
		txt += '<div class="da-form-row">';
		txt += data.msg;
		txt += '</div>';
		$('#'+divName).html(txt);
	
	}else if(data.status=="success"){
		var txt = '';
		
		/*
		txt += '<div class="row-fluid">' ;
		txt += '<div class="span6">' ;
		txt += '<div id="tbldata_length" class="dataTables_length">';
		txt += '<label>Show&nbsp;:&nbsp;';
		txt += '';
		txt += '<select id="search_record_per_page" name="search_record_per_page" size="1" onchange="showSearchPopup(\''+divName+'\',\''+show_i+'\')" class="form-control">';
		
		txt += '<option value="1"';
		if(data.optional.search_record_per_page == 1){ txt += ' selected'; }
		txt += '>1</option>';

		txt += '<option value="2"';
		if(data.optional.search_record_per_page == 2){ txt += ' selected'; }
		txt += '>2</option>';

		txt += '<option value="10"';
		if(data.optional.search_record_per_page == 10){ txt += ' selected'; }
		txt += '>10</option>';

		txt += '<option value="25"';
		if(data.optional.search_record_per_page == 25){ txt += ' selected'; }
		txt += '>25</option>';

		txt += '<option value="50"';
		if(data.optional.search_record_per_page == 50){ txt += ' selected'; }
		txt += '>50</option>';

		txt += '<option value="100"';
		if(data.optional.search_record_per_page == 100){ txt += ' selected'; }
		txt += '>100</option>';

		txt += '<option value="500"';
		if(data.optional.search_record_per_page == 500){ txt += ' selected'; }
		txt += '>500</option>';

		txt += '<option value="1000"';
		if(data.optional.search_record_per_page == 1000){ txt += ' selected'; }
		txt += '>1000</option>';

		txt += '</select>';
		txt += '';
		txt += 'entries';
		txt += '</label></div>';

		txt += '</div>';
		txt += '</div>';
		*/

		txt += '<table cellspacing="0" cellpadding="0" border="0" id="tbldata" class="display table table-bordered table-striped dataTable">';
		txt += '<thead>';
		txt += '<tr role="row">';
		if(show_i){
			txt += '<th class="w10 hcenter">No.</th>';
		}
		
		var order_option = 'asc';
		$.each(data.header_list, function(i,item){
			if(item.sort_able == "1"){
				if(data.sorting.order_by == item.field_order){
					order_option = 'asc';
					if(data.sorting.order_by_option == 'asc'){
						order_option = 'desc';
					}
					txt += '<th class="'+item.title_class+' sorting_'+data.sorting.order_by_option+'" onclick="set_order_by_popup(\''+item.field_order+' '+order_option+'\', \''+popup_name+'\');'+function_name+'(\''+divName+'\',\''+show_i+'\', \''+popup_name+'\')">'+item.title_show+'</th>';
				}else{
					txt += '<th class="'+item.title_class+' sorting" onclick="set_order_by_popup(\''+item.field_order+' asc\', \''+popup_name+'\');'+function_name+'(\''+divName+'\',\''+show_i+'\', \''+popup_name+'\')">'+item.title_show+'</th>';
				}
			}else{
					txt += '<th class="'+item.title_class+'">'+item.title_show+'</th>';
			}
		});
		txt += '</tr>';
		txt += '</thead>';
							
		txt += '<tbody>';
		$.each(data.result, function(i,item){
			if(i%2 == 1){
				txt += '<tr class="even">';
			}else{
				txt += '<tr class="odd">';
			}
				if(show_i){
					txt += '<td class="hleft">'+ ((data.optional.search_record_per_page*(data.optional.page_selected-1))+i+1) +'.</td>';
				}
				$.each(data.header_list, function(j,sub_item){
					txt += '<td class="'+sub_item.result_class+'">'+eval( "item." + sub_item.col_show )+'</td>';
				});
			txt += '</tr>';
		});
		txt += '</tbody>';
		txt += '</table>';


		txt += '<div class="row">' ;
		/*
		txt += '<div class="col-sm-12 ">' ;
		txt += '<div id="tbldata_info" class="pl0 dataTables_info">';
		txt += '<b>Showing '+ (data.optional.start_record + 1) +' to '+(data.optional.start_record + data.optional.total_in_page)+' of '+data.optional.total_record+' entries</b>';
		txt += '</div>';
		txt += '</div>';
		*/
		txt += '<div class="col-sm-12 ">' ;
		txt += '<div class="col-sm-12 pb0 mt0 mb0 dataTables_paginate paging_bootstrap pagination" id="tbldata_paginate">';
		txt += '<ul>';
		
		if(data.optional.page_selected > 1){
			txt += '<li class="first"><a class="button" onclick="set_page_popup(\''+1+'\', \''+popup_name+'\');'+function_name+'(\''+divName+'\',\''+show_i+'\');" id="tbldata_first">First</a></li>';
			txt += '<li class="prev"><a class="button" onclick="set_page_popup(\''+(parseInt(data.optional.page_selected)-1)+'\', \''+popup_name+'\');'+function_name+'(\''+divName+'\',\''+show_i+'\');" id="tbldata_previous">Previous</a></li>';
		}

		var page_selected = parseInt(data.optional.page_selected);
		var total_page = parseInt(data.optional.total_page);
		var end_page = page_selected + 2;
		
		if(end_page >= total_page) end_page = total_page;
		
		var start_page = page_selected - 2;
		if(start_page <= 0) start_page = 1;

		for(i=start_page; i<=end_page; i++){
			if(i==data.optional.page_selected){
				txt += '<li class="active">'+'<a>'+i+'</a></li>';
			}else{
				txt += '<li>'+'<a class="button" onclick="set_page_popup(\''+i+'\', \''+popup_name+'\');'+function_name+'(\''+divName+'\',\''+show_i+'\');">'+i+'</a></li>';
			}
		}

		if(data.optional.page_selected < data.optional.total_page){
			txt += '<li class="next"><a class="button" onclick="set_page_popup(\''+(parseInt(data.optional.page_selected)+1)+'\', \''+popup_name+'\');'+function_name+'(\''+divName+'\',\''+show_i+'\');" id="tbldata_next">Next</a></li>';
			txt += '<li class="last"><a class="button" onclick="set_page_popup(\''+data.optional.total_page+'\', \''+popup_name+'\');'+function_name+'(\''+divName+'\',\''+show_i+'\');" id="tbldata_last">Last</a></li>';
		}

		txt += '</ul>';
		txt += '</div>';
		txt += '</div>';
		txt += '</div>';
		
		
		// get_alert_box("Debug",txt);
		$('#'+divName).html(txt);
		// set_tooltip();
		/* Tooltips */
		
		$("#tbldata_processing").each(function() {
			// $(this).progressbar({
			// 	value:Math.floor(100)
			// });
		});
	}
	$('#tbldata_processing').addClass("hidden");
}

/************ End : Control Table Result ************/

/************ Start : Control alert box ************/
function close_model_box(modal_name){
	$("#"+modal_name).modal( "hide" );
}

function get_alert_box(title_txt, msg){
	$('#modal-header').html(title_txt);
	$('#modal-msg').html(msg);
	$('#modal-button').html('<button data-dismiss="modal" class="btn btn-default" type="button">Close</button>');
	$('#dialog_box').modal({
		backdrop: 'static',
		keyboard: false
	})
}

function get_confirm_box(title_txt, msg, function_name){
	$('#modal-header').html(title_txt);
	$('#modal-msg').html(msg);
	$('#modal-button').html('<button onclick="eval('+function_name+')" data-dismiss="modal" class="btn btn-success" type="button">OK</button><button data-dismiss="modal" class="btn btn-default" type="button">Cancel</button>');
	$('#dialog_box').modal({
		backdrop: 'static',
		keyboard: false
	})
}

function show_result_box(data){
	if (null != data) {
		eval(data.msg);
		return data.status;
	}else{
		return "error";
	}
}
/************ End : Control alert box ************/

function set_toggle_advance_search(option){
	if(option=='show'){
		$('#adv-icon').addClass('fa-chevron-down');
		$('#adv-icon').removeClass('fa-chevron-up');
		$('#adv-area').css('display', 'block');
	}else{
		$('#adv-icon').removeClass('fa-chevron-down');
		$('#adv-icon').addClass('fa-chevron-up');
		$('#adv-area').css('display', 'none');
	}
}

