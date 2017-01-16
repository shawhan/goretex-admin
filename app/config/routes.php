<?php

$router = new \Phalcon\Mvc\Router(false);
$router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);
$router->removeExtraSlashes(true);
$router->setDefaults(array(
    'controller' => 'index',
    'action' => 'index'
));

$list = array(
    'banner',
    'news',
    'media',
    'case',
    'activity'
);
foreach ($list as $k => $row) {
    $router->add("/$row", array(
        'controller'    => $row,
        'action'        => 'list',
    ));
    $router->add("/$row/add", array(
        'controller'    => $row,
        'action'        => 'add',
    ));
    $router->add("/$row/add", array(
        'controller'    => $row,
        'action'        => 'addPost',
    ))->via('POST');
    $router->add("/$row/edit/{id}", array(
        'controller'    => $row,
        'action'        => 'edit',
    ));
    $router->add("/$row/edit/{id}", array(
        'controller'    => $row,
        'action'        => 'editPost',
    ))->via('POST');
    $router->add("/$row/delete/{id}", array(
        'controller'    => $row,
        'action'        => 'delete',
    ));
}

$router->add('/login', array(
    'controller'    => 'auth',
    'action'        => 'login',
))->via('GET')->setName('auth-login');

$router->add('/login', array(
    'controller'    => 'auth',
    'action'        => 'loginPost',
))->via('POST')->setName('auth-loginPost');

$router->add('/logout', array(
    'controller'    => 'auth',
    'action'        => 'logout',
))->via('GET')->setName('auth-logout');

$router->add('/output', array(
    'controller' => 'index',
    'action' => 'output'
));
$router->add('/input', array(
    'controller' => 'index',
    'action' => 'input'
));
$router->add('/beauty_count', array(
    'controller' => 'index',
    'action' => 'beautyCount'
));
$router->add('/beauty', array(
    'controller' => 'index',
    'action' => 'beauty'
));
$router->add('/')->setName('index');

$router->notFound(array(
    'controller'    => 'error',
    'action'        => 'show404',
));

return $router;

