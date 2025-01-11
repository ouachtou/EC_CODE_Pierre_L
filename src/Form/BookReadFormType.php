<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\BookRead;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookReadFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class, ['required' => false])
            ->add('book_id', EntityType::class, [
                'required' => true,
                'class' => Book::class,
                'choice_label' => 'name'
            ])
            ->add('rating', ChoiceType::class, ['required' => true, 'choices' => [
                '1' => 1,
                '1,5' => 1.5,
                '2' => 2,
                '2,5' => 2.5,
                '3' => 3,
                '3,5' => 3.5,
                '4' => 4,
                '4,5' => 4.5,
                '5' => 5,
            ]])
            ->add('description', TextType::class)
            ->add('is_read', CheckboxType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookRead::class,
        ]);
    }
}
