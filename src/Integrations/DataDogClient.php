<?php

namespace Cmp\Monitoring\Integrations;

class DataDogClient
{
    const DATADOG_DEFAULT_SERVER = '127.0.0.1';
    const DATADOG_DEFAULT_PORT = 8125;

    /**
     * Socket for sending messages
     *
     * @var string
     */
    protected $socket;

    /**
     * DataDog Agent server ip
     *
     * @var string
     */
    protected $server;

    /**
     * DataDog Agent port
     *
     * @var int
     */
    protected $port;

    /**
     * @param Socket $socket Socket for sending messages
     * @param string $server DataDog agent host
     * @param int    $port   Port where DataDog agent is listening to
     */
    public function __construct(
        Socket $socket,
        $server = self::DATADOG_DEFAULT_SERVER,
        $port = self::DATADOG_DEFAULT_PORT
    ) {
        $this->socket = $socket;
        $this->server = $server;
        $this->port = $port;
    }

    /**
     * @param string $host
     * @param int    $port
     *
     * @return static
     */
    public static function create($host = self::DATADOG_DEFAULT_SERVER, $port = self::DATADOG_DEFAULT_PORT)
    {
        return new static(
            new Socket(), 
            $host ? $host : self::DATADOG_DEFAULT_SERVER, 
            $port ? $port : self::DATADOG_DEFAULT_PORT
        );
    }

    /**
     * Send the message over UDP
     *
     * @param $message
     */
    protected function flush($message)
    {
        $this->socket->create(AF_INET, SOCK_DGRAM, SOL_UDP);
        $this->socket->setNonBlocking();
        $this->socket->sendMessage($message, $this->server, $this->port);
        $this->socket->close();
    }
}
