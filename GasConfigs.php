#!/usr/bin/env php

<?php

/* Quick script for generating gasconfig drop down lists until i figure out how to generate them more efficiently on-the-fly */

include_once "includes/autoloader.php";

$MapsReadable = array(
	// Dead Center
	"c1m2_streets" => "Gun Shop",
	"c1m4_atrium" => "Mall Atrium",
	
	// The Passing
	"c6m1_riverbank" => "Riverbank",
	"c6m2_bedlam" => "Bedlam/Underground",
	"c6m3_port" => "Port Passing",
	
	// Dark Carnival
	"c2m1_highway" => "Motel",
	"c2m4_barns" => "Stadium Gate",
	"c2m5_concert" => "Concert",
	
	// Swamp Fever
	"c3m1_plankcountry" => "Gator Village",
	"c3m3_shantytown" => "Shanty Town",
	"c3m4_plantation" => "Plantation",
	
	// Hard Rain
	"c4m1_milltown_a" => "Burger Tank",
	"c4m2_sugarmill_a" => "Sugar Mill",
	"c4m3_sugarmill_b" => "Cane Field",
	
	// The Parish
	"c5m1_waterfront" => "Waterfront",
	"c5m2_park" => "Bus Depot",
	"c5m3_cemetery" => "Cemetery",
	"c5m4_quarter" => "Float",
	"c5m5_bridge" => "Bridge",
	
	// The Sacrifice
	"c7m1_docks" => "Traincar",
	"c7m2_barge" => "Barge",
	"c7m3_port" => "Port Sacrifice",
	
	// No Mercy
	"c8m2_subway" => "Generator Room",
	"c8m3_sewers" => "Gas Station",
	"c8m4_interior" => "Hospital",
	"c8m5_rooftop" => "Rooftop",
	
	// Crash Course
	"c9m1_alleys" => "The Bridge (cc)",
	"c9m2_lots" => "Truck Depot",
	
	// Death Toll
	"c10m2_drainage" => "Drains",
	"c10m3_ranchhouse" => "Church",
	"c10m4_mainstreet" => "Street",
	"c10m5_houseboat" => "Boathouse",
	
	// Dead Air
	"c11m2_offices" => "Crane",
	"c11m3_garage" => "Construction Site",
	"c11m4_terminal" => "Terminal",
	"c11m5_runway" => "Runway",
	
	// Blood Harvest
	"c12m2_traintunnel" => "Warehouse",
	"c12m3_bridge" => "The Bridge (BH)",
	"c12m5_cornfield" => "Farmhouse",
	
	// Cold Stream
	"c13m3_memorialbridge" => "Junkyard (Cold Stream)",
	"c13m4_cutthroatcreek" => "Waterworks",
	
	// The Last Stand
	"c14m1_junkyard" => "Junkyard (Last Stand)",
	"c14m2_lighthouse" => "Lighthouse",
	
	// custom maps
	"l4d2_syberianhusky_c1m3b" => "Simplex Survival"
);

RetrieveJSONFiles();

