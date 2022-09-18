<?php

namespace App\Controller;

use App\Datatable\Type\PostType;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Omines\DataTablesBundle\DataTableFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post', name: 'app_post_')]
class PostController extends AbstractController
{
    #[Route('', name: 'list')]
    #[IsGranted('ROLE_MODERATOR')]
    public function index(
        Request $request,
       DataTableFactory  $dataTableFactory,
        PostRepository $postRepository): Response
    {

        $table = $dataTableFactory->createFromType(PostType::class)
            ->handleRequest($request);

        if ($table->isCallback()){
            return $table->getResponse();
        }


        return $this->render('post/index.html.twig', [
            'datatable'=>$table,
        ]);
    }

    #[Route('/delete/{postId}', name: 'delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        ManagerRegistry $doctrine,
        PostRepository $postRepository,
        $postId
    ): Response
    {

        $post = $postRepository->find($postId);

        $doctrine->getManager()->remove($post);
        $doctrine->getManager()->flush();

        return $this->redirectToRoute('app_post_list');
    }
}
