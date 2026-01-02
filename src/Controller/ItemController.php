<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ItemController extends AbstractController
{

    #[Route('/item', name: 'app_item')]
    public function index(ItemRepository $itemRepository): Response
    {
        return $this->render('item/index.html.twig', [
        
            'items' => $itemRepository->findAll(),
        ]);
    }

    #[Route('/item/new', name: 'item_new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {

           // Vérifier si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            // S'il n'est pas connecté, on le renvoie vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        $item = new Item();
        
        $form = $this->createForm(ItemType::class, $item);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // On récupère le fichier image
            $imageFile = $form->get('imageFile')->getData();

            // Si une image a été uploadée, on la traite
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // On nettoie le nom
                $safeFilename = $slugger->slug($originalFilename);
                // On crée un nom unique
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    // On déplace le fichier dans le dossier public/uploads/items
                    $imageFile->move(
                        $this->getParameter('items_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                // On enregistre le NOM du fichier dans la base
                $item->setImage($newFilename);
            }
            $item->setUser($this->getUser());

            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('app_item'); // Redirection vers la liste
        }

        return $this->render('item/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/item/update/{id}', name: 'item_update')]
    public function updateItem(int $id , ItemRepository $itemRepository , EntityManagerInterface $em): Response
    {
        $item = $itemRepository->find($id);
        if(!$item){
            throw $this->createNotFoundException('Item non trouvé');
        }

        $item->setCity('Casablanca');
        $item->setStatus('found');

        $em->flush();

        return new Response('Item modifié');
    }

    #[Route('/item/delete/{id}', name: 'item_delete')]
    public function deleteItem(int $id , ItemRepository $itemRepository , EntityManagerInterface $em): Response
    {
        $item = $itemRepository->find($id);
        if(!$item){
            throw $this->createNotFoundException('Item non trouvé');
        }

        $em->remove($item);
        $em->flush();

        return new Response('Item supprimé');
    }
}
