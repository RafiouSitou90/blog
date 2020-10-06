<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Repository\PostsRepository;
use App\Repository\TagsRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BlogController
 * @package App\Controller
 * @Route("/blog", name="app_blog_")
 */
class BlogController extends AbstractController
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
     * @Route("", name="index", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $tag = null;
        if ($request->query->has('tag')) {
            $tag = $this->tagsRepository->findOneBy(['name' => $request->query->get('tag')]);
        }

        $allPublished = $this->paginator->paginate(
            $this->postsRepository->findAllLatest($tag),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('blog/index.html.twig', [
            'allPublished' => $allPublished,
        ]);
    }

    /**
     * @Route("/{slug}", name="show", methods={"GET"})
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

    /**
     * @Route("/search", name="search", methods={"POST"})
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function handleSearch(Request $request)
    {
        $words = $request->get('post_search');

        if ($words == null || $words == "") {
            return $this->redirectToRoute($request->get('_route'));
        } else {
            return $this->render('blog/search/index.html.twig', [
                'words' => $words,
                'results' => $this->postsRepository->search($words)
            ]);
        }
    }
}
