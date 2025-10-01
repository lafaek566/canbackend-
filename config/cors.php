<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |    'allowedOrigins' => ['http://www.fixleplanner.com.au', 'http://localhost:4200'],
        'supportsCredentials' => false,
    'allowedOrigins' => ['*'],
    'allowedOriginsPatterns' => [],
    'allowedHeaders' => ['*'],
    'allowedHeaders' => ['Content-Type','Authorization'],
    'allowedMethods' => ['*'],
    'exposedHeaders' => [],
    'maxAge' => 0,
    */
   
    //'supportsCredentials' => true,
    //'allowedOrigins' => ['*'],
    //'allowedOriginsPatterns' => [],
    //'allowedHeaders' => ['Content-Type','Authorization'],
    //'allowedMethods' => ['GET', 'POST', 'PUT',  'DELETE', 'PATCH', 'HEAD', 'DESTROY', "OPTION"],
    //'exposedHeaders' => [],
    //'maxAge' => 0,

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => false,

    'max_age' => false,

    'supports_credentials' => false,

];
