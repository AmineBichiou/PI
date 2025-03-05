<?php
// src/Controller/GoogleCalendarController.php
namespace App\Controller;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GoogleCalendarController extends AbstractController
{
    #[Route('/google-calendar/events', name: 'google_calendar_events')]
    public function events(): Response
    {
        $client = $this->createGoogleClient();

        // Fetch calendar events
        $service = new Calendar($client);
        $calendarId = 'primary'; // Must match the calendar in the iframe

        $optParams = [
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c', strtotime('-1 month')), // Fetch past month for visibility
        ];

        try {
            $events = $service->events->listEvents($calendarId, $optParams)->getItems();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to fetch events: ' . $e->getMessage());
            $events = [];
        }

        return $this->render('google_calendar/events.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/google-calendar/add-event', name: 'google_calendar_add_event', methods: ['POST'])]
    public function addEvent(Request $request): Response
    {
        $client = $this->createGoogleClient();
        $service = new Calendar($client);
        $calendarId = 'primary'; // Must match the calendar in the iframe

        // Create a new event with default styling
        $event = new Event([
            'summary' => $request->request->get('summary', 'New Event'),
            'description' => $request->request->get('description', 'Added via Symfony'),
            'start' => [
                'dateTime' => date('c',), // Current date/time
                'timeZone' => 'UTC', // Adjust to your timezone if needed
            ],
            'end' => [
                'dateTime' => date('c', strtotime('+40 hour')), // 1 hour from now
                'timeZone' => 'UTC', // Adjust to your timezone if needed
            ],
        ]);

        try {
            $createdEvent = $service->events->insert($calendarId, $event);
            $this->addFlash('success', 'Event added successfully: ' . $createdEvent->getSummary());
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to add event: ' . $e->getMessage());
        }

        return $this->redirectToRoute('google_calendar_events');
    }

    private function createGoogleClient(): Client
    {
        $client = new Client();

        $serviceAccountFilePath = $this->getParameter('google_service_account_path');

        if (!file_exists($serviceAccountFilePath)) {
            throw new \Exception("Service account file not found at: " . $serviceAccountFilePath);
        }

        $client->setAuthConfig($serviceAccountFilePath);
        $client->addScope(Calendar::CALENDAR);

        // Replace with your actual Google email
       $client->setSubject('amine-390@calender-452814.iam.gserviceaccount.com');

        return $client;
    }
    #[Route('/google-calendar/remove-event/{eventId}', name: 'google_calendar_remove_event', methods: ['POST'])]
    public function removeEvent(string $eventId): Response
    {
        $client = $this->createGoogleClient();
        $service = new Calendar($client);
        $calendarId = 'primary';

        try {
            $service->events->delete($calendarId, $eventId);
            $this->addFlash('success', 'Event removed successfully');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to remove event: ' . $e->getMessage());
        }

        return $this->redirectToRoute('google_calendar_events');
    }
}