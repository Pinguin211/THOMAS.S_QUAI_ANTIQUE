<?php

namespace App\Form;

use App\Entity\Booker;
use App\Entity\Client;
use App\Entity\Reservation;
use App\Service\IngredientsInterface;
use App\Service\ReservationInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ReservationType extends AbstractType
{
    public function __construct(private IngredientsInterface $ingredients,
                                private ReservationInterface $reservation,
                                private EntityManagerInterface $entityManager)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([], 'Veuillez inscrire un nom pour la reservation'),
                    new Length(['max' => 32, 'maxMessage' => 'Le nom ne doit pas dépasser 32 caractères'])
                ]
            ])
            ->add('cutlerys', ChoiceType::class, [
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
            ->add('date', DateType::class, [
                'mapped' => false,
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                ]
            ])
            ->add('stage_hour', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('ingredients', ChoiceType::class,
                [
                    'choices' => $this->ingredients->getIngredientArrayChoice(),
                    'expanded' => true,
                    'multiple' => true,
                    'mapped' => false,
                    'required' => false,
                ])
            ->add('allergy', CheckboxType::class, ['required' => false])
            ->add('submit', SubmitType::class)
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event)
            {
                $data = $event->getData();
                $form = $event->getForm();
                if (($booker = $data->getBooker()))
                {
                    $arr = [];
                    foreach ($booker->getAllergys() as $ingredient)
                        $arr[] = $ingredient->getId();
                    $form->get('ingredients')->setData($arr);
                }

            })
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event)
            {
                $data = $event->getData();
                $form = $event->getForm();

                if (($error = self::isValideDate($form->get('date')->getData())) !== true)
                    return $form->get('date')->addError($error);
                $date = $form->get('date')->getData();

                if (!($stage = self::isValideStage($form->get('stage_hour')->getData(), $date, $this->reservation)))
                    return $form->get('date')->addError(new FormError("L'horaire choisie n'est pas valide ou plus disponible"));



                if ($form->isValid())
                {
                    $service = $this->reservation->getService($stage[0], $date, true, false);
                    if (!$service)
                        return $form->get('date')->addError(new FormError("L'horaire choisie n'est pas valide ou plus disponible"));

                    if ($service->isComplet())
                        return $form->get('date')->addError(new FormError("Il n'y a plus de place disponible pour se service"));

                    $arr_ids = $form->get('ingredients')->getData();
                    if (!empty($arr_ids) && is_array($arr_ids))
                        $ingredients = $this->ingredients->getSelectedIngredients($arr_ids);
                    else
                        $ingredients = [];

                    if (!($booker = $data->getBooker()))
                        $booker = new Booker();
                    $booker->setName($form->get('name')->getData());
                    if ($data->isAllergy())
                        $booker->setAllergysFromArray($ingredients);
                    $booker->setName($form->get('name')->getData());

                    if (($client = $this->entityManager->getRepository(Client::class)->findOneBy(['booker' => $booker->getId()])))
                        $client->setCutlerys($data->getCutlerys());

                    $this->entityManager->persist($service);
                    $this->entityManager->persist($booker);
                    $this->entityManager->flush();

                    $data->setStageHour($stage[1]);
                    $data->setBooker($booker);
                    $data->setService($service);
                }
            })
           ;
    }

   private static function isValideDate(DateTime $dateTime): bool | FormError
   {
       $start = new DateTime();
       $start->setTime(0,0);
       $end = new DateTime('+2 month');
       $end->setTime(0,0);
       if ($dateTime < $start)
           return new FormError('La reservations ne peut pas être faite dans le passé');
       if ($dateTime > $end)
           return new FormError('Vous ne pouvez pas reservé aussi loin (max 2 mois)');
       return true;
   }

   private static function isValideStage(mixed $stage, DateTime $date, ReservationInterface $reservation): bool | array
   {
       if (!is_string($stage))
           return false;
       $arr = explode('.', $stage);
       if (count($arr) !== 2 || !$reservation->isValidStage($date, (int)$arr[0], (int)$arr[1]))
           return false;
       else
           return [(int)$arr[0], (int)$arr[1]];
   }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
