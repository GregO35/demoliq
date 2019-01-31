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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FixturesCommand extends Command
{
    protected static $defaultName = 'app:fixtures';
    protected $em = null;
    protected $encoder=null;
    // constructeur
    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder,
        ?string $name = null)
    {
        $this->encoder =$encoder;
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

        for($d=0; $d<25;$d++){
            $user = new User ();
            $user->setUsername($faker->unique()->userName);
            $user->setEmail($faker->unique()->email);
            //$user->setPassword($faker->password);
            $password =$user->getUsername();
            $hash =$this->encoder->encodePassword($user, $password);
            $user->setPassword($hash);

            $user->setSocialSecurityNumber($faker->numberBetween(10000,2000000));
            $user->setRoles($faker->randomElement([['admin'], ['user']]));
            $this->em->persist($user);




            for($i=0; $i<20; $i++){
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

                $question->setUser($user);
                $question->setStatus($faker->randomElement(['debating', 'voting', 'closed']));
                $question->setSupports($faker->numberBetween(0,4700000));
                $question->setCreationDate($faker->dateTimeBetween ($startDate = '-1 years', $endDate = 'now', $timezone = null) );

                //ajoute des messages sur les questions
                    $messageNumber = mt_rand(0,10);
                    for ($m = 0; $m < $messageNumber; $m++){
                        $message = new Message();
                        $message->setQuestion($question);
                        //mettre user dans un tableau et récupérer aléatoirement le user
                        $message->setUser($user);
                        $message->setClaps($faker->optional(0.5, 0)->numberBetween(0,5000));
                        $message->setCreationDate($faker->dateTimeBetween($question->getCreationDate()));
                        $message->setIsPublished($faker->boolean(95));
                        $message->setContent($faker->paragraphs($nb = mt_rand(1,3), $asText = true));
                        $this->em->persist($message);
                    }
                $this->em->persist($question);



            // données bidon pour le user


        }
        }

        // fin de la barre de progression
        $io->progressFinish();

        $this->em->flush();
        $io->success("Done!");

    }
}