function RetrieveJSONFiles()
{
	$DownloadFolder = "PlayerList/GasConfigs/";

	if (!is_dir($DownloadFolder))
	{
		mkdir($DownloadFolder);
	}
	
	// Config creation only happens once in each config's lifetime so get all relevant info from that event
	GenerateDropdownList("SELECT `config_UniqueID`, `config_name`, `is_config_active`, `map` FROM `gasconfigs_v2_logs` WHERE action = 'create' ORDER BY config_name;", $DownloadFolder . "ConfigsAll.json");
	GenerateDropdownList("SELECT `config_UniqueID`, `config_name`, `is_config_active`, `map` FROM `gasconfigs_v2_logs` WHERE action = 'create' AND is_config_active = '1' ORDER BY config_name;", $DownloadFolder . "ConfigsActive.json");
	GenerateDropdownList("SELECT `config_UniqueID`, `config_name`, `is_config_active`, `map` FROM `gasconfigs_v2_logs` WHERE action = 'create' AND is_config_active = '0' ORDER BY config_name;", $DownloadFolder . "ConfigsDeleted.json");
	
	global $MapsReadable;
	
	
	foreach($MapsReadable as $map => $mapFormatted)
	{
		$CurrentFolder = $DownloadFolder . $map . "/";
		if (!is_dir($CurrentFolder))
		{
			mkdir($CurrentFolder);
		}
		
		echo "Processing configs for: '$map' (folder: $CurrentFolder )" . PHP_EOL;
		GenerateDropdownList("SELECT `config_UniqueID`, `config_name`, `is_config_active`, `map` FROM `gasconfigs_v2_logs` WHERE action = 'create' AND `map` = '$map' ORDER BY config_name;", $CurrentFolder . "ConfigsAll.json");
		GenerateDropdownList("SELECT `config_UniqueID`, `config_name`, `is_config_active`, `map` FROM `gasconfigs_v2_logs` WHERE action = 'create' AND `map` = '$map' AND is_config_active = '1' ORDER BY config_name;", $CurrentFolder . "ConfigsActive.json");
		GenerateDropdownList("SELECT `config_UniqueID`, `config_name`, `is_config_active`, `map` FROM `gasconfigs_v2_logs` WHERE action = 'create' AND `map` = '$map' AND is_config_active = '0' ORDER BY config_name;", $CurrentFolder . "ConfigsDeleted.json");
		
		GenerateSteam64IDLists($map, $CurrentFolder);
	}
	
	GenerateSteam64IDLists("", $DownloadFolder);
}

function GenerateDropdownList($query, $filename)
{
	$mysqli = connection::establishDBConnection();
	
	if ($result = $mysqli->query($query))
	{
		$values = [];
		while ($row = $result->fetch_array(MYSQLI_ASSOC))
		{
			$configName = $row['config_name'];
			$configStatus = $row['is_config_active'];
			$map = $row['map'];
			$configID = $row['config_UniqueID'];
			// Make sure each name can safely exist in <script> tags
			$configName = htmlspecialchars($configName, ENT_QUOTES, 'UTF-8');
			$configName = str_replace('\\','\\\\', $configName);
			
			$temp_buffer = array(
				"name" => $configName,
				"status" => $configStatus,
				"map" => $map,
				"ConfigID" => $configID
			);
			array_push($values, $temp_buffer);
		}

		$fp = fopen($filename, 'w');
		fwrite($fp, print_r(json_encode($values), true));
		fclose($fp);
	} 
	connection::KillDBConnection($mysqli);
}

function GenerateSteam64IDLists($map = "", $CurrentFolder = "")
{
	$mysqli = connection::establishDBConnection();
	$query = "SELECT DISTINCT steam64ID_user_who_loaded_config FROM `gasconfigs_v2_logs`";
	
	$mapQuery = "";
	if (!empty($map))
	{
		$mapQuery = " AND `map` = '$map'";
	}

	if ($result = $mysqli->query($query))
	{
		while ($rowAuthIDs = $result->fetch_array(MYSQLI_ASSOC))
		{
			$authID = $rowAuthIDs['steam64ID_user_who_loaded_config'];
			 // `owner_steam64ID` = '$authID'
			GenerateDropdownList("SELECT `config_UniqueID`, `config_name`, `is_config_active`, `map` FROM `gasconfigs_v2_logs` WHERE action = 'create' AND `owner_steam64ID` = '$authID'$mapQuery ORDER BY config_name;", $CurrentFolder . "ConfigsAll$authID.json");
			GenerateDropdownList("SELECT `config_UniqueID`, `config_name`, `is_config_active`, `map` FROM `gasconfigs_v2_logs` WHERE action = 'create' AND is_config_active = '1' AND `owner_steam64ID` = '$authID'$mapQuery ORDER BY config_name;", $CurrentFolder . "ConfigsActive$authID.json");
			GenerateDropdownList("SELECT `config_UniqueID`, `config_name`, `is_config_active`, `map` FROM `gasconfigs_v2_logs` WHERE action = 'create' AND is_config_active = '0' AND `owner_steam64ID` = '$authID'$mapQuery ORDER BY config_name;", $CurrentFolder . "ConfigsDeleted$authID.json");
		}
	} 
	connection::KillDBConnection($mysqli);
}