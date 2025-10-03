<?php

namespace Core\Entity;

use Core\Nexus;
use Flytachi\Kernel\Extra;
use Flytachi\Kernel\Src\Unit\Algorithm;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

abstract class Unit
{
    use LoggerAwareTrait;
    protected string $name = '';

    final public function __construct(?LoggerInterface $logger = null)
    {
        if ($logger === null) {
            $processId = getmypid();
            self::setLogger(Extra::$logger->withName("[{$processId}] " . static::class));
        } else {
            self::setLogger($logger);
        }
    }

    protected function connection(): AMQPStreamConnection
    {
        return new AMQPStreamConnection(
            env('AMQP_HOST', 'localhost'),
            env('AMQP_PORT', 5672),
            env('AMQP_USER', 'guest'),
            env('AMQP_PASS', 'guest'),
        );
    }

    /**
     * Fetches statistics for a specified RabbitMQ queue via the HTTP Management API.
     *
     * This function sends a GET request to the RabbitMQ Management API endpoint for a specific
     * queue and parses the JSON response. It provides key metrics such as the number of
     * messages ready for delivery, the number of unacknowledged messages (currently being
     * processed), the total number of messages, and the number of active consumers.
     *
     * @return array|false Returns an associative array with queue statistics on success,
     *                     or `false` on failure.
     *                     The success array has the following structure:
     *                     [
     *                         'ready'     => (int) Messages ready to be delivered,
     *                         'unacked'   => (int) Messages delivered but not yet acknowledged,
     *                         'total'     => (int) Total messages (ready + unacked),
     *                         'consumers' => (int) Number of active consumers
     *                     ]
     *                     Failure can occur if the API is unreachable, authentication fails,
     *                     the queue does not exist (HTTP 404), or the response is invalid.
     */
    final public static function getQueueStats(?LoggerInterface $logger = null): array|false
    {
        $unit = new static($logger);
        $queueName = 'ut_' . (
            empty($unit->name)
                ? Algorithm::random(7)
                : $unit->name
            );
        $apiUrl = sprintf(
            'http://%s:%d/api/queues/%s/%s',
            env('AMQP_HOST', 'localhost'),
            env('AMQP_API_PORT', 15672),
            urlencode(env('AMQP_API_VHOST', '/')),
            $queueName
        );

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "Authorization: Basic " . base64_encode(
                        env('AMQP_USER', 'guest')
                        . ':'
                        . env('AMQP_PASS', 'guest')
                    ),
                'ignore_errors' => true, // Allows reading the response body on 4xx/5xx error codes
                'timeout' => 5           // Connection timeout in seconds
            ]
        ]);

        $response = @file_get_contents($apiUrl, false, $context);
        if ($response === false || !isset($http_response_header )) {
            return false;
        }

        if (strpos($http_response_header[0], '200 OK' ) === false) {
            return false;
        }
        $queueData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        return [
            'ready'     => $queueData['messages_ready'] ?? 0,
            'unacked'   => $queueData['messages_unacknowledged'] ?? 0,
            'total'     => $queueData['messages'] ?? 0,
            'consumers' => $queueData['consumers'] ?? 0,
        ];
    }

    final public static function working(int $pid, ?LoggerInterface $logger = null): void
    {
        $unit = new static($logger);
        $queueName = 'ut_' . (
            empty($unit->name)
                ? Algorithm::random(7)
                : $unit->name
        );
        $connection = $unit->connection();
        $channel = $connection->channel();
        $channel->queue_declare(
            $queueName,
            false,
            true,
            false,
            false
        );

        $channel->basic_qos(null, 1, null);
        $callback = function (AMQPMessage $msg) use (&$unit, $pid) {
            try {
                Nexus::sCondition($pid, 'active');
                $unit->run($msg);
            } catch (\Throwable $t) {
                $unit->logger->error($t->getMessage());
            } finally {
                $msg->ack();
                Nexus::sCondition($pid, 'waiting');
            }
        };

        $channel->basic_consume(
            $queueName,
            '',
            false,
            false,
            false,
            false,
            $callback
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    protected function run(AMQPMessage $msg): void
    {
        $this->logger->info('PROC working');
    }
}