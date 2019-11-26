<?php

namespace Opensoft\RolloutBundle\Storage\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Opensoft\Rollout\Storage\StorageInterface;
use Opensoft\RolloutBundle\Entity\Feature;

/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class DoctrineORMStorage implements StorageInterface
{
    /**
     * @var array
     */
    private $cache = [];
    
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
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
     * @param EntityManagerInterface $em
     * @param string $class
     */
    public function __construct(EntityManagerInterface $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->class = $class;
    }

    /**
     * @param string key
     *
     * @return mixed|null Null if the value is not found
     */
    public function get($key)
    {
        if (isset($this->cache[$key])) {
            /** @var Feature $feature */
            $feature = $this->cache[$key];

            return $feature->getSettings();
        }
        
        /** @var Feature $feature */
        $feature = $this->repository->findOneBy(array('name' => $key));
        $this->cache[$key] = $feature;
        
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
        $this->em->flush($feature);
    }

    /**
     * @param string $key
     */
    public function remove($key)
    {
        $feature = $this->repository->findOneBy(array('name' => $key));
        if ($feature) {
            $this->em->remove($feature);
            $this->em->flush($feature);
        }
    }
}
