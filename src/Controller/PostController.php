<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Posts;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\PostsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/', name: 'post')]
    public function index(PostsRepository $postRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $posts = $paginator->paginate(
            $postRepository->findAll(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }
    /**
     * @Route("/posts/new", name="new_post")
     */
    public function create(Request $request, ManagerRegistry $doctrine){
        $post = new Posts();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $post->setCreatedAt(new \DateTime());
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Upload was successful'
            );

            return $this->redirectToRoute('post');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/posts/{id}", name="post_show")
     * @return Response
     * */ 
    
     public function show(Request $request, PostsRepository $postsRepository, ManagerRegistry $doctrine): Response{
         $postID = $request->attributes->get('id');
         $post = $postsRepository->find($postID);
         $comments = new Comment();
         $formComment = $this->createForm(CommentType::class, $comments);
         $formComment->handleRequest($request);
         if ($formComment->isSubmitted() && $formComment->isValid()){
             $comments->setCreatedAt(new \DateTimeImmutable());
             $comments->setPost($post);
             $entityManager = $doctrine->getManager();
             $entityManager->persist($comments);
             $entityManager->flush();
             $this->addFlash(
                 'success',
                 'Comment was added'
             );
             return $this->redirectToRoute('post_show',[
                'id' => $post->getId()
             ]);
         }


        return $this->render('post/show.html.twig',[
            'post'=> $post,
            'commentForm' => $formComment->createView()
        ]);
    }



}
