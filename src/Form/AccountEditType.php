<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name of the bank',
                'empty_data' => '',
            ])
            ->add('accountHolder', TextType::class, [
                'label' => 'Account holder',
                'empty_data' => '',
            ])
            ->add('iban', TextType::class, [
                'label' => 'IBAN',
                'empty_data' => '',
            ])
            ->add('bic', TextType::class, [
                'label' => 'BIC',
                'empty_data' => '',
            ])
            ->add('bankCode', TextType::class, [
                'label' => 'BLZ',
                'empty_data' => '',
            ])
            ->add('url', TextType::class, [
                'label' => 'HBCI / FinTS URL',
                'empty_data' => '',
            ])
            ->add('logo', TextType::class, [
                'label' => 'Logo',
                'empty_data' => '',
            ])
            ->add('backgroundColor', TextType::class, [
                'label' => 'Background',
                'empty_data' => '',
            ])
            ->add('foregroundColor', TextType::class, [
                'label' => 'Foreground',
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
