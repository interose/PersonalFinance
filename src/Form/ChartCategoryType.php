<?php

namespace App\Form;

use App\Entity\CategoryGroup;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class ChartCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => CategoryGroup::class,
                'choice_label' => 'name',
                'multiple' => false,
                'required' => true,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('g')->orderBy('g.name', 'ASC');
                },
                'placeholder' => 'Choose category group',
            ])
            ->add('drilldown', CheckboxType::class, [
                'label' => 'show categories',
            ])
        ;
    }
}
