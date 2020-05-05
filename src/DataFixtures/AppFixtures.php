<?php

namespace App\DataFixtures;

use App\Entity\Compte;
use App\Entity\Profil;
use App\Entity\Entreprise;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder=$encoder;
    }
    public function load(ObjectManager $manager)
    {
        
        $actif='Actif';
        $profilSup=new Profil();
        $profilSup->setLibelle('SuperAdmin');
        $manager->persist($profilSup);
        
        $profilCaiss=new Profil();
        $profilCaiss->setLibelle('Caissier');
        $manager->persist($profilCaiss);
        
        $profilAdP=new Profil();
        $profilAdP->setLibelle('AdminPrincipal');
        $manager->persist($profilAdP);
        
        $profilAdm=new Profil();
        $profilAdm->setLibelle('Admin');
        $manager->persist($profilAdm);
        
        $profilUtil=new Profil();
        $profilUtil->setLibelle('utilisateur');
        $manager->persist($profilUtil);

        $saTransfert=new Entreprise();
        $saTransfert->setRaisonSociale('Transfert')
                    ->setNinea(strval(rand(150000000,979999999)))
                    ->setAdresse('Mbour')
                    ->setTelephoneEntreprise('0000011')
                    ->setEmailEntreprise('sat@gmail.com')
                    ->setStatus($actif);
        $compte=new Compte();
        $compte->setNumeroCompte('1910 1409 0043')
                   ->setEntreprise($saTransfert);
        $manager->persist($saTransfert);
        $manager->persist($compte);
        
        $SupUser=new Utilisateur();
        $motDePass=$this->encoder->encodePassword($SupUser, 'pass');
        $SupUser->setUsername('lopy')
             ->setRoles(['ROLE_SuperAdmin'])
             ->setPassword($motDePass)
             ->setConfirmPassword($motDePass)
             ->setEntreprise($saTransfert)
             ->setNom('Thesou')
             ->setImage('image.png')
             ->setEmail('Thesou@gmail.com')
             ->setTelephone('77 000 00 00')
             ->setNci(strval(rand(150000000,979999999)))
             ->setStatus($actif);
        $manager->persist($SupUser);
        $manager->flush();
    }
}
