<?php

namespace App\Form;

use App\Entity\User;
use App\Service\IngredientsInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignupType extends AbstractType
{
    public function __construct(private IngredientsInterface $ingredients)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('cutlerys', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    "1 couvert" => 1,
                    "2 couverts" => 2,
                    "3 couverts" => 3,
                    "4 couverts" => 4,
                    "5 couverts" => 5,
                    "6 couverts" => 6,
                    "7 couverts" => 7,
                    "8 couverts" => 8,
                    "9 couverts" => 9,
                ]
            ])
            ->add('ingredients_select', ChoiceType::class,
                [
                    'choices' => $this->getIngredientArrayChoice(),
                    'expanded' => true,
                    'multiple' => true,
                    'mapped' => false,
                    'required' => false,
                ])
            ->add('cgu', CheckboxType::class, ['mapped' => false])
            ->add('submit', SubmitType::class)
        ;
    }

    private function getIngredientArrayChoice(): array
    {
        $arr = [];
        foreach ($this->ingredients->getAllIngredientsOrderById() as $ingredient)
            $arr[$ingredient->getName() . ' ✕'] = $ingredient->getId();
        return $arr;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
