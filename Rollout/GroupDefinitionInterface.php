<?php

namespace Opensoft\RolloutBundle\Rollout;

/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface GroupDefinitionInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * Return a function which checks for user's inclusion in the group
     *
     * @return \Closure
     */
    public function getCallback();
}
