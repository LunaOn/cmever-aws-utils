<?php

namespace cmever\AWS\DynamoDB;

use cmever\AWS\Common\AWS;
use cmever\AWS\Common\contracts\db\DBHandler;

class DynamoDB implements DBHandler
{
    /**
     * get db handler
     * @return \Aws\DynamoDb\DynamoDbClient
     */
    public function getDb()
    {
        $sdk = AWS::getClient([
            'region' => 'us-west-1',
            'version' => 'latest',
        ]);
        return $sdk->createDynamoDb();
    }

    /**
     * sha1 data array
     * @param array $data
     * @return array
     */
    public static function sha1Data(array $data)
    {
        return (new \Aws\DynamoDb\Marshaler())->marshalJson(json_encode($data));
    }

    public function create(string $table, array $data): bool
    {
        try {
            $this->getDb()->putItem([
                'TableName' => $table,
                'Item' => self::sha1Data($data),
            ]);
            return true;
        } catch (\Aws\DynamoDb\Exception\DynamoDbException $exception) {
            return false;
        }
    }

    public function update(string $table, array $where, array $data): int
    {
        // TODO: Implement update() method.
    }

    public function incr(string $table, array $where, string $field, $num): int
    {
        // TODO: Implement incr() method.
    }

    public function decr(string $table, array $where, string $field, $num): int
    {
        // TODO: Implement decr() method.
    }

    public function delete(string $table, array $where): int
    {
        // TODO: Implement delete() method.
    }
}