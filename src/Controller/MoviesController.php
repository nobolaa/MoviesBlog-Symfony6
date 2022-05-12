<?php

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('/movies', name:'movies')]
    public function index(): Response
    {
        $repository = $this->em->getRepository(Movie::class);
        
        //findAll() - SELECT * FROM movies;
        $movies = $repository->findAll();

        //find() - SELECT * FROM movies WHERE id = 5;
        $movies = $repository->find(5);

        //findBy() - SELECT * FROM movies ORDER BY id DESC;
        $movies = $repository->findBy([], ['id' => 'DESC']);

        /*findOneBy() - SELECT * FROM movies 
                               WHERE releaseYear = 2008 AND title = 'The Dark Kinght' 
                               ORDER BY id DESC;*/        
        $movies = $repository->findOneBy(['releaseYear' => 2008, 'title' => 'The Dark Kinght'], ['id' => 'DESC']);

        //find() - SELECT COUNT() FROM movies WHERE id = 5;
        $movies = $repository->count(['id' => 10]);

        $movies = $repository->getClassName();

        dd($movies);

        //return $this->render('index.html.twig', compact('movies'));
    }
}
