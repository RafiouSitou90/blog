<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\PostsComments;
use App\Entity\Users;
use App\Form\PostsCommentsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostsCommentsController
 * @package App\Controller
 * @Route("/comments", name="app_comments_")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class PostsCommentsController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * PostsCommentsController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{postSlug}/new", name="new", methods={"GET", "POST"})
     * @ParamConverter("post", options={"mapping": {"postSlug": "slug"}})
     * @param Request $request
     * @param Posts $post
     * @return RedirectResponse|Response
     */
    public function createComment(Request $request, Posts $post)
    {
        /** @var Users $author */
        $author = $this->getUser();

        $comment = (new PostsComments())->setAuthor($author);
        $post->addComment($comment);

        $form = $this->createForm(PostsCommentsFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setState('published');
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('blog/comments/_form_error.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/replies/{id}/new", name="replies_new", methods={"GET", "POST"})
     * @ParamConverter("comment", options={"mapping": {"id": "id"}})
     * @param Request $request
     * @param PostsComments $comment
     * @return RedirectResponse|Response
     */
    public function createReply(Request $request, PostsComments $comment)
    {
        /** @var Users $author */
        $author = $this->getUser();

        /** @var Posts $post */
        $post = $comment->getPost();
        $reply = (new PostsComments())->setAuthor($author)->setParentComment($comment)->setPost($post);

        $form = $this->createForm(PostsCommentsFormType::class, $reply);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reply->setState('published');
            $this->entityManager->persist($reply);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('blog/comments/_form_error.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Posts $post
     * @return Response
     */
    public function createPostsCommentForm(Posts $post): Response
    {
        $form = $this->createForm(PostsCommentsFormType::class);

        return $this->render('blog/comments/_form.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param PostsComments $comment
     * @return Response
     */
    public function createPostsCommentsRepliesForm(PostsComments $comment): Response
    {
        $form = $this->createForm(PostsCommentsFormType::class);

        return $this->render('blog/comments/_replies_form.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }
}
