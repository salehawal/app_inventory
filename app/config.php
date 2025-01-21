<?php
require_once('lib/core.php');
require_once('lib/app.php');
sys_init();
// fict_vehicles
add_options('fv_operational',					'radio',	array('no','yes'),true);
add_options('fv_type',							'select',	array('sedan','truck','van','flatbed','suv'));
add_options('fv_location',						'select',	array('jeddah','makkah','riyadh','madina','taief','abha','dammam'));
add_options('fv_insurance_card',					'radio',	array('no','yes'),true);
add_options('fv_registration_card',				'radio',	array('no','yes'),true);
add_options('fv_make',							'select',	array('toyota','gmc','ford','nissan','suzuki','pyd','mazda'));
add_options('fv_model',							'year',	null);
add_options('fv_year',							'year',	null);
add_options('fv_window_condition',				'select',	array('broken','crackd','good'));
add_options('fv_body_condition',					'select',	array(1,2,3,4,5));
add_options('fv_paint_condition',				'select',	array(1,2,3,4,5));
add_options('fv_car_interior',					'select',	array(1,2,3,4,5));
add_options('fv_all_light_work',					'radio',	array('no','yes'), true);


// fict_buildings
add_options('fb_type', 							'select',	array('warehouse','villa','apartment','flat','room','garage'));
add_options('fb_backup_power', 					'radio', 	array('no','yes'),true);
add_options('fb_phy_security_access_sys', 		'radio', 	array('no','yes'),true);
add_options('fb_structural_condition', 			'select', 	array(1,2,3,4,5));
add_options('fb_windows_condition', 				'select', 	array(1,2,3,4,5));
add_options('fb_sanitary_condition', 			'select', 	array(1,2,3,4,5));
add_options('fb_electrical_condition', 			'select', 	array(1,2,3,4,5));
add_options('fb_ac_inst_condition', 				'select', 	array(1,2,3,4,5));
add_options('fb_mechnical_inst_condition', 		'select', 	array(1,2,3,4,5));
add_options('fb_fire_system', 					'radio',	array('no','yes'),true);
add_options('fb_odoor_equip_inst_correctly', 	'radio', 	array('no','yes'),true);


// fict_redlight_systems
//add_options('frs_location',						'location',	null);
add_options('frs_manufacturer',					'select',	array('jenoptic','vitronic','acs'));
add_options('frs_approaches',					'number',	null);
add_options('frs_pucks',							'number',	null);
add_options('frs_sensor_type',					'select',	array('radar','lidar'));
add_options('frs_cabinet_int_cond', 				'select', 	array(1,2,3,4,5));
add_options('frs_cabinet_ext_cond', 				'select', 	array(1,2,3,4,5));
add_options('frs_cooling_unit_operational', 		'radio',	array('no','yes'),true);
add_options('frs_poles_good_state', 				'radio',	array('no','yes'),true);
add_options('frs_eps_linked', 					'radio',	array('no','yes'),true);
add_options('frs_network_link', 					'radio',	array('no','yes'),true);


// fict_approach
//add_options('fapp_location',						'location',	null);
add_options('fapp_direction',					'select',	array('north','south','east','west'));
add_options('fapp_tot_lanes',					'number',	null);
add_options('fapp_selftest', 					'radio',	array('no','yes'),true);
//add_options('fapp_valid_violation_night', 		'radio',	array('no','yes'),true);
//add_options('fapp_valid_violation_day', 		'radio',	array('no','yes'),true);
//add_options('fapp_bollard_protect', 			'radio',	array('no','yes'),true);
add_options('fapp_no_obstruction', 				'radio',	array('no','yes'),true);
add_options('fapp_housing_condition', 			'select', 	array(1,2,3,4,5));
add_options('fapp_violation_system', 			'radio',	array('no','yes'),true);
add_options('fapp_calibration_sticker', 			'radio',	array('no','yes'),true);
add_options('fapp_bollard_state', 				'radio',	array('no','yes'),true);
add_options('fapp_manhole_state', 				'radio',	array('no','yes'),true);


