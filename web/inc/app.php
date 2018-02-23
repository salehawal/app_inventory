<?php
/***********************
	
	system config

***********************/
function sys_init()
{
	global $pdata;
	session_start();
	sys_config();
	// load user config
	if(!empty($_SESSION['user']))
	{
		$pdata['user'] = $_SESSION['user'];
		// configure location
		update_location();
	}
}

function sys_config()
{
	global $sysdata, $pdata;
	$sysdata['conn'] = db_connect();
	// users
	$sysdata['user_pfx'] 		= 'fu';
	$sysdata['user_table'] 		= 'fict_users';
	$sysdata['user_id_field'] 	= 'fu_code';
	// location
	$sysdata['location_pfx'] 	= 'lo';
	$sysdata['location_table'] 	= 'fict_location';
	$sysdata['flo_code']		= 'flo_code';

	$pdata['conn'] = $sysdata['conn'];
}

/***********************

	page

***********************/
function p_init()
{
	global $pdata;
	if(empty($pdata['page']))
		$table = find_table();
	else
		$table = $pdata['page']['tname'];
	//general page config
	$pdata['showmenu'] = true;
	$pdata['view']['mode'] = '2';
	$pdata['view']['cols'] = '3';
	// login status
	if(!isset($_SESSION['user']))	$_SESSION['user']['login'] 	= false;
	// load page config
	if($table != false)
		page_config($table);
	else
		header("location: main.php");
	// get item detail on update
	if(isset($_GET['iid']) && !isset($_POST['locationid']))
		$pdata['page']['item'] = get_item_data();
}

function page_config($table)
{
	global $pdata;
	// get column data
	$pdata['page']['table'] 	= get_table_columns($table);
	$field 						= $pdata['page']['table'][0]['column_name'];
	$pfx 						= explode('_', $field);
	$section					= explode('_', $table);

	$pdata['page']['pfx'] 		= $pfx[0];
	$pdata['page']['section'] 	= strtolower($section[1]);
	$pdata['page']['tname']		= $table;
	$pdata['page']['pname'] 	= $section[1];
	$pdata['page']['tidfield'] 	= $field;
	if(isset($_GET['section'])) $pdata['page']['vcols']   = get_view_fields();
	// static config
	page_static_config($table);
}

/***********************

	user

***********************/
function user_login()
{
	global $pdata;
	if(isset($_POST['username'],$_POST['password']))
	{
		// params
		$params['fu_username'] = array(':username', clean_sql($_POST['username']));
		$params['fu_password'] = array(':password', clean_sql($_POST['password']));
		$count 	= db_num_rows('fict_users', $params);
		if($count > 0)
		{
			$pq = get_sql_query($params);
			// sql
			$q = "select * from fict_users".$pq;
			// prepare & bind
			$stid 	= $pdata['conn']->prepare($q);
			$stid 	= db_bind_all($stid,$params);
			$stid->execute();
			$row = $stid->fetch(PDO::FETCH_ASSOC);
			$_SESSION['user']['login'] 	= true;
			$_SESSION['user']['data']	= $row;
			user_login_log();
			header("location: main.php");
		}
		elseif(isset($_POST['username']) && trim($_POST['username']) != '')
		{
			$id = add_user();
			if($id == null)
			{
				$row['fu_code'] = '999';
				$row['fu_username'] = $_POST['username'];
			}
			else
			{
				$row = get_user_by_id($id);
			}
			$_SESSION['user']['login'] 	= true;
			$_SESSION['user']['data']	= $row;
			user_login_log();
			header("location: main.php");
		}
		else
		{
			header("location: login.php?action=error");
		}
	}
}

function user_login_log()
{
	global $pdata;
	// add log to database

	// register user
	$pdata['user'] = $_SESSION['user'];
}

function user_login_check()
{
	if(!$_SESSION) session_start();
	if(!$_SESSION['user']['login'])
		header("location: login.php?action=login");
}

function user_logout()
{
	session_start();
	session_destroy();
	header("location: login.php");
}

function add_user()
{
	global $sysdata;
	if(strlen(trim($_POST['password'])) > 0)
	{
		$uid  	= get_next_id($sysdata['user_pfx'], $sysdata['user_table'], $sysdata['user_id_field']);
		$q 		= "insert into fict_users (fu_code, fu_username, fu_username, fu_password, fu_id) values (:fu_code, :fu_username, :fu_username, :fu_password, :fu_id)";
		$st 	= $sysdata['conn']->prepare($q);
		$user 	= clean_sql($_POST['username']);
		$pass 	= clean_sql($_POST['password']);
		$st->bindValue(':fu_code',$uid);
		$st->bindValue(':fu_username',$user);
		$st->bindValue(':fu_username', $user);
		$st->bindValue(':fu_password', $pass);
		$st->bindValue(':fu_id',$uid);
		$st->execute();
		return $uid;
	}
	else
	{
		return null;
	}
}

function get_user_by_id($id)
{
	global $pdata;
	$q 	= "select * from fict_users where fu_code = ".$id;
	$st = $pdata['conn']->prepare($q);
	$st->execute();
	$row = $st->fetch(PDO::FETCH_ASSOC);
	if(count($row) > 0)
		return $row;
	else
		return false;
}

/***********************

	location

***********************/
function update_location()
{
	global $pdata;
	if(isset($_POST['new_location']) && !empty($_POST['new_location']))
	{
		$loc = add_location();
		$_SESSION['loc'] = $pdata['loc'] = $loc;
		header("location: main.php");
	}
	elseif(isset($_POST['locationid']) && !empty($_POST['locationid']) && !isset($_POST['form-status']))
	{
		$loc = get_location_details($_POST['locationid']);
		$_SESSION['loc'] = $pdata['loc'] = $loc;
		header("location: main.php");
	}
	else
	{
		if(isset($_SESSION['loc']) && !empty($_SESSION['loc']))
			$pdata['loc'] = $_SESSION['loc'];
		elseif(empty($pdata['loc']))
			$pdata['loc'] = $_SESSION['loc'] = false;
	}

	// send to login if empty
	if($pdata['loc'] == false)
		if($_SESSION['loc'])
			header("location: login.php");
}

function add_location()
{
	global $sysdata;
	if(!empty($_POST['new_location']))
	{
		$fl = find_location();
		if($fl)
		{
			$lid 		= get_next_id($sysdata['location_pfx'], $sysdata['location_table'], $sysdata['flo_code']);
			$q 			= "insert into fict_location (flo_code, flo_address) values (:flo_code, :flo_address)";
			$st 		= $sysdata['conn']->prepare($q);
			$laddress 	= clean_sql($_POST['new_location']);
			$st->bindValue(':flo_code',$lid);
			$st->bindValue(':flo_address',$laddress);
			$rs = $st->execute();
			if($rs)
				return get_location_details($lid);
		}
	}
	else return false;
}

