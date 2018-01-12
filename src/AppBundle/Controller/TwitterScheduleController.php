<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\City;
use AppBundle\Entity\TwitterSchedule;
use AppBundle\Entity\User;
use AppBundle\Form\Type\TwitterScheduleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class TwitterScheduleController extends AbstractController
{
    public function listAction(Request $request, UserInterface $user, string $citySlug): Response
    {
        $city = $this->getCheckedCity($citySlug);

        $this->checkCityAccess($city, $user);

        $scheduleList = $this->getDoctrine()->getRepository(TwitterSchedule::class)->findByCity($city);

        return $this->render(
            'AppBundle:TwitterSchedule:list.html.twig', [
                'scheduleList' => $scheduleList,
                'city' => $city,
            ]
        );
    }

    public function addAction(Request $request, UserInterface $user, string $citySlug): Response
    {
        $city = $this->getCheckedCity($citySlug);

        $schedule = new TwitterSchedule();

        $form = $this->createForm(TwitterScheduleType::class, $schedule, [
            'city' => $city,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $schedule = $form->getData();

            $schedule->setCity($city);

            $em = $this->getDoctrine()->getManager();
            $em->persist($schedule);
            $em->flush();

            return $this->redirectToRoute('twitter_schedule_list', ['citySlug' => $city->getSlug()]);
        }

        return $this->render(
            'AppBundle:TwitterSchedule:edit.html.twig', [
                'scheduleForm' => $form->createView(),
            ]
        );
    }

    public function editAction(Request $request, UserInterface $user, string $citySlug): Response
    {
        $city = $this->getCheckedCity($citySlug);
        $schedule = $this->getTwitterScheduleByRequest($request);

        $form = $this->createForm(TwitterScheduleType::class, $schedule, [
            'city' => $city,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $schedule = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('twitter_schedule_list', ['citySlug' => $city->getSlug()]);
        }

        return $this->render(
            'AppBundle:TwitterSchedule:edit.html.twig', [
                'scheduleForm' => $form->createView(),
            ]
        );
    }

    public function removeAction(Request $request, UserInterface $user, string $citySlug): Response
    {
        $city = $this->getCheckedCity($citySlug);
        $schedule = $this->getTwitterScheduleByRequest($request);

        $this->checkTwitterScheduleAccess($schedule, $user);

        $em = $this->getDoctrine()->getManager();
        $em->remove($schedule);
        $em->flush();

        return $this->redirectToRoute('twitter_schedule_list', ['citySlug' => $city->getSlug()]);
    }

    protected function checkCityAccess(City $city, UserInterface $user): void
    {
        if (!$user->hasRole(User::ROLE_ADMIN) && $city->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }
    }

    protected function checkTwitterScheduleAccess(TwitterSchedule $schedule, UserInterface $user): void
    {
        if (!$user->hasRole(User::ROLE_ADMIN) && $schedule->getCity()->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }
    }

    protected function getCheckedCity(string $citySlug): City
    {
        $city = $this->getCityBySlug($citySlug);

        if (!$city) {
            throw $this->createNotFoundException();
        }

        return $city;
    }

    protected function getCheckedTwitterSchedule(Request $request): TwitterSchedule
    {
        $schedule = $this->getTwitterScheduleByRequest($request);

        if (!$schedule) {
            throw $this->createNotFoundException();
        }

        return $schedule;
    }
}
