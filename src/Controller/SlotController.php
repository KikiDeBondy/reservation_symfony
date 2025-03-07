<?php

namespace App\Controller;

use App\Entity\Slot;
use App\Repository\SlotRepository;
use App\Services\SlotService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class SlotController extends AbstractController
{
    public function __construct(private readonly SlotService $slotService){}

    //Retourner une semaine des slots d'un coiffeur donné
    #[Route('/slot/weekly/unreserved/{id}/{page}', name: 'app_slot_weekly_unreserved', methods: ['GET'])]
    public function weeklyUnreserved(int $id, int $page): Response
    {
        try{
            $slots = $this->slotService->weeklySlotUnreserve($id, $page);
            return $this->json($slots, 200, [], ['groups' => 'slot:read']);
        }catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }

    }
    #[Route('/slot/weekly/{id}/{page}', name: 'app_slot_weekly', methods: ['GET'])]
    public function weekly(int $id, int $page): Response
    {
        try{
            $slots = $this->slotService->weeklySlot($id, $page);
            return $this->json($slots, 200, [], ['groups' => 'slot:read']);
        }catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }

    }

    #[Route('/slot/update/{id}', name: 'app_slot_update', methods: ['PUT'])]
    public function update(Request $request, int $id): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $this->slotService->update($id, $data);

            return $this->json('Le slot a été mis à jour');
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Le slot n\'a pas pu être mis à jour',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    #[Route('/slot/generate', name: 'app_slot_generate', methods: ['POST'])]
    public function create(Request $request){
        try {
            $data = json_decode($request->getContent(), true);
            $start = new \DateTime($data['start_date']);
            $end = new \DateTime($data['end_date']);
            $slot = $this->slotService->generateSlot($data['barber_id'],$start,$end);
            return $this->json($slot, 200, [], ['groups' => 'slot:write']);
        }catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/slot/absent/{id}', name: 'app_slot_absent', methods: ['PUT'])]
    public function absent(int $id, Request $request): Response{
        try {
            $data = json_decode($request->getContent(), true);
            $start = new \DateTime($data['start_date']);
            $end = new \DateTime($data['end_date']);
            $slot = $this->slotService->absent($id, $start,$end);
            return $this->json($slot, 200, [], ['groups' => 'slot:write']);
        }catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    #[Route('/test/{id}/{page}', name: 'app_slot', methods: ['GET'])]
    public function test(int $id,int $page,SlotRepository $slotRepository)
    {
        try {
            $slots = $slotRepository->paginateSlots($id, $page);
            return $this->json($slots, 200, [], ['groups' => 'slot:read']);

        }catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
//
//    #[Route('/slot', name: 'app_slot')]
//    public function index(): Response
//    {
//        return $this->render('slot/index.html.twig', [
//            'controller_name' => 'SlotController',
//        ]);
//    }
}
