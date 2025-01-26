// Check Browser Devic
// GLOBAL SETTINGS
INPUT_ROW_MAX_ELEMENTS 	= 2;
INPUT_VALS 				= null;
INPUT_IDS				=0;

function check_device()
{
  var devcheck = false;
  (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))devcheck = true})(navigator.userAgent||navigator.vendor||window.opera);
  return devcheck;
}

function go_to_page(url)
{
  document.location=url;
}

function print_enable_switch(tcode)
{
	window.open("bcode.php?text="+tcode);
}

// Generate a Form HTML Row Block
function gen_block(ilabel, params)
{
	var field_box;
	if(arguments[2] != null) INPUT_VALS = arguments[2];
	field_box = gen_field_box(params);
	var block 	= '<div class="row"> <div class="col-md-12"> <div class="form-group"> <label for="sel1">'+ilabel+':</label> '+field_box+' </div> </div> </div>';
	document.writeln(block);
}

// Generate a Input HTML Code
function gen_field_box(params)
{
	var field_box;
	if(params.itype == 'radio' || params.itype == 'checkbox')
	{
		field_box = gen_group_block(params);
	}
	else if(params instanceof Object)
	{
		field_box = gen_input_field(params);
	}
	return field_box;
}

// Generate Group Input Fields HTML Block
function gen_group_block(params)
{
	var block = '';
	var col_size = 12 / INPUT_ROW_MAX_ELEMENTS;
	var row = '';
	var row_start 	= '<div class="row">';
	var row_end		= '</div>';
	var block_tag_open 	= '<div class="col-xs-'+col_size+'"> <div class="input-group">';
	var block_tag_close	= '</div> </div>';
	var ifields = gen_group_input_fields(params);
	var fields_length = ifields.length-1;
	var rows = 0;
	var i = 0;

	$.each(ifields, function(fieldid, fieldval) {
		// Start Row
		if(rows == 0)
		{
			block += row_start;
		}
		// Add Field
		block += block_tag_open+' '+fieldval.ifield+' '+block_tag_close;
		// Close Row
		if(rows == (INPUT_ROW_MAX_ELEMENTS-1) || fields_length == i)
		{
			block += row_end;
			rows = 0;
		}
		else
		{
			rows++;
		}
		i++;
	});

	// Block Data
	return block;
}

// Create Input Fields
function gen_group_input_fields(params)
{
	var ifield;
	var ifields  = [];
	$.each(params.vals, function(paramid, paramval)
	{
		var ifparams 	= [];
		ifparams 	  	= (JSON.parse(JSON.stringify(params)));
		ifparams.iname 	= params.iname;
		ifparams.itype 	= params.itype;
		ifparams.iid  	= params.iid+'-'+INPUT_IDS; INPUT_IDS++;
		ifparams.txt  	= paramval;
		ifparams.oval  	= paramid;

		if(paramid == ifparams.value) ifparams.checked = true; else ifparams.checked = false;

		ifield = gen_input_field(ifparams);
		ifields.push({'ifield':ifield});
	});
	// Reset INPUT AUTO ID
	INPUT_IDS = 0;
	return ifields;
}

// Generate a Single Input Field
function gen_input_field(params)
{
	var ifield;
	if(params.itype == 'number')
		ifield = gen_input_number(params);
	else if(params.itype == 'text')
		ifield = gen_input_text(params);
	else if(params.itype == 'textarea')
		ifield 	= gen_input_textarea(params);
	else if(params.itype == 'select')
		ifield 	= gen_input_select(params);
	else if(params.itype == 'radio')
		ifield 	= gen_input_radio(params);
	else if(params.itype == 'checkbox')
		ifield 	= gen_input_checkbox(params);
	else if(params.itype == 'date')
		ifield  = gen_input_date(params);
	else if(params.itype == 'year')
		ifield  = gen_input_date_year(params);
	return ifield;
}

// Create a Text Input Field
function gen_input_number(params)
{
	var attr = gen_field_properties(params);
	return '<input type="number" '+attr+' class="form-control">';
}

// Create a Text Input Field
function gen_input_text(params)
{
	var attr = gen_field_properties(params);
	return '<input type="text" '+attr+' class="form-control">';
}

