<?php

namespace App\Controller;


use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    /**
     *
     * @Route("/questions/ajouter",
     *     name="question_create",
     *     methods={"GET", "POST"}
     *     )
     * fonction pour créer une question, INSERT
     */
    public function create()
    {
        $question = new Question();

        $question->setTitle('retret');
        $question->setDescription('fsdfsdfsd');
        $question->setStatus('debated');
        $question->setSupports('123');
        $question->setCreationDate(new \DateTime());

        //récupère l'EntityManager de Doctrine
        $em = $this
            ->getDoctrine()
            ->getManager();
        // on demande à Doctrine de sauvegarder notre instance
        $em->persist($question);
        // exécute requete sql
        $em->flush();

        // modification de l'instance
       // $question->setDescription('fsozejzezoaezae');
       // $em->flush();

        //pour supprimer la requete
        // $em->remove($question);
        // $em->flush();

        return $this->render('question/create.html.twig', [

        ]);
    }

    /**
     * Symfony récupère l'id dans l'URL et le passe à notre fonction.
     * Le paramètre dans l'URL et celui de la fonction doivent être nommés identiquement.
     *
     *
     * @Route("/questions/{id}", name="question_detail",
     *     requirements={"id" : "\d+"},
     *     methods={"GET", "POST"})
     */
    public function details(int $id)
        //public function details (Question $question)
    {
        $questionRepository = $this
            ->getDoctrine()
            ->getRepository(Question::class);
        //dd($id);
        //$question = $questionRepository->findOneBy(["id" => $id]);
        //$question= $questionRepository->findOneById($id);
        $question = $questionRepository->find($id);
    // message personnalisé d'erreur si l'id de la question n'existe pas
        if (!$question){
            throw $this->createNotFoundException("Cette question n'existe pas !");
        }

        return $this ->render('question/details.html.twig',[
            // on nomme la clé comme la variable - compact("question") en argument
            'question' => $question
        ]);
    }

    /**
     * @Route("/questions", name="question_list",
     *     methods={"GET"})
     */
    public function list()
    {
        // ce repository nous permet de faire des SELECT
        $questionRepository = $this
            ->getDoctrine()
            ->getRepository(Question::class);

        // SELECT * FROM question WHERE status = 'debating'
        // ORDER BY supports DESC LIMIT 1000
        $questions = $questionRepository->findBy(
            ['status' => 'debating'], //where
            ['supports' => 'DESC'], //order by
            1000, //limit
            0 //offset
        );

        //dd($questions);
        //var dump et die()

        return $this->render('question/list.html.twig', [
                "questions" => $questions
            ]);

    }
}
