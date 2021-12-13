<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountOnlineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tanMediaName', TextType::class, [
                'label' => 'TAN Medium',
                'empty_data' => '',
            ])
            ->add('tanMechanism', IntegerType::class, [
                'label' => 'TAN Mode',
                'empty_data' => '',
            ])
            ->add('username', PasswordType::class, [
                'label' => 'User',
                'empty_data' => '',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'empty_data' => '',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
