<?php

namespace App\Form;

use App\Entity\Posts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PostsChangeStatusFormType
 * @package App\Form
 */
class PostsChangeStatusFormType extends AbstractType
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
            ->add('state', ChoiceType::class, [
                'choices' => [
                    strtoupper(Posts::getDraft()) => Posts::getDraft(),
                    strtoupper(Posts::getPublished()) => Posts::getPublished(),
                    strtoupper(Posts::getArchived()) => Posts::getArchived()
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
