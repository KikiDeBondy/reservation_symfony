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
    public function __construct(private readonly KernelInterface $kernel, private readonly SlotService $slotService){}

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
            $application = new Application($this->kernel);

            $data = json_decode($request->getContent(), true);

            // Vérifier si toutes les données nécessaires sont présentes
            if (!isset($data['barber_id'], $data['start_date'], $data['end_date'])) {
                return $this->json(['error' => 'barber_id, start_date and end_date are required'], Response::HTTP_BAD_REQUEST);
            }

            // Créer l'objet Input pour la commande
            $input = new ArrayInput([
                'command' => 'app:generate-slot',
                'barber_id' => $data['barber_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date']
            ]);

            // Créer un BufferedOutput pour capturer la sortie de la commande
            $output = new BufferedOutput();

            // Exécuter la commande
            $application->run($input, $output);

            // Récupérer la sortie de la commande (message)
            $outputMessage = $output->fetch();

            // Retourner la réponse avec le message généré par la commande
            return $this->json(['message' => $outputMessage], Response::HTTP_OK);

        }catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/slot', name: 'app_slot')]
    public function index(): Response
    {
        return $this->render('slot/index.html.twig', [
            'controller_name' => 'SlotController',
        ]);
    }
}
