<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    private $movieRepository;

    public function __construct(MovieRepository $movieRepository){
        $this->movieRepository = $movieRepository;
    }

    #[Route('/movies', methods:['GET'], name:'movies')]
    public function index(): Response
    {
        
        //findAll() - SELECT * FROM movies;
        $movies = $this->movieRepository->findAll();
        
        return $this->render('movies/index.html.twig', compact('movies'));
    }

    #[Route('/movies/{id}', methods:['GET'], name:'show')]
    public function show($id): Response
    {
        $movie = $this->movieRepository->find($id);
        
        return $this->render('movies/show.html.twig', compact('movie'));
    }
}
