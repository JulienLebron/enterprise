<?php

namespace App\Controller;

use App\Entity\Employes;
use App\Form\EmployesType;
use App\Repository\EmployesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    #[Route('/', name: 'employes')]
    public function employes(EmployesRepository $repo, EntityManagerInterface $em): Response
    {
        $columns = $em->getClassMetadata(Employes::class)->getFieldNames();
        $employes = $repo->findAll();

        return $this->render('app/index.html.twig', [
            'employes' => $employes,
            'columns' => $columns
        ]);
    }

    #[Route('/employes/new', name: 'create_employes')]
    #[Route('/employes/{id}/update-employes', name: 'update_employes')]
    public function form(Request $request, EntityManagerInterface $manager, Employes $employes = null): Response
    {
        if (!$employes) {
            $employes = new Employes;
        }
        $editMode = $employes->getId() !== NULL;
        $form = $this->createForm(EmployesType::class, $employes);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($employes); 
            $manager->flush(); 
            $editMode ? $this->addFlash('success', "✅ Employer updated successfully!") : $this->addFlash('success', "✅ Employer created successfully!");
            return $this->redirectToRoute('employes');
        }

        return $this->render('app/form.html.twig', [
            'formEmployes' => $form->createView(),
            'editMode' => $editMode
        ]);
    }

    #[Route('/employes/{id}/delete', name: 'delete_employes')]
    public function deleteEmployes(Employes $employes, EntityManagerInterface $manager): Response
    {
        $manager->remove($employes);
        $manager->flush();

        $this->addFlash('success', "✅ Employer deleted successfully!");
        return $this->redirectToRoute('employes');
    }
}
