<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function findListQuestionsQB()
    {
        $qb = $this->createQueryBuilder('q');
        //$qb->form() est sous entendu parce qu'on est dans le QuestionRepository
        $qb->andWhere('q.status = :status');
        $qb->orderBy('q.creation', 'DESC');
        $qb->join('q.subjects', 's');
        $qb->addSelect('s');
        $qb->setParameter('status','debating');
        $qb->setFirstResult(0);
        $qb->setMaxResults(200);

        $query =$qb->getQuery();
        $questions = $query->getResult();
        return $questions;

    }





    // dans la requête la classe remplace le nom de la table
    // la propriété remplace le nom de la colonne
    public function findListQuestions()
    {
        $dql ="SELECT q, s 
               FROM App\Entity\Question q
               JOIN q.subjects s
               WHERE q.status = 'debating'
               ORDER BY q.creationDate DESC";
        //where q.status = :status

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setMaxResults(200);
        $query->setFirstResult(0);
        //$query->setParameter('stauts','debating');
        $questions = $query->getResult();

        return $questions;
    }

    // /**
    //  * @return Question[] Returns an array of Question objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Question
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