function find_location()
{
	global $pdata;
	$nlocation = clean_sql($_POST['new_location']);
	$q = "select * from fict_location where flo_address like '".$nlocation."'";
	$st = $pdata['conn']->prepare($q);
	$st->execute();
	$row = $st->fetch(PDO::FETCH_ASSOC);
	if(empty($row))
		return true;
	else
		return false;
}

function get_locations()
{
	global $pdata;
	$q 	= "select * from fict_location";
	$st = $pdata['conn']->prepare($q);
	$st->execute();
	while($row = $st->fetch(PDO::FETCH_ASSOC))
		$locations[] = $row;
	if(empty($locations))
		return false;
	else
		return $locations;
}

function get_location_details($lid)
{
	global $pdata;
	$q = "select * from fict_location where flo_code = ".$lid;
	$st = $pdata['conn']->prepare($q);
	//$st->bindValue(':flo_code', $lid);
	$st->execute() or die(print_r($st->errorinfo()));
	$row = $st->fetch(PDO::FETCH_ASSOC);
	if(is_array($row))
		return $row;
	else
		return array('flo_code'=>$lid,'flo_address'=>$lid);
}

/***********************

	database

***********************/

function get_next_id($pfx = null, $table,$field)
{
	global $pdata;
	if(!$pfx) return false;
	return $pfx.gen_id();
}

function no_tables()
{
	return array('fict_users', 'fict_location', 'fict_images', 'fict_options', 'fict_options_detail', 'fict_condition', 'fict_fixed_cameras');
}

function get_tables()
{
	global $pdata;
	$ntables 	= no_tables();
	$notin 		= get_sql_items($ntables);
	$nisql 		= " and table_name not in ".$notin;
	$q 			= "select table_name from information_schema.tables where table_schema = 'pr_inventory'".$nisql;

	$stid 		= $pdata['conn']->prepare($q);
	$stid->execute();
	while($row 	= $stid->fetch(PDO::FETCH_ASSOC))
		$tables[] = $row;

	return $tables;
}

function get_view_fields()
{
	global $pdata;
	foreach ($pdata['page']['table'] as $key => $col)
	{
		$column = explode('_',$col['column_name']);
		if(isset($column[1]))
			if(isset($pdata['view']))
			{
				if($key == $pdata['view']['cols'])
					break;
				else
					$vcols[] = array($col['column_name'],$column[1]);
			}
			else
				$vcols[] = array($column[1],$column[1]);
	}
	return $vcols;
}

function find_table()
{
	global $pdata;
	if(isset($_GET['section']))
	{
		$tables = get_tables();
		foreach ($tables as $key => $table)
		{
			$tname = explode('_',$table['table_name']);
			$secname = strtolower($tname[1]);
			if($secname == $_GET['section'])
				return $table['table_name'];
		}
		return false;
	}
	return false;
}

function get_main_tables()
{
	global $pdata;
	$tables = get_tables();
	$tdata = array();
	foreach ($tables as $key => $table)
	{
		// load page config to get table details
		page_config($table['table_name']);
		$tdata[$key]['order'] 	= $pdata['page']['order'];
		$tdata[$key]['section'] = $pdata['page']['section'];
		$tdata[$key]['pname'] 	= $pdata['page']['pname'];
	}
	$tdata = sort_tables($tdata);
	// remove last generated table config
	unset($pdata['page']);
	return $tdata;
}

function sort_tables($tdata)
{
	array_multisort($tdata, SORT_ASC);
	usort($tdata, "sort_tables_compare");
	return $tdata;
}

function sort_tables_compare($a, $b)
{
  return strcmp($a["section"], $b["section"]);
}

function check_allowed_column($tablename, $colname)
{
	$keys = array('_image');
	$nocols = no_columns($tablename);
	if(is_array($nocols))
	{
		foreach ($nocols as $key => $val)
			array_push($keys,$val);
	}
	foreach ($keys as $k => $v)
	{
		if(strpos($colname,$v) !== false )
			return false;
		elseif($colname == $v)
			return false;	
	}
	return true;
}

function get_table_columns($tablename)
{
	global $pdata;
	if($tablename != null)
	{
		$q 		= "select table_name from information_schema.tables where table_name = '".$tablename."'";
		$stid 	= $pdata['conn']->prepare($q);
		$stid->execute();
		$row = $stid->fetch(PDO::FETCH_ASSOC);

		// table fields
		if(count($row) > 0)
		{
			$cq = "select column_name,column_type from information_schema.columns where table_schema = 'pr_inventory' and table_name = '".$row['table_name']."'";
			$st = $pdata['conn']->prepare($cq);
			$st->execute();
			while($rowc = $st->fetch(PDO::FETCH_ASSOC))
			{
				// get column type and length
				preg_match_all('!\d+!', $rowc['column_type'], $matches);
				if(is_array($matches) && isset($matches[0][0]))
				{
					$rowc['data_type'] 	= substr($rowc['column_type'], 0, strpos($rowc['column_type'], '('));
					$rowc['data_length'] = $matches[0][0];
				}
				else
				{
					$rowc['data_type'] 	= $rowc['column_type'];
					$rowc['data_length'] = 0;
				}
				unset($rowc['column_type']);
				// check allowed
				$checkallowed = check_allowed_column($row['table_name'], $rowc['column_name']);
				if($checkallowed)
					$table[] = $rowc;
			}
		}
		return $table;
	}
	else
		return false;
}

function gen_sql_params()
{
	global $pdata;
	foreach ($pdata['page']['table'] as $key => $value) 
	{
		$fkey = array_key_exists($value['column_name'], $_POST);
		if($fkey && $_POST[$value['column_name']] != null)
				$params[$value['column_name']] = array(":".$value['column_name'], $_POST[$value['column_name']]);
	}
	if(isset($params))
		return $params;
	else
		return false;
}

function gen_sql_select_fields()
{
	global $pdata;
	foreach ($pdata['page']['table'] as $key => $value) 
		$params[$value['column_name']] = array($value['column_name'], $value['data_type']);
	if(isset($params))
		return $params;
	else
		return false;
}

function get_column_info($col)
{
	// defualt params
	$col = guess_column_info($col);
	// database options values
	$col = get_field_vals($col);
	// load static column info
	$col = get_column_static_info($col);
	// return column info
	return $col;
}

