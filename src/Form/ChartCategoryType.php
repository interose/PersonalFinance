<?php

namespace App\Form;

use App\Entity\Transaction;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChartCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var CategoryRepository $repository */
        $repository = $options['categoryRepository'];

        $choices = [];
        foreach ($repository->getAllWithGroup() as $category) {
            $groupName = $category['groupName'] ?? '';

            if (strlen($groupName) > 0) {
                $choices[$groupName][$category['name']] = $category['id'];
            } else {
                $choices['Not grouped'][$category['name']] = $category['id'];
            }
        }

        $builder
            ->add('category', ChoiceType::class, [
                'multiple' => true,
                'required' => true,
                'choices' => $choices,
                'placeholder' => 'Choose category',
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'categoryRepository' => null
        ]);
    }
}
