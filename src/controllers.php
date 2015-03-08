<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html', array());
})
->bind('homepage')
;

$app->post('/sms', function(Request $request) use ($app) {
    $response = new Response();
    $response->headers->set('Content-Type','text/xml');
    $response->send();
    $number = $request->request->get('From');
    $response->headers->set('Content-Type', 'text/xml');

    $message = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $message .= "<Response>\n";
    $message .= "<Message>Hello, {$number}</Message>\n";
    $message .= "</Response>\n";

    return $message;
});

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html',
        'errors/'.substr($code, 0, 2).'x.html',
        'errors/'.substr($code, 0, 1).'xx.html',
        'errors/default.html',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
