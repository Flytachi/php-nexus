<?php

namespace Core;

use Flytachi\Kernel\Src\Stereotype\Cluster;
use Flytachi\Kernel\Src\Unit\File\JSON;
use Main\RuntimeUnit;

class Nexus extends Cluster
{
    public function run(mixed $data = null): void
    {
        $this->logger?->info('START');
        $this->prepare(3);
        $this->retentionUnit();

        while (true) {
            sleep(5);
            $this->retentionUnit();
        }
    }

    private function retentionUnit(): void
    {
        if ($this->threadCount() == $this->balancer) return;
        for ($i = $this->threadCount(); $i < $this->balancer; $i++) {
            $this->threadProc();
        }
    }

    public function proc(mixed $data = null): void
    {
        RuntimeUnit::working($this->logger);
    }

    protected function preparationThreadBefore(int $pid): void
    {
        JSON::write(static::stmThreadsPath() . "/{$pid}.json", [
            'pid' => $pid,
            'name' => RuntimeUnit::class
        ]);
        $this->logger?->debug("started");
    }

    public static function stats(): array
    {
        $stats = RuntimeUnit::getQueueStats();
        return [
            'ready'     => $stats['messages_ready'] ?? 0,
            'unacked'   => $stats['messages_unacknowledged'] ?? 0,
            'total'     => $stats['messages'] ?? 0,
            'consumers' => $stats['consumers'] ?? 0,
        ];
    }
}
