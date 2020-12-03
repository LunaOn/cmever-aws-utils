# cmever-aws-utils
内部调用到 AWS 的服务

## 安装
```shell script
composer install cmever/aws-utils
```

### Lambda（可用）

调用样例 `demos/LambdaInvoke.php` ：

```php
<?php

use cmever\AWS\Lambda\LambdaHandler;

require(__DIR__.'/vendor/autoload.php');

LambdaHandler::invoke('arn:aws:lambda:us-west-1:xxx:function:xxx', json_encode([
    'times' => 1,
]), 'us-west-1');
```

## 队列服务（整理中）

使用队列服务时，底层使用了 AWS SQS 的标准队列

文档：https://docs.aws.amazon.com/zh_cn/AWSSimpleQueueService/latest/SQSDeveloperGuide/standard-queues.html

创建的队列：https://us-west-1.console.aws.amazon.com/sqs/v2/home?region=us-west-1#/queues/https%3A%2F%2Fsqs.us-west-1.amazonaws.com%2F882956674931%2Fcmever-utils

队列配额：https://docs.aws.amazon.com/zh_cn/AWSSimpleQueueService/latest/SQSDeveloperGuide/quotas-queues.html

### 权限配置

根据 SQS 的访问策略与 IAM 用户组策略的结合，考虑了如下方案：

方案一：需要创建两个用户组，一个只读 `SQS-cmever-utils-read`，一个可写 `SQS-cmever-utils-write`，创建完成写入到 SQS 的访问策略中，每个服务端新建一个用户，再根据需要加入到响应组中即可。

方案二：需要创建两个 IAM 访问策略，一个只读一个只写，每个服务端使用的角色或用户根据需要加入 IAM 策略即可。

### 测试结果

#### 推送消息测试

| 次序 | 执行次数 | 成功处理 | 总时间 | 平均时间 | 最小时间 | 最大时间 |
| ---- | -------- | -------- | ------ | -------- | -------- | -------- |
| 0    | 10       | 10       | 388    | 38.8     | 25       | 143      |
| 1    | 10       | 10       | 274    | 27.4     | 25       | 45       |
| 2    | 10       | 10       | 258    | 25.8     | 24       | 26       |

#### 推送获取延迟

| 次序 | 执行次数 | 成功处理 | 总时间 | 平均时间 | 最小时间 | 最大时间 |
| ---- | -------- | -------- | ------ | -------- | -------- | -------- |
| 0    | 10       | 10       | 884    | 88.4     | 74       | 104      |
| 1    | 10       | 10       | 1074   | 107.4    | 79       | 184      |
| 2    | 10       | 10       | 1036   | 103.6    | 79       | 175      |

#### Lambda 调用延迟

| 次序 | 执行次数 | 成功处理 | 总时间 | 平均时间 | 最小时间 | 最大时间 |
| ---- | -------- | -------- | ------ | -------- | -------- | -------- |
| 0    | 10       | 0        | 1451   | 145.1    | 111      | 417      |
| 1    | 10       | 0        | 1147   | 114.7    | 107      | 122      |
| 2    | 10       | 0        | 1148   | 114.8    | 111      | 119      |

#### Dynamodb 调用延迟

第一次

| 次序 | 执行次数 | 成功处理 | 总时间 | 平均时间 | 最小时间 | 最大时间 |
| ---- | -------- | -------- | ------ | -------- | -------- | -------- |
| 0    | 10       | 10       | 893    | 89.3     | 27       | 639      |
| 1    | 10       | 10       | 283    | 28.3     | 26       | 31       |
| 2    | 10       | 10       | 305    | 30.5     | 27       | 37       |

再一次

| 次序 | 执行次数 | 成功处理 | 总时间 | 平均时间 | 最小时间 | 最大时间 |
| ---- | -------- | -------- | ------ | -------- | -------- | -------- |
| 0    | 10       | 10       | 413    | 41.3     | 26       | 159      |
| 1    | 10       | 10       | 266    | 26.6     | 26       | 27       |
| 2    | 10       | 10       | 267    | 26.7     | 26       | 27       |