<?php

declare(strict_types=1);

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;				// dependency injection 처리용 ... 얘가 핵심으로 보임 .... 
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../vendor/autoload.php';

// Slim 뜯어보기용 함수. ... 
function leahDebug( $className ) {
	$tmpr = new ReflectionClass($className);

	foreach( $tmpr->getConstants() as $key => $item ) {
		echo '<i><font color="blue">' . $key . ' - ' . $item . '</font></i></br>';
	}
	echo '<br />';
	foreach( $tmpr->getProperties() as $key => $item ) {
		echo '<i><font color="red">' . $key . ' - ' . $item . '</font></i></br>';
	}
	echo '<br />';
	foreach( $tmpr->getMethods() as $key => $item ) {
		//echo $key . " : \n";
		echo '<b>' . $item->name . '</b> : ' . $item->getReturnType() . '<br />';
		foreach ( $item->getParameters() as $item2 ) {
			echo $item2->getType();
			echo ' - ';
			echo $item2->getName();
			echo "<br />";
		}
		//$rtn_type = new ReflectionFunction( $item->name );		// NO !! function ONLY
		//echo 'rtn : ' . $rtn_type->getReturnType() . '<br />';
		echo '<pre>';
		var_dump( $item->getParameters() );
		echo '</pre><hr />';
	}
}

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();		// 일단 생성하면 내부적으로 Container 생성.. 
												// vendor/php-di/ ... /Cointainer.php 
												// psr-11 에서 정의된 container 의 get() - return mixed entry /has() - return bool 
												// 					make() - factory interface make() 	::	https://stackoverflow.com/questions/2083424/what-is-a-factory-design-pattern-in-php
												//					call() - invokeinteface ... call function and paream... 

if (false) { // Should be set to true in production
	$containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
												// return ContainerBuilder
												// set param.... 
												// 캐싱하기위한 디렉토리 경로와 .. 파라메터들 ??? 캐싱 처리용 ??
}

// Set up settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

// 3가지 함수다 .. ContainerBuilder->definitionSources[] 에 읽은 값들을 추가시킴 ... 
// 이후.. $app->호출 ... 로 적용하기 위해서.. 

// Build PHP-DI Container instance
$container = $containerBuilder->build();
	// container 리턴.... 
//leahDebug($container); exit;

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
//leahDebug($app); exit;

// set subdirectory. added by hwoo.
$app->setBasePath('/slim_skeleton_test');

$callableResolver = $app->getCallableResolver();	// ????? 
//leahDebug($callableResolver); exit;

// Register middleware
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

/** @var SettingsInterface $settings */
$settings = $container->get(SettingsInterface::class);
// leahDebug( $app->getContainer()->get(SettingsInterface::class) ); exit;		// its same ... !!  

$displayErrorDetails = $settings->get('displayErrorDetails');
$logError = $settings->get('logError');
$logErrorDetails = $settings->get('logErrorDetails');

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();
	// 이렇게 해서, psr-7 ?? request 를 가져옴. 
	// https://psr.kkame.net/accepted/psr-7-http-message
//leahDebug($request); exit;

// Create Error Handler
$responseFactory = $app->getResponseFactory();
	// $responseFactory->createResponse() 로 Psr\Http\Message\ResponseInterface 반환... 
//leahDebug($responseFactory); exit;
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

// Create Shutdown Handler
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Body Parsing Middleware
$app->addBodyParsingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
