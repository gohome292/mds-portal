<?php
mb_language('Japanese');
mb_internal_encoding(Configure::read('App.encoding'));
date_default_timezone_set('Asia/Tokyo');

Configure::write('App.mod_rewrite', true);
// Apacheのmod_rewriteが有効でない
if (!Configure::read('App.mod_rewrite')) {
    Configure::write('App.baseUrl', $_SERVER['SCRIPT_NAME']);
}

define('MAX_FILE_SIZE', intval(ini_get('upload_max_filesize')) * 1048576);
// 1048576 = 1024 * 1024

define('UPLOADS', TMP . 'uploads' . DS);
define('DOWNLOADS', TMP . 'downloads' . DS);

define('DEBUG', Configure::read('debug'));
define('SQLLOG', TRUE);

App::import('Vendor', 'Iggy.log');
App::import('Vendor', 'Iggy.error404');
App::import('Vendor', 'Iggy.mbstring');
App::import('Vendor', 'Iggy.yaml');
App::import('Vendor', 'Iggy._default');

Configure::write('App.name', 'Managed Document Services&trade; Customer Portal site | RICOH JAPAN');
Configure::write('App.pause', 2);
Configure::write('App.screen.compact', false);
Configure::write('App.maintenance.user_id', 1);
Configure::write('App.loginRedirect', '/information/index/');
Configure::write('App.loginAutoRedirect', true);

Configure::write('App.simple_message', true);

Configure::write('App.salt', '5c9ee43da13e3abf2a79f512474e72f8a12b97dc');
Configure::write('App.auto_attach', false);

ini_set('log_errors', '1');
ini_set('error_log', LOGS . 'phperr' . date('Ymd') . '.log');

config('mds_config');

//config('shutdown_config');
