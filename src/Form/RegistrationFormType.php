<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("nombre",TextType::class)
            ->add("apellido",TextType::class)
            ->add("edad",IntegerType::class)
            ->add('email',EmailType::class)
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('user_type',ChoiceType::class,[
                'label' => 'Role',
                'choices' => [
                    'Médico' => 'ROLE_MEDICO',
                    'Paciente' => 'ROLE_PACIENTE'
                ],
                'mapped' => false,
                'data' => 'ROLE_MEDICO'
            ])
            ->add('especialidad',TextType::class,[
                'label' => 'Especialidades',
                'mapped' => false,
                'required' => false
            ])
            ->add('historiaC', FileType::class,[
                'label' => 'Historia Clínica',
                'mapped' => false,
                'required' => false,
            ])
            ->add('register',SubmitType::class,[
                "label" => "Registrate"
            ])
        ;
        /*
        $builder->addEventListener(FormEvents::PRE_SUBMIT,function (FormEvent $event) use ($builder) {
            $form = $event->getForm();
            $data = $event->getData();

            $especialidad = $data['especialidad'];
            $role = $data['user_type'];

            if($role == 'ROLE_MEDICO'){
                unset($data['historiaC']);
            }

            $event->setData($data);

            if($especialidad){
                $form->add("especialidad",ChoiceType::class,[
                    "label" => "Especialidad",
                    "choices" => [$especialidad => $especialidad],
                    "mapped" => false
                ]);
            }
        });
        */
        /*
        $builder->get('role')->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if($data == 'ROLE_MEDICO'){
                $form->getParent()->add('especialidad', TextType::class, [
                    'label' => 'Especialidad Médica',
                ]);
            } else {
                $form->getParent()->add('historiaClinica', FileType::class, [
                    'label' => 'Historia Clínica',
                    'required' => false,
                ]);
            }
        });
        */
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
