<?php

namespace App\Controller\Admin;

use App\Entity\Flat;
use App\Entity\Images;
use App\Repository\FlatRepository;
use App\Form\FlatForm;
use App\Repository\ImagesRepository;
use App\Service\PictureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;




#[Route('/admin/plat', name: 'admin_flat_')]

class FlatController extends AbstractController
{
    #[Route('/', name: 'index')]

    public function index(FlatRepository $flatRepository, ImagesRepository $imagesRepository): Response
    {
        $flat = $flatRepository->findAll();
        $images = $imagesRepository->findAll();
        return $this->render('admin/flat/index.html.twig', compact('flat', 'images'));
    }

    // Route ajout Flat Admin
    #[Route('/ajouter', name: 'add')]

    public function add(Request $request, EntityManagerInterface $em, PictureService $pictureService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //On crée un nouvel objet Photo
        $flat = new Flat();

        //On crée le formulaire
        $flatFormulaire = $this->createForm(FlatForm::class, $flat);

        //On traite la requête du formulaire
        $flatFormulaire->handleRequest($request);

        if ($flatFormulaire->isSubmitted() && $flatFormulaire->isValid()) {
            // On récupère l'image
            $images = $flatFormulaire->get('images')->getData();

            foreach ($images as $image) {
                // On définit le dossier de destination
                $folder = 'flats';

                // On appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Images();
                $img->setTitre($fichier);
                $flat->addImage($img);
            }

            $em->persist($flat);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès');

            // On redirige vers la liste des photos
            return $this->redirectToRoute('admin_flat_index');
        }

        return $this->render('admin/flat/add.html.twig', [
            'flatFormulaire' => $flatFormulaire->createView()
        ]);
    }

    #[Route('/modidier/{id}', name: 'edit')]
    public function edit(Flat $flat, Request $request, EntityManagerInterface $em, PictureService $pictureService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // On crée le formulaire
        $flatFormulaire = $this->createForm(FlatForm::class, $flat);

        // On traite la requête du formulaire
        $flatFormulaire->handleRequest($request);

        //On vérifie si le formulaire est soumis ET valide
        if ($flatFormulaire->isSubmitted() && $flatFormulaire->isValid()) {

            foreach ($flat->getImages() as $image) {
                // Supprime l'image du dossier
                $pictureService->delete($image->getTitre());
                // Supprime l'image de la collection
                // $flat->removeImage($image);
                $flat->getImages()->removeElement($image);
            }

            $images = $flatFormulaire->get('images')->getData();

            foreach ($images as $image) {
                // On définit le dossier de destination
                $folder = 'flats';

                // On appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Images();
                $img->setTitre($fichier);
                $flat->addImage($img);
            }

            // On stocke
            $em->persist($flat);
            $em->flush();

            $this->addFlash(
                'success',
                'Produit modifié avec succès'
            );

            // On redirige
            return $this->redirectToRoute('admin_flat_index');
        }

        return $this->render('admin/flat/edit.html.twig', [
            'flatFormulaire' => $flatFormulaire->createView(),
            'flat' => $flat
        ]);
    }

    #[Route('/supprimer/{id}', name: "delete")]
    public function delete(Flat $flat, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($flat);
        $em->flush();

        $this->addFlash(
            'success',
            'Produit supprimé avec succès'
        );

        return $this->redirectToRoute("admin_flat_index");
    }
}
