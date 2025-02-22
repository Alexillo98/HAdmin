<?php

namespace App\Controller;

use App\Entity\Cita;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class IndexController extends AbstractController
{
    #[Route('/citas', name: 'app_citas')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig');
    }

    #[Route('/getCitas', name: 'get_citas')]
    public function getCitas(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Cita::class);
        $citas = $repository->findAll();

        $data = [];

        foreach ($citas as $cita) {
            $data[] = [
                // Incluimos el ID para poder eliminar luego
                'id' => $cita->getId(),
                'startDate' => $cita->getFecha()->format('Y-m-d-H:i:s'),
                'endDate'   => $cita->getFecha()->format('Y-m-d-H:i:s'),
                'summary'   => $cita->getDescripcion(),
                'name'    => $cita->getNombre()
            ];
        }

        return $this->json($data);
    }


    /**
     * @throws \DateMalformedStringException
     */
    #[Route('/addCita', name: 'add_cita', methods: ['POST'])]
    public function addCita(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $fecha = $request->request->get('fecha');       // "YYYY-MM-DD" desde JavaScript
        $descripcion = $request->request->get('descripcion');
        $nombre = $request->request->get("nombre");

        // Creamos la entidad
        $cita = new Cita();
        $cita->setFecha(new \DateTime($fecha));
        $cita->setDescripcion($descripcion);
        $cita->setNombre($nombre); // en caso de que necesites 'nombre'

        $entityManager->persist($cita);
        $entityManager->flush();

        return $this->json([
            'status' => 'OK',
            'message' => 'Cita guardada correctamente'
        ]);
    }
    #[Route('/deleteCita', name: 'delete_cita', methods: ['POST'])]
    public function deleteCita(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $id = $request->request->get('id'); // id de la cita a eliminar
        $entityManager = $doctrine->getManager();

        // Buscamos la cita
        $cita = $entityManager->getRepository(Cita::class)->find($id);

        // Si no existe, devolvemos error
        if (!$cita) {
            return $this->json([
                'status' => 'error',
                'message' => 'No se encontró la cita con id '.$id
            ], 404);
        }

        // Eliminamos y confirmamos
        $entityManager->remove($cita);
        $entityManager->flush();

        return $this->json([
            'status' => 'OK',
            'message' => 'Cita eliminada correctamente'
        ]);
    }
}