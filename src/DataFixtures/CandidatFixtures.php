<?php

namespace App\DataFixtures;

use App\Entity\Candidat;
use App\Entity\Curriculum;
use App\Entity\Skills;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CandidatFixtures extends Fixture implements DependentFixtureInterface
{
    public const CANDIDATS = [
        [
            'age' => 25,
            'linked_in' => 'https://www.linkedin.com/Baboulinet',
            'github' => 'https://www.github.com/Baboulinet',
            'zip_code' => '33789',
            'address' => '19 Rue Pommier',
            'city' => 'Metz',
            'description' => 'Salut Joe Maboule je maitrise le HTML',
            'can_postulate' => true,
        ],
        [
            'age' => 38,
            'linked_in' => 'https://www.linkedin.com/Glandouille',
            'github' => 'https://www.github.com/Glandouille',
            'zip_code' => '91000',
            'address' => '74 Impasse Philippe Etchebest',
            'city' => 'Marmande',
            'description' => 'Salut Jacques Glandux je maitrise MariaDB',
            'can_postulate' => false,
        ],
        [
            'age' => 75,
            'linked_in' => 'https://www.linkedin.com/BientotDead',
            'github' => 'https://www.github.com/PorteDeLaMort',
            'zip_code' => '98765',
            'address' => '74 Boulevard Jacquie & Michel',
            'city' => 'Brest',
            'description' => 'Salut Marius DuTrou je maitrise rien',
            'can_postulate' => true,
        ],
        [
            'age' => 98,
            'linked_in' => 'https://www.linkedin.com/decede',
            'github' => 'https://www.github.com/rip',
            'zip_code' => '77000',
            'address' => '74 Rue Rhinocéros',
            'city' => 'Calcuta',
            'description' => 'Salut depuis le cercueil je faisais du .NET',
            'can_postulate' => true,
        ],
        [
            'age' => 14,
            'linked_in' => 'https://www.linkedin.com/JeSuisHPI',
            'github' => 'https://www.github.com/PorteDuMIT',
            'zip_code' => '95000',
            'address' => '74 Rue Précoce',
            'city' => 'Le Havre',
            'description' => 'Bonjour Marcel Pagnol je suis hacker a la Défense',
            'can_postulate' => true,
        ],
        [
            'age' => 8,
            'linked_in' => 'https://www.linkedin.com/JeSuIHPI',
            'github' => 'https://www.github.com/PorteDuMdIT',
            'zip_code' => '95080',
            'address' => '74 Rue Préuoce',
            'city' => 'Le Havrke',
            'description' => 'Bonjour Marcele Pagnol je suis hacker a la Défense',
            'can_postulate' => true,

        ],


    ];
    public function load(ObjectManager $manager): void
    {

        $iteration = 1; //phpstan m'empêche de déclarer une variable de moins de 3 caractères
        foreach (self::CANDIDATS as $candidatinf) {
            $candidat = new Candidat();
            $curriculum = new Curriculum();
            $candidat->setAge($candidatinf['age']);
            $candidat->setLinkedIn($candidatinf['linked_in']);
            $candidat->setGithub($candidatinf['github']);
            $candidat->setZipCode($candidatinf['zip_code']);
            $candidat->setAddress($candidatinf['address']);
            $candidat->setCity($candidatinf['city']);
            $candidat->setDescription($candidatinf['description']);
            $candidat->setCanPostulate($candidatinf['can_postulate']);
            $candidat->setCurriculum($curriculum);
            $candidat->setUser($this->getReference('userCandidat_' . $iteration));
            $skills = new Skills();
            $skills->setCurriculum($curriculum);
            $manager->persist($curriculum);
            $manager->persist($candidat);
            $manager->persist($skills);
            $iteration++;
        }


        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
