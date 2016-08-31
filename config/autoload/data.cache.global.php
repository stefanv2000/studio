<?php

$env = getenv('APP_ENV') ?: 'production';

return array(
    'data.cache.json' => array(
        'folder' => 'cache/json/',
        'shouldcache' => ($env == 'production'),
        'displayname' => ($env == 'production'),
    ),
);
