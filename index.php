<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Blog\PostMapper;

require __DIR__ . '/vendor/autoload.php';

$loader = new FilesystemLoader(__DIR__ . '/templates');
$view = new Environment($loader);

$config = include __DIR__ . '/config/database.php';

$dsn = $config['dsn'];
$username = $config['username'];
$password = $config['password'];

try {
    $connection = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "CONNECTED SUCCESS!";

} catch (PDOException $exception) {
    echo 'Connection error: ' . $exception->getMessage();
    exit;
}
$postMapper = new PostMapper($connection);

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
    $post =$postMapper->getPostByUrlKey((string) $args['url_key']);
    $body = $view->render('post.twig', ['post' => $post]);
    $response->getBody()->write($body);
    return $response;
});
$app->run();