function get_clean_column_name($name)
{
	global $pdata;
	// find in static configuration
	foreach ($pdata['page']['vcols'] as $key => $col)
		if($name == $col[0])
			return $col[1];
	// get column prefix string
	if(substr_count($name,'_'))
		$cpfx = explode('_',$name);
	else
		$cpfx = false;
	// remove column frefix of exists
	if(isset($cpfx[0]))
		$name = str_replace($cpfx[0].'_','',$name);
	// replace _ with space
	$name = str_replace('_', ' ', $name);

	return $name;
}

function get_field_vals($col)
{
	global $pdata;
	$q = "select * from fict_options where fo_column='".$col['column_name']."'";
	$st = $pdata['conn']->prepare($q);
	$st->execute();

	$row = $st->fetch(PDO::FETCH_ASSOC);
	if(!empty($row))
	{
		// set params
		$col['iname'] 	= $row['fo_column'];
		$col['itype'] 	= $row['fo_column_type'];
		$col['iid'] 	= $row['fo_column'];

		$q = "select * from fict_options_detail where fo_code='".$row['fo_code']."'";
		$st = $pdata['conn']->prepare($q);
		$st->execute();
		while($rowc = $st->fetch(PDO::FETCH_ASSOC))
		{
			$key = str_replace(' ', '_', $rowc['fo_options_value']);
			$col['vals'][$key] = $rowc['fo_options_name'];
		}
	}
	return $col;
}

function guess_column_info($col)
{
	global $pdata;
	if($col['data_type'] == 'varchar' || $col['data_type'] == 'int')
	{
		if($col['data_length'] > 100)
			$col['itype'] 	= 'textarea';
		else
			$col['itype'] 	= 'text';
	}
	elseif($col['data_type'] == 'date')
	{
		$col['itype'] = 'date';
	}
	elseif($col['data_type'] == 'blob')
	{
		$pdata['page']['img_field'] = $col['column_name'];
		return false;
	}
	// default
	$col['iid'] 	= $col['column_name'];
	$col['iname'] 	= $col['column_name'];
	$col['size']   	= $col['data_length'];
	return $col;
}

/***********************

	item

***********************/
function get_item_data()
{
	global $pdata;
	// generate params
	$params = gen_sql_select_fields();
	$fq 	= get_sql_select_fields($params); 
	// data
	$q = "select ".$fq." from ".$pdata['page']['tname']." where ".$pdata['page']['tidfield']." = '".$_GET['iid']."'";
	$st = $pdata['conn']->prepare($q);
	$st->execute();
	$row = $st->fetch(PDO::FETCH_ASSOC);
	// images
	$row['images'] = get_images($row[$pdata['page']['tidfield']]);
	return $row;
}

function search_item($iid)
{
	global $pdata;
	$q 		= "select table_name from information_schema.tables where table_schema = 'pr_inventory'";
	$stid 	= $pdata['conn']->prepare($q);
	$stid->execute();
	while($row = $stid->fetch(PDO::FETCH_ASSOC))
	{
		// get field name
		$cq = "select column_name from information_schema.columns where table_schema = 'pr_inventory' and table_name ='".$row['table_name']."' and column_name like '%_code'";
		$st = $pdata['conn']->prepare($cq);
		$st->execute();
		$rowc = $st->fetch(PDO::FETCH_ASSOC);
		$field = $rowc['column_name'];

		// check iid
		$cq = "select * from ".$row['table_name']." where ".$field." = '".$iid."'";
		$st = $pdata['conn']->prepare($cq);
		$st->execute();
		$rowi = $st->fetch(PDO::FETCH_ASSOC);
		if($rowi)
			return array($row,$rowc,$rowi,$iid);
	}
	return false;
}

function add_item()
{
	global $pdata;
	// add post values
	$params = gen_sql_params();
	// sql
	if(isset($_POST['action']) && $_POST['action'] == 'add')
	{
		//page_static_config();
		$pfx 								= $pdata['page']['pfx'];
		$pdata['page']['iid']  				= get_next_id($pfx,$pdata['page']['tname'],$pdata['page']['tidfield']);
		$params[$pdata['page']['tidfield']] = array(':'.$pdata['page']['tidfield'],$pdata['page']['iid']);
		$q 									= get_sql_insert($params);
		$q 									= "insert into ".$pdata['page']['tname']." ".$q;
	}
	elseif(isset($_POST['action']) && $_POST['action'] == 'update')
	{
		$pdata['page']['iid']  				= $_POST['iid'];
		$q 									= get_sql_update($params);
		$params[$pdata['page']['tidfield']] = array(':'.$pdata['page']['tidfield'],$pdata['page']['iid']);
		$q 									= "update ".$pdata['page']['tname']." set ".$q." where ".$pdata['page']['tidfield']." = :".$pdata['page']['tidfield'];
	}
	else
		exit;
	//$st->debugdumpparams();
	$rs = add_multiple_images();
	if($rs)
	{
		// prepare & bind
		$st = $pdata['conn']->prepare($q);
		$st = db_bind_all($st,$params);
		// execute
		$st->execute() or die(print_r($st->errorinfo()));
	} 
	else {  echo 'FAILED...!'; exit;  }
	header("location: item.php?section=".$pdata['page']['section']."&iid=".$pdata['page']['iid']);
}

function view_item()
{
	global $pdata;
	// feed values if item not new
	if(isset($_GET['iid'])) view_item_update();
	// show item from
	foreach($pdata['page']['table'] as $key => $col) if(check_allowed_field($col['column_name'])) write_field($col);
}

function view_item_update()
{
	global $pdata;
	foreach ($pdata['page']['table'] as $key => $val)
	{
		$fkey = array_key_exists($val['column_name'], $pdata['page']['item']);
		if($fkey) $pdata['page']['table'][$key]['value'] = $pdata['page']['item'][$val['column_name']];
	}
}

function write_field($col)
{
	$col['i'] 		= get_column_info($col);
	if(is_array($col['i']))
	{
		$object 	= write_object($col['i']);
		$lname 		= (isset($col['i']['lname']))?$col['i']['lname']:get_clean_column_name($col['i']['iname']);
		echo "gen_block('".$lname."', ".$object.");\n";
	}
}

