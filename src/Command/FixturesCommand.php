<?php

namespace App\Command;

use App\Entity\Question;
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
        $conn->query ('TRUNCATE question');

        for($i=0; $i<20; $i++){

            $question = new Question();

            $question->setTitle($faker->sentence);
            $question->setDescription($faker->realText(500));
            $question->setStatus($faker->randomElement(['debating', 'voting', 'closed']));
            $question->setSupports($faker->numberBetween(0,4700000));
            $question->setCreationDate($faker->dateTimeBetween ($startDate = '-1 years', $endDate = 'now', $timezone = null) );

            $this->em->persist($question);
        }

        $this->em->flush();
        $io->success("Done!");

    }
}
