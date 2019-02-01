<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Message;

class ClapController extends AbstractController
{
    /**
     * @Route("/api/message/{id}/clap", name="api_clap_post", methods={"POST"})
     *
     */

    public function addClap($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Message::class);

        $message = $repo->find($id);
        $message->setClaps( $message->getClaps()+1);
        $em->flush();

        return new JsonResponse([
           "status" => "ok",
           "message"=>"",
           "data"=>[
               "claps"=> $message->getClaps()
           ]
        ]);
    }
}