function write_object($arr)
{
	$data = '{';
	$i = 1;
	$c = count($arr);
	if(is_array($arr))
	{
		foreach ($arr as $key => $value)
		{
			if(no_fields($key))
			{
				$i++;
				continue;
			}
			
			if(is_array($value))
			{
				$obj = write_object($value);
				$data .= $key.":".$obj;
			}
			elseif($value != null)
			{
				$obj = $value;
				$data .= $key.":'".$obj."'";
			}
			else
			{
				$i++;
				continue;
			}

			if($i < $c)
				$data .= ', ';
			$i++;
		}
	}
	$data .= '}';
	return $data;
}

function check_allowed_field($field)
{
	$keys = array('_code','_status');
	foreach ($keys as $k => $v)
	{
		if(strpos($field,$v) !== false || strpos($field,strtolower($v)) !== false)
			return false;
	}
	return true;
}

function no_fields($field)
{
	$props = array('column_name','data_type','data_length');
	foreach ($props as $key => $val)
	{
		if($val == $field && !is_numeric($field))
			return true;
	}
	return false;
}

function get_items()
{
	global $pdata;
	$q = "select * from ".$pdata['page']['tname']." order by ".$pdata['page']['tidfield']." desc limit 50";
	$stid = $pdata['conn']->prepare($q);
	$stid->execute();
	
	while($row = $stid->fetch(PDO::FETCH_ASSOC))
		$data[] = $row;

	if(empty($data))
		$pdata['items'] = false;
	else
		$pdata['items'] = $data;

	// generate header info
	$pdata['data']['th'] = gen_view_header();
	// generate data info
	$pdata['data']['td'] = gen_view_data();
}

function find_item()
{
	global $pdata;
	print_r($_POST);
	print_r($_GET);
	if(isset($_POST['iid']) && strlen($_POST['iid']) == 10)
	{
		$pfx = strtolower(substr($_POST['iid'],0,2));
		$tinfo = get_table_pfx($pfx);
		page_config($tinfo[1]);
		if(is_array($pdata['page']['table']))
		{
			$q = "select ".$pdata['page']['tidfield']." from ".$pdata['page']['tname']." where ".$pdata['page']['tidfield']." = '".$_POST['iid']."'";
			$st = $pdata['conn']->prepare($q);
			$rs = $st->execute();
			if($rs)
			{
				$r = $st->fetch(PDO::FETCH_ASSOC);
				if(is_array($r))
					header("location: item.php?section=".$pdata['page']['section']."&iid=".$r[$pdata['page']['tidfield']]);
				else
					header("location: find.php?action=notfound&reason=notindb");
				return;
			}
		}
	}
	else
		header("location: find.php?action=notfound&reason=falseid");
}

function remove_item($table,$field,$id)
{
	global $pdata;
	$q = "delete from ".$table." where ".$field." = '".$id."'";
	$stid = $pdata['conn']->prepare($q);
	$rs = $stid->execute();
	if($rs)
	{
		// remove related images
		$q = "delete from fict_images where fim_ref_code ='".$id."'";
		$st = $pdata['conn']->prepare($q);
		$st->execute();
	}
}

function show_item($table,$field,$id)
{
	global $pdata;
	$q   = "select * from ".$table." where ".$field."='".$id."'";
	$st  = $pdata['conn']->prepare($q);
	$rs  = $st->execute();
	$row = $st->fetch(PDO::FETCH_ASSOC);
	if(gettype($row) == 'array')
		return $row;
	else
		return false;
}

function gen_view_header()
{
	global $pdata;
	$hdata = $pdata['page']['vcols'];
	$idata = $pdata['items'];

	$code  = '<tr>';
	$code .= '<th>view</th>';
	foreach ($hdata as $key => $value)
	{
		if($key == $pdata['view']['cols'])
			break;
		else
			$code .= '<th>'.$value[1].'</th>'."\n";
	}
	$code .= '<th>remove</th>';
	// get data
	if(!empty($idata))
		$tdata = gen_view_data();
	else
		$tdata = gen_empty_row();
	$code .= '</tr>';
	return $code;
}

function gen_view_data()
{
	global $pdata;
	$rows = '';
	// column filter
	$hdata = $pdata['page']['vcols'];
	// items data
	$idata = $pdata['items'];

	if(!empty($idata))
	{
		foreach ($idata  as $key => $val)
		{
			// check status
			if($val['status'] == 'n')
				$code = '<tr class="danger">';
			else
				$code = '<tr>';
			// add view link
			$code .= '<td class="icon"><a href="item.php?section='.$pdata["page"]["section"].'&iid='.$val[$pdata["page"]["tidfield"]].'"><img src="img/view.png"></a></td>';
			foreach ($val as $k => $v) {
				foreach ($hdata as $hx => $hv) {
					if($k == $hv[0])
						$code .= '<td>'.$v.'</td>'."\n";
				}
			}
			$code .= '<td class="icon"><a href="#" onclick="confirm_action(\'delete this item('.$val[$pdata["page"]["tidfield"]].')\',\'list_item.php?section='.$pdata["page"]["section"].'&iid='.$val[$pdata["page"]["tidfield"]].'&action=del\');"><img src="img/delete.png"></a></td>';
			$code .= '</tr>';
			$rows .= $code."\n\n";
		}
	}
	else
	{
		$rows = gen_empty_row();
	}
	return $rows;
}

function gen_empty_row()
{
	global $pdata;
	$cols = 2;
	$code = '<tr>';
	foreach ($pdata['page']['vcols'] as $key => $val)
	{
		if($key == $pdata['view']['cols'])
			break;
		else
			$cols++;
	}
	$code .= '<td colspan="'.$cols.'" class="empty">no records</td>';
	$code .= '</tr>';
	return $code;
}

function add_options($colname, $type, $val, $aindex = false)
{
	global $sysdata;
	// check if option exists
	$q = "select * from fict_options where fo_column = '".$colname."'";
	$st = $sysdata['conn']->prepare($q);
	$st->execute();
	$r = $st->fetch(PDO::FETCH_ASSOC);
	if(is_array($r))
	{
		if($r['fo_column_type'] == $type)
		{
			add_option_values($r['fo_code'], $val, $aindex);
		}
		else
		{
			$q 	= "update fict_options set fo_column_type = :fo_column_type where fo_code = :fo_code";
			$st = $sysdata['conn']->prepare($q);
			$st->bindValue(':fo_code', $r['fo_code']);
			$st->bindValue(':fo_column_type', $type);
			$st->execute();
		}
	}
	else
	{
		// insert new option
		$foid 	= get_next_id('fo', 'fict_options', 'fo_code');
		$q 		= "insert into fict_options (fo_code, fo_column, fo_column_type) values (:fo_code, :fo_column, :fo_column_type)";
		$st 	= $sysdata['conn']->prepare($q);
		$st->bindValue(':fo_code', 			$foid);
		$st->bindValue(':fo_column', 		$colname);
		$st->bindValue(':fo_column_type', 	$type);
		$rs = $st->execute() or die(print_r($st->errorinfo()));
		if($rs) add_option_values($foid, $val, $aindex);
	}
}

