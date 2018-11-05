<?php

    return array(
        'app_name' => 'control',
        'server_mode' => (PHP_SAPI === 'cli') ? 'Cli' : 'Http',
        'app_path'=>'apps',
        'project'=>array(
            'name'=>'control',
            'init_apn'=>'main',
        	'view_mode'=>'Php',
        	'ctrl_name'=>'controller',
        	'method_name'=>'action',
            'tpl_path'=>'/apps/control/template'
        ),
        'db'=>array(
            'host'=>'127.0.0.1',
            'DBName'=>'is_1',
            'DBUser'=>'root',
            'DBPass'=>'root',
            'DBPort'=>3306
        ),
        'redis'=>array(
            'host'=>'127.0.0.1',
            'port'=>6379,
            'expire_time'=>'600'
        ),
    );
