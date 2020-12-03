<?php


class Benchmark
{
    private $benchmarkResult = [];

    public function getBenchmarkResult()
    {
        return $this->benchmarkResult;
    }

    public static function getMicroTime()
    {
        return intval(microtime(true) * 1000);
    }

    public function benchmarkTest(callable $callback, int $total = 100, int $repeat = 10)
    {
        $result = [];
        for ($curr = 0; $curr < $repeat; $curr++) {
            $totalTime = 0;
            $minTime = PHP_INT_MAX;
            $maxTime = 0;
            $success = 0;

            for ($i = 0; $i < $total; $i++) {
                $singleTimeStart = self::getMicroTime();
                if (is_callable($callback)) {
                    $return = call_user_func($callback, $i, $repeat);
                    $success += intval($return);
                }
                $singleTimeEnd = self::getMicroTime();
                $singleTime = $singleTimeEnd - $singleTimeStart;

                $minTime = min($singleTime, $minTime);
                $maxTime = max($singleTime, $maxTime);
                $totalTime += $singleTime;
            }

            $result[] = [
                'curr' => $curr,
                'total' => $total,
                'count' => $repeat,
                'sum' => $totalTime,
                'avg' => $totalTime / $total,
                'min' => $minTime,
                'max' => $maxTime,
                'success' => $success,
            ];
        }
        $this->benchmarkResult = $result;
        return $this;
    }

    public function renderBenchmarkTable(string $title = '测试', bool $output = true)
    {
        $info = $this->getBenchmarkResult();
        if (empty($info)) {
            return false;
        }
        $tbody = '';
        foreach ($info as $val) {
            $tbody .= "<tr><td>{$val['curr']}</td><td>{$val['total']}</td><td>{$val['success']}</td><td>{$val['sum']}</td><td>{$val['avg']}</td><td>{$val['min']}</td><td>{$val['max']}</td></tr>";
        }
        $out = "<div>
            <h1>{$title} - 执行测试结果</h1>
            <table>
                <thead>
                    <tr><th>次序</th><th>执行次数</th><th>成功处理</th><th>总时间</th><th>平均时间</th><th>最小时间</th><th>最大时间</th></tr>
                </thead>
                <tbody>{$tbody}</tbody>
            </table>
         </div>";

        if ($output) {
            echo $out;
        }
        return $out;
    }
}