function add_option_values($oid, $val, $aindex)
{
	if(is_array($val))
	{
		foreach ($val as $k => $v)
		{
			if($aindex)
				add_option_val($oid, $v, $k); // add key and value
			else
				add_option_val($oid, $v, $v); // add value and value
		}
	}
}

function add_option_val($oid, $dname, $dval)
{
	global $sysdata;
	$q = "select * from fict_options_detail where fo_code = :fo_code and fo_options_name = :fo_options_name";
	$st = $sysdata['conn']->prepare($q);
	$st->bindValue(':fo_code',$oid);
	$st->bindValue(':fo_options_name',$dname);
	$st->execute();
	$r = $st->fetch(PDO::FETCH_ASSOC);
	if(!is_array($r))
	{
		$q = "insert into fict_options_detail (fo_code, fo_options_name, fo_options_value) values (:fo_code, :fo_options_name, :fo_options_value)";
		$st = $sysdata['conn']->prepare($q);
		$st->bindValue(':fo_code',$oid);
		$st->bindValue(':fo_options_name',$dname);
		$st->bindValue(':fo_options_value',$dval);
		$st->execute();
	}
	elseif($r['fo_options_value'] != $dval)
	{
		$q = "update fict_options_detail  set fo_options_value = :fo_options_value where fo_code = :fo_code and fo_options_name = :fo_options_name";
		$st = $sysdata['conn']->prepare($q);
		$st->bindValue(':fo_options_name', $dname);
		$st->bindValue(':fo_options_value', $dval);
		$st->bindValue(':fo_code', $r['fo_code']);
		$st->execute();
	}
}

/***********************

	image

***********************/
function reorder_images($files)
{
	$idx = 0; $images = array();
	foreach ($files as $key => $param)
	{
		if(is_array($param))
		{
			foreach ($param as $k => $val) 
			{
				$images[$idx][$key] = $val;
				$idx++;
			}
			$idx=0;
		}
		else
			return false;
	}
	return $images;
}

function add_multiple_images()
{
	global $pdata;
	$images = reorder_images($_FILES['images']);
	if(is_array($images))
	{
		foreach ($images as $key => $imgf) {
			if(is_array($imgf) == 'array' && $imgf['error'] == 0)
			{
				$rs = add_image($imgf);
				if(!$rs) return false;
			}
		}
		return true;
	}
	return false;
}

function add_image($img_file)
{
	global $pdata;
	$id = get_next_id('im','fict_images','fim_code');
	// sql
	$q 	= "insert into fict_images (fim_code,fim_ref_code,fim_image) values (:fim_code, :fim_ref_code, :fim_image) ";
	// prepare & bind
	$stid = $pdata['conn']->prepare($q);
	$stid->bindValue(':fim_code', $id);
	$stid->bindValue(':fim_ref_code', $pdata['page']['iid']);
	// read binadry
	$image 	= fopen($img_file['tmp_name'], 'rb');
	// insert image
	$stid->bindValue(':fim_image', $image, PDO::PARAM_LOB);
	$pdata['conn']->beginTransaction();
	$rs = $stid->execute();
	$pdata['conn']->commit();
	if($rs)
		return true;
	else
		return false;
}

function remove_image($img_id)
{
	global $pdata;
	$q = "delete from fict_images where fim_code = :fim_code";
	$stid = $pdata['conn']->prepare($q);
	$stid->bindValue(':fim_code', $img_id);
	if($stid->execute()) return true; else return false;
}

function get_images($iid)
{
	global $pdata;
	$images = array();
	// sql
	$q = "select * from fict_images where  fim_ref_code = '".$iid."'";
	//echo $q; exit;
	// prepare & bind
	$st = $pdata['conn']->prepare($q);
	$st->execute();
	while($row = $st->fetch(PDO::FETCH_ASSOC))
		$images[] = $row;

	if(gettype($images) == 'array')
		return $images;
	else
		return false;
}

/***********************

	configuration

***********************/

function get_table_pfx($tinfo)
{
	global $pdata;
	$pfxs = table_pfxs();

	foreach ($pfxs as $key => $val)
		if($tinfo == $key || $tinfo == $val)
			return array($key,$val);
	return false;
}

function table_pfxs()
{
	$pfxs = array('vh'=>'fict_vehicles',
	'ap'=>'fict_approach',
	'bc'=>'fict_battery_charger',
	'fc'=>'fict_cameras_rl',
	'sc'=>'fict_scripts',
	'sn'=>'fict_sensors',
	'bd'=>'fict_buildings',
	'db'=>'fict_databases',
	'fu'=>'fict_flash_bulbs',
	'ms'=>'fict_mobile_speed_systems',
	'fs'=>'fict_fixed_speedsystem',
	'ga'=>'fict_gaps',
	'pc'=>'fict_portable_cameras',
	'pu'=>'fict_pucks',
	'rl'=>'fict_redlight_systems',
	'dc'=>'fict_documents',
	'ts'=>'fict_traffic_signal_control',
	'bt'=>'fict_batteries',
	'hd'=>'fict_hardware',
	'sf'=>'fict_software',
	'cb'=>'fict_cabinets',
	'fu'=>'fict_flash_units',
	'nt'=>'fict_networks',
	'oe'=>'fict_office_equipment');
	return $pfxs;
}

