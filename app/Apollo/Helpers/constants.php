<?php
/**
 * @author lvt20160109
 */
define('GMAP_API_KEY', '');
define('PAGINATE', 20);
define('PAGINATION', 20);
define('LIMIT_BIZ', 100);
define('CACHE_MINUTE', 10);
define('CACHE_DAY', 60*24);
define('ADMIN_NAME', 'doodle');
define('PRO_WIDTH', 250);
define('PRO_HEIGHT', 232);
define('WIDTH', 100);
define('HEIGHT', 100);
define('BACKGROUND_WIDTH', 920);
define('BACKGROUND_HEIGHT', 752);
define('AVATAR_SIZE', 135);
if (!defined('IMAGES_URL')) {
    define('IMAGES_URL', env('IMAGES_URL', '/img'));
}
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

?>