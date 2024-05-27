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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


#[Route('/reservation')]
class ReservationController extends AbstractController
{

    /**
     * @Route("/agenda", name="reservation_agenda", methods={"GET"})
     */
    public function agenda(
        Request $request,
        ReservationRepository $reservationRepository,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        $currentWeek = $request->query->get('week', (new \DateTime())->format('W'));
        $currentYear = $request->query->get('year', (new \DateTime())->format('Y'));

        $prevWeek = $currentWeek - 1;
        $nextWeek = $currentWeek + 1;

        $prevWeekUrl = $urlGenerator->generate('reservation_agenda', ['week' => $prevWeek, 'year' => $currentYear]);
        $nextWeekUrl = $urlGenerator->generate('reservation_agenda', ['week' => $nextWeek, 'year' => $currentYear]);

        $currentWeekStart = new \DateTime();
        $currentWeekStart->setISODate($currentYear, $currentWeek);

        $currentWeekEnd = clone $currentWeekStart;
        $currentWeekEnd->modify('+6 days');

        $reservations = $reservationRepository->findReservationsByWeek($currentWeekStart, $currentWeekEnd);

        return $this->render('reservation/agenda.html.twig', [
            'currentWeek' => $currentWeek,
            'currentYear' => $currentYear,
            'prevWeek' => $prevWeek,
            'nextWeek' => $nextWeek,
            'prevWeekUrl' => $prevWeekUrl,
            'nextWeekUrl' => $nextWeekUrl,
            'currentWeekStart' => $currentWeekStart,
            'currentWeekEnd' => $currentWeekEnd,
            'reservations' => $reservations,
        ]);
    }

    /**
     * @Route("/save_reservation", name="save_reservation", methods={"POST"})
     */
    public function saveReservation(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $title = $data['reservationTitle'];
        $startTime = $this->convertTo24HourFormat($data['reservationStartTime']);
        $endTime = $this->convertTo24HourFormat($data['reservationEndTime']);
        $date = $data['reservationDate'];

        $reservation = new Reservation();
        $reservation->setTitle($title);
        $reservation->setStartDate(new \DateTime($date . ' ' . $startTime));
        $reservation->setEndDate(new \DateTime($date . ' ' . $endTime));

        $entityManager->persist($reservation);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    private function convertTo24HourFormat(string $time): string
    {
        $date = \DateTime::createFromFormat('H:i', $time);
        return $date->format('H:i');
    }
}