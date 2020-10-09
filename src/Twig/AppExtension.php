<?php

namespace App\Twig;

use App\Form\SearchFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class AppExtension
 * @package App\Twig
 */
class AppExtension extends AbstractExtension
{
    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $factory;
    /**
     * @var Environment
     */
    private Environment $twig;

    /**
     * AppExtension constructor.
     * @param FormFactoryInterface $factory
     * @param Environment $twig
     */
    public function __construct(FormFactoryInterface $factory, Environment $twig)
    {
        $this->factory = $factory;
        $this->twig = $twig;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('search_form', [$this, 'getSearchForm']),
        ];
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getSearchForm()
    {
        $form = $this->factory->create(SearchFormType::class);

        return $this->twig->render('navigations/header/_search_form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
