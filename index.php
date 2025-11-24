<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

require __DIR__ . '/vendor/autoload.php';
$loader = new FilesystemLoader(__DIR__ . '/templates');
$view = new Environment($loader);
$config = include 'config/database.php';
$dsn = $config['dsn'];
$username = $config['username'];
$password = $config['password'];

try {
    $conection = new PDO($dsn, $username, $password);
    $conection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $exception) {
    echo 'Connection error: ' . $exception->getMessage();
    exit;
}

$app = AppFactory::create();
$app->get('/', function (Request $request, Response $response, $args) use ($view) {
    $body = $view->render('index.twig');
    $response->getBody()->write($body);
    return $response;
});
$app->get('/about', function (Request $request, Response $response, $args) use ($view) {
    $body = $view->render('about.twig', ['name' => 'Yevhenii']);
    $response->getBody()->write("$body");
    return $response;
});

$app->get('/{url_key}', function (Request $request, Response $response, $args) use ($view) {
    $body = $view->render('post.twig', ['url_key' => $args['url_key']]);
    $response->getBody()->write($body);
    return $response;
});
$app->run();