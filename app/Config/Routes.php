<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::map');
$routes->post('/auth', 'Home::auth');
$routes->get('logout', 'Home::logout');
$routes->get('/map', 'Home::index');
$routes->get('/api/locations', 'MapController::getLocations');

$routes->group('map', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('getLocations', 'MapController::getLocations');
    $routes->post('addLocation', 'MapController::addLocation');
    $routes->put('editLocation/(:num)', 'MapController::editLocation/$1');
    $routes->delete('deleteLocation/(:num)', 'MapController::deleteLocation/$1');
});

