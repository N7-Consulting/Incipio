<?php

namespace App\DataFixtures;

use App\Entity\Publish\Document;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadDocTypeData.
 */
class LoadDocTypeData extends Fixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        //avant-projet
        $ap = new Document();
        $ap->setName('Devis1');
        $ap->setPole('chiffrage');
        $ap->setType('devis');
        $ap->setPath('devis1.docx');
        $ap->setSize(98000);
        $manager->persist($ap);

        //convention client
        $cc = new Document();
        $cc->setName('Devis2');
        $cc->setPath('devis2.docx');
        $cc->setPole('chiffrage');
        $cc->setType('devis');
        $cc->setSize(31000);
        $manager->persist($cc);

        //bulletin adhésion
        $ba = new Document();
        $ba->setName('etude1');
        $ba->setPath('CE.docx');
        $ba->setPole('actico');
        $ba->setType('CE');
        $ba->setSize(20000);
        $manager->persist($ba);

        //proces verbal de reception final
        $pvr = new Document();
        $pvr->setName('PVRF');
        $pvr->setPath('PVRF.docx');
        //$pvr->setPole('actico');
        //$pvr->setType('PVRF');
        $pvr->setSize(17000);
        $manager->persist($pvr);

        //recapitulatif de mission
        $rm = new Document();
        $rm->setName('etude2');
        $rm->setPath('CE.docx');
        $rm->setPole('actico');
        $rm->setType('CE');
        $rm->setSize(26000);
        $manager->persist($rm);

        //facture d'acompte
        $fa = new Document();
        $fa->setName('etude3');
        $fa->setPath('PVRF.docx');
        $fa->setPole('actico');
        $fa->setType('PVRF');
        $fa->setSize(50200);
        $manager->persist($fa);

        //facture de solde
        $fs = new Document();
        $fs->setName('titrefacture');
        $fs->setPath('FS.docx');
        $fs->setPole('treso');
        $fs->setType('FS');
        $fs->setSize(50200);
        $manager->persist($fs);

        // Bulletin de versement
        $bv = new Document();
        $bv->setName('BVtitre');
        $bv->setPath('BV.docx');
        $bv->setType('fiscaux');
        $bv->setPole('treso');
        $bv->setSize(25490);
        $manager->persist($bv);

        //convention client
        $fa = new Document();
        $fa->setName('titrefacture');
        $fa->setPath('Fa.docx');
        $fa->setPole('treso');
        $fa->setType('FA');
        $fa->setSize(61000);
        $manager->persist($fa);

        $manager->flush();
    }
}
