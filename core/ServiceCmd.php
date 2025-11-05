<?php

namespace Core;

use Flytachi\Kernel\Console\Inc\CmdCustom;
use Flytachi\Kernel\Src\Thread\Entity\ProcessStats;
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
            self::print("Example: extra run script main.service <status|start|stop|list>");
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
                case 'list':
                    $this->listArg();
                    break;
                default:
                    self::printMessage("Argument '{$this->args['arguments'][1]}' not found");
                    break;
            }
        }
    }

    private function statusArg(): void
    {
        $info = Nexus::status(isset($this->args['options']['stats']));
        if ($info != null) {
            self::printLabel("STATUS", 32);
            self::print("PID: " . $info->status->pid, 32);
            self::print("ClassName: " . $info->status->className, 32);
            self::print("Balancer: " . $info->status->balancer, 32);
            self::print("Condition: " . $info->status->condition->name, 32);
            self::print("StartedAt: " . $info->status->getStartedAt(), 32);
            $this->printStats($info->stats);
        } else {
            self::printMessage("No active");
        }
    }

    private function printStats(?ProcessStats $stats): void
    {
        if ($stats) {
            self::printLabel("STATS", 32);
            self::print("CMD: {$stats->command} (pid:{$stats->pid }, ppid:{$stats->ppid})", 32);
            self::print("Mem/Rss(Mb): {$stats->mem} % / " . $stats->rssMb(), 32);
            self::print("Cpu: {$stats->cpu} %", 32);
            self::print("User/Etime: {$stats->user} / {$stats->etime}", 32);
        }
    }

    private function startArg(): void
    {
        $info = Nexus::status();
        if ($info == null) {
            $pid = Nexus::dispatch();
            self::printMessage("Service started [PID:{$pid}]", 32);
        } else {
            self::printMessage("Service is active [PID:{$info->status}]");
        }
    }

    private function stopArg(): void
    {
        $info = Nexus::status();
        if ($info != null) {
            if (Signal::interruptAndWait($info->status->pid)) {
                self::printMessage("Service stopped [PID:{$info->status->pid}]", 32);
            } else {
                self::printMessage("Service stopped [PID:{$info->status->pid}] timeout");
            }
        } else {
            self::printMessage("Service is not active");
        }
    }

    private function listArg(): void
    {
        $infos = Nexus::threadListInfo(isset($this->args['options']['stats']));
        if (!empty($infos)) {
            self::printLabel("--------------------------------------", 34);
            foreach ($infos as $info) {
                self::printLabel("UNIT ({$info->status->pid})", 32);
                self::print("PID: " . $info->status->pid, 32);
                self::print("Condition: " . $info->status->condition->name, 32);
                self::print("StartedAt: " . $info->status->getStartedAt(), 32);
                $this->printStats($info->stats);
                self::printLabel("--------------------------------------", 34);
            }

        } else {
            self::printMessage("No active");
        }
    }
}
