<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    private $em;
    private $movieRepository;

    public function __construct(MovieRepository $movieRepository, EntityManagerInterface $em){
        $this->movieRepository = $movieRepository;
        $this->em = $em;
    }

    #[Route('/movies', methods:['GET'], name:'movies')]
    public function index(): Response
    {
        
        //findAll() - SELECT * FROM movies;
        $movies = $this->movieRepository->findAll();
        
        return $this->render('movies/index.html.twig', compact('movies'));
    }

    #[Route('/movies/create', methods:['GET', 'POST'], name:'create_movie')]
    public function create(Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $newMovie = $form->getData();

            $imagePath = $form->get('imagePath')->getData();
            if($imagePath){
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $newMovie->setImagePath('/uploads/' . $newFileName);
            }

            $this->em->persist($newMovie);
            $this->em->flush();

            return $this->redirectToRoute('movies');
        }
        
        return $this->render('movies/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/movies/{id}', methods:['GET'], name:'show_movie')]
    public function show($id): Response
    {
        $movie = $this->movieRepository->find($id);
        
        return $this->render('movies/show.html.twig', compact('movie'));
    }

    
    #[Route('/movies/edit/{id}', methods:['GET', 'POST'], name:'edit_movie')]
    public function edit($id, Request $request): Response
    {
        $movie = $this->movieRepository->find($id);
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $imagePath = $form->get('imagePath')->getData();
            if($imagePath){
                $oldPath = $this->getParameter('kernel.project_dir') . '/public' . $movie->getImagePath();
                if($movie->getImagePath() !== null && file_exists($oldPath)){    
                    unlink($oldPath);
                }

                $newFileName = uniqid() . '.' . $imagePath->guessExtension();
                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $movie->setImagePath('/uploads/' . $newFileName);  
            }
                
            $movie->setTitle($form->get('title')->getData());
            $movie->setReleaseYear($form->get('releaseYear')->getData());
            $movie->setDescription($form->get('description')->getData());
            
            $this->em->flush();

            return $this->redirectToRoute('movies');
        }
        
        return $this->render('movies/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView()
        ]);
    }
}
