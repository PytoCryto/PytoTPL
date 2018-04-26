<?php

namespace PytoTPL\Traits;

trait SharedDataAwareTrait
{
    /**
     * The list of shared data
     * 
     * @var array
     */
    protected $shared = [];

    public function setShared($key, $value = null)
    {
        if (is_array($key)) {
            $this->shared = array_merge($this->shared, $key);
        } else {
            $this->shared[$key] = $value;
        }

        return $this;
    }

    public function hasShared($key)
    {
        return array_key_exists($key, $this->shared);
    }

    public function getShared()
    {
        return $this->shared;
    }

    public function flushSharedData()
    {
        $this->shared = [];
    }
}
