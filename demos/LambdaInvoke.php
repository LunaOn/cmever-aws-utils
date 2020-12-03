<?php

use cmever\AWS\Lambda\LambdaHandler;

require(__DIR__.'/../vendor/autoload.php');
require(__DIR__.'/Benchmark.php');

function main()
{
    $times = 3;
    $repeat = 3;

    $pushTest = new Benchmark();
    $pushTest->benchmarkTest(function (int $i) {
        return LambdaHandler::invoke('arn:aws:lambda:us-west-1:xxxx:function:xxx', json_encode([
            'times' => $i,
        ]), 'us-west-1');
    }, $times, $repeat)->renderBenchmarkTable('调用 lambda');
}

main();