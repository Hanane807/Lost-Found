<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface ;
use App\Entity\Item;
use App\Repository\ItemRepository;


final class ItemController extends AbstractController
{
    #[Route('/item', name: 'app_item')]
    public function index(): Response
    {
        return $this->render('item/index.html.twig', [
            'controller_name' => 'ItemController',
        ]);
    }

    #[Route('/item/add', name: 'item_add')]
    public function addItem(EntityManagerInterface $em): Response
    {
       $item = new Item() ;

       $item->setTitle('Telephone perdu') ;
       $item->setDescription('Galaxy A51 noir') ;
       $item->setCity('Oujda') ;
       $item->setDateLost(new \DateTime('2025-05-15')) ;
       $item->setStatus('found') ;
       $item->setImage('image.jpg') ;
       $item->setColor(' Noir') ;
       $item->setKeywords('Telephone, samsung , noir , perdu') ;

       $em->persist($item) ;
       $em->flush();

       return new Response ("Item ajouté avec succés");

    }

    #[Route('/item/update/{id}', name: 'item_update')]
    public function updateItem(int $id , ItemRepository $itemRepository , EntityManagerInterface $em): Response
    {
        $item = $itemRepository -> find($id) ;
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
        $item = $itemRepository -> find($id) ;
        if(!$item){
            throw $this->createNotFoundException('Item non trouvé');
        }

    $em->remove($item);
    $em->flush();

    return new Response('Item supprimé');
    }
}
