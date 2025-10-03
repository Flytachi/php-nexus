<?php

namespace Core;

use Flytachi\Kernel\Src\Stereotype\Cluster;
use Flytachi\Kernel\Src\Thread\Signal;
use Flytachi\Kernel\Src\Unit\File\JSON;
use Flytachi\Kernel\Src\Unit\TimeTool;
use Main\RuntimeUnit;

class Nexus extends Cluster
{
    private ?Balancer $_balancer = null;

    public function run(mixed $data = null): void
    {
        $this->logger?->info('START');
        $this->prepare((int) env('UNIT_BALANCER', 1));
        $this->retentionUnit();

        while (true) {
            TimeTool::sleepSec(10);
            $this->retentionUnit();
        }
    }

    public function proc(mixed $data = null): void
    {
        RuntimeUnit::working($this->pid, $this->logger);
    }

    private function retentionUnit(): void
    {
        // basic level balance
        if ($this->threadCount() == $this->balancer) return;
        for ($i = $this->threadCount(); $i < $this->balancer; $i++) {
            $this->threadProc();
        }

//        // pro level balance
//        $this->balancing();
    }

    public static function stats(): array
    {
        $stats = RuntimeUnit::getQueueStats();
        return [
            'ready'     => $stats['ready'] ?? 0,
            'unacked'   => $stats['unacked'] ?? 0,
            'total'     => $stats['total'] ?? 0,
            'consumers' => $stats['consumers'] ?? 0,
        ];
    }

    protected function preparationThreadBefore(int $pid): void
    {
        JSON::write(static::stmThreadsPath() . "/{$pid}.json", [
            'pid' => $pid,
            'name' => RuntimeUnit::class,
            'condition' => 'waiting',
        ]);
        $this->logger?->debug("started");
    }

    public static function sCondition(int $childPid, string $newCondition): void
    {
        $path = static::stmThreadsPath() . '/' . $childPid . '.json';
        $data = JSON::read($path);
        $data['condition'] = $newCondition;
        JSON::write($path, $data);
    }

    public static function gCondition(int $childPid): string
    {
        $data = JSON::read(static::stmThreadsPath() . '/' . $childPid . '.json');
        return $data['condition'];
    }

    private function balancing(): void
    {
        if ($this->_balancer === null) {
            $this->_balancer = new Balancer();
        }
        $qty = $this->_balancer->balance(self::stats(), $this->balancer);
        $currentQty = $this->threadCount();

        if ($currentQty == $qty) return;
        else {
            if ($currentQty < $qty) {
                $diff = $qty - $currentQty;
                for ($i = 0; $i < $diff; $i++) {
                    $this->threadProc();
                }
            } else {
                // down
                $rotation = 5;
                $diff = $currentQty - $qty;
                while ($diff > 0 && $rotation > 0) {
                    foreach ($this->threadList() as $thread) {
                        if ($diff <= 0) break;
                        $status = Nexus::gCondition($thread);
                        if ($status === 'waiting') {
                            Signal::close($thread);
                            --$diff;
                        }
                    }
                    --$rotation;
                }
            }
        }
    }
}
