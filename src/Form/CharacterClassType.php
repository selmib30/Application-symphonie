<?php

namespace App\Form;

use App\Entity\CharacterClass;
use App\Entity\Skill;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CharacterClassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('healthDice')
            ->add('skills', EntityType::class, [
                'class' => Skill::class,
                'choice_label' => 'name', // On affiche le NOM de la compétence
                'multiple' => true,
                'expanded' => true,      // 'true' transforme la liste en cases à cocher (plus ergonomique)
                'label' => 'Compétences de classe',
                'by_reference' => false, // Important pour que Symfony appelle addSkill/removeSkill
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CharacterClass::class,
        ]);
    }
}
