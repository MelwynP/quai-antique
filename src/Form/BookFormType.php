<?php

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class BookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {



        $builder

            ->add('dateReservation', DateType::class, [
                'label' => 'Date',
                'placeholder' => 'Sélectionnez une date',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'min' => (new \DateTime())->format('Y-m-d'), // ajoute l'attribut min avec la date actuelle

                ],
                'constraints' => [

                    new Callback([
                        'callback' => function ($value, ExecutionContextInterface $context) {
                            $dayOfWeek = $value->format('N'); // Récupère le jour de la semaine (1 pour lundi, 7 pour dimanche)
                            if ($dayOfWeek >= 1 && $dayOfWeek <= 3) { // Vérifie si c'est un jour de fermeture
                                $context->buildViolation('Nous sommes en repos les lundis, mardis et mercredis. Merci de choisir une autre date.')
                                    ->addViolation();
                            }
                        },
                    ]),
                ],
            ])

            ->add('hour', ChoiceType::class, [
                'label' => 'Heure',
                'placeholder' => 'Sélectionnez une heure',
                'choices' => $this->getTimeSlotsWithLabels(),
                'group_by' => function ($value, $key, $index) {
                    if (strpos($value, 'Déjeuner') !== false) {
                        return 'Déjeuner';
                    }
                    return 'Dîner';
                }
            ])
            /*->add('service', ChoiceType::class, [
                'label' => 'Service',
                'choices' => [
                    'Déjeuner' => 'dejeuner',
                    'Dîner' => 'diner',
                ],
                'attr' => [
                    'placeholder' => 'Choisissez un service',
                    'class' => 'form-control',
                    'id' => 'service',
                ],
            ])*/



            ->add('numberPeople', IntegerType::class, [
                'label' => 'Nombre de convives',
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => 8,
                        'message' => 'Au delà de 8 convives, merci de nous appeler.',
                    ]),
                ],
            ])



            ->add('allergy', null, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Allergie(s)'
            ])

            ->add('civility', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Civilité',
                'choices' => [
                    'Monsieur' => 'Monsieur',
                    'Madame' => 'Madame',
                ]
            ])

            ->add('firstname', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Prénom'
            ])

            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nom'
            ])

            ->add('phone', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Téléphone'
            ])

            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'E-mail'
            ])

            ->add('RGPDConsent', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
                'label' => 'J\'accepte que mes données personnelles soient utilisées pour la gestion de ma réservation et de ma relation commerciale avec l\'établissement.'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }



    private function getTimeSlotsWithLabels()
    {
        $timeSlots = [];
        $lunchStart = new \DateTime('12:00');
        $dinnerStart = new \DateTime('19:00');
        $interval = new \DateInterval('PT15M'); // 15 minutes interval
        $lunchEnd = (clone $lunchStart)->add(new \DateInterval('PT1H'));
        $dinnerEnd = (clone $dinnerStart)->add(new \DateInterval('PT1H'));
        while ($lunchStart < $lunchEnd) {
            $timeSlots[$lunchStart->format('H:i')] = 'Déjeuner - ' . $lunchStart->format('H:i');
            $lunchStart->add($interval);
        }
        while ($dinnerStart < $dinnerEnd) {
            $timeSlots[$dinnerStart->format('H:i')] = 'Dîner - ' . $dinnerStart->format('H:i');
            $dinnerStart->add($interval);
        }
        return $timeSlots;
    }
}
