<?php

namespace App\Command;

use App\Entity\Slot;
use App\Repository\SlotRepository;
use App\Repository\UserRepository;
use App\Services\SlotService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-slot',
    description: 'Générer les créneaux d\'un barbier',
)]
class GenerateSlotCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly SlotService $slotService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('barber_id', InputArgument::REQUIRED, 'Id du barber')
            ->addArgument('start_date', InputArgument::REQUIRED, 'Date de début')
            ->addArgument('end_date', InputArgument::REQUIRED, 'Date de fin')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
{
    $barberId = $input->getArgument('barber_id');
    $startDate = new \DateTime($input->getArgument('start_date'));
    $endDate = new \DateTime($input->getArgument('end_date'));

    $barber = $this->userRepository->find($barberId);
    if (!$barber) {
        $output->writeln('Barbier introuvable.');
        return Command::FAILURE;
    }

    $slotOfBarberExist = $this->slotService->slotOfBarberExist($barberId, $startDate);
    var_dump($slotOfBarberExist);
    if ($slotOfBarberExist) {
        $output->writeln('Une des dates est déjà ajouter à vos créneaux');
    }


    // Génération des créneaux de 30 minutes de 9 à 18h, pour chaque jour entre $startDate et $endDate
    $interval = new \DateInterval('PT30M');
    $start = new \DateTime('09:00:00');
    $end = new \DateTime('18:00:00');
    $date = clone $startDate;
    while ($date <= $endDate) {
        $current = clone $start;
        while ($current < $end) {
            $slot = new Slot();
            $slot->setBarberId($barber);
            $slot->setDate(clone $date);
            $slot->setStart(clone $current);
            $current->add($interval);
            $slot->setEnd(clone $current);
            $slot->setIsReserved(false);
            $this->entityManager->persist($slot);
            $this->entityManager->flush();
        }
        $date->modify('+1 day');
    }

    $output->writeln('Créneaux générés avec succès.');
    return Command::SUCCESS;
}
}
