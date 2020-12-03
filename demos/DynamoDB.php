<?php

use cmever\AWS\DynamoDB\DynamoDB;
use cmever\AWS\Lambda\LambdaHandler;

require(__DIR__.'/../vendor/autoload.php');
require(__DIR__.'/Benchmark.php');

function main()
{
    $times = 10;
    $repeat = 3;

    $pushTest = new Benchmark();
    $pushTest->benchmarkTest(function (int $i) {
        $db = new DynamoDB();
        $key = [
            'user_id' => '02a1422de8bd494a8869356077b41212',
        ];
        $params = [
            ':c' => 1,
        ];
        try {
            $res = $db->getDb()->updateItem([
                'TableName' => 'testuser',
                'Key' => DynamoDB::sha1Data($key),
                'UpdateExpression' => 'set coins = coins + :c',
                'ExpressionAttributeValues' => DynamoDB::sha1Data($params),
                'ReturnValues' => 'UPDATED_NEW'
            ]);
//            var_dump($res);
            return 1;
        } catch (\Exception $exception) {
            echo $exception->getMessage();
            return 0;
        }
    }, $times, $repeat)->renderBenchmarkTable('调用 lambda');
}

main();