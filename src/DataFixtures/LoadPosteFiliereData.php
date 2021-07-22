<?php

/*
   Alexandre Couedelo @ 2015-02-17 20:15:24
    Importé depuis Emagine/incipio
 */

namespace App\DataFixtures;

use App\Entity\Personne\SecteurActivite;
use App\Entity\Personne\Filiere;
use App\Entity\Personne\Poste;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPosteFiliereData extends Fixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $postes = [
            //Bureau
            'Président',
            'Président par procuration',
            'Vice-président',
            'Trésorier',
            'Suiveur Manager Qualité',
            'Secrétaire général',
            //ca
            'Manager Qualité-Tréso',
            'Vice-Trésorier',
            'Binome Qualité',
            'Respo. Communication',
            'Respo. SI',
            "Respo. Dev'Co",
            //Membre
            'membre',
            'Intervenant',
            'Chef de Projet',
        ];

        foreach ($postes as $poste) {
            $p = new Poste();
            $p->setIntitule($poste);
            $p->setDescription('a completer');

            $manager->persist($p);
        }

        $filiere = new Filiere();
        $filiere->setNom('Filière d\'exemple');
        $filiere->setDescription('Filière par défault, à éditer après l\'installation');

        $secteurs = [
            'Aeronautique',
            'Spatial',
            'BTP',
            'Energies',
            'Développement Durable',
            'Loisir / Culture / Restauration / Hôtellerie',
            'Conseil / Service',
            'Finance / Banque',
            'Informatique et Télécommunications',
            'Commerce',
            'Agro - Alimentaire',
            'Enseignement',
        ];

        foreach ($secteurs as $secteur) {
            $secteurActivite = new SecteurActivite();
            $secteurActivite->setIntitule($secteur);
            $secteurActivite->setDescription('a completer');

            $manager->persist($secteurActivite);
        }

        $manager->flush();
    }
}
