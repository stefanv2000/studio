<?php


$env = getenv('APP_ENV') ?: 'production';



return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => array(
                    'path'=> __DIR__.'/../../data/studio.db',
                )
            ),
            'ormtemp' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => array(
                    'path'=> __DIR__.'/../../data/ studiotemp.db',
                )
            )
        ),
        'entitymanager' => array(
            'orm_default' => array(
                'connection'    => 'orm_default',
                'configuration' => 'orm_default',
            ),
            'ormtemp' => array(
                'connection'    => 'ormtemp',
                'configuration' => 'ormtemp',
            ),
        ),
        'configuration' => array(
            'orm_default' => array(
                'generate_proxies' => ($env === 'development'),
            ),
            'ormtemp' => array(
                'generate_proxies' => ($env === 'development'),
            ),
        )
    ),
);
