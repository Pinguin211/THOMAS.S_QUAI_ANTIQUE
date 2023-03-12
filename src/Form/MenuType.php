<?php

namespace App\Form;

use App\Entity\Dish;
use App\Entity\Menu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('archived', CheckboxType::class, ['required' => false])
            ->add('dishes', ChoiceType::class,
                [
                    'choices' => $this->getArrayMenuChoice(),
                    'expanded' => true,
                    'multiple' => true,
                    'mapped' => false,
                    'required' => false,
                ])
            ->add('submit', SubmitType::class)
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event)
            {
                $event->getData()->setCollectionDishFromArray($this->getFormulesFromIds($event->getForm()->get('dishes')->getData()));
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event)
            {
                if (!empty($event->getData()->getDishes()))
                {
                    $arr = [];
                    foreach ($event->getData()->getDishes() as $dish)
                        $arr[] = $dish->getId();
                    $event->getForm()->get('dishes')->setData($arr);
                }

            })
        ;
    }

    private function getArrayMenuChoice(): array
    {
        $arr = [];
        $formules = $this->entityManager->getRepository(Dish::class)->findBy(['type' => Dish::TYPE_FORMULES]);
        foreach ($formules as $formule)
            $arr[$formule->getFormuleTitle()] = $formule->getId();
        return $arr;
    }

    private function getFormulesFromIds(array $ids): array
    {
        $arr = [];
        foreach ($ids as $id)
        {
            $formule = $this->entityManager->getRepository(Dish::class)->findOneBy(['id'=>$id]);
            if ($formule)
                $arr[] = $formule;
        }
        return $arr;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
