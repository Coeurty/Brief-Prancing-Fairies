<?php

namespace App\Form;

use App\Entity\Topic;
use App\Entity\TopicCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('category', EntityType::class, [
                'class' => TopicCategory::class,
                'choice_label' => 'name'
            ])
            ->add('strandFirst', TextareaType::class)
        ;

        if ($options['is_new_topic']) {
            $builder->add('message', TextareaType::class, [
                'required' => true,
                'mapped' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Topic::class,
            'is_new_topic' => false,
        ]);
    }
}
