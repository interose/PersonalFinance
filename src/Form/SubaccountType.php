<?php

namespace App\Form;

use App\Entity\SubAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubaccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('accountNumber', TextType::class, [
                'attr' => ['readonly' => true],
            ])
            ->add('iban', TextType::class, [
                'attr' => ['readonly' => true],
            ])
            ->add('bic', TextType::class, [
                'attr' => ['readonly' => true],
            ])
            ->add('blz', TextType::class, [
                'attr' => ['readonly' => true],
            ])

            ->add('isEnabled')
            ->add('description')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SubAccount::class,
        ]);
    }
}
