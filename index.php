<?php

	/**
	*	Copyright 2010-2013 René Michlke
	*
	*	This file is part of RM Internet Suite.
	*/
	
	error_reporting(E_ALL);

	// Tell PHP that we're using UTF-8 strings until the end of the script
	mb_internal_encoding('UTF-8');
	
	// Tell PHP that we'll be outputting UTF-8 to the browser
	mb_http_output('UTF-8');
	mb_http_input('UTF-8');
	header("Content-Type: text/html; charset=UTF-8");
	
	session_start();
	
	// Load required files:
	require_once('protected/Config.inc.php');
	require_once('classes/Qualify.class.php');
	
	// Make the data, which comes from the visitor, save to handle:
	$request = array_merge($_GET, $_POST, $_FILES);
	$request = Qualify::process_array($request, 'Qualify::make_save_str_in');
	
	// Set default area:
	if(!isset($request['module'])) header('Location: index.php?module=bookmarks');
	
	// Check wether to load the ajax controller:
	$type = (@$request['ajax'] == 1) ? ".ajax" : "";
	
	// Make Loading the controller and eval($eval) safe:
	switch($request['module']) {
	
		default:
		case 'bookmarks':
			$module = 'bookmarks';
		break;
		
		case 'universal':
			$module = 'universal';
		break;
		
	}
	
	// Load the controller
	require_once('modules/Controller.'.$module.$type.'.inc.php');
	
	// Create new controller entity
	$eval = '$controller = new '.$module.'($request);';

	eval($eval);
	
	// Run the control
	$controller->control();
	
	// Display content
	echo $controller->display();
	
?>
