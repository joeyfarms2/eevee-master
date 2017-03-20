$.validator.addMethod(
"greaterThan",
function(value, element, params) {
	if(value == ''){
		return true;
	}
	var target = $(params).val();
	var isValueNumeric = !isNaN(parseFloat(value)) && isFinite(value);
	var isTargetNumeric = !isNaN(parseFloat(target)) && isFinite(target);
	if (isValueNumeric && isTargetNumeric) {
		return Number(value) > Number(target);
	}

	if (!/Invalid|NaN/.test(new Date(value))) {
		return new Date(value) > new Date(target);
	}

	return false;
},
'Must be greater than {0}.');

$.validator.addMethod(
"greaterThanOrEqual",
function(value, element, params) {
	if(value == ''){
		return true;
	}
	var target = $(params).val();
	var isValueNumeric = !isNaN(parseFloat(value)) && isFinite(value);
	var isTargetNumeric = !isNaN(parseFloat(target)) && isFinite(target);
	if (isValueNumeric && isTargetNumeric) {
		return Number(value) >= Number(target);
	}

	if (!/Invalid|NaN/.test(new Date(value))) {
		return new Date(value) >= new Date(target);
	}

	return false;
},
'Must be greater than {0}.');

$.validator.addMethod('cbSelectone', function(value,element){
    if(element.length>0){
        for(var i=0;i<element.length;i++){
            if($(element[i]).val('checked')) return true;
        }
        return false;
    }
    return false;
}, 'Please select at least one option');

$.validator.addMethod(
  "idcard",
  function(value, element) {
    if(!/^[1-8]\d{12}/.test(value.toString())) return false;
    var t = value.toString().split('');
    var m = 13, s = 0;
    for(i=0, sum=0; i<12; i++) s += parseFloat(t[i])*(13-i);
    return (11-s%11)%10==t[12];
  },
  "Please enter a valid ID card."
);