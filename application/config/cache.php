<?
return [
    'memcache' => array
    (
        'driver'             => 'memcache',
        'default_expire'     => 3600,
        'compression'        => FALSE,              // Use Zlib compression
        'servers'            => array
        (
            'local' => array
            (
                'host'             => 'localhost',  // Memcache Server
                'port'             => 11211,        // Memcache port number
                'persistent'       => FALSE,        // Persistent connection
            ),
        ),
),
];