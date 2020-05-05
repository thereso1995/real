<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\Utilisateur;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }



     /**
     * @return Utilisateur[] Returns an array of Utilisateur objects
     */

    public function findUserEntreprise(Entreprise $entreprise,Utilisateur $user)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.entreprise = :val')
            ->andWhere('u != :val2')
            ->setParameter('val', $entreprise)
            ->setParameter('val2', $user)
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function findResponsable(Entreprise $entreprise): ?Utilisateur
    {   

        return $this->createQueryBuilder('u')
            ->andWhere('u.entreprise = :val')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('val', $entreprise)
            ->setParameter('role', '%"'.'ROLE_admin-Principal'.'"%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
        //le premier sera toujour l afmin principal car c est le premier à etre ajouter donc il est le premier à avoir id de l entreprise
    }

    // /**
    //  * @return Utilisateur[] Returns an array of Utilisateur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Utilisateur
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