// fict_fixed_speedsystem
//add_options('ffss_location',						'select',	array('jenoptic','vitronic','acs'));
add_options('ffss_manufacturer',					'select',	array('jenoptic','vitronic','acs'));
add_options('ffss_operational', 					'radio',	array('no','yes'),true);
add_options('ffss_bollard_protection',			'radio',	array('no','yes'),true);
//add_options('ffss_camera_unit',				'select',	array('no','yes'),true);
add_options('ffss_lanes',						'number',	null);
//add_options('ffss_flash_unit',					'number',	null);
add_options('ffss_self_test_successful',			'select',	array('no','yes'),true);
//add_options('ffss_valid_violations_night',		'select',	array('no','yes'),true);
//add_options('ffss_valid_violations_day',		'select',	array('no','yes'),true);
add_options('ffss_cooling_unit_operational',		'radio',	array('no','yes'),true);
add_options('ffss_housing_condition', 			'select', 	array(1,2,3,4,5));
add_options('ffss_valid_violation_insystem',		'select',	array('no','yes'),true);
add_options('ffss_current_calibration_stk',		'select',	array('no','yes'),true);
add_options('ffss_poles_in_good_state',			'select',	array('no','yes'),true);
add_options('ffss_manhole_cover_lids',			'select',	array('no','yes'),true);
add_options('ffss_network_exists',				'select',	array('no','yes'),true);
add_options('ffss_electrical_power_source',		'select',	array('no','yes'),true);


// fict_portable_cameras
add_options('fpc_type',							'radio',	array('dual','singal'),true);
add_options('fpc_manufacturer',					'select',	array('jenoptic','vitronic','acs','redflex'));
add_options('fpc_operational',					'radio',	array('no','yes'),true);
add_options('fpc_connected_batteries',			'number',	null);
//add_options('fpc_camera_unit',					'number',	null);
add_options('fpc_calibration_stik',				'radio',	array('no','yes'),true);
add_options('fpc_commpc',						'radio',	array('no','yes'),true);
//add_options('fpc_flash_unit',					'radio',	array('no','yes'),true);
add_options('fpc_self_test',						'radio',	array('no','yes'),true);
//add_options('fpc_valid_violations_night',		'number',	null);
//add_options('fpc_valid_violations_day',			'number',	null);
add_options('fpc_cooling_unit_operational',		'select',	array('no','yes'),true);
add_options('fpc_harddrive_type',				'select',	array('flash','hard_disk'));
add_options('ffss_housing_condition', 			'select', 	array(1,2,3,4,5));
add_options('fpc_valid_violations_system', 		'radio', 	array('no','yes'),true);
add_options('fpc_success_bootup', 				'radio', 	array('no','yes'),true);
add_options('fpcu_lanes_monitored_correctly', 	'radio', 	array('no','yes'),true);


// fict_mobile_speed_systems
add_options('fmss_type',							'select',	array('dual','singal'),true);
add_options('fmss_manufacturer',					'select',	array('jenoptic','vitronic','acs','redflex'));
add_options('fmss_operational', 					'radio', 	array('no','yes'),true);
//add_options('fmss_no_of_batteries',			'number',	null);
//add_options('fmss_battery', 					'select', 	array('no','yes'),true);
//add_options('fmss_camera',						'number',	null);
add_options('fmss_current_calib_sticker', 		'radio', 	array('no','yes'),true);
add_options('fmss_commpc', 						'radio', 	array('no','yes'),true);
add_options('fmss_flash_unit', 					'radio', 	array('no','yes'),true);
add_options('fmss_self_test_success', 			'radio', 	array('no','yes'),true);
//add_options('fmss_valid_violations_night', 		'select', 	array('no','yes'),true);
//add_options('fmss_valid_violations_day', 		'select', 	array('no','yes'),true);
add_options('fmss_cooling_unit_operational', 	'radio', 	array('no','yes'),true);
add_options('fmss_hard_drive',					'select',	array('flash','hard_disk'));
add_options('fmss_cabinet_housing_condition', 	'select', 	array(1,2,3,4,5));
add_options('fmss_valid_violation_system', 		'radio', 	array('no','yes'),true);
add_options('fmss_success_bootup', 				'radio', 	array('no','yes'),true);
add_options('fmss_monitor_lanes', 				'radio', 	array('no','yes'),true);

// fict_cabinets
add_options('fcab_aircondition_type',			'select',	array('type1','type2'));
add_options('fcab_manufacturer',					'select',	array('jenoptic','vitronic','acs','redflex'));
add_options('fcab_condition_int', 				'select', 	array(1,2,3,4,5));
add_options('fcab_condition_ext', 				'select', 	array(1,2,3,4,5));

