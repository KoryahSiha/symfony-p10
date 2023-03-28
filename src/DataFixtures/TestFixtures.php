<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\SchoolYear;
use App\Entity\Student;
use App\Entity\Tag;
use App\Entity\User;
use \DateTime;
use \DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixtures extends Fixture implements FixtureGroupInterface
{
    private $doctrine;
    private $faker;
    private $hasher;
    private $manager;

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function __construct(ManagerRegistry $doctrine, UserPasswordHasherInterface $hasher)
    {
        $this->doctrine = $doctrine;
        $this->faker = FakerFactory::create('fr_FR');
        $this->hasher = $hasher;

    }

    public function load(ObjectManager $manager): void
    {
        // ajouter compte admin non rattaché à un utilisateur
        $this->manager = $manager;

        $this->loadTags();
        $this->loadSchoolYears();
        $this->loadProjects();
        $this->loadStudents();
        // $this->loadUsers();

    }

    public function loadStudents(): void
    {
        // school years
        $repository = $this->manager->getRepository(SchoolYear::class);
        // select * from schoolYear
        // la variable $schoolYears va récupéré un tableau avec toutes les schoolYear
        $SchoolYears = $repository->findAll();
        // dump($schoolYears);
        
        // tags
        $repository = $this->manager->getRepository(Tag::class);
        $Tags = $repository->findAll();
        
        // projects
        $repository = $this->manager->getRepository(Project::class);
        $Projects = $repository->findAll();
        // dump($Projects);

        // students
        // données de test statiques
        $datas = [
            [
                // user
                'email' => 'foo.bar@example.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                // student
                'firstname' => 'Foo',
                'lastname' => 'Bar',
                'createdAt' => DateTimeImmutable::createFromFormat('Y/m/d', '2022/01/01'),
                'schoolYear' => $SchoolYears[0],
                'project' => $Projects[0],
                'tags' => [$Tags[0], $Tags[1], $Tags[2]],
            ],
            [
                'email' => 'baz.baz@example.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'firstname' => 'Baz',
                'lastname' => 'Baz',
                'createdAt' => DateTimeImmutable::createFromFormat('Y/m/d', '2022/01/02'),
                'schoolYear' => $SchoolYears[0],
                'project' => $Projects[0],
                'tags' => [$Tags[0], $Tags[1], $Tags[2]],
            ],
        ];

        foreach ($datas as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $password = $this->hasher->hashPassword($user, $data['password']);
            $user->setPassword($password);
            $user->setRoles($data['roles']);

            $student = new Student();
            $student->setFirstname($data['firstname']);
            $student->setLastname($data['lastname']);
            $student->setCreatedAt($data['createdAt']);
            $student->setUser($user);
            $student->setSchoolYear($data['schoolYear']);
            $student->setProject($data['project']);

            foreach ($data['tags'] as $tag) {
                $student->addTag($tag);
            }

            $this->manager->persist($student);
        }

        // données de test statiques

        for ($i = 0; $i < 100; $i++) {
            $user = new User();

            $user->setEmail($this->faker->email());
            $password = $this->hasher->hashPassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE-USER']);

            $student = new Student();
            $student->setFirstname($this->faker->firstname());
            $student->setLastname($this->faker->lastname());
            $student->setCreatedAt(new DateTimeImmutable());
            $student->setUser($user);
            $student->setSchoolYear($this->faker->randomElement($SchoolYears));
            $student->setProject($this->faker->randomElement($Projects));

            foreach ($this->faker->randomElements($Tags) as $tag) {
                $student->addTag($tag);
            }

            $this->manager->persist($student);
        }

        $this->manager->flush();

        // users
        $repository = $this->manager->getRepository(User::class);
        $Users = $repository->findAll();
    }

    public function loadTags(): void
    {
        $datas = [
            // données de test statiques
            // équivalent d'un objet
            [
                'name' => 'HTML',
                'description' => null,
            ],
            [
                'name' => 'CSS',
                'description' => 'Langage de programmation pour styliser',
            ],
            [
                'name' => 'JS',
                'description' => 'Langage de programmation pour rendre dynamique',
            ],
        ];

        foreach ($datas as $data) {
            // création d'un nouvel objet
            $tag = new Tag();
            // affectation des valeurs statiques
            $tag->setName($data['name']);
            $tag->setDescription($data['description']);

            // demande d'enregistrement de l'objet
            // créer les requêtes SQL
            $this->manager->persist($tag);
        }

        // données de test dynamiques
        for ($i = 0; $i < 10; $i++) {
            // création d'un nouvel objet
            $tag = new Tag();
            // affectation des valeurs dynamiques
            $tag->setName(ucfirst($this->faker->word()));
            $tag->setDescription($this->faker->sentence());
            
            // demande d'enregistrement de l'objet
            // créer les requêtes SQL
            // ça dit : 'je veux que ces données soient enregistrées'
            $this->manager->persist($tag);
        }


        // éxecution des requêtes SQL
        $this->manager->flush();
    }

    public function loadSchoolYears(): void
    {

        $datas = [
            // données de test statiques
            // équivalent d'un objet
            [
                'name' => 'Promo Foo Bar Baz',
                'description' => null,
                'startDate' => DateTime::createFromFormat('Y/m/d', '2022/01/01'),
                'endDate' => DateTime::createFromFormat('Y/m/d', '2022/04/30'),
            ],
            [
                'name' => 'Promo Lorem Ipsum',
                'description' => 'Une promo formidable',
                'startDate' => DateTime::createFromFormat('Y/m/d', '2022/06/01'),
                'endDate' => DateTime::createFromFormat('Y/m/d', '2022/09/30'),
            ]
        ];

        foreach ($datas as $data) {
            // création d'un nouvel objet
            $schoolYear = new SchoolYear();
            // affectation des valeurs statiques
            $schoolYear->setName($data['name']);
            $schoolYear->setDescription($data['description']);
            $schoolYear->setStartDate($data['startDate']);
            $schoolYear->setEndDate($data['endDate']);

            // demande d'enregistrement de l'objet
            // créer les requêtes SQL
            $this->manager->persist($schoolYear);
        }

        // données de test dynamiques
        for ($i = 0; $i < 10; $i++) {
            // création d'un nouvel objet
            $schoolYear = new SchoolYear();
            // affectation des valeurs dynamiques
            $schoolYear->setName(ucfirst($this->faker->word()));
            $schoolYear->setDescription($this->faker->sentence());
            $schoolYear->setStartDate($this->faker->dateTimeBetween('-10 week', '-6 week'));
            $schoolYear->setEndDate($this->faker->dateTimeBetween('+8 week', '+12 week'));
            
            // demande d'enregistrement de l'objet
            // créer les requêtes SQL
            // ça dit : 'je veux que ces données soient enregistrées'
            $this->manager->persist($schoolYear);
        }


        // éxecution des requêtes SQL
        $this->manager->flush();
    }

    public function loadProjects(): void
    {
        $repository = $this->manager->getRepository(Tag::class);
        $Tags = $repository->findAll();

        // ajouter tags aux projets

        $datas = [
            [
                'name' => 'Maquettage',
                'description' => null,
                'clientName' => 'Foo Bar',
                'startDate' => DateTime::createFromFormat('Y/m/d', '2023/02/01'),
                'checkpointDate' => DateTime::createFromFormat('Y/m/d', '2023/03/01'),
                'deliveryDate' => DateTime::createFromFormat('Y/m/d', '2023/04/01'),
                'tags' => [$Tags[0], $Tags[1], $Tags[2]],
                // ajouter tags id 1,  id , id 3 de la liste des tags
            ],
            [
                'name' => 'Student',
                'description' => null,
                'clientName' => 'Foo Bar',
                'startDate' => DateTime::createFromFormat('Y/m/d', '2023/02/01'),
                'checkpointDate' => DateTime::createFromFormat('Y/m/d', '2023/03/01'),
                'deliveryDate' => DateTime::createFromFormat('Y/m/d', '2023/04/01'),
                'tags' => [$Tags[0], $Tags[1], $Tags[2]],
                // ajouter tags id 1,  id , id 3 de la liste des tags
            ],
        ];

        foreach ($datas as $data) {
            // création d'un nouvel objet
            $project = new Project();
            // affectation des valeurs statiques
            $project->setName($data['name']);
            $project->setDescription($data['description']);
            $project->setClientName($data['clientName']);
            $project->setStartDate($data['startDate']);
            $project->setCheckpointDate($data['checkpointDate']);
            $project->setDeliveryDate($data['deliveryDate']);

            // demande d'enregistrement de l'objet
            // créer les requêtes SQL
            $this->manager->persist($project);
        };



        for ($i = 0; $i < 10; $i++) {

            $project = new Project();

            $project->setName(ucfirst($this->faker->sentence(3, true)));
            $project->setDescription($this->faker->optional($weight = 0.6)->sentence());
            $project->setClientName($this->faker->name());
            $project->setStartDate($this->faker->dateTimeBetween('-2 month', '-1 month'));
            $project->setcheckpointDate($this->faker->dateTimeBetween('-2 week', '-1 week'));
            $project->setDeliveryDate($this->faker->dateTimeBetween('+2 month', '+3 month'));

            foreach ($data['tags'] as $tag) {
                $project->addTag($tag);
            }

            $this->manager->persist($project);
        }   

        $this->manager->flush();
    }

}
