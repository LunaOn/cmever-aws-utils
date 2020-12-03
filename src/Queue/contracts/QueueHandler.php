<?php

namespace cmever\AWS\Queue\contracts;

interface QueueHandler
{
    /**
     * push notification to driver (sns/sqs/redis etc.) and notify event
     * @param string $event
     * @param string $bundleId
     * @param array $data
     * @return bool
     */
    public function push(string $event, string $bundleId, array $data = []): bool;

    /**
     * handle notify callback post data
     * @param array $data origin post data
     * [
     *  'MessageAttributes' => [],
     *  'MessageBody' => 'xxx'
     * ]
     * @return bool
     */
    public function handle(array $data): bool;

    /**
     * long polling receive message
     * @param array $option
     * @return int
     */
    public function longPulling(array $option): int;
}