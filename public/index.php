<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/vendor/autoload.php';

use DI\ContainerBuilder;
use function DI\create;

const HANDLER_DELIMITER = '@';
$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->useAnnotations(false);
$container = $containerBuilder->build();

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/api/intervals', 'App\Controllers\IntervalsController@getAllIntervals');
    $r->addRoute('POST', '/api/intervals/new', 'App\Controllers\IntervalsController@createIntervals');
    $r->addRoute('GET', '/api/intervals/deleteall', 'App\Controllers\IntervalsController@deleteAll');
    $r->addRoute('GET', '/', 'App\Controllers\IndexController@index');
    /*$r->addRoute('POST', '/api/intervals', 'App\Controllers\IntervalsController@Greetings');*/
});
// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$queryString = parse_url($uri, PHP_URL_QUERY);
if($queryString)
    parse_str($queryString, $queryString);
// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        header("HTTP/1.0 404 Not Found");        
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        header("HTTP/1.0 405 Method Not Allowed");
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        list($state, $handler, $vars) = $routeInfo;
        list($class, $method) = explode(HANDLER_DELIMITER, $handler, 2);
        $controller = $container->get($class);
        switch ($httpMethod) {
            case 'GET':
                $vars = $queryString;
                break;
            case 'POST':
                $json = file_get_contents("php://input");
                $vars[] = $json; 
                break;
            default:
                $vars = $queryString;
                break;
        }
        $data = '';
        if(!isset($vars)){
            $data = $controller->{$method}();
        }else{
            $data = $controller->{$method}(...array_values($vars));
        }
        echo $data;
        unset($state);
        break;
}