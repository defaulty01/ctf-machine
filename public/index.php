<?php
include_once __DIR__.'/config/configs.php';

spl_autoload_register(function ($name){
    if (preg_match('/Controller$/', $name))
    {
        $name = "controllers/${name}";
    }
    else if (preg_match('/Model$/', $name))
    {
        $name = "models/${name}";
    }
    include_once "${name}.php";
});

$database = new Database('localhost', DBUSER, DBPASS, DBNAME);

$db = $database->connect();

$handler = new CustomSessionHandler();

if (is_null($handler->read('username')))
{
    $handler->write('username', uniqid());
}

SessionModel::update();

$router = new Router();
$router->new('GET', '/', 'UrlController@index');
$router->new('POST', '/c', 'UrlController@create');
$router->new('GET', '/s/{param}', 'UrlController@show');
$router->new('GET', '/p/{param}', 'UrlController@preview');
$router->new('GET', '/pdf/{param}', 'PdfController@index');

$router->new('GET', '/info', function(){
    return phpinfo();
});



$response = $router->match();
$handler->save();

die($response);