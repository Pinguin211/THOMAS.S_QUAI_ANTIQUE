<?php

namespace App\Form;

use App\Entity\Timetable\Day;
use App\Entity\Timetable\Moment;
use App\Entity\Timetable\Session;
use App\Entity\Timetable\Timetable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimetableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $timetable = $builder->getData();
        $days = Timetable::ARR_KEY_DAYS;
        $sessions = Day::ARR_KEY;
        $open_close = Session::ARR_KEY;
        $moments = ['hours', 'min'];

        foreach ($days as $day)
        {
            $time_day = $timetable ? $timetable->getDay($day) : false;
            foreach ($sessions as $session)
            {
                $time_session = $time_day ? $time_day->getSessionByName($session) : false;
                foreach ($open_close as $opcl)
                {
                    $time_opcl = $time_session ? $time_session->getOpenClosedByName($opcl) : false;
                    foreach ($moments as $moment)
                    {
                        $attr = ['required' => false, 'mapped' => false];
                        if ($time_opcl)
                        {
                            if ($moment === 'min')
                                $attr = array_merge($attr, ['attr' => ['value' => $time_opcl->getMin()]]);
                            else if ($moment === 'hours')
                                $attr = array_merge($attr, ['attr' => ['value' => $time_opcl->getHours()]]);
                        }
                        $builder->add("$day:$session:$opcl:$moment", IntegerType::class,$attr);
                    }
                }
                $attr = ['required' => false, 'mapped' => false];
                if (!$time_session)
                    $attr = array_merge($attr, ['attr' => ['checked' => true]]);
                $builder->add("$day:$session", CheckboxType::class, $attr);
            }
        }
        $builder
            ->add('submit', SubmitType::class)
            ->addEventListener(FormEvents::SUBMIT,
                function (FormEvent $event)
                {
                    $event->getData()->setDays(self::getTimetableByForm($event->getForm()));
                });
    }

    private static function getTimetableByForm(FormInterface $form): array
    {
        $timetable = [];
        foreach (Timetable::ARR_KEY_DAYS as $day)
            $timetable[] = self::getDayByForm($form, $day);
        return $timetable;
    }

    private static function getDayByForm(FormInterface $form, string $day): Day
    {
        $arr = [];
        foreach (Day::ARR_KEY as $key)
        {
            if ($form->get("$day:$key")->getData())
                $arr[$key] = null;
            else
                $arr[$key] = self::getSessionByForm($form, "$day:$key");
        }
        return new Day($day, $arr[Day::KEY_DAY], $arr[Day::KEY_NIGHT]);
    }

    private static function getSessionByForm(FormInterface $form, string $session): Session
    {
        $arr = [];
        foreach (Session::ARR_KEY as $key)
        {
            if (!($min = $form->get("$session:$key:min")->getData()))
                $min = 0;
            if (!($hours = $form->get("$session:$key:hours")->getData()))
                $hours = 0;
            $arr[$key] = new Moment($hours, $min);
        }
        return new Session($arr[Session::KEY_OPEN], $arr[Session::KEY_CLOSED]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Timetable::class,
        ]);
    }
}
