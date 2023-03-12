<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Service\IngredientsInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngredientType extends AbstractType
{

    public function __construct(private IngredientsInterface $ingredients)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['required' => false])
            ->add('type', ChoiceType::class, [
                'choices' => self::getIngredientsTypeArrayChoices(),
                'expanded' => false,
                'multiple' => false,
                'mapped' => true,
                'required' => true,
            ])
            ->add('ingredients', ChoiceType::class,[
                'choices' => $this->ingredients->getIngredientArrayChoice(),
                'expanded' => true,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    private static function getIngredientsTypeArrayChoices(): array
    {
        $arr = [];
        $arr['Sélectionner un type'] = -1;
        $i = 0;
        foreach (Ingredient::TYPE_NAMES as $type)
        {
            $arr[$type] = $i;
            $i++;
        }
        return $arr;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }
}
