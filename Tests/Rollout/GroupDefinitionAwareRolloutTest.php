<?php

namespace Opensoft\RolloutBundle\Tests\Rollout;

use Opensoft\Rollout\RolloutUserInterface;
use Opensoft\Rollout\Storage\ArrayStorage;
use Opensoft\RolloutBundle\Rollout\GroupDefinitionAwareRollout;

/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class GroupDefinitionAwareRolloutTest extends \PHPUnit_Framework_TestCase
{
    public function testGroupDefinitionAware()
    {
        $group = $this->getMock('Opensoft\RolloutBundle\Rollout\GroupDefinitionInterface');
        $group->expects($this->once())->method('getName')->will($this->returnValue('test_group'));

        $callback = function (RolloutUserInterface $user) {
            return $user->name == 'test_user';
        };

        $group->expects($this->once())->method('getCallback')->will($this->returnValue($callback));

        $rollout = new GroupDefinitionAwareRollout(new ArrayStorage());

        $rollout->addGroupDefinition($group);

        $this->assertEquals(1, count($rollout->getGroupDefinitions()));
        $this->assertTrue($rollout->isActiveInGroup('test_group', new User('test_user')));
        $this->assertFalse($rollout->isActiveInGroup('test_group', new User('other_user')));
    }
}

class User implements RolloutUserInterface
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getRolloutIdentifier()
    {
        return '';
    }

}
