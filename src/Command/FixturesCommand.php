<?php

namespace App\Command;

use App\Entity\Question;
use App\Entity\Subject;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixturesCommand extends Command
{
    protected static $defaultName = 'app:fixtures';
    protected $em = null;
    // constructeur
    public function __construct(EntityManagerInterface $em, ?string $name = null)
    {
        $this->em = $em;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('Load dummy data in our database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 200);

        $io = new SymfonyStyle($input, $output);
        $io->text("coucou");
        $io->text("Now loading success");

        $faker = \Faker\Factory::create('fr_FR');

        $answer =$io->ask("Truncating all tables... Sure ? yes|no" ,'no');
        if ($answer !== "yes"){
            $io->text("Aborting");
            die();
        }


        $conn=$this->em->getConnection();
        //désactive la vérification des clefs etrangères
        $conn->query ('SET FOREIGN_KEY_CHECKS = 0');
        //vide les tables
        $conn->query ('TRUNCATE question');
        $conn->query ('TRUNCATE message');
        $conn->query ('TRUNCATE subject');
        $conn->query ('TRUNCATE question_subject');
        $conn->query ('TRUNCATE user');
        //récupéré la vérification
        $conn->query ('SET FOREIGN_KEY_CHECKS = 1');

        $subjects = [
            "Affaires étrangères",
            "Affaires européennes",
            "Agriculture, alimentation, pêche",
            "Ruralité",
            "Aménagement du territoire",
            "Économie et finance",
            "Culture",
            "Communication",
            "Défense",
            "Écologie et développement durable",
            "Transports",
            "Logement",
            "Éducation",
            "Intérieur",
            "Outre-mer et collectivités territoriales",
            "Immigration",
            "Justice et Libertés",
            "Travail",
            "Santé",
            "Démocratie"
        ];

        //garder en mémoire nos objets Subject
        $allSubjects =[];
        foreach ($subjects as $label){
            $subject = new Subject();
            $subject->setLabel($label);
            $this->em->persist($subject);
            //on ajoute ce sujet à notre tableau d'objets
            $allSubjects[] = $subject;
        }
        $this->em->flush();

        // démarre la barre de progression avec 200 opérations
        $io->progressStart(200);

        for($i=0; $i<100; $i++){
            //fait avancer la barre de progression
            $io->progressAdvance(1);

            $question = new Question();

            $question->setTitle($faker->sentence);
            $question->setDescription($faker->realText(500));

            //ajoute entre 1 et 3 sujets à cette question
            $num= mt_rand(1,3);
            for ($b = 0; $b < $num; $b++){
                $s = $faker->randomElement($allSubjects);
                    $question->addSubject($s);
            }

            $question->setStatus($faker->randomElement(['debating', 'voting', 'closed']));
            $question->setSupports($faker->numberBetween(0,4700000));
            $question->setCreationDate($faker->dateTimeBetween ($startDate = '-1 years', $endDate = 'now', $timezone = null) );

            //ajoute des messages sur les questions
            $messageNumber = mt_rand(0,20);
            for ($m = 0; $m < $messageNumber; $m++){
                $message = new Message();
                $message->setQuestion($question);
                $message->setClaps($faker->optional(0.5, 0)->numberBetween(0,5000));
                $message->setCreationDate($faker->dateTimeBetween($question->getCreationDate()));
                $message->setIsPublished($faker->boolean(95));
                $message->setContent($faker->paragraphs($nb = mt_rand(1,3), $asText = true));
                $this->em->persist($message);
            }
            $this->em->persist($question);



            // données bidon pour le user


        }

        for($d=0; $d<100;$d++){
            $user = new User ();
            $user->setUsername($faker->name);
            $user->setEmail($faker->email);
            $user->setPassword($faker->password);
            $user->setSocialSecurityNumber($faker->numberBetween(10000,2000000));
            $user->setRoles($faker->randomElement([['admin'], ['user']]));
            $this->em->persist($user);

        }
        // fin de la barre de progression
        $io->progressFinish();

        $this->em->flush();
        $io->success("Done!");

    }
}
