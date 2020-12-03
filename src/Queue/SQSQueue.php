<?php


namespace cmever\AWS\Queue;


use Aws\Sqs\SqsClient;
use cmever\AWS\Common\contracts\EventHandler;
use cmever\AWS\Common\traits\EventHandlerTrait;
use cmever\AWS\Queue\exception\QueueException;
use cmever\AWS\Queue\contracts\QueueHandler;

class SQSQueue implements EventHandler, QueueHandler
{
    use EventHandlerTrait;

    protected $client = null;

    protected $config = [
        'region' => 'us-west-1',
        'queryUrl' => '',
    ];

    public function __construct(array $option = [])
    {
        $option = array_merge([
            'profile' => 'default',
            'region' => 'us-west-1',
            'version' => '2012-11-05'
        ], $option);
        $this->client = new SqsClient($option);
    }

    public function push(string $event, string $bundleId, array $data = []): bool
    {
        $params = [
            'DelaySeconds' => 0,
            'MessageAttributes' => [
                "Event" => [
                    'DataType' => "String",
                    'StringValue' => $event
                ],
                "BundleId" => [
                    'DataType' => "String",
                    'StringValue' => $bundleId
                ],
            ],
            'MessageBody' => json_encode([
                'data' => $data,
            ]),
            'QueueUrl' => $this->config['queryUrl'],
        ];
        try {
            $this->client->sendMessage($params);
            return true;
        } catch (\Exception $exception) {
            throw new QueueException($exception->getMessage());
        }
    }

    public function handle(array $data): bool
    {
//        var_dump($data);
        return true;
    }

    public function longPulling(array $option = []): int
    {
        try {
            $option = array_merge([
                'AttributeNames' => ['SentTimestamp'],
                'MaxNumberOfMessages' => 1,
                'MessageAttributeNames' => ['All'],
                'QueueUrl' => $this->config['queryUrl'],
                'WaitTimeSeconds' => 10,
            ], $option);
            $result = $this->client->receiveMessage($option);
            $messages = $result->get('Messages');
            if (empty($messages)) {
                return 0;
            }
            foreach ($messages as $value) {
                if ($this->handle($value)) {
                    $this->client->deleteMessage([
                        'QueueUrl' => $this->config['queryUrl'],
                        'ReceiptHandle' => $value['ReceiptHandle'] // REQUIRED
                    ]);
                }
            }
            return count($messages);
        } catch (\Exception $exception) {
            throw new QueueException($exception->getMessage());
        }
    }
}