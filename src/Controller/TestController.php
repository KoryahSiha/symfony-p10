<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Entity\Project;
use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/test')]
class TestController extends AbstractController
{
    #[Route('/project', name: 'app_test_project')]
    public function project(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $repository = $doctrine->getRepository(Project::class);

        $dateStart = DateTime::createFromFormat('d/m/Y', '01/03/2023');
        $dateEnd = DateTime::createFromFormat('d/m/Y', '01/05/2023');
        $projects = $repository->findByDeliveryDateBetween($dateStart, $dateEnd);
        dump($projects);

        $project1 = $repository->find(1);
        
        if ($project1) {
            foreach ($project1->getStudents() as $student) {
                $student->setProject(null);
            }

            $em->remove($project1);
            $em->flush();
        }

        exit();
    }
    
    #[Route('/user', name: 'app_test_user')]
    public function user (UserRepository $repository): Response
    {
        $students = $repository->findAllStudents();
        dump($students);

        $admins = $repository->findAllAdmins();
        dump($admins);

        exit();
    } 

    // le préfixe + l'url de la route => '/test/tag'
    #[Route('/tag', name: 'app_test')]
    public function tag(ManagerRegistry $doctrine, TagRepository $repository): Response
    {
        // récupération de l'entity manager
        $em = $doctrine->getManager();

        $tags = $repository->findAllOrderByName();
        dump($tags);

        // recherche d'un objet par id
        $tag1 = $repository->find(1);
        dump($tag1);

        // recherche d'un objet par id
        $tag123 = $repository->find(123);
        dump($tag123);

        // recherche d'objets par name
        $tags = $repository->findBy(
            [
                'name' => 'HTML',
            ]
        );
        dump($tags);

        // recherche par mot-clé dans name ou description
        $tags = $repository->findByKeyword('numquam');
        dump($tags);

        $tag = new Tag();
        $tag->setName('Tag de test');
        $tag->setDescription('Ce tag est un test');

        // avant enregistrement, l'objet n'a pas d'id
        dump($tag->getId());

        // enregistre l'objet dans la BDD
        $em->persist($tag);
        // exécute l'ordre d'enregistrement
        $em->flush();

        // après enregistrement, l'objet a un id
        dump($tag->getId());

        // recherche d'un objet par id
        $tag1 = $repository->find(1);
        dump($tag1);
        // modicfication d'un objet
        $tag1->setName('Un autre nom de tag de test');
        $tag1->setDescription(null);

        // l'objet est déjà stocké en BDD, ce n'est pas nécessaire d'appeler la méthode persist()
        // $em->persist($tag1);
        $em->flush();
        dump($tag1);

        // recherche d'un objet par id
        $tag14 = $repository->find(14);
        
        // suppression d'un objet
        // gestion des exceptions
        try {
            $em->remove($tag14);
            $em->flush();
        } catch (Exception $e) {
            // interception d'une exception (objet)
            // la description de l'erreur
            dump($e->getMessage());
            // éventuellement numéro de code de l'erreur
            dump($e->getCode());
            // l'endroit où l'erreur a été détectée, le fichier
            dump($e->getFile());
            // l'endroit où l'erreur a été détectée, la ligne
            dump($e->getLine());
            // renvoie ce tableau sous chaîne de caractères
            dump($e->getTraceAsString());
        }

        dump($tag14);

        // recherche d'un objet par id
        $tag1 = $repository->find(1);

        if ($tag1) {
            foreach ($tag1->getStudents() as $student) {
                dump($student);
            }

            foreach ($tag1->getProjects() as $project) {
                dump($project);
            }

            // supression d'un objet
            $em->remove($tag1);
            $em->flush();
        }



        exit();
    }
}
