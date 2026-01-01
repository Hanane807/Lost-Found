<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User ;
use App\Repository\UserRepository ;


final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/add', name: 'add_user')]
    public function addUser(EntityManagerInterface $em ,  UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        $user->setEmail("hanane@gmail.com");
        $hashedPassword = $passwordHasher->hashPassword($user, "Hanane");
        $user->setPassword($hashedPassword);
        $user->setRoles(["ROLE_USER"]);

        $em->persist($user);
        $em->flush();

        return new Response("Utilisateur ajouté avec succès");
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
