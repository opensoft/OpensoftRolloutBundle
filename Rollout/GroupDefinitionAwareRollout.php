<?php

namespace Opensoft\RolloutBundle\Rollout;

use Opensoft\Rollout\Rollout;

/**
 * Ease the way to define group definitions with a tagged service
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class GroupDefinitionAwareRollout extends Rollout
{
    /**
     * @var GroupDefinitionInterface[]
     */
    protected $groupDefinitions;

    /**
     * @return GroupDefinitionInterface[]
     */
    public function getGroupDefinitions()
    {
        return $this->groupDefinitions;
    }

    /**
     * @param GroupDefinitionInterface $groupDefinition
     */
    public function addGroupDefinition(GroupDefinitionInterface $groupDefinition)
    {
        $this->groupDefinitions[] = $groupDefinition;

        $this->defineGroup($groupDefinition->getName(), $groupDefinition->getCallback());
    }
}
