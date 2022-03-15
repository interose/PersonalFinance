<?php

namespace App\Form;

use App\Entity\CategoryGroup;
use App\Entity\Transaction;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class ChartCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => CategoryGroup::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => true,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('g')->orderBy('g.name', 'ASC');
                },
                'placeholder' => 'Choose category group',
            ])
            ->add('grouping', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'yearly' => Transaction::GROUPING_YEARLY,
                    'monthly' => Transaction::GROUPING_MONTHLY,
                ],
                'empty_data' => Transaction::GROUPING_YEARLY,
            ])
        ;
    }
}
