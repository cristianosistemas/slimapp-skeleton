<?php
require __DIR__ . '/vendor/autoload.php';

use \Doctrine\ORM\Tools\Setup;
use \Doctrine\ORM\EntityManager;
use \Doctrine\Common\EventManager;
use \SimpleThings\EntityAudit\AuditConfiguration;
use \SimpleThings\EntityAudit\AuditManager;

define('PASS_SALT', 'ChangeIt,Please!');

$isDev = true;
$paths = array(__DIR__ . '/lib/Application/Model');
$config = Setup::createAnnotationMetadataConfiguration($paths, $isDev);
$config->setAutoGenerateProxyClasses($isDev);
$config->setProxyDir(__DIR__ . '/tmp');

$conn = array(
    'host'     => 'localhost',
    'user'     => 'root',
    'password' => '',
    'dbname'   => '',
    'driver'   => 'pdo_mysql'
);

$auditconfig = new AuditConfiguration();
$auditconfig->setAuditedEntityClasses(array(
    //'Application\Model\AuditableModelHere'
));
$evm = new EventManager();
$auditManager = new AuditManager($auditconfig);
$auditManager->registerEvents($evm);

$entityManager = EntityManager::create($conn, $config, $evm);
