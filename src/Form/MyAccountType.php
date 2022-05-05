<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class MyAccountType extends AbstractType
{

    public function __construct(private EntityManagerInterface $entityManager, private Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('profilePicture', FileType::class, [
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/bmp',
                            'image/png',
                        ],
                    ]),
                ],
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Insert your Email'],
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                    new Length(['max' => 100]),
                ],
                'required' => false,
            ])
            ->add('dateOfBirth', BirthdayType::class, [
                'widget' => 'single_text',
                'label' => 'Date of birth',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'placeholder' => 'Date of birth',
                    'class' => 'date-input',
                ],
                'html5' => false,
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Male' => 'Male',
                    'Female' => 'Female',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('bio', TextareaType::class, [
                'constraints' => [
                    new Length(['max' => 1000]),
                ],
                'required' => false,
            ])
            ->add('submit', SubmitType::class, ['label' => "Submit"])
            ->get('email')->addEventSubscriber(new EmailEventListener($this->entityManager, $this->security));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
