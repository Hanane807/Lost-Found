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
use App\Form\SearchType;

final class ItemController extends AbstractController
{


    #[Route('/item', name: 'app_item')]
    public function index(ItemRepository $itemRepository, Request $request): Response
    {

        // 1. On crée le formulaire
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);


        // 2. Par défaut, on prend tout
        $items = $itemRepository->findAll();

        // 3. Si on fait une recherche, on filtre
        if ($form->isSubmitted() && $form->isValid()) {
            $items = $itemRepository->findWithSearch($form->getData());
        }

        return $this->render('item/index.html.twig', [
            'items' => $items,
            'searchForm' => $form->createView() // On envoie le formulaire à la vue
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

    #[Route('/item/{id}', name: 'item_show')]
    public function show(Item $item): Response
    {
        return $this->render('item/show.html.twig', [
            'item' => $item,
        ]);
    }

}

