<?php
namespace cmever\AWS\Lambda;

use Aws\Lambda\Exception\LambdaException;
use Aws\Lambda\LambdaClient;
use Psr\Http\Message\StreamInterface;

class LambdaHandler
{
    const INVOCATION_TYPE_EVENT = 'Event';
    const INVOCATION_TYPE_REQUEST = 'RequestResponse';
    const INVOCATION_TYPE_DRY = 'DryRun';

    const INVOCATION_RESULT_CODE_MAP = [
        self::INVOCATION_TYPE_REQUEST => 200,
        self::INVOCATION_TYPE_EVENT => 202,
        self::INVOCATION_TYPE_DRY => 204,
    ];

    const LOG_TYPE_NONE = 'None';
    const LOG_TYPE_TAIL = 'Tail';

    /**
     * invoke lambda, doc: https://docs.aws.amazon.com/aws-sdk-php/v2/api/class-Aws.Lambda.LambdaClient.html#_invoke
     * @param string $functionName function name for lambda
     * @param string $payload json string
     * @param string $version function version
     * @param string $region us-west-2 etc.
     * @param array $config
     * [
     *  'profile' => 'credentials file (optional)',
     *  'InvocationType' => 'default: Event',
     *  'LogType' => 'default: None',
     *  'ClientContext' => 'default: base64($payload)',
     * ]
     * @return string
     */
    public static function invoke(string $functionName, string $payload, string $region = 'us-west-1', string $version = '$LATEST', array $config = [])
    {
        $clientConfig = array_merge([
            'region' => $region,
            'version' => '2015-03-31'
        ], $config);
        try {
            $client = new LambdaClient($clientConfig);
            $invokeConfig = [
                'FunctionName' => $functionName,
                'InvocationType' => $config['InvocationType'] ?? self::INVOCATION_TYPE_REQUEST,
                'LogType' => $config['LogType'] ?? self::LOG_TYPE_NONE,
                'ClientContext' => $config['ClientContext'] ?? base64_encode($payload),
                'Payload' => $payload,
                'Qualifier' => $version,
            ];
            $response = $client->invoke($invokeConfig);

            // check code
            $sourceCode = $response->get('StatusCode');
            $exceptCode = self::INVOCATION_RESULT_CODE_MAP[$invokeConfig['InvocationType']] ?? 200;
            if ($sourceCode !== $exceptCode) {
                throw new \LogicException('Invoke Error, except code: '.$exceptCode.',actual code:'.$sourceCode.',message:'.$response->get('FunctionError').',logs:'.$response->get('LogResult'));
            }

            // get response data
            /** @var StreamInterface $responsePayload */
            $responsePayload = $response->get('Payload');
            return $responsePayload->getContents();
        } catch (LambdaException $exception) {
            throw new \LogicException($exception->getMessage());
        }
    }
}