<?php
class DATABASE_CONFIG {
/*
    var $default = array(
        'driver'     => 'mysql',
        'persistent' => false,
        'host'       => 'asteria',
        'login'      => 'root',
        'password'   => 'hkdricoh',
        'database'   => 'ricoh_mds_portal_test',
        'prefix'     => '',
        'encoding'   => 'utf8',
    );
    */
    var $default = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'localhost',
        'login'      => 'root',
        'password'   => '',
        'database'   => 'mds',
        'prefix'     => '',
        'encoding'   => 'utf8',
    );
}
