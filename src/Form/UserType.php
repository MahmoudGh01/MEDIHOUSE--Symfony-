<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Captcha\Bundle\CaptchaBundle\Form\Type\CaptchaType;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints\ValidCaptcha;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 255]),
                ],
            ])
            ->add('prenom')
            ->add('genre', ChoiceType::class, [
                'choices' => [
                    'Male' => 'male',
                    'Female' => 'female'
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'genre'
            ])
            ->add('telephone')
            ->add('adresse')
            ->add('profilepicture', FileType::class, array('data_class' => null), [
                'required' => false, [
                    'mapped' => false
                ]
            ])

            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'L\'adresse email ne peut pas être vide',
                    ]),

                ],
            ])

            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
