<?php


use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use MongoDB\BSON\ObjectId;
use Phalcon\Http\Response;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;


require_once"../vendor/autoload.php";
$loader = new Loader();
$loader->registerNamespaces(
    [
        'MyApp\Models' => __DIR__ . '/models/',
    ]
);
$loader->register();
// echo __DIR__;
$container = new FactoryDefault();
$container->set(
    'mongo',
    function () {
        $mongo = new \MongoDB\Client("mongodb://mongo", array("username"=>'root', "password"=>"password123"));
        // mongo "mongodb+srv://sandbox.g819z.mongodb.net/myFirstDatabase" --username root
        
        return $mongo;
    },
    true
);

$app = new Micro($container);

$app->get(
    '/api/robots',
    function () use ($app) {
        echo "Hello Buddy <br>";
        $result = $app->mongo->api->robots->find();
        echo "<pre>";
        foreach ($result as $r) {

            echo json_encode($r);
        }
    }
);

$app->get(
    '/api/robots/search/{name}',
    function ($name) use ($app) {
        echo "Search Reslut <br>";
        $result = $app->mongo->api->robots->find(["name"=>$name]);
        foreach ($result as $r) {
            echo json_encode($r);
        }
    }
);

$app->get(
    '/api/robots/{id}',
    function ($id) use ($app) {
        // echo "Hello";
        $result = $app->mongo->api->robots->find(["_id"=>(new ObjectId($id))]);
        foreach ($result as $r) {
            // echo json_encode($r);
            $response = new Response();
            if (count($r) == 0) {
                $response->setJsonContent(
                    [
                        'status' => 'NOT-FOUND'
                    ]
                );
            } else {
                $response->setJsonContent(
                    [
                        'status' => 'FOUND',
                        'data'   => $r
                    ]
                );
            }
    
            return $response;
        }
    }
);

$app->post(
    '/api/robots',
    function () use ($app) {
        // $robot = $app->request->getJsonRawBody();
        $robot = $app->request->getPost();
        echo "<pre>";
        print_r($robot);
        $result = $app->mongo->api->robots->insertOne(['name'=>$robot['name']]);
        print_r($result);
    }
);

$app->put(
    '/api/robots/{id}',
    function ($id) use ($app) {
        $robot = $app->request->getPost();
        print_r($robot);
        // $result = $app->mongo->api->robots->update(
        //     [
        //         "_id" => (new ObjectId($id))
        //     ],
        //     [
        //         "name" => $robot['name']
        //     ]
        // );
        // echo "<pre>";
        // print_r($result);
    }
);

$app->delete(
    '/api/robots/{id:[0-9]+}',
    function ($id) {
    }
);

$app->handle(
    $_SERVER["REQUEST_URI"]
);
// echo $_SERVER["REQUEST_URI"];