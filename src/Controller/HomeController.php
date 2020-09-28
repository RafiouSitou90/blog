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
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $tag = null;
        if ($request->query->has('tag')) {
            $tag = $this->tagsRepository->findOneBy(['name' => $request->query->get('tag')]);
        }

        $latestPosts = $this->paginator->paginate(
            $this->postsRepository->findAllLatest($tag),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('home/index.html.twig', [
            'posts' => $latestPosts,
        ]);
    }

    /**
     * @Route("/show/{slug}", name="show", methods={"GET"})
     *
     * @param string $slug
     * @return Response
     * @throws NonUniqueResultException
     */
    public function show(string $slug): Response
    {
        /** @var Posts|null $post */
        $post = $this->postsRepository->findOneBySlug($slug);

        if (!$post) {
            throw new NotFoundHttpException('Post not found');
        }

        return $this->render('blog/show/index.html.twig', [
            'post' => $post,
        ]);
    }
}