// fict_cameras_rl
add_options('fcrl_manufacturer',					'select',	array('jenoptic','vitronic','acs','redflex'));
//add_options('fcrl_id_approach',				'select',	array('jenoptic','vitronic','acs','redflex'));
//add_options('fcrl_serialno',					'select',	array('jenoptic','vitronic','acs','redflex'));
//add_options('fcrl_model',						'select',	array('jenoptic','vitronic','acs','redflex'));
add_options('fcrl_housing_condition', 			'select', 	array(1,2,3,4,5));
add_options('fcab_aircondition_type',			'select',	array('video','still'));

// fict_sensors
add_options('fsns_manufacturer',					'select',	array('jenoptic','vitronic','acs','redflex'));
//add_options('fsns_id_approach',				'select',	array('jenoptic','vitronic','acs','redflex'));
//add_options('fsns_serialno',					'select',	array('jenoptic','vitronic','acs','redflex'));
//add_options('fsns_model',						'select',	array('jenoptic','vitronic','acs','redflex'));
add_options('fsns_house_condition', 				'select', 	array(1,2,3,4,5));

// fict_flash_units
add_options('ffu_manufacturer',					'select',	array('jenoptic','vitronic','acs','redflex'));
//add_options('ffu_serial_nos',					'select',	array('jenoptic','vitronic','acs','redflex'));
//add_options('ffu_id_approach',					'select',	array('jenoptic','vitronic','acs','redflex'));
add_options('ffu_operational', 					'radio', 	array('no','yes'),true);
add_options('ffu_housing_condition', 			'select', 	array(1,2,3,4,5));

// fict_batteries
add_options('fbt_type',							'select',	array('gel','acid','acs','lithium'));
//add_options('ffu_manufacturer',					'select',	array('jenoptic','vitronic','acs','redflex'));
add_options('fbt_condition', 					'select', 	array(1,2,3,4,5));
add_options('fbt_id', 							'text', 	'');
add_options('fbt_device_type', 					'text', 	'');
add_options('fbt_amp', 							'text', 	'');
add_options('fbt_voltage', 						'text', 	'');

// fict_pucks
//add_options('fpu_location', 					'text', 	'');
add_options('fpu_serial_no', 					'text', 	'');
//add_options('fpu_manufacturer',					'select',	array('jenoptic','vitronic','acs','redflex'));
add_options('fpu_operational', 					'radio', 	array('no','yes'),true);
add_options('fpu_remarks', 					'textarea', 	'');

// fict_battery_charger
//add_options('fbc_manufacturer',					'select',	array('jenoptic','vitronic','acs','redflex'));
add_options('fpu_serial_no', 					'number', 	'');
add_options('fbc_amp', 							'text', 	'');
add_options('fbc_volts', 						'text', 	'');
add_options('fbc_locations_utilized', 			'text', 	'');
add_options('fbc_condition', 					'text', 	'');

// fict_hardware
add_options('fhh_type',							'select',	array('pc','laptop','mobile','printer','scanner','rack','network','device','storage'));
add_options('fhh_ip', 							'text', 	'');
add_options('fhh_sw_code', 						'text', 	'');
//add_options('fhh_location', 					'text', 	'');
add_options('fhh_manufactureer', 				'text', 	'');
add_options('fhh_model', 						'text', 	'');
add_options('fhh_user', 							'text', 	'');
add_options('fhh_qty_each_type', 				'number', 	'');
add_options('fhh_owner_each_asset', 				'text', 	'');
add_options('fhh_security_method', 				'radio', 	array('no','yes'),true);
add_options('fhh_operational', 					'radio', 	array('no','yes'),true);
add_options('fhh_purpose', 						'text', 	'');
add_options('fhh_spec', 							'textarea', 	'');
add_options('fhh_phy_condition', 				'select', 	array(1,2,3,4,5));
add_options('fhh_secure_storage', 				'radio', 	array('no','yes'),true);
add_options('fhh_storage_temp', 					'text', 	'');
add_options('fhh_notice_vib', 					'radio', 	array('no','yes'),true);
add_options('fhh_dust_pres', 					'radio', 	array('no','yes'),true);
add_options('fhh_medium_storage', 				'text', 	'');
add_options('fhh_last_maint_date', 				'date', 	'');
add_options('fhh_purch_date', 					'date', 	'');
add_options('fhh_oper_date', 					'date', 	'');

