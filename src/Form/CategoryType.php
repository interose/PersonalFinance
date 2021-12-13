<?php

namespace App\Form;

use App\Entity\Category;

use App\Entity\CategoryGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
            ])
            ->add('treeIgnore', CheckboxType::class, [
                'label' => 'Tree Ignore',
                'required' => false,
            ])
            ->add('dashboardIgnore', CheckboxType::class, [
                'label' => 'Dashboard Ignore',
                'required' => false,
            ])
            ->add('categoryGroup', EntityType::class, [
                'label' => 'Group',
                'class' => CategoryGroup::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Please select',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
