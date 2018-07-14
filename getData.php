<?php
	//need to check whether request header is valid
	$putdata = file_get_contents('php://input');
	$putjson = json_decode($putdata, true);

	$put_northEast = $putjson['bounds']['_northEast'];
	$put_southWest = $putjson['bounds']['_southWest'];

	$put_zoom = $putjson['zoom'];

	//load json data
	$str = file_get_contents('./json_source/bicycle_thefts_Part1.geojson.txt');
	$datajson = json_decode($str, true);
	
	//initialize lazy bound
	$northEast = array('lat' => 0.00, 'lng' => 0.00);
	$southWest = array('lat' => 0.00, 'lng' => 0.00);

	//update lazy bound
	$northEast['lat'] = $put_northEast['lat'] + 0.02;
	$northEast['lng'] = $put_northEast['lng'] + 0.02;
	$southWest['lat'] = $put_southWest['lat'] - 0.02;
	$southWest['lng'] = $put_southWest['lng'] - 0.02;

	$red = '#ff0000';
	$orange = '#ff7800';
	$green = '#32CD32';

	//use lazy bound to first select the features within the region
	$processed_data = array();
	for($i = 0; $i < sizeof($datajson['features']); $i++){

		$tmp = $datajson['features'][$i]['geometry']['coordinates'];

		if($tmp[0] < $northEast['lng'] && $tmp[0] > $southWest['lng'] &&
			$tmp[1] < $northEast['lat'] && $tmp[1] > $southWest['lat']){

			//initliaze color setting
			if($datajson['features'][$i]['properties']['STOLEN_VAL'] < 10){
				$datajson['features'][$i]['properties']['COLOR'] = $green;
			}
			else if($datajson['features'][$i]['properties']['STOLEN_VAL'] < 100){
				$datajson['features'][$i]['properties']['COLOR'] = $orange;
			}
			else{
				$datajson['features'][$i]['properties']['COLOR'] =  $red;
			}

			array_push($processed_data, $datajson['features'][$i]);

		}
	}


	// zoom >= 7 does not require process
	if($put_zoom < 7){
		$per_lng = ($northEast['lng'] - $southWest['lng'])/50;
		$per_lat = ($northEast['lat'] - $southWest['lat'])/50;

		$groups = array();
		for($i = 0; $i < sizeof($processed_data); $i++){
			$tmp = $processed_data[$i]['geometry']['coordinates'];

			$num_lng = round(($tmp[0] - $southWest['lng'])/$per_lng);
			$num_lat = round(($tmp[1] - $southWest['lat'])/$per_lat);
			$num = $num_lng*50+$num_lat; //calculate the key number

			if(array_key_exists($num, $groups)){
				array_push($groups[$num], $processed_data[$i]);
			}
			else{
				$groups[$num] = array();
				array_push($groups[$num], $processed_data[$i]);
			}
		}

		$processed_data1 = array();

		//averaging all group results
		foreach($groups as $item){
				$sum = 0;
				$tmp_lng = 0;
				$tmp_lat = 0;
				foreach($item as $tmp){
					$num = $tmp['properties']['STOLEN_VAL'];
					$sum = $sum + $num;
					$tmp_lng = $tmp_lng + $num * $tmp['geometry']['coordinates'][0];
					$tmp_lat = $tmp_lat + $num * $tmp['geometry']['coordinates'][1];
				}
				$tmp_lng = $tmp_lng/$sum;
				$tmp_lat = $tmp_lat/$sum;

				//update this group
				$item[0]['geometry']['coordinates'][0] = $tmp_lng;
				$item[0]['geometry']['coordinates'][1] = $tmp_lat;
				$item[0]['properties']['STOLEN_VAL'] = $sum;

				if($sum < 100){$item[0]['properties']['COLOR'] = $green;}
				else if($sum < 300){$item[0]['properties']['COLOR'] = $orange;}
				else{$item[0]['properties']['COLOR'] = $red;}

				$item[0]['properties']['LOCATION_B'] = 'Zoom in for details';
				array_push($processed_data1, $item[0]);
			}
		$processed_data = $processed_data1;

	}
	
	//echo sizeof($processed_data);

	$resultdata = array();
	$resultdata['type'] = $datajson['type'];
	$resultdata['features'] = $processed_data; 

	$result = array();
	$result['data'] = $resultdata;
	$result['position'] = array('_northEast' => $northEast, '_southWest' => $southWest);

	echo json_encode($result);
?>
