<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Repository\PostsRepository;
use App\Repository\TagsRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller
 * @Route(name="app_home_")
 */
class HomeController extends AbstractController
{
    /**
     * @var PaginatorInterface
     */
    private PaginatorInterface $paginator;
    /**
     * @var TagsRepository
     */
    private TagsRepository $tagsRepository;
    /**
     * @var PostsRepository
     */
    private PostsRepository $postsRepository;

    /**
     * HomeController constructor.
     * @param PaginatorInterface $paginator
     * @param TagsRepository $tagsRepository
     * @param PostsRepository $postsRepository
     */
    public function __construct(
        PaginatorInterface $paginator,
        TagsRepository $tagsRepository,
        PostsRepository $postsRepository
    ) {
        $this->paginator = $paginator;
        $this->tagsRepository = $tagsRepository;
        $this->postsRepository = $postsRepository;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'posts' => $this->postsRepository->findAllLatest(null, 3),
        ]);
    }

}
