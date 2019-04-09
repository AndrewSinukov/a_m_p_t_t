<?php

namespace App\ProfileBundle\Event;

use App\ProfileBundle\Entity\Profile;
use Symfony\Component\EventDispatcher\Event;

class ProfileEvent extends Event
{
    /**
     * @var Profile
     */
    private $profile;

    /**
     * ProfileEvent constructor.
     *
     * @param Profile $profile
     */
    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    /**
     * @return Profile
     */
    public function getProfile(): Profile
    {
        return $this->profile;
    }

    /**
     * @param Profile $profile
     *
     * @return $this
     */
    public function setProfile(Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }
}