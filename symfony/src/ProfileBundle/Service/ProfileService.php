<?php

namespace App\ProfileBundle\Service;

use RedisException;

class ProfileService
{
    private $redisListProfiles;

    private $redis;

    /**
     * ProfileService constructor.
     *
     * @param RedisService $redis
     * @param $redisListProfiles
     */
    public function __construct(RedisService $redis, $redisListProfiles)
    {
        $this->redis = $redis;
        $this->redisListProfiles = $redisListProfiles;
    }

    /**
     * @param array $profile
     * @return int
     */
    public function setProfile(array $profile)
    {
        $profile = json_encode($profile);

        $result = null;
        try {
            $this->redis->rpush($this->redisListProfiles, $profile);
        } catch (RedisException $e) {
            $result = $e->getMessage();
        }

        return $result;
    }
}