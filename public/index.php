<?php
declare(strict_types=1);

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Http\Response;
/* JWT */
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;
/* JWT */

/* REDIS */
use Phalcon\Cache\Adapter\Redis;
use Phalcon\Storage\SerializerFactory;
/* REDIS */

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

function Redis() {
    $serializerFactory = new SerializerFactory();

    $options = [
        'defaultSerializer' => 'Json',
        'lifetime'          => 7200,
        'host'              => '127.0.0.1',
        'port'              => 6379,
        'index'             => 1,
    ];

    Return new Redis($serializerFactory, $options);
}

$loader = new Loader();

$loader->registerNamespaces(
    [
        'MyApp\Models' => __DIR__ . '/models/',
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'db',
    function () {
        return new PdoMysql(
            [
                'host'     => 'localhost',
                'username' => 'root',
                'password' => '',
                'dbname'   => 'company',
            ]
        );
    }
);

$app = new Micro($container);

$app->get(
    '/', function () {
});

$app->post(
    '/api/login',
    function () use ($app) {

        $data = $app->request->getJsonRawBody();
        $phql  = 'SELECT * '
            . 'FROM MyApp\Models\Merchant '
            . ' WHERE name = :name:'
            . ' AND password = :password:'
        ;

        $status = $app
            ->modelsManager
            ->executeQuery(
                $phql,
                [
                    'name'      =>  $data->name,
                    'password'  =>  $data->password,
                ]
            )
            ->getFirst()
        ;


        $response = new Response();

        if ($status === null) {
            $response->setJsonContent(
                [
                    'status' => 'NOT-FOUND'
                ]
            );
        } else {

            // token oluşturup kayıt edelim
            $signer  = new Hmac();
            $builder = new Builder($signer);

            $now        = new DateTimeImmutable();
            $issued     = $now->getTimestamp();
            $notBefore  = $now->modify('-1 minute')->getTimestamp();
            $expires    = $now->modify('+1 day')->getTimestamp();
            $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

            $builder
                ->setAudience('https://target.phalcon.io')  // aud
                ->setContentType('application/json')        // cty - header
                ->setExpirationTime($expires)               // exp
                ->setId('abcd123456789')                    // JTI id
                ->setIssuedAt($issued)                      // iat
                ->setIssuer('https://phalcon.io')           // iss
                ->setNotBefore($notBefore)                  // nbf
                ->setSubject('my subject for this claim')   // sub
                ->setPassphrase($passphrase)                // password
            ;

            $tokenObject = $builder->getToken();

            redis()->set($status->id, $tokenObject->getToken()); // id ve hash
        }
        return $response;
    }
);

$app->post(
    '/api/newOrder',
    function () use ($app) {

        $data = $app->request->getJsonRawBody();
        if (Redis()->get("$data->user_id")) {
            $phql = 'INSERT INTO MyApp\Models\Orders '
                . '(orderCode, product_id, quantity, adress, shippingDate) '
                . 'VALUES '
                . '(:orderCode:, :product_id:, :quantity:, :adress:, :shippingDate:)'
            ;
            if ($data->shippingDate == "") {
                $data->shippingDate = null;
            }
            $status = $app
                ->modelsManager
                ->executeQuery(
                    $phql,
                    [
                        'orderCode' => $data->orderCode,
                        'product_id' => $data->product_id,
                        'quantity' => $data->quantity,
                        'adress' => $data->adress,
                        'shippingDate' => $data->shippingDate,
                    ]
                );
            $response = new Response();

            if ($status->success() === true) {
                $response->setStatusCode(201, 'Created');

                $data->id = $status->getModel()->id;

                $response->setJsonContent(
                    [
                        'status' => 'OK',
                        'data'   => $data,
                    ]
                );
            } else {
                $response->setStatusCode(409, 'Conflict');

                $errors = [];
                foreach ($status->getMessages() as $message) {
                    $errors[] = $message->getMessage();
                }

                $response->setJsonContent(
                    [
                        'status'   => 'ERROR',
                        'messages' => $errors,
                    ]
                );
            }

            return $response;
        } else {
            // yetkisiz
        }


    }
);

$app->put(
    '/api/update/{id:[0-9]+}',
    function ($id) use ($app) {

        $data = $app->request->getJsonRawBody();

        if (Redis()->get("$data->user_id")) {

            $phql = 'SELECT id FROM MyApp\Models\Orders WHERE id = :id: AND shippingDate IS NULL';

            $status = $app
                ->modelsManager
                ->executeQuery(
                    $phql,
                    [
                        'id' => $id,
                    ]
                )->getFirst();
            $response = new Response();

            if ($status != null) {

                $phql = 'UPDATE MyApp\Models\Orders '
                    . 'SET '
                    . 'orderCode = :orderCode: ,'
                    . 'product_id = :product_id: , '
                    . 'quantity = :quantity: , '
                    . 'adress = :adress: , '
                    . 'shippingDate = :shippingDate: '
                    . 'WHERE id = :id:'
                ;

                $status = $app
                    ->modelsManager
                    ->executeQuery(
                        $phql,
                        [
                            'id' => $id,
                            'orderCode' => $data->orderCode,
                            'product_id' => $data->product_id,
                            'quantity' => $data->quantity,
                            'adress' => $data->adress,
                            'shippingDate' => $data->shippingDate,
                        ]
                    )
                ;

                $response = new Response();

                if ($status->success() === true) {
                    $response->setJsonContent(
                        [
                            'status' => 'OK'
                        ]
                    );
                } else {
                    $response->setStatusCode(409, 'Conflict');

                    $errors = [];

                    foreach ($status->getMessages() as $message) {
                        $errors[] = $message->getMessage();
                    }

                    $response->setJsonContent(
                        [
                            'status'   => 'ERROR',
                            'messages' => $errors,
                        ]
                    );
                }
            } else {
                $response->setJsonContent(
                    [
                        'status'   => 'ERROR',
                        'messages' => 'Not Found Id or Shipping Dates not null',
                    ]
                );
            }

            return $response;

        } else {
            //yetkisiz


        }
    }
);

$app->post(
    '/api/getOrders',
    function () use ($app){

        $data = $app->request->getJsonRawBody();

        if (Redis()->get("$data->user_id")) {

            $phql = 'SELECT * FROM MyApp\Models\Orders WHERE id = :id:';

            $status = $app
                ->modelsManager
                ->executeQuery(
                    $phql,
                    [
                        'id' => $data->id,
                    ]
                )->getFirst();

            $response = new Response();

            if (empty($status)) {
                $response->setJsonContent(
                    [
                        'status' => 'NOT-FOUND'
                    ]
                );
            } else {
                $response->setJsonContent(
                    [
                        'status' => 'FOUND',
                        'data'   => [
                            'id'   => $status->id,
                            'orderCode' => $status->orderCode,
                            'product_id' => $status->product_id,
                            'quantity' => $status->quantity,
                            'adress' => $status->adress,
                            'shippingDate' => $status->shippingDate,
                        ]
                    ]
                );
            }

            return $response;
        } else {
            // yetkisiz
        }
    }
);

$app->post(
    '/api/Orders',
    function () use ($app) {

        $data = $app->request->getJsonRawBody();

        if (Redis()->get("$data->user_id")) {

            $phql = 'SELECT * '
                . 'FROM MyApp\Models\Orders '
            ;

            $orders = $app
                ->modelsManager
                ->executeQuery($phql)
            ;

            $data = [];

            foreach ($orders as $order) {
                $data[] = [
                    'id'   => $order->id,
                    'orderCode' => $order->orderCode,
                    'product_id' => $order->product_id,
                    'quantity' => $order->quantity,
                    'adress' => $order->adress,
                    'shippingDate' => $order->shippingDate,
                ];
            }

            echo json_encode($data);
        } else {
            // yetkisiz
        }

    }
);

$app->handle(
    $_SERVER["REQUEST_URI"]
);
