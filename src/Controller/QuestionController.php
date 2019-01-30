<?php

namespace App\Controller;


use App\Entity\Message;
use App\Entity\Question;
use App\Form\MessageType;
use App\Form\QuestionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function create(Request $request) //prendre le http/fondation pour le use
    {
        $question = new Question();

        // création intance formulaire, les données sont mises directement dans l'entité
        $questionForm= $this->createForm(QuestionType::class, $question);

        $questionForm->handleRequest($request);

        if ($questionForm->isSubmitted()&&
            $questionForm->isValid()){
                 $em = $this
                     ->getDoctrine()
                     ->getManager();
                 $em->persist($question);
                 $em->flush();

                 // crée un flash message
                $this->addFlash('success', 'Merci pour votre participation !');

                //redirige vers la page de détails de cette question
                return $this->redirectToRoute('question_detail',
                    ['id'=> $question->getId()]);
        }
        /*
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
        */
        return $this->render('question/create.html.twig', [
            "questionForm"=> $questionForm->createView()
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
    public function details(Request $request, int $id)
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

        // création du formulaire message
        $message = new Message();
        // création intance formulaire, les données sont mises directement dans l'entité
        $messageForm= $this->createForm(MessageType::class, $message);

        //récupère les données présentes dans la requête et les injecte dans notre message
        $messageForm->handleRequest($request);
        //si le formulaire est valide et soumis
        if ($messageForm->isSubmitted()&&
            $messageForm->isValid()){
            // récupère l'Entity Manager
            $em = $this
                ->getDoctrine()
                ->getManager();
            //sauvegarde l'instance
            $em->persist($message);
            //exécute
            $em->flush();

            // crée un flash message
            $this->addFlash('success', 'Merci pour votre message !');

            //redirige vers la page de détails où apparait le message (pour vider le formulaire)
            return $this->redirectToRoute('question_detail',
                ['id'=> $question->getId()]);
        }
        //récupère le messageRepository
        $messageRepository = $this
            ->getDoctrine()
            ->getRepository(Message::class);
        //récupère les 200 messages les plus récents
        $messages= $messageRepository->findBy(
            ['isPublished'=>true],
            ['creationDate'=>'DESC'],
            200
        );


        return $this ->render('question/details.html.twig',[
            // on nomme la clé comme la variable - compact("question") en argument
            'question'=> $question, //passe les questions à twig
            'messages' => $messages, //passe les messages à twig
            "messageForm"=> $messageForm->createView() //passe le formulaire à twig
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
