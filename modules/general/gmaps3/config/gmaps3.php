<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
  // Configuration
  'maps_url'       => 'http://maps.google.com/maps/api/js', // V3 URL
	'geocoding_url'  => 'http://maps.googleapis.com/maps/api/geocode',	
	'sensor'         => FALSE,           // Activate localization sensor	
	'language'       => 'ru',            // i18n = Autodetect language from Kohana framework (Also you can use en, es, de...)
	'region'         => FALSE,           // Use GB, ES, US and force the region localization (FALSE = auto)  	
	'default_lat'    => '57.397',     // Default center lat
  'default_lon'    => '70.644',    	 // Default center lon
  'default_zoom'   => 3,							 // Default zoom 
  'default_type'   => 'ROADMAP',       // Default map type (Possible values: ROADMAP, SATELLITE, HYBRID, TERRAIN)  
  
  // Map options    
  'options'       => array
                     (
                      // Zoom options                      
                      'maxZoom'               => 'null',     // Set max zoom (null = Max zoom allowed)
                      'minZoom'               => 'null',     // Set min zoom (null = Min zoom allowed)
                      'zoomControl'           => TRUE,       // Show zoom control
                      'zoomControlOptions'    => array
                                                 (
                                                  'position' => 'DEFAULT',    // DEFAULT, TOP_CENTER, RIGHT_CENTER, BOTTOM_CENTER...
                                                  'style'    => 'DEFAULT'     // DEFAULT, SMALL, LARGE
                                                 ),
                                                    
                      
                      // Pan options
                      'panControl'            => TRUE,   // Show pan control
                      'panControlOptions'     => array
                                                 (
                                                  'position' => 'DEFAULT'    // DEFAULT, TOP_CENTER, RIGHT_CENTER, BOTTOM_CENTER...
                                                 ),
                                                   
                      
                      // Map type options
                      'mapTypeControl'        => TRUE,   // Show maptype control
                      'mapTypeControlOptions' => array
                                                 (
                                                  'position' => 'DEFAULT', // DEFAULT, TOP_CENTER, RIGHT_CENTER, BOTTOM_CENTER...
                                                  'style'    => 'DEFAULT'  // DEFAULT, HORIZONTAL_BAR, DROPDOWN_MENU
                                                 ),
                                                       
                      // Scale options
                      'scaleControl'          => TRUE,   // Show scale control
                      'scaleControlOptions'   => array
                                                 (
                                                  'position' => 'DEFAULT'  // DEFAULT, TOP_CENTER, RIGHT_CENTER, BOTTOM_CENTER...
                                                 ),
                      
                      // Streetview options
                      'streetViewControl'     => TRUE,   // Show streeview control
                      'streetViewControlOptions' => array
                                                    (
                                                     'position' => 'DEFAULT' // DEFAULT, TOP_CENTER, RIGHT_CENTER, BOTTOM_CENTER...
                                                    ),
                      
                      // Overview options 
                      'overviewMapControl'    => TRUE,  // Show overview control
                      'overviewControlOptions'=> array
                                                 (
                                                  'opened' => TRUE       // Overview open by default
                                                 ),
                      
                      // Usability options
                      'scrollwheel'           => FALSE, // Use scrollwheel
                      
                      
                                                                                                                              
                     ), 
                     
   
                                      
  // Perspective options
  'tilt'           => 0,               // 45 = Enable 45º imagery, 0 = Disabled (1 to 360º)
  'rotation'       => 0,               // Image orientation, only works if tilt > 0. Recommended value = 90
  
  // Marker options                                                                             
  'default_icon'   => URL::base().Modules::uri('gmaps3').('/public/css/frontend/img/map-point.png'),
  'draggable'      => FALSE,           // Draggable
  'icon_size'      => array('width' => 25, 'height' => 46),   // Icon size (Width, Height) (Default 32 x 32)
  'icon_origin'    => array('x'     => 0 , 'y'      => 0 ),   // Icon origin (Default x=0 y=0)
  'icon_anchor'    => array('width' => 12 , 'height' => 45),   // Icon anchor (Width, Height) (Default 9 x 34)
  'view_shadow'    => FALSE,
  //'default_shadow' => 'http://maps.google.com/mapfiles/ms/micons/msmarker.shadow.png',       
  //'shadow_size'    => array('width' => 59, 'height' => 32),   // Shadow icon size (Top, Left)  
  
  // Layers (You can include you own layer library)
  'layers'         => array(//array('lib' => 'panoramio', 'instance' => 'google.maps.panoramio.PanoramioLayer()'),
                            //array('lib' => NULL,        'instance' => 'google.maps.BicyclingLayer()'          ),
                            //array('lib' => NULL,        'instance' => 'google.maps.TrafficLayer()'            ),
                           )     
);
