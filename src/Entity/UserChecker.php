<?php
namespace App\Entity;

use App\Entity\Utilisateur as Utilisateur ;
use Symfony\Component\Security\Core\User\UserInterface ;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\User\UserCheckerInterface ;


class UserChecker implements UserCheckerInterface
{//gerer dans security.yaml avec user_checker et dans services.yaml 
    public function checkPreAuth ( UserInterface $user )
    {
        $actif='Actif';
        if ( ! $user instanceof Utilisateur ) {//si l'utilisateur n'existe pas ne rien retourner
            return ;
        }

        if ( $user->getStatus()!=$actif) {//si l'utilisateur est bloqué
            throw new HttpException(403,'Ce compte est bloqué, veuillez contacter l\'administrateur');
        }

        if ( $user->getEntreprise() && $user->getEntreprise()->getStatus()!=$actif) {//si l'entreprise de l'utilisateur est bloqué
            throw new HttpException(403,'Ce partenaire est bloqué, veuillez contacter la société SA Transfert');
        }
    }

    public function checkPostAuth ( UserInterface $user ){}
}
