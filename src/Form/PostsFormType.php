<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Posts;
use App\Form\Type\TagsInputType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PostsFormType
 * @package App\Form
 */
class PostsFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [])
            ->add('summary', TextType::class, [])
            ->add('content', TextareaType::class, [])
            ->add('state', ChoiceType::class, [
                'choices' => [
                    strtoupper(Posts::getPublished()) => Posts::getPublished(),
                    strtoupper(Posts::getDraft()) => Posts::getDraft(),
                ],
            ])
            ->add('publishedAt', DateTimeType::class, [
                'required' => false,
            ])
            ->add('commentState', ChoiceType::class, [
                'choices' => [
                    strtoupper(Posts::getCommentOpened()) => Posts::getCommentOpened(),
                    strtoupper(Posts::getCommentClosed()) => Posts::getCommentClosed()
                ],
            ])
            ->add('category', EntityType::class, [
                'placeholder' => 'Select the post category',
                'label' => 'Category',
                'required' => false,
                'class' => Categories::class,
                'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('c')->orderBy('c.name', 'ASC'),
                'choice_label' => 'name',
                'choice_value' => 'id',
            ])
            ->add('images', FileType::class, [
                'mapped' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control',
                    'is' => 'drop-files',
                    'label' => 'Drop files here or click to upload.',
                    "help" => "Upload files here and they won't be sent immediately"
                ],
                'required' => false
            ])
            ->add('tags', TagsInputType::class, [
                'label' => 'Tags',
                'required' => false,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Posts::class,
        ]);
    }
}
