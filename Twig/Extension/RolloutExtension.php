<?php

namespace Opensoft\RolloutBundle\Twig\Extension;

use Opensoft\Rollout\RolloutUserInterface;
use Opensoft\RolloutBundle\Rollout\GroupDefinitionAwareRollout;

/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class RolloutExtension extends \Twig_Extension
{
    /**
     * @var GroupDefinitionAwareRollout
     */
    private $rollout;

    /**
     * @param GroupDefinitionAwareRollout $rollout
     */
    public function __construct(GroupDefinitionAwareRollout $rollout)
    {
        $this->rollout = $rollout;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('rollout_is_active', function ($feature, RolloutUserInterface $user = null) {
                return $this->rollout->isActive($feature, $user);
            })
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'rollout_is_active';
    }
}