function no_columns($table)
{
	$noc['fict_vehicles'][]	 		= 'fv_chassis_no';
	$noc['fict_vehicles'][]	 		= 'fv_wheels';
	$noc['fict_vehicles'][]	 		= 'fv_engine';
	$noc['fict_vehicles'][]	 		= 'fv_seats';
	$noc['fict_vehicles'][]	 		= 'fv_battery';
	$noc['fict_vehicles'][]	 		= 'fv_battery_id';
	$noc['fict_vehicles'][]	 		= 'fv_assigned_driver';
	$noc['fict_vehicles'][]	 		= 'fv_status';
	$noc['fict_vehicles'][]	 		= 'fv_remarks';
	$noc['fict_vehicles'][]	 		= 'status';
	$noc['fict_vehicles'][]	 		= 'fv_cre_date';
	$noc['fict_vehicles'][]	 		= 'fv_cre_by';
	$noc['fict_vehicles'][]	 		= 'fv_update_date';
	$noc['fict_vehicles'][]	 		= 'fv_update_by';
	$noc['fict_vehicles'][]	 		= 'fv_color';
	$noc['fict_vehicles'][]	 		= 'fv_plate_no';
	$noc['fict_vehicles'][]	 		= 'fv_image';
	$noc['fict_vehicles'][]	 		= 'fv_valid_annual_insp_card';
	$noc['fict_vehicles'][]	 		= 'fv_all_door_and_equip_work';
	$noc['fict_vehicles'][]	 		= 'fv_window_clean_free_obstruct';

	$noc['fict_buildings'][] 		= 'fb_type';
	$noc['fict_buildings'][] 		= 'fb_desc';
	$noc['fict_buildings'][] 		= 'fb_dept';
	$noc['fict_buildings'][] 		= 'fb_remarks';
	$noc['fict_buildings'][] 		= 'fb_location';
	$noc['fict_buildings'][] 		= 'fb_covered_area';
	$noc['fict_buildings'][] 		= 'fb_owner';
	$noc['fict_buildings'][] 		= 'fb_cre_date';
	$noc['fict_buildings'][] 		= 'fb_cre_by';
	$noc['fict_buildings'][] 		= 'status';
	$noc['fict_buildings'][] 		= 'fb_image';
	
	$noc['fict_redlight_systems'][] = 'frs_remarks';
	$noc['fict_redlight_systems'][] = 'frs_camera_unit';
	$noc['fict_redlight_systems'][] = 'status';
	$noc['fict_redlight_systems'][] = 'frs_cre_date';
	$noc['fict_redlight_systems'][] = 'frs_cre_by';
	$noc['fict_redlight_systems'][] = 'frs_update_date';
	$noc['fict_redlight_systems'][] = 'frs_update_by';
	$noc['fict_redlight_systems'][] = 'frs_image';
	
	$noc['fict_approach'][] 		= 'status';
	$noc['fict_approach'][] 		= 'fapp_cre_by';
	$noc['fict_approach'][] 		= 'fapp_cre_date';
	$noc['fict_approach'][] 		= 'fapp_remarks';

	$noc['fict_fixed_speedsystem'][] = 'status';
	$noc['fict_fixed_speedsystem'][] = 'ffss_cre_by';
	$noc['fict_fixed_speedsystem'][] = 'ffss_cre_date';
	$noc['fict_fixed_speedsystem'][] = 'ffss_update_by';
	$noc['fict_fixed_speedsystem'][] = 'ffss_update_date';
	$noc['fict_fixed_speedsystem'][] = 'ffss_camera_sensor_unit_serial';
	$noc['fict_fixed_speedsystem'][] = 'ffss_flash_unit_serial_number';

	$noc['fict_portable_cameras'][] = 'fpc_id';
	$noc['fict_portable_cameras'][] = 'fpc_battery';
	$noc['fict_portable_cameras'][] = 'fpc_remarks';
	$noc['fict_portable_cameras'][] = 'fpc_location';
	$noc['fict_portable_cameras'][] = 'fpc_cabinet';
	$noc['fict_portable_cameras'][] = 'status';
	$noc['fict_portable_cameras'][] = 'fpc_cre_date';
	$noc['fict_portable_cameras'][] = 'fpc_cre_by';
	$noc['fict_portable_cameras'][] = 'fpc_update_date';
	$noc['fict_portable_cameras'][] = 'fpc_update_by';
	$noc['fict_portable_cameras'][] = 'fpc_system_manufact_specs';
	$noc['fict_portable_cameras'][] = 'fpc_radar';
	$noc['fict_portable_cameras'][] = 'fpc_vh_violation_rate_90';
	$noc['fict_portable_cameras'][] = 'fpc_vh_class_capability';
	$noc['fict_portable_cameras'][] = 'fpc_test_outcome';
	$noc['fict_portable_cameras'][] = 'fpcu_equip_installed_properly';
	$noc['fict_portable_cameras'][] = 'fpcu_equipments_new';
	$noc['fict_portable_cameras'][] = 'spareparts_availability';
	$noc['fict_portable_cameras'][] = 'spareparts_quantity';
	$noc['fict_portable_cameras'][] = 'sparepart_compatible';
	$noc['fict_portable_cameras'][] = 'fpc_cabinet_housing_condition';


	$noc['fict_mobile_speed_systems'][] = 'fmss_power_type';
	$noc['fict_mobile_speed_systems'][] = 'fmss_mt_rack_operational';
	$noc['fict_mobile_speed_systems'][] = 'status';
	$noc['fict_mobile_speed_systems'][] = 'fmss_cre_date';
	$noc['fict_mobile_speed_systems'][] = 'fmss_cre_by';
	$noc['fict_mobile_speed_systems'][] = 'fmss_update_date';
	$noc['fict_mobile_speed_systems'][] = 'fmss_update_by';
	$noc['fict_mobile_speed_systems'][] = 'fmss_camera_serial_no';
	$noc['fict_mobile_speed_systems'][] = 'fmss_remarks';

	$noc['fict_cabinets'][] 				= 'fict_cabinets';
	$noc['fict_cabinets'][] 				= 'fcab_id';
	$noc['fict_cabinets'][] 				= 'status';
	$noc['fict_cabinets'][] 				= 'fcab_cre_by';
	$noc['fict_cabinets'][] 				= 'fcab_cre_date';
	$noc['fict_cabinets'][] 				= 'fcab_remarks';

	$noc['fict_cameras_rl'][] 			= 'fcrl_location';
	$noc['fict_cameras_rl'][] 			= 'fcrl_typeofcamera';
	$noc['fict_cameras_rl'][] 			= 'spareparts_availability';
	$noc['fict_cameras_rl'][] 			= 'spareparts_quantity';
	$noc['fict_cameras_rl'][] 			= 'sparepart_compatible';
	$noc['fict_cameras_rl'][] 			= 'status';
	$noc['fict_cameras_rl'][] 			= 'fcrl_cre_by';
	$noc['fict_cameras_rl'][] 			= 'fcrl_cre_date';
	$noc['fict_cameras_rl'][] 			= 'fcrl_remarks';

	$noc['fict_sensors'][] 				= 'fict_sensors';
	$noc['fict_sensors'][] 				= 'status';
	$noc['fict_sensors'][] 				= 'fsns_cre_by';
	$noc['fict_sensors'][] 				= 'fsns_cre_date';
	$noc['fict_sensors'][] 				= 'fsns_location';
	$noc['fict_sensors'][] 				= 'fsns_remarks';

	$noc['fict_flash_units'][]				= 'ffu_desc';
	$noc['fict_flash_units'][]				= 'ffu_ffc_code';
	$noc['fict_flash_units'][]				= 'ffu_fpc_code';
	$noc['fict_flash_units'][]				= 'ffu_location';
	$noc['fict_flash_units'][]				= 'ffu_remarks';
	$noc['fict_flash_units'][]				= 'ffu_cre_by';
	$noc['fict_flash_units'][]				= 'ffu_cre_date';
	$noc['fict_flash_units'][]				= 'status';
	$noc['fict_flash_units'][]				= 'ffu_type';
	$noc['fict_flash_units'][]				= 'ffu_spare_parts_availability';
	$noc['fict_flash_units'][]				= 'ffu_spare_parts_quantity';
	$noc['fict_flash_units'][]				= 'ffu_spare_parts_compatible';


	$noc['fict_batteries'][]					= 'fbt_battery';
	$noc['fict_batteries'][]					= 'fbt_operational';
	$noc['fict_batteries'][]					= 'fbt_remarks';
	$noc['fict_batteries'][]					= 'fbt_camera_unit';
	$noc['fict_batteries'][]					= 'fbt_location';
	$noc['fict_batteries'][]					= 'fbt_type';
	$noc['fict_batteries'][]					= 'fbt_device';
	$noc['fict_batteries'][]					= 'fbt_manufacturer';
	$noc['fict_batteries'][]					= 'status';
	$noc['fict_batteries'][]					= 'fbt_cre_by';
	$noc['fict_batteries'][]					= 'fbt_cre_date';
	$noc['fict_batteries'][]					= 'spareparts_availability';
	$noc['fict_batteries'][]					= 'spareparts_quantity';
	$noc['fict_batteries'][]					= 'spareparts_compatible';

	$noc['fict_pucks'][]						= 'fpu_desc';
	$noc['fict_pucks'][]						= 'fpu_gps_location';
	$noc['fict_pucks'][]						= 'fpu_cre_by';
	$noc['fict_pucks'][]						= 'fpu_cre_date';
	$noc['fict_pucks'][]						= 'status';
	$noc['fict_pucks'][]						= 'spareparts_availability';
	$noc['fict_pucks'][]						= 'spareparts_quantity';
	$noc['fict_pucks'][]						= 'spareparts_compatible';

	$noc['fict_battery_charger'][]			= 'fbc_qty';
	$noc['fict_battery_charger'][]			= 'fbc_cre_by';
	$noc['fict_battery_charger'][]			= 'fbc_cre_date';
	$noc['fict_battery_charger'][]			= 'status';
	$noc['fict_battery_charger'][]			= 'spareparts_availability';
	$noc['fict_battery_charger'][]			= 'spareparts_quantity';
	$noc['fict_battery_charger'][]			= 'spareparts_compatible';
	$noc['fict_battery_charger'][]			= 'fbc_remarks';

	$noc['fict_hardware'][]					= 'fhh_make';
	$noc['fict_hardware'][]					= 'fhh_mac_add';
	$noc['fict_hardware'][]					= 'fhh_product_no';
	$noc['fict_hardware'][]					= 'fhh_serial_no';
	$noc['fict_hardware'][]					= 'fhh_cre_by';
	$noc['fict_hardware'][]					= 'fhh_cre_date';
	$noc['fict_hardware'][]					= 'fhh_update_by';
	$noc['fict_hardware'][]					= 'fhh_update_date';
	$noc['fict_hardware'][]					= 'fhh_remarks';
	$noc['fict_hardware'][]					= 'status';
	$noc['fict_hardware'][]					= 'fhh_secure_temp';

	$noc['fict_networks'][]					= 'fnt_cre_by';
	$noc['fict_networks'][]					= 'fnt_cre_date';
	$noc['fict_networks'][]					= 'status';
	$noc['fict_networks'][]					= 'fnt_secure_temp';
	$noc['fict_networks'][]					= 'fnt_remarks';

	$noc['fict_databases'][]				= 'fdb_cre_by';
	$noc['fict_databases'][]				= 'fdb_cre_date';
	$noc['fict_databases'][]				= 'status';
	$noc['fict_databases'][]				= 'fdb_update_date';
	$noc['fict_databases'][]				= 'fdb_update_by';
	$noc['fict_databases'][]				= 'fdb_remarks';


	$noc['fict_scripts'][]					= 'status';
	$noc['fict_scripts'][]					= 'fsc_cre_date';
	$noc['fict_scripts'][]					= 'fsc_cre_by';
	$noc['fict_scripts'][]					= 'fsc_update_date';
	$noc['fict_scripts'][]					= 'fsc_update_by';
	$noc['fict_scripts'][]					= 'fsc_remarks';

	$noc['fict_software'][]					= 'fsw_producer';
	$noc['fict_software'][]					= 'fsw_system_key';
	$noc['fict_software'][]					= 'fsw_doc_code';
	$noc['fict_software'][]					= 'fsw_login_detail';
	$noc['fict_software'][]					= 'fsw_used_by';
	$noc['fict_software'][]					= 'fsw_cre_by';
	$noc['fict_software'][]					= 'fsw_cre_date';
	$noc['fict_software'][]					= 'fsw_update_by';
	$noc['fict_software'][]					= 'fsw_update_date';
	$noc['fict_software'][]					= 'fsw_remarks';
	$noc['fict_software'][]					= 'status';

	$noc['fict_office_equipment'][]			= 'foe_user';
	$noc['fict_office_equipment'][]			= 'foe_make';
	$noc['fict_office_equipment'][]			= 'foe_model';
	$noc['fict_office_equipment'][]			= 'foe_owner';
	$noc['fict_office_equipment'][]			= 'status';
	$noc['fict_office_equipment'][]			= 'foe_cre_by';
	$noc['fict_office_equipment'][]			= 'foe_cre_date';
	$noc['fict_office_equipment'][]			= 'foe_remarks';
	$noc['fict_office_equipment'][]			= 'foe_location';

	if(array_key_exists($table,$noc))
		$nocolumn = $noc[$table];
	else
		$nocolumn = false;
	
	if(!empty($nocolumn))
		return $nocolumn;
	else
		return false;
}