// fict_networks
add_options('fnt_type',							'select',	array('router','switces','firewall'));
add_options('fnt_qty', 							'number', 	'');
add_options('fnt_operational', 					'radio', 	array('no','yes'),true);
add_options('fnt_tool_available', 				'radio', 	array('no','yes'),true);
add_options('fnt_tool_operational', 				'radio', 	array('no','yes'),true);
add_options('fnt_conn_disaster_rec', 			'radio', 	array('no','yes'),true);
add_options('fnt_security', 						'radio', 	array('no','yes'),true);
add_options('fnt_service_provider', 				'text', 	'');
add_options('fnt_owner_asset_manufacturer', 		'text', 	'');
add_options('fnt_manufacturer', 					'text', 	'');
add_options('fnt_model', 						'text', 	'');
add_options('fnt_version', 						'text', 	'');
//add_options('fnt_location', 						'text', 	'');
add_options('fnt_purpose', 						'text', 	'');
add_options('fnt_phy_condition', 				'select', 	array(1,2,3,4,5));
add_options('fnt_secure_storage', 				'radio', 	array('no','yes'),true);
add_options('fnt_storage_temp', 					'text', 	'');
add_options('fnt_notice_vib', 					'radio', 	array('no','yes'),true);
add_options('fnt_dust_pres', 					'radio', 	array('no','yes'),true);
add_options('fnt_medium_storage', 				'text', 	'');
add_options('fnt_last_maint_date', 				'date', 	'');
add_options('fnt_purch_date', 					'date', 	'');
add_options('fnt_oper_date', 					'date', 	'');

// fict_databases
add_options('fdb_db_name', 						'text', 	'');
add_options('fdb_operational', 					'radio', 	array('no','yes'),true);
add_options('fdb_owner', 						'text', 	'');
add_options('fdb_security', 						'radio', 	array('no','yes'),true);
add_options('fdb_manufacturer', 					'text', 	'');
add_options('fdb_model', 						'text', 	'');
add_options('fdb_version', 						'text', 	'');
//add_options('fdb_location', 						'text', 	'');
add_options('fdb_purpose', 						'text', 	'');
add_options('fdb_licensed', 						'radio', 	array('no','yes'),true);
add_options('fdb_sec_storage', 					'radio', 	array('no','yes'),true);
add_options('fdb_last_maint_date', 					'date', 	'');
add_options('fdb_purch_date', 					'date', 	'');
add_options('fdb_oper_date', 					'date', 	'');

// fict_scripts
add_options('fsc_name', 						'text', 	'');
add_options('fsc_type',						'select',	array('system','automation','database'));
add_options('fsc_location', 					'textarea', 	'');
add_options('fsc_schedule', 					'text', 		'');
add_options('fsc_purpose', 					'textarea', 	'');
add_options('fsc_owner', 					'text', 		'');
add_options('fsc_operational', 				'radio', 	array('no','yes'),true);
add_options('fsc_security', 					'radio', 	array('no','yes'),true);
add_options('fsc_secured_storage', 			'radio', 	array('no','yes'),true);
add_options('fsc_last_maintenance_date', 	'date', 	'');
add_options('fsc_asset_purchase_date',		'date', 	'');
add_options('fsc_asset_operational_date', 	'date', 	'');


// fict_software
add_options('fsw_sw',						'select',	array('server','application','os'));
add_options('fsw_covered_license', 			'radio', 	array('no','yes'),true);
add_options('fsw_covered_support', 			'radio', 	array('no','yes'),true);
add_options('fsw_qty_each_type', 			'number', 		'');
add_options('fsw_manufacturer', 				'text', 		'');
add_options('fsw_model', 					'text', 		'');
add_options('fsw_ver', 						'text', 		'');
add_options('fsw_operational', 				'radio', 	array('no','yes'),true);
add_options('fsw_owner_each_asset', 			'text', 		'');
add_options('fsw_security', 					'radio', 	array('no','yes'),true);
add_options('fsw_location', 					'text', 		'');
add_options('fsw_purpose', 					'text', 		'');
add_options('fsw_secured_storage', 			'radio', 	array('no','yes'),true);
add_options('fsw_last_maintenance_date', 	'date', 	'');
add_options('fsw_asset_purchase_date', 		'date', 	'');
add_options('fsw_asset_operational_date', 	'date', 	'');

// fict_office_equipment
add_options('foe_type', 						'text', 		'');
add_options('foe_qty', 						'number', 		'');
//add_options('foe_location', 					'text', 		'');

?>
