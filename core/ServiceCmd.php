<?php

namespace Core;

use Flytachi\Kernel\Console\Inc\CmdCustom;
use Flytachi\Kernel\Src\Thread\Signal;

class ServiceCmd extends CmdCustom
{
    public function handle(): void
    {
        self::printTitle("Service", 32);
        if (
            count($this->args['arguments']) == 2
        ) {
            $this->resolution();
        } else {
            self::printMessage("Enter argument");
            self::print("Example: extra run script main.service <status|start|stop>");
        }
        self::printTitle("Service", 32);
    }

    private function resolution(): void
    {
        if (array_key_exists(1, $this->args['arguments'])) {
            switch ($this->args['arguments'][1]) {
                case 'status':
                    $this->statusArg();
                    break;
                case 'start':
                    $this->startArg();
                    break;
                case 'stop':
                    $this->stopArg();
                    break;
                default:
                    self::printMessage("Argument '{$this->args['arguments'][1]}' not found");
                    break;
            }
        }
    }

    private function statusArg(): void
    {
        $status = Nexus::status();
        if ($status != null) {
            self::print("PID: " . $status['pid'], 32);
            self::print("ClassName: " . $status['className'], 32);
            self::print("Balancer: " . $status['balancer'], 32);
            self::print("CONDITION: " . $status['condition'], 32);
            self::print("STARTED_AT: " . $status['startedAt'], 32);
        } else {
            self::printMessage("No active");
        }
    }

    private function startArg(): void
    {
        $status = Nexus::status();
        if ($status == null) {
            $pid = Nexus::dispatch();
            self::printMessage("Service started [PID:{$pid}]", 32);
        } else {
            self::printMessage("Service is active [PID:{$status['pid']}]");
        }
    }

    private function stopArg(): void
    {
        $status = Nexus::status();
        if ($status != null) {
            Signal::interrupt($status['pid']);
            self::printMessage("Service stopped [PID:{$status['pid']}]", 32);
        } else {
            self::printMessage("Service is not active");
        }
    }
}