// table static configuration
function page_static_config($table)
{
	global $pdata;
	switch ($table)
	{
		
		case 'fict_vehicles':
			$pdata['page']['order']				= 1;
			$pdata['page']['pfx'] 				= 'vh';
			$pdata['page']['section'] 			= 'vehicles';
			$pdata['page']['tname']				= $table;
			$pdata['page']['pname'] 			= 'vehicles';
			$pdata['page']['tidfield'] 			= 'fv_code';
			//$vcols[]							= array($pdata['page']['table'][0]['column_name'],  'id');
			//$vcols[]							= array($pdata['page']['table'][27]['column_name'], 'model');
			//$vcols[]							= array($pdata['page']['table'][14]['column_name'], 'year');
			//$vcols[]							= array($pdata['page']['table'][18]['column_name'], 'statu');
			//$pdata['page']['vcols']				= $vcols;
		break;

		case 'fict_buildings':
			$pdata['page']['order']				= 2;
			$pdata['page']['pfx'] 				= 'bd';
			$pdata['page']['tidfield'] 			= 'fb_code';
		break;

		case 'fict_redlight_systems':
			$pdata['page']['order']				= 3;
			$pdata['page']['pfx'] 				= 'rl';
			$pdata['page']['tidfield'] 			= 'frs_code';
		break;

		case 'fict_approach':
			$pdata['page']['order']				= 4;
			$pdata['page']['pfx'] 				= 'ap';
			$pdata['page']['tidfield'] 			= 'fapp_code';
		break;

		case 'fict_fixed_speedsystem':
			$pdata['page']['order']				= 5;
			$pdata['page']['pfx'] 				= 'fs';
			$pdata['page']['tidfield'] 			= 'fapp_code';
		break;

		case 'fict_portable_cameras':
			$pdata['page']['order']				= 6;
			$pdata['page']['pfx'] 				= 'pc';
			$pdata['page']['tidfield'] 			= 'fpc_code';
		break;

		case 'fict_mobile_speed_systems':
			$pdata['page']['order']				= 7;
			$pdata['page']['pfx'] 				= 'ms';
			$pdata['page']['tidfield'] 			= 'fmss_code';
		break;

		case 'fict_cabinets':
			$pdata['page']['order']				= 8;
			$pdata['page']['pfx'] 				= 'cb';
			$pdata['page']['tidfield'] 			= 'fcab_code';
		break;

		case 'fict_cameras_rl':
			$pdata['page']['order']				= 9;
			$pdata['page']['pfx'] 				= 'fc';
			$pdata['page']['tidfield'] 			= 'fcrl_code';
		break;

		case 'fict_sensors':
			$pdata['page']['order']				= 10;
			$pdata['page']['pfx'] 				= 'bd';
			$pdata['page']['tidfield'] 			= 'fsns_code';
		break;

		case 'fict_flash_units':
			$pdata['page']['order']				= 11;
			$pdata['page']['pfx'] 				= 'fu';
			$pdata['page']['tidfield'] 			= 'ffu_code';
		break;

		case 'fict_batteries':
			$pdata['page']['order']				= 12;
			$pdata['page']['pfx'] 				= 'bt';
			$pdata['page']['tidfield'] 			= 'fbt_code';
		break;

		case 'fict_pucks':
			$pdata['page']['order']				= 13;
			$pdata['page']['pfx'] 				= 'pu';
			$pdata['page']['tidfield'] 			= 'fpu_code';
		break;

		case 'fict_battery_charger':
			$pdata['page']['order']				= 14;
			$pdata['page']['pfx'] 				= 'bc';
			$pdata['page']['tidfield'] 			= 'fbc_code';
		break;

		case 'fict_hardware':
			$pdata['page']['order']				= 15;
			$pdata['page']['pfx'] 				= 'hd';
			$pdata['page']['tidfield'] 			= 'fhh_code';
			$pdata['page']['pname'] 			= 'it '.$pdata['page']['pname'];
		break;

		case 'fict_networks':
			$pdata['page']['order']				= 16;
			$pdata['page']['pfx'] 				= 'nt';
			$pdata['page']['tidfield'] 			= 'fnt_code';
			$pdata['page']['pname'] 			= 'it '.$pdata['page']['pname'];
		break;

		case 'fict_databases':
			$pdata['page']['order']				= 17;
			$pdata['page']['pfx'] 				= 'db';
			$pdata['page']['tidfield'] 			= 'fdb_code';
			$pdata['page']['pname'] 			= 'it '.$pdata['page']['pname'];
		break;

		case 'fict_scripts':
			$pdata['page']['order']				= 18;
			$pdata['page']['pfx'] 				= 'sc';
			$pdata['page']['tidfield'] 			= 'fsc_code';
			$pdata['page']['pname'] 			= 'it '.$pdata['page']['pname'];
		break;

		case 'fict_software':
			$pdata['page']['order']				= 19;
			$pdata['page']['pfx'] 				= 'sf';
			$pdata['page']['tidfield'] 			= 'fsw_code';
			$pdata['page']['pname'] 			= 'it '.$pdata['page']['pname'];
		break;

		case 'fict_office_equipment':
			$pdata['page']['order']				= 20;
			$pdata['page']['pfx'] 				= 'oe';
			$pdata['page']['tidfield'] 			= 'foe_code';
		break;

		case 'fict_flash_bulbs':
			$pdata['page']['order']				= 21;
			$pdata['page']['pfx'] 				= 'fu';
			$pdata['page']['tidfield'] 			= 'ffb_code';
		break;

		case 'fict_gaps':
			$pdata['page']['order']				= 22;
			$pdata['page']['pfx'] 				= 'ga';
			$pdata['page']['tidfield'] 			= 'fga_code';
		break;

		case 'fict_documents':
			$pdata['page']['order']				= 23;
			$pdata['page']['pfx'] 				= 'dc';
			$pdata['page']['tidfield'] 			= 'fd_code';
		break;

		case 'fict_traffic_signal_control':
			$pdata['page']['order']				= 24;
			$pdata['page']['pfx'] 				= 'ts';
			$pdata['page']['tidfield'] 			= 'ftsc_code';
		break;
	}
}

// column configuration
function get_column_static_info($col)
{
	// hardcoded values
	$options['fv_all_door_and_equip_work']['lname'] = 'all_door_equip';  // label name that will show in form field...
	$options['fv_all_door_and_equip_work']['itype'] = 'select';
	$options['fv_all_door_and_equip_work']['vals']  = array(0=>'zero',1=>'one',2=>'tow',3=>'three',4=>'four',5=>'five');
	//$options['fv_all_door_and_equip_work']['value'] = 3;

	$options['fv_model']['feed']  = 'year';
	$options['fv_model']['value'] = '2009';

	// override params with static ones
	if(isset($options[$col['column_name']]))
	{
		$op = $options[$col['column_name']];
		if(is_array($op))
			foreach ($op as $key => $value)
				$col[$key] = $value;
	}
	return $col;
}
?>