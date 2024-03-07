<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Form\CoachType;
use App\Repository\CoachRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/coach')]
class CoachController extends AbstractController
{
    #[Route('/', name: 'app_coach_index', methods: ['GET'])]
    public function index(CoachRepository $coachRepository): Response
    {
        return $this->render('coach/index.html.twig', [
            'coaches' => $coachRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_coach_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $activity = new Coach();
        $form = $this->createForm(CoachType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('img')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('destination_img'),
                        $newFilename
                    );
                    
                } catch (FileException $e) {
                     // Handle exception if something happens during file upload
               
                }
                $activity->setImg($newFilename);
            }
            //if (!$brochureFile) {
                // Handle error if 'img' is not provided
               // $this->addFlash('error', 'Please upload an image.');
                //return $this->redirectToRoute('app_activities_new');
           // }
            $entityManager->persist($activity);
            $entityManager->flush();

            return $this->redirectToRoute('app_coach_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('coach/new.html.twig', [
            'Coach' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_coach_show', methods: ['GET'])]
    public function show(Coach $coach): Response
    {
        return $this->render('coach/show.html.twig', [
            'coach' => $coach,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_coach_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Coach $coach, EntityManagerInterface $entityManager,$id): Response
   { $activity = $entityManager->getRepository(Coach::class)->find($id);
    
    // Create the form and bind it to the activity entity
    $form = $this->createForm(CoachType::class, $activity);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Retrieve the uploaded file from the request
        $imageFile = $form->get('img')->getData();

        // Check if a file was uploaded
        if ($imageFile) {
            // Generate a unique name for the file
            $newFilename = md5(uniqid()).'.'.$imageFile->guessExtension();

            // Move the file to the desired location
            try {
                $imageFile->move(
                    $this->getParameter('destination_img'),
                    $newFilename
                );
            } catch (FileException $e) {
                // Handle file upload error
            }

            // Set the file name in the activity entity
            $activity->setImg($newFilename);
        }

        // Persist changes to the database
        $entityManager->flush();

        // Redirect the user
        return $this->redirectToRoute('app_coach_index');
    }

    // Render the form
    return $this->render('Coach/edit.html.twig', [
        'coach' => $activity,
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id}', name: 'app_coach_delete', methods: ['POST'])]
    public function delete(Request $request, Coach $coach, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$coach->getId(), $request->request->get('_token'))) {
            $entityManager->remove($coach);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_coach_index', [], Response::HTTP_SEE_OTHER);
    }
  

    
}
