<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User ;
use App\Form\UserFormType;
use App\Repository\UserRepository ;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\AppCustomAuthenticator;



final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

#[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator, 
        AppCustomAuthenticator $authenticator
    ): Response
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // Hachage du mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_USER']);

            // Enregistrement en BDD
            $entityManager->persist($user);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('user/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/user/delete/{id}', name: 'delete_user')]
    public function deleteUser(EntityManagerInterface $em , UserRepository $userepo , int $id ,Security $security): Response
    {
        $user = $userepo->find($id);

        if(!$user){
            throw $this->createNotFoundException('utilisateur non trouvé');
        }

        $currentUser = $security->getUser(); 

        if ($currentUser !== $user) {
        throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce compte');
        }
        
        $em->remove($user);
        $em->flush();


        return new Response("Utilisateur supprimé avec succès");
    }

    #[Route('/user/update/{id}', name: 'update_user')]
    public function updateUser(EntityManagerInterface $em , UserRepository $userepo , int $id , Security $security): Response
    {
        $user = $userepo->find($id);

        if(!$user){
            throw $this->createNotFoundException('utilisateur non trouvé');
        }

        $currentUser = $security->getUser(); 

        if ($currentUser !== $user) {
        throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce compte');
        }
        
        $user->setEmail("mezhanane76@gmail.com");
        $em->flush();


        return new Response("Utilisateur mis à jour avec succès");
    }
}
