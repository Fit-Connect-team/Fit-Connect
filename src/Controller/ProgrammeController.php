<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Entity\Programme;
use App\Form\ProgrammeType;
use App\Repository\ProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;


#[Route('/programme')]
class ProgrammeController extends AbstractController
{
    #[Route('/', name: 'app_programme_index', methods: ['GET'])]
    public function index(ProgrammeRepository $programmeRepository): Response
    {
        return $this->render('programme/index.html.twig', [
            'programmes' => $programmeRepository->findAll(),
        ]);
    }


    #[Route('/myprogrmmes', name: 'app_programme_indexf', methods: ['GET'])]
    public function indexf(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request)
    {
        // Récupérer tous les articles
        $destQuery = $entityManager->getRepository(Programme::class)->findAll(); // Get all destinations query
        
        $dest = $paginator->paginate(
            $destQuery, // Query to paginate
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );

        // Créer le rendu Twig
        return $this->render('programme/indexf.html.twig', [
            'programmes' => $dest,
        ]);
    }  


   
   

    #[Route('/new', name: 'app_programme_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $programme = new Programme();
        $form = $this->createForm(ProgrammeType::class, $programme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($programme);
            $entityManager->flush();

            return $this->redirectToRoute('app_programme_indexf', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('programme/new.html.twig', [
            'programme' => $programme,
            'form' => $form,
        ]);
    }


    
    #[Route('/stats', name: 'app_programme_stats', methods: ['GET', 'POST'])]
    public function coachWithMostPrograms(EntityManagerInterface $entityManager): Response
    {
        // Get all programs
        $programs = $entityManager->getRepository(Programme::class)->findAll();

        // Count the number of programs for each coach
        $coachProgramCounts = [];
        foreach ($programs as $program) {
            $coachName = $program->getCoach()->getName(); // Assuming getName() returns the coach's name
            if (!isset($coachProgramCounts[$coachName])) {
                $coachProgramCounts[$coachName] = 0;
            }
            $coachProgramCounts[$coachName]++;
        }

        // Sort coaches by the number of programs
        arsort($coachProgramCounts);

        // Render the view with the best coach and the number of programs
        return $this->render('coach/with_most_programs.html.twig', [
            'coachProgramCounts' => $coachProgramCounts,
        ]);
    }



    #[Route('/{id}/edit', name: 'app_programme_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Programme $programme, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProgrammeType::class, $programme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_programme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('programme/edit.html.twig', [
            'programme' => $programme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/editf', name: 'app_programme_editf', methods: ['GET', 'POST'])]
    public function editf(Request $request, Programme $programme, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProgrammeType::class, $programme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_programme_indexf', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('programme/editf.html.twig', [
            'programme' => $programme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_programme_delete', methods: ['POST'])]
    public function delete(Request $request, Programme $programme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$programme->getId(), $request->request->get('_token'))) {
            $entityManager->remove($programme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_programme_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/show/{id}', name: 'app_reservation_showpdf', methods: ['GET'])]
    public function showpdf(Programme $reservation): Response
    {
        try {
            $coach = new Coach();
            // Configure Dompdf options
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');
            
            // Instantiate Dompdf with options
            $dompdf = new Dompdf($pdfOptions);
            
            // Retrieve HTML content from Twig template
            $html = $this->renderView('programme/program.html.twig', [
                'programme' => $reservation,
                'coach' => $coach
            ]);
            
            // Load HTML into Dompdf
            $dompdf->loadHtml($html);
            
            // (Optional) Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render HTML as PDF
            $dompdf->render();

            // Generate response with PDF content and download headers
            return new Response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="programdetails.pdf"',
            ]);
        } catch (\Exception $e) {
            // Handle exceptions
            return new Response('An error occurred: ' . $e->getMessage());
        }
    }

        #[Route('/{id}', name: 'app_programme_show', methods: ['GET'])]
        public function show(Programme $programme): Response
        {
            return $this->render('programme/show.html.twig', [
                'programme' => $programme,
            ]);
        }
        #[Route('/showf/{id}', name: 'app_programme_showf', methods: ['GET'])]
        public function showf(Programme $programme): Response
        {
            return $this->render('programme/showf.html.twig', [
                'programme' => $programme,
            ]);
        }
   




 
}
