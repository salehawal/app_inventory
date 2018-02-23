<?php
print_r($_POST);
?>
<!DOCTYPE html>
<html>
<head>
	<title>HTML TEST</title>
	<script src="funcs.js"></script>
	<script src="jquery-1.11.3.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/bootstrap-3.3.5-dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../css/reset.css">
	<link rel="stylesheet" type="text/css" href="../css/bootstrap-fileinput-master/css/fileinput.min.css">
	<link rel="stylesheet" type="text/css" href="../css/ionicons.min.css">
	<link rel="stylesheet" type="text/css" href="../css/main.css">
	<link rel="stylesheet" type="text/css" href="../css/main_mobile.css">
	<link href="../css/_all-skins.min.css" rel="stylesheet" type="text/css" />
  	<link href="../css/blue.css" rel="stylesheet" type="text/css" />
	<link href="../css/bootstrap-switch-master/dist/css/bootstrap3/bootstrap-switch.min.css" rel="stylesheet">
	<script src="../css/bootstrap-switch-master/dist/js/bootstrap-switch.min.js"></script>
</head>
<body>
<form id="data_form" method="post">
<script type="text/javascript">
//genBlock('Car Details',[{ iname:'CAR_NAME', itype:'checkbox', iid:'car_id'},{ iname:'CAR_NAME', itype:'checkbox', iid:'car_id'},{ iname:'CAR_NAME', itype:'checkbox', iid:'car_id'}]);


//genBlock('Car Details',[{ iname:'CAR_NAME', itype:'checkbox', iid:'car_id'},{ iname:'CAR_NAME', itype:'checkbox', iid:'car_id'},{ iname:'CAR_NAME', itype:'checkbox', iid:'car_id'}]);
//genBlock('Car Date',		{ iname:'CAR_NAME', 		itype:'year', 		iid:'car_date' });
//genBlock('Car Name',		{ iname:'CAR_NAME', 		itype:'number', 	iid:'car_id', 	id:'car_id', 	onkeyup:'forceLowercase(this)' });
//genBlock('Car Name',		{ iname:'CAR_NAME', 		itype:'text', 		iid:'car_id' }, {placeholder:'Please Enter Your Name', value:'Saleh Abdulaziz'});
//genBlock('Car Name',		{ iname:'CAR_NAME', 		itype:'text', 		iid:'car_id' }, {placeholder:'Please Enter Your Name', alt:'Saleh Abdulaziz'});
//genBlock('Car Note',		{ iname:'CAR_NOTE', 		itype:'textarea', 	iid:'car_id' });
//genBlock('Car Select',		{ iname:'CAR_TYPE', 		itype:'select', 	iid:'car_id' });
//genBlock('Car Radio',[{ iname:'CAR_STATUS', itype:'radio', iid:'car_id' }, { iname:'CAR_STATUS', itype:'radio', iid:'car_id'}]);
//genBlock('Car Checkbox',[{ iname:'CAR_OPTION', itype:'checkbox', iid:'car_id' }, { iname:'CAR_OPTION', itype:'checkbox', iid:'car_id'}]);
//genBlock('Car Name',		{ iname:'CAR_NAME', 		itype:'text', 		iid:'car_id' });
//genBlock('Car Checkbox',[{ iname:'CAR_OPTION', itype:'checkbox', iid:'car_id' }, { iname:'CAR_OPTION', itype:'checkbox', iid:'car_id'}]);

//genBlock('All Door Equip', {ilable:'All Door Equip',iname:'All Door Equip',itype:'select',iid:'select',vals: {0:'zero',1:'one',2:'tow',3:'three',4:' four',5:'five'}, params:{size:'1'}});

//genBlock('FV_OPERATIONAL', {iname:'FV_OPERATIONAL',itype:'radio',vals:{1:'Yes',0:'No'}});
//genBlock('FV_OPERATIONAL', {iname:'FV_OPERATIONAL',itype:'radio',iid:'FV_OPERATIONAL',vals:{1:'Yes',0:'No'}});
//genBlock('FV_COLOR', {iname:'FV_COLOR',itype:'checkbox',iid:'FV_COLOR',vals:{1:'Yes',0:'No'}});
//genBlock('FV_OPERATIONAL', {iname:'FV_OPERATIONAL',itype:'radio',iid:'FV_OPERATIONAL',vals:{1:'Yes',0:'No'},value:'1'});
//genBlock('FV_CHASSIS_NO', {itype:'text',iid:'FV_CHASSIS_NO',iname:'FV_CHASSIS_NO',size:'50',value:'VHSCC001010212'});
//genBlock('FV_CHASSIS_YEAR', {itype:'select',iid:'FV_CHASSIS_YEAR',iname:'FV_CHASSIS_YEAR',size:'50',value:'2010',feed:'year'});
//genBlock('Model', {itype:'text',iid:'FV_MODEL',iname:'FV_MODEL',size:'100',value:'2009',feed:'year'});
//genBlock('INSURANCE CARD', {value:'1',itype:'checkbox',ilabel:'FV_INSURANCE_CARD',iid:'FV_INSURANCE_CARD',iname:'FV_INSURANCE_CARD',size:'1',vals:{0:'no',1:'yes'}});
//genBlock('INSURANCE CARD', {COLUMN_NAME:'FV_INSURANCE_CARD',DATA_TYPE:'VARCHAR2',DATA_LENGTH:'1',value:'',itype:'radio',ilabel:'FV_INSURANCE_CARD',iid:'FV_INSURANCE_CARD',iname:'FV_INSURANCE_CARD',size:'1',vals:{0:'no',1:'yes'}});
genBlock('All Door Equip', {itype:'select', ilabel:'FV_ALL_DOOR_AND_EQUIP_WORK', iid:'FV_ALL_DOOR_AND_EQUIP_WORK', iname:'All Door Equip', size:'1', vals:{0:'zero', 1:'one', 2:'tow', 3:'three', 4:'four', 5:'five'}});

function enable_switch()
{
	$.each($('#data_form input'), function(ifield, ival) {
		if($(ival).is(':radio') || $(ival).is(':checkbox'))
		{
			var iname = $(ival).attr('id');
			$("#"+iname).bootstrapSwitch();
		}
	});
}
$(document).ready(function()
{
	enable_switch();
});
</script>
<input type="submit" value="SUBMIT">
</form>
</body>
</html>