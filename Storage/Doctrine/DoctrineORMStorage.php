<?php
/**
 * 
 */

namespace Opensoft\RolloutBundle\Storage\Doctrine;
use Doctrine\ORM\EntityManager;
use Opensoft\Rollout\Storage\StorageInterface;
use Opensoft\RolloutBundle\Entity\Feature;


/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class DoctrineORMStorage implements StorageInterface
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param EntityManager $em
     * @param string        $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->class = $class;
    }

    /**
     * @param  string $key
     * @return mixed|null Null if the value is not found
     */
    public function get($key)
    {
        /** @var Feature $feature */
        $feature = $this->repository->findOneBy(array('name' => $key));
        if (!$feature) {
            return null;
        }

        return $feature->getSettings();
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        /** @var Feature $feature */
        $feature = $this->repository->findOneBy(array('name' => $key));
        if (!$feature) {
            $feature = new Feature();
        }

        $feature->setName($key);
        $feature->setSettings($value);

        $this->em->persist($feature);
        $this->em->flush();
    }
} 
