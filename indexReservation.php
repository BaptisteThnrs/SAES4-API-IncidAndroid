<?php
const PREFIX_TO_RELATIVE_PATH = "/statisalle";
require $_SERVER[ 'DOCUMENT_ROOT' ] . PREFIX_TO_RELATIVE_PATH . '/lib/vendor/autoload.php';

use application\DefaultComponentFactory;
use yasmf\DataSource;
use yasmf\Router;

$dataSource = new DataSource(
    $host = 'statisalle-db',
    $port = '3306', 
    $db = 'statisalle', 
    $user = 'statisalle', 
    $pass = 'statisalle', 
    $charset = 'utf8mb4'
);

$router = new Router(new DefaultComponentFactory(), $dataSource);
$router->route(PREFIX_TO_RELATIVE_PATH, $dataSource);