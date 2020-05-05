<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Compte;
use App\Form\DepotType;
use App\Form\CompteType;
use App\Entity\Entreprise;
use App\Entity\Utilisateur;
use App\Form\EntrepriseType;
use App\Form\UtilisateurType;
use App\Entity\UserCompteActuel;
use App\Repository\CompteRepository;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserCompteActuelRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/** 
 * @Route("/api")
 */

class EntrepriseController extends AbstractFOSRestController
{

    private $actif;
    private $message;
    private $status;
    private $Transfert;
    private $groups;
    private $contentType;
    private $utilisateurStr;
    private $compteStr;
    private $bloqueStr;
    private $listUserCmpt;
    private $listUser;
    private $numeroCompte;
    private $listCompte;
    private $applicationJson;
    public function __construct()
    {
        $this->actif="Actif";
        $this->message="message";
        $this->status="status";
        $this->Transfert="Transfert";
        $this->groups='groups';
        $this->contentType='Content-Type';
        $this->utilisateurStr='utilisateur';
        $this->compteStr='compte';
        $this->bloqueStr='Bloqué';
        $this->listUserCmpt='list-userCmpt';
        $this->listUser='list-user';
        $this->numeroCompte= "numeroCompte";
        $this->listCompte= 'list-compte';
        $this->applicationJson='application/json';
    }
/**
* @Route("/entreprise", name="enregistre", methods={"POST"})
*/
    public function ajouPartenaire(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder,ValidatorInterface $validator,SerializerInterface $serializer): Response
    {

        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $user);
        $data = $request->request->all();
        $form->submit($data);

        $user->setPassword($passwordEncoder->encodePassword($user, $data["password"]));
        $user->setTelephone(rand(770000000,779999999));
        $user->setNci(strval(rand(150000000,279999999)));
        $user->setStatus($this->actif);
        $user->setRoles(['ROLE_AdminPrincipal']);

        $entreprise = new Entreprise();
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $data = $request->request->all();
        $form->submit($data);

        $entreprise->setStatus($this->actif);
        $user->setEntreprise($entreprise);

        $compte= new Compte();
        $form = $this->createForm(CompteType::class, $compte);// liaison de notre formulaire avec l'objet de type depot
        $data=$request->request->all(); //conversion de notre element de la requette
        $form->submit($data);

        $compte->setNumeroCompte(date('y').date('m').' '.date('d').date('H').' '.date('i').date('s'));
        $compte->setEntreprise($entreprise);

        $entityManager = $this->getDoctrine()->getManager();
        
    
        $entityManager->persist($user);
        $entityManager->persist($entreprise);
        $entityManager->persist($compte);

