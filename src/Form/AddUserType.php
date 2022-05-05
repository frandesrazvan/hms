<?php

declare(strict_types=1);

namespace App\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use App\Entity\User;
use App\Entity\Hotel;
use App\Enum\UserEnum;

class AddUserType extends AbstractType
{
    public function __construct(private EntityManagerInterface $entityManager, private Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();

        foreach ($options['hotels_list'] as $hotel) {
            $hotelChoices[$hotel->getName()] = $hotel;
        }

        if ($user->isOwner()) {
            $roleChoices = [
                'Manager' => UserEnum::MANAGER_ROLE,
                'Employee' => UserEnum::EMPLOYEE_ROLE,
            ];
        }

        if ($user->isManager()) {
            $roleChoices = [
                'Employee' => UserEnum::EMPLOYEE_ROLE,
            ];
        }

        $builder
            ->add('username', TextType::class, [
                'attr' => ['placeholder' => 'Insert your Username'],
                'constraints' => [
                    new Length([
                        'max' => 30,
                        'min' => 4,
                        'minMessage' => 'This field does not meet the length requirement',
                        'maxMessage' => 'This field does not meet the length requirement',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9]+$/',
                        'message' => 'Only alphanumeric characters',
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Passwords dont match',
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(?=.*[\d])(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&])[a-zA-z\d@$!%*?&]{8,}$/',
                        'message' => 'This field does not meet the required complexity',
                    ]),
                ],
                'first_options' => [
                    'attr' => ['placeholder' => 'Insert your Password'],
                    'label' => 'Password',
                ],
                'second_options' => [
                    'attr' => ['placeholder' => 'Confirm your Password'],
                    'label' => 'Confirm password',
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Insert your Email'],
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
            ->add('role', ChoiceType::class, [
                'choices' => $roleChoices ?? [],
            ])
            ->add('hotel', ChoiceType::class, [
                'choices' => $hotelChoices ?? [],
            ])
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Length([
                        'max' => 100,
                        'min' => 2,
                        'minMessage' => 'This field does not meet the required complexity.',
                        'maxMessage' => 'This field does not meet the required complexity.',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Length([
                        'max' => 100,
                        'min' => 2,
                        'minMessage' => 'This field does not meet the required complexity.',
                        'maxMessage' => 'This field does not meet the required complexity.',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => "Submit"])
            ->get('username')->addEventSubscriber(new UsernameEventListener($this->entityManager));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'hotels_list' => false,
        ]);
    }

}
