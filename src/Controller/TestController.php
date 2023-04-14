<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

//use Slim\Factory\AppFactory;  // 로딩해서 쓸 수 있지만 index.php 에서 초기화 된 값이 넘어오지 않음.. 초기화 로직 그대로 탐 ... 
use App\Infrastructure\Persistence\User\InMemoryUserRepository as UserRepository;

use App\Application\Actions\Action;

class TestController
{
    //public function __construct( UserRepository $userRepository ) {     // 뭐 이케 주입되는거 같고 ... 
    public function __construct() {     // 뭐 이케 주입되는거 같고 ... 
        echo '<p>constructor called</p>';

        //var_dump( $userRepository->findAll() );
        //leahDebug($userRepository); exit;
        //leahDebug($appFactory); exit;
    }

    /*
    public function __invoke(Request $request, Response $response, $args = []) {
        return $response->getBody()->write("Hello World");
    }

    public function action()    //: Response
    {
        echo 'hmmmbob';
    }
    */

    public function test(Request $request, Response $response): Response
    {
        //leahDebug($this); exit;
        //leahDebug($request); exit;
        //leahDebug($response); exit;

        /*
        $accept = $request->getHeaders();
        echo '<pre>';
        var_dump( $accept );
        echo '</pre>';

        $accept = $request->getHeader('Accept');
        var_dump( $accept );

        $accept = $request->getHeaderLine('Accept');
        var_dump( $accept );

        echo '<hr />';
        
        //$response->getBody()->write('hello sidney ');
        //$response->getBody()->write('byebye sidney ');

        echo json_encode( [ 'aaa' => 'bbb'] );
        */

        /*
        echo '<pre>';
        var_dump( $request->getUri()->getHost() );
        var_dump( $request->getUri()->getPort() );
        var_dump( $request->getUri()->getPath() );
        echo '</pre>';
        */
        
        $response = $response->withAddedHeader('autho', '오뜨케오뜨케');         // 헤더 쑤셔넣기 ... 리턴값을 받아서 적용시켜야 함 ... 
                                                                                // 404 에도 먹힌다 ... 
                                                                                // 즉, 404 오류도 header를 통해서 써먹을 수 있음 ... 
        //$response->getBody()->write( json_encode( [ 'aaa' => 'bbb'] ) );

        //$response->withStatus( 404, json_encode( [ 'aaa' => 'bbb'] ) );       // 안먹힘..
        //$response->withAddedHeader('autho', '오뜨케오뜨케');
        $response = $response->withStatus(404, '낫 found');                     // 먹힘... 대신 앞에서 response write가 없어야 먹힘 ... 

        $response->getBody()->write('blurblur...');                             // 404 에도 본문 싣는건 문제 없음.. 공용 코드일 뿐. 어떻게 쓰느냐는 유저 맘대루.
                                                                                // 일반 브라우저도 body에 내용이 있으면 404라도 출력됨 ... 

        //$response = $response->withBody('updated body');                        // 안됨.. 얘는 스트림만 잡는 애 ... 

        // json 출력 ... 
        $response->getBody()->write( json_encode( [ 'aaa' => 'bbb'] ) );
        $response = $response->withHeader('Content-Type', 'application/json')->withStatus(201, '뻘짓함');     // 한번에는 안됨 ... 나눠서 ..

        return $response;
    }
}
