<?php
//
//	Calculate great circle distance
//
function great_circle_distance($lat1,$lat2,$lon1,$lon2) 	{
	$lat1 = deg_to_rad(abs($lat1));
	$lon1 = deg_to_rad((abs($lon1));
	$lat2 = deg_to_rad(abs($lat2));
	$lon2 = deg_to_rad(abs($lon2));
	$delta_lat = $lat2 - $lat1;
	$delta_lon = $lon2 - $lon1;
	$temp = pow(sin($delta_lat/2.0),2) + cos($lat1) * cos($lat2) * pow(sin($delta_lon/2.0),2);
	$EARTH_RADIUS = 3956;
	$distance = $EARTH_RADIUS * 2 * atan2(sqrt($temp),sqrt(1-$temp));
	return $distance;
}

function deg_to_rad($deg) {
	$radians = 0.0;
	$radians = $deg * M_PI/180.0;
	return($radians);
}

