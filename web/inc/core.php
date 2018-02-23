<?php
/*
	tools
*/
function get_current_datetime()
{
	$datetime = date('d/m/y h:i:s',time());
	return $datetime;
}

// add zero to signl char number values (1-9)
function date_add_zero($val)
{
	if($val <= 9)
		$val = '0'.$val;
	return $val;
}

// fomrat arabic date...
function formatardate($y,$m,$d)
{
	$date = $y."/".date_add_zero($m)."/".date_add_zero($d);
	return $date;
}

// get day number
function day_num()
{
	return date('w',time());
}
// replace all \n with <br>
function to_html($txt)
{
	$html_txt = nl2br($txt);
	return $html_txt;
}

// converts english date to arabic date
function get_ar_date($endate,$print=0)
{
	$time = strtotime($endate);
	$date = arabictools::arabicdate("hj:y/m/d åü", $time);
	$date = '<span dir="rtl">'.$date.'</span>';
	if($print)
		echo $date;
	else
		return $date;
}

function db_connect(){
	try {
    	$c = new pdo('mysql:host=localhost;dbname=pr_inventory', 'root', 'root');
	} catch (pdoexception $e) {
	    echo "error!: " . $e->getmessage() . "<br/>";
	   	die();
	}
	return $c;
}

function db_disconnect($conn)
{
	global $pdata;
	$pdata['conn'] = null;
}

function db_num_rows($tabel,$params)
{
	global $pdata;
	$pq 	= get_sql_query($params);
	$q 		= "select count(*) as count from ".$tabel.' '.$pq;
	$stid 	= $pdata['conn']->prepare($q);
	$stid 	= db_bind_all($stid,$params);
	$stid->execute();
	$row = $stid->fetch(PDO::FETCH_ASSOC);
	return $row['count'];
}

function db_bind_all($stid, $params)
{
	foreach ($params as $param => $val)
		$stid->bindValue($val[0],$val[1]);
	return $stid;
}

function get_sql_select_fields($params)
{
	$q = '';
	foreach ($params as $param => $val)
	{
		// check fields
		$val[0] = check_select_type($val[0]);
		// assign value
		$q .= ' '.strtolower($val[0]);
		if(end($params) !== $val)
    		$q .= ',';
	}
	return $q;
}

function get_sql_query($params)
{
	$i=0;
	$q='';
	foreach ($params as $param => $val)
	{
		if($i == 0) $kw = 'where '; else $kw = 'and';
		$q .= ' '.$kw.' '.$param.' = '.$val[0];
		$i++;
	}
	return $q;
}

function get_sql_update($params)
{
	$sql = '';
	$count = count($params)-1;
	$i = 0;
	foreach ($params as $key => $val)
	{
		// check if key is date
		$val[0] = check_insert_type($val[0]);
		$sql .= $key.' = '.$val[0];
		if($i != $count) $sql .= ', ';
		$i++;
	}
	return $sql;
}

function get_sql_insert($params)
{
	$lastelement = end($params);
	foreach ($params as $par => $val)
	{
		// check if key is date
		$val[0] = check_insert_type($val[0]);
		// add values
		if(isset($fields)) $fields .= ','.$par; else $fields = "(".$par;
		if(isset($values)) $values .= ','.$val[0]; else $values = "(".$val[0];

		// close statment
		if($val == $lastelement)
		{
			if(isset($fields))
			{
				$fields .= ")";
				$values .= ")";
			} 
		}
	}
	return $fields." values ".$values;
}

function get_sql_items($params)
{
	$lastelement = end($params);
	foreach ($params as $par => $val)
	{
		// add values
		if(isset($fields)) $fields .= ",'".$val."'"; else $fields = "('".$val."'";

		// close statment
		if($val == $lastelement)
			if(isset($fields))
				$fields .= ")";
	}
	return $fields;
}

function check_insert_type($key)
{
	// check key is date
	if(strpos(strtolower($key),'date') !== false)
		//return "to_date(".$key.",'yyyy-mm-dd')";
		return "'".$key."'";
	else
		return $key;
}

function check_select_type($key)
{
	// check key is date
	if(strpos(strtolower($key),'date') !== false)
		return "to_char(".$key.", 'yyyy-mm-dd') as ".$key;
	else
		return $key;
}

function gen_id()
{
    return substr(md5(uniqid(mt_rand(), true)), 0, 8);;
}

function app_sleep()
{
	sleep(2);
}

function clean_sql($str)
{
	return trim(strtolower($str));
}
?>