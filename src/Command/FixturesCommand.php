<?php

namespace App\Command;

use App\Entity\Question;
use App\Entity\Subject;
use App\Entity\Message;
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
        $io = new SymfonyStyle($input, $output);
        $io->text("coucou");
        $io->text("Now loading success");

        $faker = \Faker\Factory::create('fr_FR');

        $conn=$this->em->getConnection();
        //désactive la vérification des clefs etrangères
        $conn->query ('SET FOREIGN_KEY_CHECKS = 0');
        //écrase les tables
        $conn->query ('TRUNCATE question');
        $conn->query ('TRUNCATE message');
        $conn->query ('TRUNCATE subject');
        $conn->query ('TRUNCATE question_subject');
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

        for($i=0; $i<20; $i++){

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
                for($c=0; $c<50;$c++){
                    $message =new Message();

                    $message->setContent($faker->realText(200));

                    $message->setQuestion($question);

                    //à continuer
                    $this->em->persist($message);

                }
            $this->em->persist($question);
        }

        $this->em->flush();
        $io->success("Done!");

    }
}
