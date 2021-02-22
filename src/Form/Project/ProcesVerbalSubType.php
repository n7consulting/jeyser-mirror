<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Project;

use App\Entity\Project\Etude;
use App\Entity\Project\Phase;
use App\Entity\Project\ProcesVerbal;
use App\Repository\Project\PhaseRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProcesVerbalSubType extends DocTypeType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            /** @var ProcesVerbal $proces */
            $proces = $event->getData();
            $form = $event->getForm();

            $form->add('phases', EntityType::class, [
                'class' => Phase::class,
                'query_builder' => function (PhaseRepository $pr) use ($options, $proces) {
                    if (isset($proces)) {
                        return $pr->getByEtudeAndPv($options['etude'], $proces);
                    }

                    return $pr->getByEtudeAndPv($options['etude']);
                },
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
                'attr' => ['class' => 'select2-multiple'],
            ]);
        });

        DocTypeType::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'project_procesverbalsubtype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProcesVerbal::class,
            'type' => null,
            'prospect' => null,
            'phases' => null,
        ]);
        $resolver->setRequired(['etude']);
        $resolver->addAllowedTypes('etude', Etude::class);
    }
}
