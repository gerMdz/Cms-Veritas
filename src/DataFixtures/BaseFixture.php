<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

abstract class BaseFixture extends Fixture
{
    /** @var ObjectManager */
    private $manager;

    /** @var Generator */
    protected $faker;

    private array $referencesIndex = [];

    abstract protected function loadData(ObjectManager $manager);

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create();

        $this->loadData($manager);
    }

    protected function createMany(int $count, string $groupName, callable $factory)
    {
        for ($i = 0; $i < $count; ++$i) {
            $entity = $factory($i);
            if ($entity === null) {
                throw new \LogicException('Did you forget to return the entity object from your factory '.$factory.'()?');
            }
            $this->manager->persist($entity);
            // store a reference to this class' $groupName object with Id $i
            $this->addReference(sprintf('%s_%d', $groupName, $i), $entity);
        }
    }

    protected function getRandomReference(string $groupName, string $className)
    {
        if (!isset($this->referencesIndex[$groupName])) {
            $this->referencesIndex[$groupName] = [];
            for ($i = 0; ; ++$i) {
                $referenceName = sprintf('%s_%d', $groupName, $i);
                if (!$this->hasReference($referenceName, $className)) {
                    break;
                }
                $this->referencesIndex[$groupName][] = $referenceName;
            }
            if (empty($this->referencesIndex[$groupName])) {
                throw new \InvalidArgumentException(sprintf('Did not find any references saved with the group name "%s"', $groupName));
            }
        }
        $randomReferenceKey = $this->faker->randomElement($this->referencesIndex[$groupName]);
        return $this->getReference($randomReferenceKey, $className);
    }

    protected function getRandomReferences(string $className, int $count)
    {
        $references = [];
        while (count($references) < $count) {
            $references[] = $this->getRandomReference($className);
        }
        return $references;
    }
}