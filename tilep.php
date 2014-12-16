<?php

class tilep {
	
	public $xmlDir = './'; /* mapnik xml directory */
	public $pyDir = './'; /* tilep.py directory */
	
	public function getLonLat($xtile, $ytile, $zoom)
	{
		$n = pow(2, intval($zoom));
		$lon_deg = $xtile / $n * 360.0 - 180.0;
		$lat_deg = rad2deg(atan(sinh(pi() * (1 - 2 * $ytile / $n))));	
		return array($lon_deg,$lat_deg);
	}

	public function createTile($xml,$zoom,$xtile,$ytile,$type = false,$clear = false)
	{						
		$dir = $xml."/".$zoom."/".$xtile;
		$bfile = $dir."/".$ytile;						
		
		header("Access-Control-Allow-Origin: *");	
		if ($type == 'utf')
		{
			header("Content-Type: text/json");
			$file = $bfile.".json";	
		}
		else
		{				
			header("Content-Type: image/png");
			$file = $bfile.".png";
		}				
		
		if (file_exists($file) && !$clear)
		{			
			readfile($file);
		}
		else
		{						
			$width = 256; 
			$height = 256;
			$tile_size = 256;

			$xtile_s = ($xtile * $tile_size - $width/2) / $tile_size;
			$ytile_s = ($ytile * $tile_size - $height/2) / $tile_size;
			$xtile_e = ($xtile * $tile_size + $width/2) / $tile_size;
			$ytile_e = ($ytile * $tile_size + $height/2) / $tile_size; 

			$s0 = $this->getLonLat($xtile_s, $ytile_s, $zoom);
			$e0 = $this->getLonLat($xtile_e, $ytile_e, $zoom);

			$s = $this->getLonLat($xtile, $ytile, $zoom);
			$e = $this->getLonLat($xtile+1, $ytile+1, $zoom);

			$s[0] = ($s[0]%360)+($s[0]-floor($s[0]));
			$e[0] = ($e[0]%360)+($e[0]-floor($e[0]));

			$s[0] = $s[0] >= 180? $s[0]-360:$s[0];
			$e[0] = $e[0] > 180? $e[0]-360:$e[0];
			 
			$bbox = $s[0].",".$e[1].",".$e[0].",".$s[1];				
			
			shell_exec("python ".$this->pyDir."tilep.py -i ".$this->xmlDir.$xml.".xml -o ".$bfile." -b ".$bbox);				
			//die("python ".$this->pyDir."tilep.py -i ".$this->xmlDir.$xml.".xml -o ".$bfile." -b ".$bbox);
			readfile($file);
		
		}

	}

}
?>
