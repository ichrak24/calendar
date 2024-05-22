<?php

// src/Controller/ReservationController.php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    #[Route('/agenda', name: 'reservation_agenda', methods: ['GET', 'POST'])]
    public function agenda(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('reservation_agenda');
        }

        $month = $request->query->get('month', date('m'));
        $year = $request->query->get('year', date('Y'));

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $firstDayOfMonth = (new \DateTime("$year-$month-01"))->format('w');
        $currentMonth = (int)$month;
        $currentYear = (int)$year;
        $currentMonthName = date('F', mktime(0, 0, 0, $currentMonth, 1, $currentYear));


        return $this->render('reservation/agenda.html.twig', [
            'form' => $form->createView(),
            'reservations' => $reservationRepository->findAll(),
            'currentMonth' => $month,
            'currentYear' => $year,
            'daysInMonth' => $daysInMonth,
            'firstDayOfMonth' => $firstDayOfMonth,
            'currentMonthName' => $currentMonthName,
        ]);
    }
}