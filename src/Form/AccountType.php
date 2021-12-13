<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name of the Bank',
            ])
            ->add('accountHolder', TextType::class, [
                'label' => 'Account holder',
            ])
            ->add('iban', TextType::class, [
                'label' => 'IBAN',
            ])
            ->add('bic', TextType::class, [
                'label' => 'BIC',
            ])
            ->add('bankCode', TextType::class, [
                'label' => 'BLZ',
            ])
            ->add('url', TextType::class, [
                'label' => 'HBCI / FinTS URL',
            ])
            ->add('tanMediaName', TextType::class, [
                'label' => 'TAN Medium',
            ])
            ->add('tanMechanism', IntegerType::class, [
                'label' => 'TAN Mode',
            ])
            ->add('username', PasswordType::class, [
                'label' => 'User',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
            ])
            ->add('logo', TextType::class, [
                'label' => 'Logo',
            ])
            ->add('backgroundColor', TextType::class, [
                'label' => 'Background',
            ])
            ->add('foregroundColor', TextType::class, [
                'label' => 'Foreground',
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
