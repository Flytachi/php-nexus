<?php

namespace Core;

final class Balancer
{
    /**
     * The minimum number of workers to run when there is any work to do.
     * Expressed as a percentage of the maximum number of workers.
     * @var int
     */
    private const int MODE_MIN_PERCENT = 20;

    /**
     * The medium (or normal) number of workers. The system will scale up to this level
     * when the load exceeds the capacity of the minimum workers.
     * Expressed as a percentage of the maximum number of workers.
     * @var int
     */
    private const int MODE_MEDIUM_PERCENT = 50;

    /**
     * The threshold for scaling up. If the number of ready messages per active worker
     * is greater than or equal to this value, the system will increase the number of workers.
     * A lower value makes the balancer more aggressive in scaling up.
     * @var float
     */
    private const float SCALE_UP_READY_PER_WORKER_THRESHOLD = 2.0;

    /**
     * The threshold for scaling down. If there are no ready messages and the number of
     * unacknowledged (in-progress) messages per worker is below this value, the system
     * becomes a candidate for scaling down. A higher value makes the balancer more
     * aggressive in scaling down.
     * @var float
     */
    private const float SCALE_DOWN_UNACKED_PER_WORKER_THRESHOLD = 0.5;

    /**
     * The number of consecutive balancing cycles the system must meet the scale-down criteria
     * before the number of workers is actually reduced. This prevents "flapping" (rapidly
     * scaling down and then up again).
     * @var int
     */
    private const int HYSTERESIS_DOWN_CYCLES = 2;

    private int $scaleDownCandidateCycles = 0;

    public function balance(array $stats, int $maxQty): int
    {
        $ready = $stats['ready'] ?? 0;
        $unacked = $stats['unacked'] ?? 0;
        $currentQty = $stats['consumers'] ?? 0;

        if ($maxQty === 0) {
            return 0;
        }

        $minQty = (int) max(1, round($maxQty * (self::MODE_MIN_PERCENT / 100)));
        $mediumQty = (int) round($maxQty * (self::MODE_MEDIUM_PERCENT / 100));

        // cool start
        if ($currentQty === 0) {
            $this->scaleDownCandidateCycles = 0;
            $requiredQty = (int) ceil($ready / self::SCALE_UP_READY_PER_WORKER_THRESHOLD);

            if ($requiredQty > $mediumQty) {
                return $maxQty;
            }
            if ($requiredQty > $minQty) {
                return $mediumQty;
            }
            return $minQty;
        }

        $readyPerWorker = $ready / $currentQty;
        $unackedPerWorker = $unacked / $currentQty;

        // >> Up
        if ($readyPerWorker >= self::SCALE_UP_READY_PER_WORKER_THRESHOLD) {
            $this->scaleDownCandidateCycles = 0;
            if ($currentQty < $mediumQty) return $mediumQty;
            if ($currentQty < $maxQty) return $maxQty;
            return $maxQty;
        }

        // >> Down
        if ($ready === 0 && $unackedPerWorker < self::SCALE_DOWN_UNACKED_PER_WORKER_THRESHOLD) {
            $this->scaleDownCandidateCycles++;
            if ($this->scaleDownCandidateCycles >= self::HYSTERESIS_DOWN_CYCLES) {
                $this->scaleDownCandidateCycles = 0;
                if ($currentQty > $mediumQty) return $mediumQty;
                if ($currentQty > $minQty) return $minQty;
                return $minQty;
            }
        } else {
            $this->scaleDownCandidateCycles = 0;
        }

        return $currentQty;
    }
}
