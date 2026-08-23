<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    // 1. ¿A qué rutas de tu API se le aplicarán estas reglas?
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // 2. ¿Qué métodos HTTP están permitidos? (GET, POST, PUT, DELETE, etc.)
    'allowed_methods' => ['*'],

    // 3. ¿Quién tiene permiso de hacer peticiones a tu API?
    // Aquí ponemos el puerto exacto donde corre el servidor de desarrollo de Angular
    'allowed_origins' => ['http://localhost:4200'],

    'allowed_origins_patterns' => [],

    // 4. ¿Qué cabeceras se permiten enviar? (Importante para enviar el Authorization: Bearer...)
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // 5. ¿Se permiten credenciales (como cookies de sesión)? Para JWT no es estrictamente necesario,
    // pero es buena práctica tenerlo en false si solo usas el token en los headers.
    'supports_credentials' => false,

];