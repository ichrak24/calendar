<?php

// src/Controller/ReservationController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Response;


#[Route('/reservation')]
class ReservationController extends AbstractController
{


    /**
     * @Route("/reservation/agenda", name="reservation_agenda", methods={"GET"})
     */
    public function agenda(Request $request, ReservationRepository $reservationRepository): Response
    {
        $currentMonth = $request->query->get('month', date('n'));
        $currentYear = $request->query->get('year', date('Y'));
        $firstDayOfMonth = new \DateTime("$currentYear-$currentMonth-01");
        $daysInMonth = $firstDayOfMonth->format('t');
        $firstDayOfWeek = $firstDayOfMonth->format('w');

        // Récupérer les réservations du mois
        $reservations = $reservationRepository->findReservationsByMonth($currentYear, $currentMonth);

        return $this->render('reservation/agenda.html.twig', [
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
            'firstDayOfMonth' => $firstDayOfWeek,
            'daysInMonth' => $daysInMonth,
            'currentMonthName' => $firstDayOfMonth->format('F'),
            'reservations' => $reservations,
        ]);
    }

    /**
     * @Route("/save_reservation", name="save_reservation", methods={"POST"})
     */
    public function saveReservation(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $title = $data['title'];
        $startDate = new \DateTime($data['startDate']);
        $endDate = new \DateTime($data['endDate']);

        $reservation = new Reservation();
        $reservation->setTitle($title);
        $reservation->setStartDate($startDate);
        $reservation->setEndDate($endDate);
        // Ajoutez les autres champs nécessaires

        $entityManager->persist($reservation);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}