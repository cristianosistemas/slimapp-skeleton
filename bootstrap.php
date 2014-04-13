<?php
require __DIR__ . '/vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

define('PASS_SALT', 'ChangeIt,Please!');

$isDev = true;
$paths = array(__DIR__ . '/src/Application/Model');
$driver = new AnnotationDriver(new AnnotationReader(), $paths);
AnnotationRegistry::registerLoader('class_exists');
$config = Setup::createConfiguration($isDev);
$config->setMetadataDriverImpl($driver);
$config->setAutoGenerateProxyClasses($isDev);
$config->setProxyDir(__DIR__ . '/tmp');


$conn = array(
    'host'     => 'localhost',
    'user'     => 'root',
    'password' => '',
    'dbname'   => '',
    'driver'   => 'pdo_mysql',
    'charset' => 'UTF8',
    'driverOptions' => array(
       'SET NAMES utf8;'
    )
);

$entityManager = EntityManager::create($conn, $config, $evm);