// Create a Textare Input Field
function gen_input_textarea(params)
{
	var attr = gen_field_properties(params);
	var val = '';
	if(params.hasOwnProperty("value")) val = params.value;
	return '<textarea '+attr+' rows="5" class="form-control">'+val+'</textarea>';
}

// Create a Select Input Field
function gen_input_select(params)
{
	var attr = gen_field_properties(params);
	var opts = '';
	if(params.vals instanceof Object)
		opts = gen_select_options(params, false);
	else if(params.hasOwnProperty('feed'))
		if(params.feed == 'year')
		{
			params.vals = gen_years();
			opts = gen_select_options(params, true);
		}
	return '<select '+attr+' class="form-control" id="car_type"> '+opts+' </select>';
}

function gen_select_options(params,ival)
{
	var ocode = '<option value="0"> -- </option>';
	$.each(params.vals, function(key, val)
	{
		// Check if Value is Index or not
		if(ival) key = val;
		// check if Value Selected
		if(key == params.value)
			ocode += '<option value="'+key+'" selected="selected">'+val+'</option>';
		else
			ocode += '<option value="'+key+'">'+val+'</option>';
	});
	return ocode;
}

// Create a Radio Input Field
function gen_input_radio(params)
{
	var ilabel 	= gen_input_text_lable(params);
	var attr 	= gen_field_properties(params);
	var checked = '';
	if(params.hasOwnProperty("checked")) if(params.checked) checked = ' checked';
	var ifield 	= '<span class="input-group-addon" id="'+params.iname+'-'+params.iid+'"> <input '+attr+' type="radio" class="btn btn-lg btn-default"'+checked+'> </span>';
	var iblock = ifield+' '+ilabel;
	return iblock;
}

// Create a Checkbox Input Field
function gen_input_checkbox(params)
{
	var ilabel = gen_input_text_lable(params);
	var attr = gen_field_properties(params);
	var checked = '';
	if(params.hasOwnProperty("checked")) if(params.checked) checked = ' checked';
	var ifield = '<span class="input-group-addon" id="'+params.iname+'-'+params.iid+'"> <input '+attr+' type="checkbox" class="btn btn-lg btn-default"'+checked+'> </span>';
	var iblock = ifield+' '+ilabel;
	return iblock;
}

// Create a Date Field
function gen_input_date(params)
{
	var attr = gen_field_properties(params);
	var val = ''; if(params.hasOwnProperty("value")) val = params.value;
	return '<input type="date" '+attr+' value="'+val+'" class="form-control">';
}

// Create a Date Field | Year
function gen_input_date_year(params)
{
	params.feed = 'year';
	return gen_input_select(params);
}

// Create a Label for a Input Field
function gen_input_text_lable(params)
{
	return '<input name="'+params.iname+'" value="'+params.txt+'" type="text" class="form-control" disabled="disabled">';
}

function gen_field_properties(params)
{
	var attr = '';
	if(params.hasOwnProperty("id")) params.id = params.iid;
	$.each(params, function(key, val)
	{
		if(typeof(val) == "string")
		{
			if(val != "")
			{
				if(key == 'iid')
					attr += ' id="'+val+'"';
				else if(key == 'iname')
					attr += ' name="'+val+'"';
				else if(key == 'val')
					attr += ' value="'+val+'"';
				else if(key == 'oval')
					attr += ' value="'+val+'"';
				else if(key == 'value')
					attr += ' value="'+val+'"';
				else if(key == 'onkeyup')
					attr += ' onkeyup="'+val+'"';
			}
		}
	});
	return attr;
}

function gen_years()
{
	var date = new Date();
	var eyear = date.getFullYear();
	var fyear = eyear - 20;
	var years = new Array();
	var ix = 0;
	for (var i = fyear; i < eyear; i++) {
		years[ix++] = i;
	};
	return years;
}

function force_lower_case(itar)
{
	$(itar).val($(itar).val().toLowerCase());
}

function force_upper_case(itar)
{
	$(itar).val($(itar).val().toUpperCase());
}

function confirm_action(msg,link)
{
	var res = confirm(msg);
	if(res) document.location = link;
}