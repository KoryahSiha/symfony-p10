<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// préfixe de route, toutes les URL des routes de la classe devront démarrer par le préfixe pour être reconnues
#[Route('/hello')]
class HelloController extends AbstractController
{
    // la route associe un verbe HTPP (GET par défaut) et une URL à la fonction qui est juste en-dessous
    // quand il y a /hello et app_hello ça appelle la fonction index()
    // nom de l'app + nom de de la route + nom de la fonction
    #[Route('/hello', name: 'app_hello_index')]
    // fonction
    public function index(): Response // fonction associée à la route du dessus
    {
        // rendu de la vue (réponse HTPP) qui est renvoyée au client web
        // 'hello/index.html.twig' = chemin relatif au dossier templates
        return $this->render('hello/index.html.twig', [
            // transmission de variables au template twig
            // 'controller_name' = variable, clé alphanumérique (car tableau), 'GreenController' = valeur
            'controller_name' => 'GreenController',
        ]);
    }

    // une URL comprenant un paramètre envoyé par l'utilisateur
    // '/age/{birthYear}' passer une variable en paramètres de l'URL et l'injecte dans les paramètres de la fonction
    #[Route('/age/{birthYear}', name: 'app_hello_age', methods: ['GET'])]
    // la fonction doit accepteru n paramètre qui porte le même nom que le paramètre de la fonction
    // il est possible de filtrer les données en rajoutant un type hinting
    public function age(int $birthYear): Response // réponse HTPP
    {
        // traitement de données
        $year = 2023;
        $age = $year - $birthYear;

        return $this->render('hello/age.html.twig', [
            'birthYear' => $birthYear,
            'year' => $year,
            'age' => $age,
        ]);
    }
}
