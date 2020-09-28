<?php

namespace App\Controller;

use App\Entity\PostMedias;
use App\Entity\Posts;
use App\Entity\Users;
use App\Form\PostsChangeStatusFormType;
use App\Form\PostsFormType;
use App\Repository\PostsRepository;
use App\Repository\TagsRepository;
use App\Security\Voter\PostsVoter;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostsController
 * @package App\Controller
 * @Route("/posts", name="app_posts_")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class PostsController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var TagsRepository
     */
    private TagsRepository $tagsRepository;
    /**
     * @var PostsRepository
     */
    private PostsRepository $postsRepository;
    /**
     * @var PaginatorInterface
     */
    private PaginatorInterface $paginator;

    /**
     * PostsController constructor.
     * @param EntityManagerInterface $entityManager
     * @param TagsRepository $tagsRepository
     * @param PostsRepository $postsRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TagsRepository $tagsRepository,
        PostsRepository $postsRepository,
        PaginatorInterface $paginator
    ) {
        $this->entityManager = $entityManager;
        $this->tagsRepository = $tagsRepository;
        $this->postsRepository = $postsRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        /** @var Users $author */
        $author = $this->getUser();

        $tag = null;
        if ($request->query->has('tag')) {
            $tag = $this->tagsRepository->findOneBy(['name' => $request->query->get('tag')]);
        }

        $latestPosts = $this->paginator->paginate(
            $this->postsRepository->findAllLatestForAuthor($author, $tag),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('posts/index.html.twig', [
            'posts' => $latestPosts,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function new(Request $request)
    {
        $post = new Posts();

        /** @var Users $author */
        $author = $this->getUser();

        $post->setAuthor($author);

        $form = $this->createForm(PostsFormType::class, $post)
            ->add('saveAndCreateNew', SubmitType::class, [
                'label' => 'Save and create new'
            ])
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            if ($images) {
                foreach ($images as $image) {
                    $postMedia = (new PostMedias())->setMediaFile($image);

                    $post->addMedia($postMedia);
                }
            }

            $this->setPostsPublishedAt($post);
            $this->entityManager->persist($post);
            $this->entityManager->flush();

            $this->addFlash('success', 'New post created successfully');

            /** @var ClickableInterface $saveAndCreateNewButton */
            $saveAndCreateNewButton = $form->get('saveAndCreateNew');

            if ($saveAndCreateNewButton->isClicked()) {
                return $this->redirectToRoute('app_posts_new');
            }

            return $this->redirectToRoute('app_posts_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('posts/new/index.html.twig', [
            'post' => $post,
            'form' => $form->createView()
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
        /** @var Users $author */
        $author = $this->getUser();

        /** @var Posts|null $post */
        $post = $this->postsRepository->findOneBySlugForAuthor($slug, $author);

        if (!$post) {
            throw new NotFoundHttpException('Post not found');
        }

        $this->denyAccessUnlessGranted(
            PostsVoter::SHOW,
            $post,
            'Access denied! Sorry you cannot show this post'
        );

        $updateStatusForm = $this->createForm(PostsChangeStatusFormType::class, $post, ['method' => 'PUT']);

        return $this->render('posts/show/index.html.twig', [
            'post' => $post,
            'updateStatusForm' => $updateStatusForm->createView()
        ]);
    }

    /**
     * @Route("/edit/{slug}", name="edit", methods={"GET", "PUT"})
     *
     * @param Request $request
     * @param Posts $post
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function edit(Request $request, Posts $post)
    {
        /** @var Users $author */
        $author = $this->getUser();

        /** @var Posts|null $post */
        $post = $this->postsRepository->findOneBySlugForAuthor($post->getSlug(), $author);

        if (!$post) {
            throw new NotFoundHttpException('Post not found');
        }

        $this->denyAccessUnlessGranted(
            PostsVoter::EDIT,
            $post,
            'Access denied! Sorry you cannot show this post'
        );

        $form = $this->createForm(PostsFormType::class, $post, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            if ($images) {
                foreach ($post->getMedias() as $media) {
                    $post->removeMedia($media);
                }

                foreach ($images as $image) {
                    $postMedia = (new PostMedias())->setMediaFile($image);

                    $post->addMedia($postMedia);
                }
            }

            $this->setPostsPublishedAt($post);
            $this->entityManager->flush();

            $this->addFlash('success', 'Post updated successfully');

            return $this->redirectToRoute('app_posts_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('posts/edit/index.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/change-status/{slug}", name="change_status", methods={"PUT"})
     *
     * @param Request $request
     * @param Posts $post
     * @return RedirectResponse
     * @throws NonUniqueResultException
     */
    public function changeStatus(Request $request, Posts $post): RedirectResponse
    {
        /** @var Users $author */
        $author = $this->getUser();

        /** @var Posts|null $post */
        $post = $this->postsRepository->findOneBySlugForAuthor($post->getSlug(), $author);

        if (!$post) {
            throw new NotFoundHttpException('Post not found');
        }

        $this->denyAccessUnlessGranted(
            PostsVoter::DELETE,
            $post,
            'Access denied! Sorry you cannot show this post'
        );

        $form = $this->createForm(PostsChangeStatusFormType::class, $post, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setPostsPublishedAt($post);
            $this->entityManager->flush();

            $status = $this->getPostStatusAfterUpdate($post);

            $this->addFlash('success', "Post $status successfully");

            return $this->redirectToRoute('app_posts_show', ['slug' => $post->getSlug()]);
        }

        $this->addFlash('error', 'An error occurred during the status changing, please try again');

        return $this->redirectToRoute('app_posts_show', ['slug' => $post->getSlug()]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param Posts $post
     * @return RedirectResponse
     */
    public function delete(Request $request, Posts $post): RedirectResponse
    {
        $this->denyAccessUnlessGranted(
            PostsVoter::SHOW,
            $post,
            'Access denied! Sorry you cannot show this post'
        );

        if ($this->isCsrfTokenValid('delete_post_' . $post->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($post);
            $this->entityManager->flush();
            $this->addFlash('success', 'Post deleted successfully');

            return $this->redirectToRoute('app_posts_index');
        }

        $this->addFlash('error', 'An error occurred during the deletion, please try again');

        return $this->redirectToRoute('app_posts_show', ['slug' => $post->getSlug()]);
    }

    /**
     * @param Posts $post
     *
     * @return void
     */
    private function setPostsPublishedAt(Posts $post): void
    {
        if ($post->getState() !== Posts::getPublished()) {
            $post->setPublishedAt(null);
        } else {
            if ($post->getPublishedAt() === null) {
                $post->setPublishedAt(new DateTime());
            }
        }
    }

    /**
     * @param Posts $post
     * @return string|null
     */
    private function getPostStatusAfterUpdate(Posts $post): ?string
    {
        $state = null;
        switch ($post->getState()) {
            case Posts::getDraft():
                $state = Posts::getDraft();
                break;
            case Posts::getArchived():
                $state = Posts::getArchived();
                break;
            case Posts::getPublished():
                $state = Posts::getPublished();
                break;
        }

        return $state;
    }
}
