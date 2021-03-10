<?php

use cmever\AWS\Queue\SQSQueue;

require(__DIR__.'/../vendor/autoload.php');
require(__DIR__.'/Benchmark.php');

function main()
{
    $sqs = new SQSQueue([
        'profile' => 'default'
    ]);
    $sqs->setConfig([
        'queryUrl' => 'https://sqs.us-west-1.amazonaws.com/xxxxxx/xxxx'
    ]);
    $times = 1;
    $repeat = 3;

    $pushTest = new Benchmark();
    $pushTest->benchmarkTest(function (int $i) use ($sqs) {
        return $sqs->pushToSQS([
            "BundleId" => [
                'DataType' => "String",
                'StringValue' => "test.unit.aws-utils"
            ],
        ], "for test");
    }, $times, $repeat)->renderBenchmarkTable('推送消息');

    // clear
    $count = 0;
    do {
        $res = $sqs->longPulling([
            'MaxNumberOfMessages' => 10,
        ]);
        $count += $res;
//        echo "clearing curr:".$count."\n";
    } while($res);
    echo "clear about ".$count." messages <br>";

    $delayTest = new Benchmark();
    $delayTest->benchmarkTest(function (int $i) use ($sqs) {
        $sqs->push('add-coins', 'test.bundle.id', [
            'total' => 100 + $i,
        ]);
        return $sqs->longPulling([
            'WaitTimeSeconds' => 5
        ]);
    }, $times, $repeat)->renderBenchmarkTable('推送获取延迟');
}

main();