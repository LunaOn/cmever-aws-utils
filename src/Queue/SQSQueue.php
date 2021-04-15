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
        'queryUrl' => '',
    ];

    public function setConfig(array $config): bool
    {
        $this->config = array_merge($this->config, $config);
        return true;
    }

    public function __construct(array $option = [])
    {
        $option = array_merge([
            'region' => 'us-west-1',
            'version' => '2012-11-05',
        ], $option);
        $this->client = new SqsClient($option);
    }

    public function push(string $event, string $bundleId, array $data = []): bool
    {
        $messageAttributes = [
            "Event" => [
                'DataType' => "String",
                'StringValue' => $event
            ],
            "BundleId" => [
                'DataType' => "String",
                'StringValue' => $bundleId
            ],
        ];
        $body = json_encode([
            'data' => $data,
        ]);
        return $this->pushToSQS($messageAttributes, $body);
    }

    public function pushToSQS($messageAttributes, $body="", $params = []): bool
    {
        $params = array_merge([
            'DelaySeconds' => 0,
            'MessageAttributes' => $messageAttributes,
            'MessageBody' => $body,
            'QueueUrl' => $this->config['queryUrl'],
        ], $params);
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