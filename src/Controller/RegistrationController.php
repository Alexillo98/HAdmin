<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Entity\Paciente;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // --> VALOR NO ESCALAR $especialidad = $request->request->get('registration_form') ?? null;
            $especialidad = $request->request->all()['registration_form']['especialidad'] ?? null;
            if($especialidad !== ""){
                $user = new Medico();
                $user->setEspecialidad($especialidad);
            } else {
                $user = new Paciente();
                $file = $form->get('historiaC')->getData();
                if ($file){
                    $originalFileName = pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);
                    //  esto se hace para poder incluir de manera segura el nombre del archivo como parte de la URL
                    $safeFilename = $slugger -> slug($originalFileName);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                    //  mover el archivo al directorio donde se almacenan las imagenes

                    try {
                        $file->move( // <----- REVISAR ESTA PARTE
                            $this->getParameter('images_directory'), $newFilename
                        );
                    }catch (FileException $e){
                    }

                    //  actualiza la propiedad $filename de $file para que guarde
                    // el nombre del PDF en vez de su contenido
                    $user->setHistoriaClinica($newFilename);
                }
            }

            $user->setNombre($form->get('nombre')->getData());
            $user->setApellido($form->get('apellido')->getData());
            $user->setEdad($form->get('edad')->getData());
            $user->setEmail($form->get('email')->getData());
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData())
            );

            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
