<?php

namespace App\Form;

use App\Entity\Transfer;
use App\Form\DataTransformer\CurrencyTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransferType extends AbstractType
{
    private $transformer;

    public function __construct(CurrencyTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('info', TextType::class, [
                'attr' => [
                    'placeholder' => 'Usage',
                    'class' => 'form-control-sm',
                ],
                'label_attr' => [
                    'class' => 'sr-only',
                ],
            ])
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Recipient ',
                    'class' => 'form-control-sm',
                ],
                'label_attr' => [
                    'class' => 'sr-only',
                ],
            ])
            ->add('iban', TextType::class, [
                'attr' => [
                    'placeholder' => 'IBAN',
                    'class' => 'form-control-sm',
                ],
                'label_attr' => [
                    'class' => 'sr-only',
                ],
            ])
            ->add('amount', MoneyType::class, [
                'attr' => [
                    'class' => 'form-control-sm',
                ],
                'currency' => false,
                'grouping' => true,
                'label_attr' => [
                    'class' => 'sr-only',
                ],
            ])
            ->add('bic', TextType::class, [
                'attr' => [
                    'class' => 'form-control-sm',
                    'readonly' => true,
                ],
                'label_attr' => [
                    'class' => 'sr-only',
                ],
            ])
            ->add('bankName', TextType::class, [
                'attr' => [
                    'class' => 'form-control-sm',
                    'readonly' => true,
                ],
                'label_attr' => [
                    'class' => 'sr-only',
                ],
            ]);

        $builder->get('amount')
            ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transfer::class,
        ]);
    }
}
