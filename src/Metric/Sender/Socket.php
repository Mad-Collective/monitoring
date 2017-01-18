<?php
namespace Cmp\Monitoring\Metric\Sender;

class Socket
{
    /**
     * Socket resource
     *
     * @var resource
     */
    protected $socket;

    /**
     * Creates new socket connection
     *
     * @see http://php.net/manual/en/function.socket-create.php
     *
     * @param int $domain   Protocol family
     * @param int $type     Type of socket
     * @param int $protocol Communication protocol
     *
     * @throws \Exception If the socket could not be created
     */
    public function create($domain, $type, $protocol)
    {
        $this->socket = socket_create($domain, $type, $protocol);
        if (!$this->socket) {
            throw new \Exception('Could not create socket');
        }
    }

    /**
     * Sets the socket as non-blocking
     *
     * @return bool
     */
    public function setNonBlocking()
    {
        return socket_set_nonblock($this->socket);
    }

    /**
     * Send a message using the socket to a server
     *
     * @see http://php.net/manual/en/function.socket-sendto.php
     *
     * @param string $message Message to send
     * @param string $server  Server ip address
     * @param int    $port    Server port
     * @param int    $flags   Option flags
     *
     * @return int Number of bytes send
     * @throws \RuntimeException If the message could not be sent
     */
    public function sendMessage($message, $server, $port, $flags = 0)
    {
        $send = socket_sendto($this->socket, $message, strlen($message), $flags, $server, $port);
        if ($send === false) {
            throw new \RuntimeException('Message could not be sent');
        }

        return $send;
    }

    /**
     * Closes the socket
     */
    public function close()
    {
        socket_close($this->socket);
    }
}
