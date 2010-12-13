if (typeof (Omeka) == 'undefined') {
	Omeka = new Object();
}

if (!Omeka.NeatlineFeatures) {
	Omeka.NeatlineFeatures = new Array();
}

Omeka.NeatlineFeatures.initializeWidget = function() {

	var wgs84 = new OpenLayers.Projection("EPSG:4326");
	var spherical = new OpenLayers.Projection("EPSG:900913");
	
	var myStyles = new OpenLayers.StyleMap({
        "default": new OpenLayers.Style({
            fillColor: "none",
            strokeColor: "blue",
            strokeWidth: 3
        }),
        "select": new OpenLayers.Style({
            fillColor: "red",
            strokeColor: "red"
        })
    });

	 map = new OpenLayers.Map('map', {
		projection : wgs84,
		controls: [new OpenLayers.Control.Navigation(),new OpenLayers.Control.PanZoom(), new OpenLayers.Control.LayerSwitcher()], 
		numZoomLevels : 128
	});
	
/*
 * map.addLayer(new OpenLayers.Layer.WMS( "Terraserver", "
 * http://terraservice.net/ogcmap.ashx", {layers: 'DOQ', srs:"EPSG:4326"},
 * {projection: wgs84} ));
 */
	map.addLayer(new OpenLayers.Layer.OSM("OpenStreetMap"));
	
	var gml = jQuery("textarea[name='" + inputNameStem + "[text]']").val();
	features = gml ? new OpenLayers.Format.GML().read(gml) : new Array();
	jQuery(features).each(function(){this.geometry.transform(wgs84,spherical)});
	featurelayer = new OpenLayers.Layer.Vector("feature", { styleMap: myStyles, projection: wgs84 });
	if (features) {
		featurelayer.addFeatures(features);
	}
	map.addLayer(featurelayer);

var controls = {
            modify: new OpenLayers.Control.ModifyFeature(featurelayer, {
                onModificationEnd : function(feature) {
                /* the UPDATE state is modified here!!!! */
                feature.state = OpenLayers.State.UPDATE;
				        },
				        onDelete : function(feature) {
				        },
				        displayClass : "olControlModifyFeature",
				        title: "Modify a feature on the image"
				}),
            drag: new OpenLayers.Control.DragFeature(featurelayer, {
            		displayClass : "olControlDragFeature",
            		title: "Move a feature around once selected"
            }),
            polygon: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Polygon,
                        { handlerOptions : {
            				multi : true
        				},
        				displayClass : "olControlDrawFeaturePolygon",
        		        title: "Draw a polygonal feature"
                    }),
            line: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Path,
                        { handlerOptions : {
            				multi : true
        				},
        				displayClass : "olControlDrawFeaturePath",
        		        title: "Draw a linear feature"
            }),
            point: new OpenLayers.Control.DrawFeature(featurelayer,
                        OpenLayers.Handler.Point,
                        { handlerOptions : {
                				multi : true
            				},
            				displayClass : "olControlDrawFeaturePoint",
            		        title: "Draw a point feature"
            }),
            save : new OpenLayers.Control.Button( {
                    trigger : function() {
            					jQuery(featurelayer.features).each(function(){this.geometry.transform(spherical,wgs84)});	
		                    var gml = new OpenLayers.Format.GML().write(featurelayer.features);
		                    jQuery("textarea[name='" + inputNameStem + "[text]']").html(gml);
		                    },
                    displayClass : "olControlSaveFeatures",
                    title: "Save your changes"
            }),
            newlayer : new OpenLayers.Control.Button( {
                trigger : function() { addlayerdialog.dialog("open"); },
                displayClass : "olNewLayer",
                title: "Add new layer"
            }),
            selectCtrl : new OpenLayers.Control.SelectFeature(featurelayer,
                    { clickout: true,
            			displayClass: "olControlSelectFeatures",
            			title: "Use this control to select shapes and navigate the map"}
                )
        };

    		var panel = new OpenLayers.Control.Panel({
				div: document.getElementById('mappanel')
    	    });
        for(var key in controls) {
            panel.addControls(controls[key]);
        }
    map.addControl(panel);
    
	var addlayerdialog = jQuery("#addlayerdialog").dialog( {
		"autoOpen": false,
		"draggable": true,
		"height": 'auto',
		"width": 500,
		"title": "Add a Layer...",
		"closeOnEscape": true,
		"buttons": { "Add": 
				function() { 
					var id = jQuery("#layerselect")[0].value;
					jQuery.get("/maps/serviceaddy/" + id, function(serviceaddy){ 
						jQuery.get("/maps/layername/" + id, function(layername) {
							var label =jQuery("#layerselect option")[jQuery("#layerselect")[0].selectedIndex].label;
							map.addLayer(new OpenLayers.Layer.WMS( label, serviceaddy, {"layers": layername}));
						});
					});
					jQuery(this).dialog("close"); } }
		});

    controls.selectCtrl.activate();
    if (features.length > 0) {  	
    		var coll = new OpenLayers.Geometry.Collection();
    		var coll = new OpenLayers.Geometry.Collection();
        jQuery(features).each(function() {
        		coll.addComponents([this.geometry]);
        });
    		coll.calculateBounds();
    		map.zoomToExtent(coll.getBounds());
	}
    else {
    		map.zoomToMaxExtent();
    }
    
}

