<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\CategoryAssignmentRule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CategoryAssignmentRuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rule', TextType::class, [
                'label' => 'Rule',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Ruletype',
                'choices' => CategoryAssignmentRule::AVAILABLE_TYPE_CHOICES,
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Please select',
                'required' => true,
            ])
            ->add('transactionField', ChoiceType::class, [
                'label' => 'Transactionfield',
                'choices' => CategoryAssignmentRule::AVAILABLE_TRANSACTION_FIELD_CHOICE,
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Please select',
                'required' => true,
            ])
            ->add('category', EntityType::class, [
                'label' => 'Category',
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Please select',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CategoryAssignmentRule::class,
        ]);
    }
}
