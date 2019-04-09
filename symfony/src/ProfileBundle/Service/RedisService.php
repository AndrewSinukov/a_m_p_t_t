<?php

namespace App\ProfileBundle\Service;

class RedisService
{
    private const MIN_TTL = 1;

    private const MAX_TTL = 3600;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * RedisService constructor.
     *
     * @param string $redisHost
     * @param string $redisPort
     */
    public function __construct(string $redisHost, string $redisPort)
    {
        $this->host = $redisHost;
        $this->port = $redisPort;
    }

    /**
     * Get the value related to the specified key.
     *
     * @param $key
     *
     * @return bool|string
     */
    public function get($key)
    {
        $this->connect();

        return $this->redis->get($key);
    }

    /**
     * @param $key
     *
     * @return string
     */
    public function lpop($key): string
    {
        $this->connect();

        return $this->redis->lpop($key);
    }

    /**
     * @param $key
     * @param $value
     *
     * @return int
     */
    public function rpush($key, $value): int
    {
        $this->connect();

        return $this->redis->rpush($key, $value);
    }

    /**
     * @param     $list
     * @param int $start
     * @param int $end
     *
     * @return array
     */
    public function lrange($list, $start = 0, $end = -1)
    {
        $this->connect();

        return $this->redis->lrange($list, $start, $end);
    }

    /**
     * @param     $key
     * @param int $start
     * @param int $end
     *
     * @return array
     */
    public function ltrim($key, $start = -1, $end = 0)
    {
        $this->connect();

        return $this->redis->ltrim($key, $start, $end);
    }

    /**
     * @param     $key
     * @param int $value
     * @param int $end
     */
    public function lRemove($key, $value = 0, $end = -1)
    {
        $this->connect();

        $this->redis->lRemove($key, $value, $end);
    }

    /**
     * @param $key
     *
     * @return string
     */
    public function dump($key): string
    {
        $this->connect();

        return $this->redis->dump($key);
    }

    /**
     * @param $key
     * @param $ttl
     * @param $value
     *
     * @return bool
     */
    public function restore($key, $ttl, $value): bool
    {
        $this->connect();

        return $this->redis->restore($key, $ttl, $value);
    }

    /**
     * set(): Set persistent key-value pair.
     * setex(): Set non-persistent key-value pair.
     *
     * @param      $key
     * @param      $value
     * @param null $ttl
     */
    public function set($key, $value, $ttl = null): void
    {
        $this->connect();

        if ($ttl === null) {
            $this->redis->set($key, $value);
        } else {
            $this->redis->setex($key, $this->normaliseTtl($ttl), $value);
        }
    }

    /**
     * Returns 1 if the timeout was set.
     * Returns 0 if key does not exist or the timeout could not be set.
     *
     * @param     $key
     * @param int $ttl
     *
     * @return bool
     */
    public function expire($key, $ttl = self::MIN_TTL): bool
    {
        $this->connect();

        return $this->redis->expire($key, $this->normaliseTtl($ttl));
    }

    /**
     * Removes the specified keys. A key is ignored if it does not exist.
     * Returns the number of keys that were removed.
     *
     * @param $key
     *
     * @return int
     */
    public function delete($key): int
    {
        $this->connect();

        return $this->redis->del($key);
    }

    /**
     * Returns -2 if the key does not exist.
     * Returns -1 if the key exists but has no associated expire. Persistent.
     *
     * @param $key
     *
     * @return int
     */
    public function getTtl($key): int
    {
        $this->connect();

        return $this->redis->ttl($key);
    }

    /**
     * Returns 1 if the timeout was removed.
     * Returns 0 if key does not exist or does not have an associated timeout.
     *
     * @param $key
     *
     * @return bool
     */
    public function persist($key): bool
    {
        $this->connect();

        return $this->redis->persist($key);
    }

    /**
     * The ttl is normalised to be 1 second to 1 hour.
     *
     * @param $ttl
     *
     * @return float|int
     */
    private function normaliseTtl($ttl)
    {
        $ttl = ceil(abs($ttl));

        return ($ttl >= self::MIN_TTL && $ttl <= self::MAX_TTL) ? $ttl : self::MAX_TTL;
    }

    /**
     * Connect only if not connected.
     */
    private function connect(): void
    {
        if (!$this->redis || $this->redis->ping() !== '+PONG') {

            $this->redis = new \Predis\Client(
                array(
                    'host' => $this->host,
                    'port' => $this->port,
                )
            );

            $this->redis->connect($this->host, $this->port);
        }
    }
}