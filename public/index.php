<?php
require '../bootstrap.php';

// Prepare app
$app = new \Slim\Slim(array(
    'templates.path' => '../templates',
));

$app->controllerNamespace = "\\Application\\Controller";

$auth = new RogerioLino\Slim\AuthMiddleware();
$app->add($auth);

$app->container->singleton('em', function() use ($entityManager) {
    return $entityManager;
});

// Prepare view
$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$app->view->parserExtensions = array(new \Slim\Views\TwigExtension());

// Define routes
$app->get('/(index)', function() use ($app) {
    $app->render('index.html.twig');
});

$app->get('/login', function() use ($app) {
    $app->render('login.html.twig');
});

$app->post('/login', function() use ($app, $auth) {
    try {
        $username = $app->request()->post('username');
        $password = $app->request()->post('password');
        // TODO: login controller
        $controller = new \Application\Controller\LoginController($app->em);
        $controller->auth($username, $password);
        // register and redirect
        $auth->register($username);
    } catch (Exception $e) {
        $app->view()->set('error', $e->getMessage());
        $app->render('login.html.twig');
    }
});

$app->get('/logout', function() use ($app, $auth) {
    $auth->destroy();
    $app->redirect($app->request()->getRootUri() . "/login");
});

// controllers
$app->any('/:controller(/)(:action(/)(:params+))', function($controller, $action = 'index', $params = array()) use ($app) {
    $class = "{$app->controllerNamespace}\\" . ucfirst($controller) . "Controller";
    try {
        if (!class_exists($class)) {
            throw new \Exception('Controller not found');
        }
        $ctrl = new $class($app);
        $methodName = preg_replace('/[^A-z0-9]/', '_', $action);
        $ref = new \ReflectionMethod($class, $methodName);
        if (!$ref->isPublic()) {
            throw new \Exception('Controller method is not public.');
        }
        $view = $methodName;
        $rs = $ref->invokeArgs($ctrl, $params);
        if ($rs) {
            $view = $rs;
        }
        $app->render("{$controller}/{$view}.html.twig");
    } catch (\Exception $e) {
//        echo $e->getMessage();
        $app->notFound();
    }
});

// Run app
$app->run();
