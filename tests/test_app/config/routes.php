<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::prefix('Admin', function (RouteBuilder $routes) {
    $routes->fallbacks('DashedRoute');
});
