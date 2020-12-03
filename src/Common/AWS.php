<?php

namespace cmever\AWS\Common;

class AWS
{
    protected static $clientMap = [];

    /**
     * @param array $config
     * [
     *  'endpoint' => '',
     *  'region' => 'us-west-1',
     *  'version' => 'latest',
     * ]
     * @return \Aws\Sdk
     */
    public static function getClient(array $config)
    {
        $key = md5(json_encode($config));
        if (!isset(self::$clientMap[$key])) {
            self::$clientMap[$key] = new \Aws\Sdk([
                'region' => 'us-west-1',
                'version' => 'latest',
            ]);
        }
        return self::$clientMap[$key];
    }
}