<?php

namespace App\DataFixtures;

use App\Entity\Entrada;
use App\Entity\Principal;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PrincipalFixtures extends BaseFixture implements DependentFixtureInterface
{
    private static $principalTitles = [
        'grupospequeños',
        'ofrenda',
        'notas',
        'oracion',
        'contacto',
        'avanza',
    ];

    protected function loadData(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $this->createMany(6, 'main_principal', function ($count) {
            $principal = new Principal();
            $indice = $count;
            $principal->setTitulo(self::$principalTitles[$indice])
            ->setContenido($this->faker->realText(100))
                ->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));

            $principal->setLinkRoute(self::$principalTitles[$indice]);
            $principal->setAutor($this->getRandomReference('escitor_users', User::class));
            $principal->addEntrada($this->getRandomReference('main_entradas', Entrada::class));

            return $principal;
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
