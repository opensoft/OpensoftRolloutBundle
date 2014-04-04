<?php
/**
 * 
 */

namespace Opensoft\RolloutBundle\Rollout;

use Opensoft\Rollout\RolloutUserInterface;

/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface UserProviderInterface
{
    /**
     * @param  mixed $id
     * @return RolloutUserInterface|null
     */
    public function findByRolloutIdentifier($id);
} 
