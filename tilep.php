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
	
	public function getTile($lon, $lat, $zoom)
	{
		$xtile = floor((($lon + 180) / 360) * pow(2, $zoom));
		$ytile = floor((1 - log(tan(deg2rad($lat)) + 1 / cos(deg2rad($lat))) / pi()) /2 * pow(2, $zoom));
		return array($zoom,$xtile,$ytile);
	}
	
	public function dumpTiles($xml,$bbox,$minzoom,$maxzoom)
	{
		$bbox = split(",",$bbox);
		$tiles = [];
		$min = [floatval($bbox[0]),floatval($bbox[1])];
		$max = [floatval($bbox[2]),floatval($bbox[3])];				
		
		for ($zoom = $minzoom;$zoom <= $maxzoom;$zoom++)
		{
			$t0 = $this->getTile($min[0], $min[1], $zoom);
			$t1 = $this->getTile($max[0], $max[1], $zoom);						
						
			for($x = $t0[1];$x<=$t1[1];$x++)
			{													
				for($y = $t0[2];$y>=$t1[2];$y--)
				{																					
					$tile = [$zoom,$x,$y];					
					if (!in_array($tile,$tiles))
					{
						array_push($tiles,$tile);
						$this->putTile($xml,$zoom,$x,$y);
						$this->putTile($xml,$zoom,$x,$y,"utf");
						echo $zoom."  ".$x."  ".$y." berhasil\n";
					}					
				}	
			}					
		}	
		
		//print_r($tiles);	
	}
	
	public function putTile($xml,$zoom,$xtile,$ytile,$type = false)
	{						
		$dir = $xml."/".$zoom."/".$xtile;
		$bfile = $dir."/".$ytile;						
				
		if ($type == 'utf')
		{		
			$file = $bfile.".json";	
		}
		else
		{						
			$file = $bfile.".png";
		}				
				
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
			$this->putTile($xml,$zoom,$xtile,$ytile,$type);
			readfile($file);
		
		}

	}

}
?>
