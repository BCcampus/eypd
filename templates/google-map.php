<div id="et_main_map"></div>
<?php

require get_stylesheet_directory() .'/vendor/autoload.php';

use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Map;

$map = new Map();

// Disable the auto zoom flag (disabled by default)
$map->setAutoZoom(false);
// Sets the center
$map->setCenter(new Coordinate(0, 0));
// Sets the zoom
$map->setMapOption('zoom', 3);
$map->setHtmlContainerId( 'et_main_map' );

