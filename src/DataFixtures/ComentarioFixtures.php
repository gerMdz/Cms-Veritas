<?php

namespace App\DataFixtures;

use App\Entity\Comentario;
use App\Entity\Entrada;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ComentarioFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(100, 'main_comentarios', function ($count) {
            $comment = new Comentario();
            $comment->setContenido(
                $this->faker->boolean ? $this->faker->paragraph : $this->faker->sentences(2, true)
            );
            $comment->setAutor($this->getRandomReference('main_users', User::class))
                ->setCreatedAt($this->faker->dateTimeBetween('-1 months', '-1 seconds'))
                ->setEntrada($this->getRandomReference('main_entradas', Entrada::class))
                ->setIsDeleted($this->faker->boolean(20));

            return $comment;
        });
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
//            TagFixture::class,
            UserFixtures::class,
            EntradaFixtures::class,
        ];
    }
}
