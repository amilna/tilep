tilep
=====

Map tile based on PHP &amp; Python-Mapnik. On apache or nginx webserver this will act as tile servers such as tilestache or tile stream.

Require
-----------
PHP 5, Python-Mapnik

How to Use?
-----------

<p>
Put following code on your php controller (for example /var/www/index.php):
<br>
<br>
<code>
$xml = $_REQUEST['xml'];<br>
$zoom = $_REQUEST['z'];<br>
$xtile = $_REQUEST['x'];<br>
$ytile = $_REQUEST['y'];<br>
$type = $_REQUEST['type'];<br>
$clear = $_REQUEST['clear'];<br>
<br>
include('./tilep.php');<br>
<br>
$tilep = new tilep();<br>
$tilep->xmlDir = './data/'<br>  // this is directory of mapnik xml file
$tilep->createTile($xml,$zoom,$xtile,$ytile,$type,$clear); 
</code>
<br>
<br>
where $xml is path of Mapnik XML (relatively from your php script without .xml, for example: if Mapnik XML path is /var/www/world_style.xml, then $xml should be /var/www/world_style).<br>
$clear is parameter to determine if tilep should re-create or just use existing file. Put any value to clear cache and re-create output files, or leave it blank to use existing cache files.<br>
$type is parameter to determine output type. Available options are utf for output utfgrid, or leave it blank for utput png.<br>
$zoom is zoom level of desired tile, $xtile and $ytile is x and y sequence of tile. Please read Slippy map tilenames convention (http://wiki.openstreetmap.org/wiki/Slippy_map_tilenames).
</p>

<p>
To get png tile, access it by http://localhost/index.php?xml=world_style&z=0&x=0&y=0.<br>
To get utfgrid, access it by http://localhost/index.php?xml=world_style&z=0&x=0&y=0&type=utf.
</p>
<p>
To get utfgrid, you should add fields parameter inside layer datasource tag, for example:<br>
<br>
<code>
&lt;Datasource&gt;<br>
  ..<br>
  &lt;Parameter&gt; name="fields">NAME,ABBREV,POP_EST&lt;/Parameter&gt;<br>
  ..<br>
&lt;/Datasource&gt;<br>
</code>
<br>
this will give utfgrid with data from NAME,ABBREV and POP_EST column (any misstyping will not produce any utfgrid).
</p>
<p>
The produced png or json will located relatively from php script file, for example if  index.php script path is /var/www/index.php, then http://localhost/index.php?xml=world_style&z=0&x=0&y=0 will produce png as /var/www/world_style/0/0/0.png.<br>
So if you need to access the same file at some other time, you don't have to re-create it, just directly request http://localhost/world_style/0/0/0.png.
</p>



