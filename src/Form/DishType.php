<?php

namespace App\Form;

use App\Entity\Dish;
use App\Service\IngredientsInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;

class DishType extends AbstractType
{
    public function __construct(private IngredientsInterface $ingredients)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $price_constraint = [
            new Type([
                'type' => 'integer',
                'message' => 'Le champ doit être un entier',
            ]),
            new Range([
                'min' => 0,
                'max' => 999999,
                'minMessage' => "Lep prix doit être supérieure ou égale à {{ limit }}, on ne donne pas d'argent au client ;)" ,
                'maxMessage' => "La prix doit être inférieure à 1 000 000 €, Abuse pas quand meme c'est qu'un repas ! :o Défi: Tu peux réussir à donner un prix au dessus d'un 1 000 000 € ;)",
            ]),
        ];

        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('euro', IntegerType::class, ['required' => true, 'mapped' => false, 'constraints' => $price_constraint])
            ->add('centime', IntegerType::class, ['required' => false, 'mapped' => false, 'constraints' => $price_constraint])
            ->add('archived', CheckboxType::class, ['required' => false])
            ->add('type', ChoiceType::class, [
                    'choices' => self::getDishTypeArrayChoices(),
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
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event)
            {
                $data = $event->getData();
                $form = $event->getForm();
                $ingredients_list = $this->ingredients->getSelectedIngredients($form->get('ingredients')->getData());
                $data->setIngredientsByArray($ingredients_list);
                $data->setPrice(($form->get('euro')->getData() * 100) + $form->get('centime')->getData());
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event)
            {
                $data = $event->getData();
                $form = $event->getForm();
                if ($data->getPrice())
                {
                    $euro = intdiv($data->getPrice(), 100);
                    $centime = $data->getPrice() % 100;
                    $form->get('euro')->setData($euro);
                    $form->get('centime')->setData($centime);
                }
                if (!empty($data->getIngredients()))
                {
                    $arr = [];
                    foreach ($data->getIngredients() as $ingredient)
                        $arr[] = $ingredient->getId();
                    $form->get('ingredients')->setData($arr);
                }

            })
        ;
    }

    private static function getDishTypeArrayChoices(): array
    {
        $arr = [];
        foreach (Dish::ARRAY_TYPE as $type)
            $arr[Dish::getDishTypeNameById($type)] = $type;
        return $arr;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dish::class,
        ]);
    }
}
