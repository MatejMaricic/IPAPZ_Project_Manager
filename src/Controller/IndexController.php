<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 15.02.19.
 * Time: 18:06
 */

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class IndexController extends AbstractController
{
    /**
     * @param Request $request
     * @Route ("/", name="index_page")
     * @return Response
     */

    public function index(Request $request,  ProjectRepository $projectRepository)
    {


        return $this->render('index.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}