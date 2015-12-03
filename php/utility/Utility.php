<?php

class Utility {
	

	//TODO: Fix this!!
	public static function fixTimeFormat($badTime) {
		return $badTime;
	}

	public static function fixDateFormat ($badDate) {
	//echo "Fixing " . $badDate . "\n";
    	if ($badDate == null || $badDate == "")	
    		return null;
    	

	if (strpos ($badDate, ".") == true)
	{
		$dd = substr($badDate, 0, 2);
	    	$month = substr($badDate, 3, 2);
	    	$yy = substr($badDate, 6, 2);
	
	    	if ($yy > 90)
	    		$yyyy = "19" . $yy;
    		else
    			$yyyy = "20" . $yy;
    		
	    	return $yyyy . "-" . $month . "-" . $dd;

	}

    	$dd = substr($badDate, 0, 2);
    	$month = substr($badDate, 3, 3);
    	$yy = substr($badDate, 7, 2);
    	
    	if (strcmp($month, 'JAN') == 0)
    		$mm = '01';
    	else if (strcmp($month, 'FEB') == 0)
    		$mm = '02';
    	else if (strcmp($month, 'MAR') == 0)
    		$mm = '03';
      	else if (strcmp($month, 'APR') == 0)
   			$mm = '04';
       	else if (strcmp($month, 'MAY') == 0)
    		$mm = '05';
       	else if (strcmp($month, 'JUN') == 0)
    		$mm = '06';
       	else if (strcmp($month, 'JUL') == 0)
    		$mm = '07';
       	else if (strcmp($month, 'AUG') == 0)
    		$mm = '08';
       	else if (strcmp($month, 'SEP') == 0)
    		$mm = '09';
       	else if (strcmp($month, 'OCT') == 0)
    		$mm = '10';
       	else if (strcmp($month, 'NOV') == 0)
    		$mm = '11';
       	else if (strcmp($month, 'DEC') == 0)
    		$mm = '12';
    	else 
    		die ('Unsupported DATE format (' . $badDate . ")");
    	
    	$yyyy = -1;	
    	if ($yy > 90)
    		$yyyy = "19" . $yy;
    	else
    		$yyyy = "20" . $yy;
    		
    	return $yyyy . "-" . $mm . "-" . $dd;
    }		

}



?>
