#!/usr/bin/env python

import sys, getopt, os, json, mapnik

from xml.dom import minidom

try:
  from mapnik import ProjTransform, Projection, Box2d
except ImportError, E:
  sys.exit("Requires Mapnik SVN r822 or greater:\n%s" % E)

def main(argv):
	b = "-180,-90,180,90"	
	i = ""
	o = ""
	l = "0"
	   
	try:
	  opts, args = getopt.getopt(argv,"hb:i:o:l:",["bbox","inputXml","output","layerGrid"])
	except getopt.GetoptError:
	  print 'tilep.py -b <bbox> -i <inputXml> -o <output> -l <layerGrid>'
	  sys.exit(2)	  	
	  
	for opt, arg in opts:
	  if opt == '-h':
		 print 'tilep.py -b <bbox> -i <inputXml> -o <output> -l <layerGrid>'
		 sys.exit()
	  elif opt in ("-b", "--bbox"):
		 b = arg	  
	  elif opt in ("-i", "--inputXml"):
		 i = arg      
	  elif opt in ("-o", "--output"):
		 o = arg   			
	  elif opt in ("-l", "--layerGrid"):
		 l = arg   				 
			
	if not os.path.exists(os.path.dirname(o+".png")):
		os.makedirs(os.path.dirname(o+".png"))
	
	box = []
	for s in b.split(",") :
		box.append(float(s))
		
	geo_extent = Box2d(box[0],box[1],box[2],box[3])
	
	lgrids = []
	for s in l.split(",") :
		lgrids.append(int(s))
	
	geo_proj = Projection('+init=epsg:4326')
	merc_proj = Projection('+init=epsg:3857')	

	transform = ProjTransform(geo_proj,merc_proj)
	merc_extent = transform.forward(geo_extent)
				 	
	image = o+".png"
	mp = mapnik.Map(256,256)
	mapnik.load_map(mp, i)	
	mp.zoom_to_box(merc_extent)
	mapnik.render_to_file(mp, image)
	print "rendered image to '%s'" % image
		
	xmldoc = minidom.parse(i)
	itemlist = xmldoc.getElementsByTagName('Layer') 
	
	for ly in lgrids :	
		dat = itemlist[ly].getElementsByTagName('Datasource')[0] 
		par = dat.getElementsByTagName('Parameter')	
		for s in par :
			if s.attributes['name'].value == 'fields':			
				text = s.childNodes[0].nodeValue.encode("utf-8")			
				fields = text.split(",")		
						
		layer_index = ly #First layer on the map - index in m.layers
		key = "__id__"  #Field used for the key in mapnik (should probably be unique)
		resolution = 4  #Pixel resolution of output.   
		
		enfix = ""
		if ly > 0:
			enfix = "_"+ly
			
		d = mapnik.render_grid(mp, layer_index, key, resolution, fields) #returns a dictionary		
		d = "grid("+json.dumps(d)+")"
		f = open(o+enfix+".json",'wb')
		f.write(d)	
		f.close()

if __name__ == "__main__":
   main(sys.argv[1:])
