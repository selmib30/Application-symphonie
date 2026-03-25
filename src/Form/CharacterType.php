<?php

namespace App\Form;

use App\Entity\Character;
use App\Entity\CharacterClass;
use App\Entity\Race;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class CharacterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du personnage',
                'constraints' => [new NotBlank()],
            ])
            ->add('race', EntityType::class, [
                'class' => Race::class,
                'choice_label' => 'name',
                'label' => 'Race',
            ])
            ->add('characterClass', EntityType::class, [
                'class' => CharacterClass::class,
                'choice_label' => 'name',
                'label' => 'Classe',
            ])
            ->add('image', TextType::class, [
                'label' => 'URL de l\'avatar',
                'required' => false,
                'attr' => ['placeholder' => 'https://...']
            ])
            ->add('strength', IntegerType::class, [
                'label' => 'Force',
                'attr' => ['min' => 8, 'max' => 15],
                'constraints' => [new Range(min: 8, max: 15)],
            ])
            ->add('dexterity', IntegerType::class, [
                'label' => 'Dextérité',
                'attr' => ['min' => 8, 'max' => 15],
                'constraints' => [new Range(min: 8, max: 15)],
            ])
            ->add('constitution', IntegerType::class, [
                'label' => 'Constitution',
                'attr' => ['min' => 8, 'max' => 15],
                'constraints' => [new Range(min: 8, max: 15)],
            ])
            ->add('intelligence', IntegerType::class, [
                'label' => 'Intelligence',
                'attr' => ['min' => 8, 'max' => 15],
                'constraints' => [new Range(min: 8, max: 15)],
            ])
            ->add('wisdom', IntegerType::class, [
                'label' => 'Sagesse',
                'attr' => ['min' => 8, 'max' => 15],
                'constraints' => [new Range(min: 8, max: 15)],
            ])
            ->add('charisma', IntegerType::class, [
                'label' => 'Charisme',
                'attr' => ['min' => 8, 'max' => 15],
                'constraints' => [new Range(min: 8, max: 15)],
            ])
            ->add('level', IntegerType::class, [
                'label' => 'Niveau',
                'attr' => ['min' => 1, 'max' => 20],
                'data' => 1,
            ])
            ->add('healthPoints', IntegerType::class, [
                'label' => 'Points de Vie',
               'constraints' => [new NotBlank()],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Character::class,
        ]);
    }
}
