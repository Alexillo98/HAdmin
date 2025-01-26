<?php

namespace App\Controller;


use App\Entity\Cita;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig');
    }
    #[Route('/getCitas', name: 'get_citas')]
    public function getCitas(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine -> getRepository(Cita::class);
        $citas = $repository -> findAll();
        $data = [
            "startDate" => $citas[0]->getFecha(),
            "endDate" => $citas[0]->getFecha(),
            "summary" => $citas[0]->getDescripcion()
        ];
        return $this->json($data);
    }
}
