<?php

namespace App\DataFixtures;

use App\Entity\Entrada;
use App\Entity\User;
use App\Service\UploaderHelper;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class EntradaFixtures extends BaseFixture implements DependentFixtureInterface
{
    private array $entradaTitles = [
        'Ante lo Inesperado',
        'Indomita',
        'Rescate en las llamas',
        'Maravillas cotidianas',
    ];

    private array $entradaImages = [
        '01-Ante-lo-inesperado.jpg',
        '02-Indomita.jpg',
        '03-Rescate-en-las-llamas.jpg',
    ];

    public function __construct(private UploaderHelper $uploaderHelper)
    {
    }

    protected function loadData(ObjectManager $manager): void
    {
        $this->createMany(10, 'main_entradas', function () {
            $entrada = new Entrada();
            $entrada->setTitulo($this->faker->randomElement($this->entradaTitles))
                ->setContenido('Una dato más')
                ->setLinkRoute(strtolower(str_replace(' ', '-', trim($entrada->getTitulo() . random_int(0, 100)))))
                ->setImageFilename($this->fakeUploadImage())
                ->setAutor($this->getRandomReference('escitor_users', User::class));

            if ($this->faker->boolean(70)) {
                $entrada->setPublicadoAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            }

            return $entrada;
        });

        $manager->flush();
    }

    private function fakeUploadImage(): string
    {
        $randomImage = $this->faker->randomElement($this->entradaImages);
        $filesystem = new Filesystem();
        $targetPath = sys_get_temp_dir() . '/' . $randomImage;
        $filesystem->copy(__DIR__ . '/images/' . $randomImage, $targetPath, true);

        return $this->uploaderHelper->uploadEntradaImage(new File($targetPath), false);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}