        $entityManager->flush();
        $afficher = [
            $this->status => 201,
            $this->message => 'Le partenaire '.$entreprise->getRaisonSociale().' ainsi que son admin principal ont bien été ajouté !! ',
           $this->compteStr =>'Le compte numéro '.$compte->getNumeroCompte().' lui a été assigné'
        ];
        return $this->handleView($this->view($afficher,Response::HTTP_OK));       
    }

  

      /**
     * @Route("/liste/entreprise", name="entreprises", methods={"GET"})
     * @Route("/entreprise/{id}", name="entreprise", methods={"GET"})
     */
    public function lister(EntrepriseRepository $repo, SerializerInterface $serializer,Entreprise $entreprise=null,$id=null)
    {
        
        $entreprise = $repo->findAll();
        $data = $serializer->serialize($entreprise,'json',[ $this->groups => ['list-entreprise']]);
        return new Response($data,200,[$this->contentType => $this->applicationJson]);
    }

    /**
    * @Route("/nouveau/depot", methods={"POST"})
    */

    public function depot (Request $request, ValidatorInterface $validator, UserInterface $Userconnecte,CompteRepository $repo, EntityManagerInterface $entityManager){

        $depot = new Depot();
        $form = $this->createForm(DepotType::class, $depot);
        $data = $request->request->all();
        if($compte=$repo->findOneBy([ $this->numeroCompte=>$data[$this->compteStr]])){
            $data[$this->compteStr]=$compte->getId();//on lui donne directement l'id
            if($compte->getEntreprise()->getRaisonSociale()==$this->Transfert){
                throw new HttpException(403,'On ne peut pas faire de depot dans le compte de  Transfert!');
            }
        }
        else{
            throw new HttpException(404,'Ce numero de compte n\'existe pas!');
        }
        $form->submit($data);

        $depot->setDate(new \Datetime());
        $depot->setCaissier($Userconnecte);
        $compte=$depot->getCompte();
        $compte->setSolde($compte->getSolde()+$depot->getMontant());
        $entityManager->persist($compte);
        $entityManager->persist($depot);
        $entityManager->flush();
        $afficher = [
             $this->status => 201,
             $this->message => 'Le depot a bien été effectué dans le compte '.$compte->getNumeroCompte()
        ];

        return $this->handleView($this->view($afficher,Response::HTTP_CREATED));

    }

   /**
     * @Route("/liste/user", name="user_entrepriseAll", methods={"GET"})
     */
    public function liste(SerializerInterface $serializer,UserInterface $userConnecte,UtilisateurRepository $repo)
    {
        $entreprise=$userConnecte->getEntreprise();//tous les users meme l admin principal
        $users=$repo->findBy(['entreprise'=>$entreprise]);
        $data = $serializer->serialize($users,'json',[ $this->groups => [$this->listUser]]);
        return new Response($data,200);
    }


        /**
    * @Route("/nouveau/compte/{id}", name="nouveau_compte", methods={"GET"})
    */ 
    public function addCompte( EntityManagerInterface $entityManager, Entreprise $entreprise){//securiser la route
        $compte =new Compte();
        if(!$entreprise){
            throw new HttpException(404,'Ce partenaire n\'existe pas !');
        }
        elseif($entreprise->getRaisonSociale()==$this->saTransfert){
            throw new HttpException(403,'Impossible de créer plusieurs compte pour Transfert!');
        }
        $compte->setNumeroCompte(date('y').date('m').' '.date('d').date('H').' '.date('i').date('s'))
                ->setEntreprise($entreprise)
                ->setSolde(0);
        $entityManager->persist($compte);
        $entityManager->flush();
        $afficher = [
            $this->status => 201,
            $this->message => 'Un nouveau compte est créé pour l\'entreprise '.$entreprise->getRaisonSociale(),
            $this->compteStr=> $compte->getNumeroCompte()
        ];
        return $this->handleView($this->view($afficher,Response::HTTP_OK));
    }


      /**
     * @Route("/changer/compte" ,name="change_compte")
     */
    public function changeCompte(Request $request,EntityManagerInterface $entityManager, UserInterface $Userconnecte,UtilisateurRepository $repoUser,CompteRepository $repoCompte,UserCompteActuelRepository $repoUserComp)
    {   
        $data=json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();//si non json
        }
        if(!isset($data[$this->utilisateurStr],$data[$this->compteStr])){
            throw new HttpException(404,'Remplir un utilisateur et  compte existant!');
        }
        elseif(!$user=$repoUser->find($data[$this->utilisateurStr])){
            throw new HttpException(404,'Cet utilisateur n\'existe pas !');
        }
        elseif($user->getRoles()[0]=='ROLE_SuperAdmin' || $user->getRoles()[0]=='ROLE_Caissier'){
            throw new HttpException(403,'Impossible d\'affecter un compte à cet utilisateur !');
        }
        elseif($user->getEntreprise()!=$Userconnecte->getEntreprise()){
            throw new HttpException(403,'Cet utilisateur n\'appartient pas à votre entreprise !');
        }
        if(!$compte=$repoCompte->find($data[$this->compteStr])){
            throw new HttpException(404,'Ce compte n\'existe pas !');
        }
        elseif($compte->getEntreprise()!=$Userconnecte->getEntreprise()){
            throw new HttpException(404,'Ce compte n\'appartient pas à votre entreprise !');
        }
        $idcompActuel=null;
        if($userComp=$repoUserComp->findBy([$this->utilisateurStr=>$user])){
            $idcompActuel=$userComp[count($userComp)-1]->getCompte()->getId();//l id du compte qu il utilise actuellement
        }
        
        if($idcompActuel==$compte->getId()){
            throw new HttpException(403,'Cet utilisateur utilise ce compte actuellement!');
        }
        $userCompte=new UserCompteActuel();

        $userCompte->setCompte($compte)
                   ->setUtilisateur($user)
                   ->setDateAffectation(new \DateTime());
        $entityManager->persist($userCompte);
        $entityManager->flush();
        $afficher = [
                $this->status => 201,
                $this->message => 'Le compte de l\'utilisateur a été modifié !!'
           ];
        return $this->handleView($this->view($afficher,Response::HTTP_OK));
    }


      /**
     * @Route("/compte/entreprise/{id}", name="compte_entr", methods={"GET"})
     * @Route("/MesComptes", name="compte_userCon", methods={"GET"})
     */
    public function getCompte(UserInterface $userConnecte, SerializerInterface $serializer,Entreprise $entreprise=null,$id=null)
    {
        
        if($id && !$entreprise instanceof Entreprise) {
            throw new HttpException(404,'Ce partenaire n\'existe pas!');
        }
        elseif(!$id){
            $entreprise=$userConnecte->getEntreprise();
            
        }
        
        $data = $serializer->serialize($entreprise->getComptes(),'json',[ $this->groups => [ $this->listCompte]]);
        return new Response($data,200,[$this->contentType => $this->applicationJson]);
    }


      /**
     * @Route("/comptes/all", name="comptesAll", methods={"GET"})
     */
    public function getAllCompte(SerializerInterface $serializer,CompteRepository $repo)
    {
        $comptes=$repo->findAll();
        $data = $serializer->serialize($comptes,'json',[ $this->groups => [ $this->listCompte]]);
        return new Response($data,200,[$this->contentType => $this->applicationJson]);
    }



    /**
     * @Route("/utilisateur/affecterCompte/{id}", name="utilisateurCmpt", methods={"GET"})
     */
    public function getUtilisateursActuCompte(SerializerInterface $serializer,Compte $compte,UserCompteActuelRepository $repo,UserInterface $userConnecte)
    {
        $tab=[];
        $users=$userConnecte->getEntreprise()->getUtilisateurs();//tous les users de l entreprise
         
        for($i=0;$i<count($users);$i++){
            $tous=$repo->findBy(['utilisateur'=>$users[$i]]);//on recup toutes les affectations de compte d un user
            if($tous){
                $usercompt=$tous[count($tous)-1];//il est actuellement affecter au dernier
                $compteAct=$usercompt->getCompte();//son compte
                if($compteAct==$compte){//si c est le mm que selui du id on l ajoute dans le array
                    array_push($tab ,$usercompt);
                }
            }
        }
        $data = $serializer->serialize($tab,'json',[ $this->groups => ['liste-affCmpt']]);
        return new Response($data,200,[$this->contentType => $this->applicationJson]);
    }


      /**
     * @Route("/gestion/comptes/liste", name="user_comptes", methods={"GET"})
     * @Route("/gestion/compte/{id}", name="user_compte", methods={"GET"})
     */
    public function listerUserCompt(UserCompteActuelRepository $repo, SerializerInterface $serializer,UserInterface $userConnecte,UserCompteActuel $userCompte=null,$id=null)
    {
        
        if($id && !$userCompte instanceof UserCompteActuel) {
            throw new HttpException(404,'Resource non trouvée ! ');
        }
        elseif(!$userCompte){
            $userCompte=$repo->findByEntreprise($userConnecte->getEntreprise());
        }
        
        $data = $serializer->serialize($userCompte,'json',[ $this->groups => [$this->listUserCmpt]]);
        return new Response($data,200);
    }

      /**
     * @Route("/compte/user/{id}", name="userCompte", methods={"GET"})
     */
    public function userCompte(SerializerInterface $serializer,UserCompteActuelRepository $repo,Utilisateur $user){
        $userComp=$repo->findUserComptActu($user);
        $data = $serializer->serialize($userComp,'json',[ $this->groups => [$this->listUserCmpt]]);
        return new Response($data,200);
    }


      /**
     * @Route("/comptes/affecte/user/{id}", name="userCompteAffecte", methods={"GET"})
     */
    public function userComptesAffecte(SerializerInterface $serializer,UserCompteActuelRepository $repo,Utilisateur $user){
        $userComp=$repo->findUserComptesAff($user);
        $data = $serializer->serialize($userComp,'json',[ $this->groups => [$this->listUserCmpt]]);
        return new Response($data,200);
    }


      /**
     * @Route("/entreprise/responsable/{id}", name="adminPartenaire", methods={"GET"})
     */
    public function getResponsable(SerializerInterface $serializer,Entreprise $entreprise,UtilisateurRepository $repo){
        $userComp=$repo->findResponsable($entreprise);
        $data = $serializer->serialize($userComp,'json',[ $this->groups => [$this->listUser]]);
        return new Response($data,200);
    }


      /**
     * @Route("/compte/numeroCompte", name="leCompte", methods={"POST"})
     */
    public function getLeCompte(Request $request, SerializerInterface $serializer,CompteRepository $repo){
        $data=json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();//si non json
        }
        
        if($compte=$repo->findOneBy([ $this->numeroCompte=>$data[$this->numeroCompte]])){
            
            if($compte->getEntreprise()->getRaisonSociale()==$this->saTransfert){
                throw new HttpException(403,'On ne peut pas faire de depot dans le compte de SA Transfert !');
            }
        }
        else{
            throw new HttpException(404,'Ce numero de compte n\'existe pas !');
        }
        $data = $serializer->serialize($compte,'json',[ $this->groups => ["list-compte"]]);
        return new Response($data,200);
    }
    

}
