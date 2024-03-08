<?php

namespace App\Controller;

use App\Entity\Livraison;
use App\Form\LivraisonType;
use App\Form\LivraisonTypeb;
use App\Repository\LivraisonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Rest\Client;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Builder\BuilderRegistryInterface;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCodeBundle\Response\QrCodeResponse;

#[Route('/livraison')]
class LivraisonController extends AbstractController
{
    #[Route('/', name: 'app_livraison_index', methods: ['GET'])]
    public function index(LivraisonRepository $livraisonRepository): Response
    {
        return $this->render('livraison/index.html.twig', [
            'livraisons' => $livraisonRepository->findAll(),
        ]);
    }

    #[Route('/back', name: 'app_livraison_indexb', methods: ['GET'])]
    public function indexb(LivraisonRepository $livraisonRepository): Response
    {
        return $this->render('livraison/indexb.html.twig', [
            'livraisons' => $livraisonRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_livraison_new', methods: ['GET', 'POST'])]
    public function new(Request $request,Client $twilioClient, EntityManagerInterface $entityManager): Response
    {
        $livraison = new Livraison();
        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livraison->setStatus(0);
            $entityManager->persist($livraison);
            $entityManager->flush();
            $twilioClient->messages->create(
                '+21628919047',
                array(
                    'from' => $this->getParameter('twilio_number'),
                    'body' => "Merci pour votre commande de " . (new \DateTime())->format('d-m-Y') . " nous vous confirmons votre commande Bonne réception"
                )
            );
    
            return $this->redirectToRoute('app_livraison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livraison/new.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
        ]);
    }

    #[Route('/qrcode/{id}', name: 'qrcode')]
    public function qrcode(BuilderInterface $customQrCodeBuilder , $id ,EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Livraison::class);
        $Livraison = $repository->find($id);
  
        $titre = $Livraison->getId();
        $produit = $Livraison->getProduit()->getNom();
        $status = $Livraison->getstatus();
  
  
        $data = "Les détails de votre livraison sont :\n";
        $data .= "- id : " . $id . "\n";
        $data .= "- produit : " . $produit . "\n";
        $data .= "- status : " . $status;
       
   
        $qrCode = $customQrCodeBuilder
            ->size(400)
            ->margin(20)
            ->data($data)
            ->build();
   
        return new QrCodeResponse($qrCode);
    } 

    #[Route('/{id}', name: 'app_livraison_show', methods: ['GET'])]
    public function show(Livraison $livraison): Response
    {
        return $this->render('livraison/show.html.twig', [
            'livraison' => $livraison,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livraison_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livraison $livraison, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_livraison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livraison/edit.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/editb', name: 'app_livraison_editb', methods: ['GET', 'POST'])]
    public function editb(Request $request, Livraison $livraison, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivraisonTypeb::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_livraison_indexb', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livraison/editb.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_livraison_delete', methods: ['POST'])]
    public function delete(Request $request, Livraison $livraison, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livraison->getId(), $request->request->get('_token'))) {
            $entityManager->remove($livraison);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_livraison_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/excell', name: 'excell', methods: ['GET'])]
    public function excell(LivraisonRepository $livraisonRepository): Response
    {
        // Fetch all products from the database
        $produits = $livraisonRepository->findAll();

        // Create a new Spreadsheet object and populate it with the data
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setCellValue('A1', 'id');
        $worksheet->setCellValue('B1', 'produit_id');
        $worksheet->setCellValue('C1', 'status');

        // Row counter for data insertion
        $row = 2;

        // Loop through each product and fill the spreadsheet
        foreach ($produits as $produit) {
            $worksheet->setCellValue('A' . $row, $produit->getId());
            $worksheet->setCellValue('B' . $row, $produit->getProduit()->getNom());
            $worksheet->setCellValue('C' . $row, $produit->getstatus());

            // Increment row counter
            $row++;
        }

        // Create a new Xlsx writer and write the Spreadsheet object to it
        $writer = new Xlsx($spreadsheet);
        $excelFilename = 'livraison_all.xlsx';
        $writer->save($excelFilename);

        // Send the Excel file as a download to the user
        $response = new BinaryFileResponse($excelFilename);
        $response->setContentDisposition(\Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT, $excelFilename);

        return $response;
    }
}
