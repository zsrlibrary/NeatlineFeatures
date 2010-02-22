
$wkt = item("Dublin Core","Coverage");
$tags = item_tags_as_string( );
$query = array("tags" => $tag);
$backgroundMaps = get_items($query);
$backgroundLayers = array();
foreach ( $backgroundMaps as $mapid )
{
	$map = get_item_by_id($mapid,"Item");
	$layertitle = "A map with no title";
	try {
		$layertitle = item( 'Dublin Core', 'Title', array("delimiter" => ",", "snippet" => 20, "all" => true),$map);
	}
	catch (Omeka_Record_Exception $e) {
		$logger->err($e);
	}
	$serviceaddy = $this->getServiceAddy($map);
	$layername = $this->getLayerName($map);
	$backgroundLayers["$layertitle"] = array("layername" => $layername, "serviceaddy" => $serviceaddy);
}
?>
<div id='Locate'>
<link rel="stylesheet"
	href="http://dev.openlayers.org/releases/OpenLayers-2.8/theme/default/style.css"
	type="text/css" />
<link rel="stylesheet" href="<?php echo css('edit'); ?>" />
<script type="text/javascript"
	src="http://openlayers.org/api/OpenLayers.js">�</script> <script
	type="text/javascript" defer="defer">
			//<![CDATA[
				itemid = "<?php echo item('ID'); ?>";
				feature = new OpenLayers.Format.WKT().read("<?php echo $wkt ; ?>");		
				layers = new Array();
				<?php 
					foreach ($backgroundLayers as $layername => $layervalues) {
	 				   ?> 
	 				   layers.push( { "title":"<?php echo $layername ?>", 
	 		 				   			"address":"<?php echo $layervalues["serviceaddy"] ?>",
	 		 		 				   	"layername":"<?php echo $layervalues["layername"] ?>" } ) ;
	 				   <?php 
					}
				?>
				//]]> 	
			</script> <?php echo js("features/edit/edit"); ?> <?php echo js("features/edit/save"); ?>


<div id="map"
	style="height: 400px; width: 700px; border: 1px solid #ccc; float: right;"></div>
<script type="text/javascript" defer="defer">
		//<![CDATA[
		edit();
		//]]>
	</script></div>

