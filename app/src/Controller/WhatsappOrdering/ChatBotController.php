<?php

namespace App\Controller\WhatsappOrdering;

use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twilio\Rest\Client;

class ChatBotController extends AbstractController
{
    private ParameterBagInterface $bag;
    private HttpClientInterface $client;

    public function __construct(ParameterBagInterface $bag, HttpClientInterface $client)
    {
        $this->bag = $bag;
        $this->client = $client;
    }

    /**
     * @Route("/whatsapp", name="whatsapp_order", methods={"POST"})
     */
    public function checkWhatsapp()
    {
        // Find your Account SID and Auth Token at twilio.com/console
        // and set the environment variables. See http://twil.io/secure

        $sid = $this->bag->get('twilio_sid');
        $token = $this->bag->get('twilio_token');
        $twilioWhatsappNumber = $this->bag->get('twilio_whatsapp_number');
        $serviceId = $this->bag->get('twilio_service_id');
        $twilio = new Client($sid, $token);

        $message = $twilio->messages
            ->create("whatsapp:+447387269221", // to
                [
                    "from" => "whatsapp:$twilioWhatsappNumber",
                    "body" => "Hello there!",
                ]
            );

        dd($message);
        print($message->sid);
    }


    /**
     * @Route("/whatsapp/listin", name="whatsapp_listin", methods={"POST"})
     */
    public function listenToReplies(Request $request)
    {

//        $twilioWhatsappNumber = $this->bag->get('twilio_whatsapp_number');
//        $from = "whatsapp:$twilioWhatsappNumber";
        $body = $request->get('Body');
        $persistentAction= $request->get('PersistentAction');

        $client = $this->client;
        try {
//            if ($body === 'amirsalkhori') {
//                $response = $client->request('GET', "https://api.github.com/users/$body");
//                $githubResponse = json_decode($response->getContent(), true);
//                if ($response->getStatusCode() == 200) {
//                    $message = "*Name:* ." . $githubResponse['name'] . "\n";
//                    $message .= "*Name:* ." . $githubResponse['bio'] . "\n";
//                    $message .= "*Name:* ." . $githubResponse['location'] . "\n";
//                    $message .= "*Name:* ." . $githubResponse['followers'] . "devs\n";
//                    $message .= "*Name:* ." . $githubResponse['following'] . "devs\n";
//                    $message .= "*Name:* ." . $githubResponse['html_url'] . "\n";
//
//                    $this->sendWhatsAppMessage($message);
//                } else {
//                    $this->sendWhatsAppMessage($githubResponse->message);
//                }
//            }
//            else{

                $sid = $this->bag->get('twilio_sid');
                $token = $this->bag->get('twilio_token');
                $twilioWhatsappNumber = $this->bag->get('twilio_whatsapp_number');
                $serviceId = $this->bag->get('twilio_service_id');
                $twilio = new Client($sid, $token);

                $message = $twilio->messages
                    ->create("whatsapp:+15005550006", // to
                        [
                            "messagingServiceSid" => $serviceId,
                            "body" => "This is one of the Twilio office locations",
                            "persistentAction" => ["$persistentAction"]
                        ]
                    );

                dd($message);

//            }

        } catch (RequestException $th) {
            $response = json_decode($th->getResponse()->getBody());
            $this->sendWhatsAppMessage($response->message);
        }
        return;
    }

    /**
     * Sends a WhatsApp message  to user using
     * @param string $message Body of sms
     * @param string $recipient Number of recipient
     */
    public function sendWhatsAppMessage(string $message)
    {
        $sid = $this->bag->get('twilio_sid');
        $token = $this->bag->get('twilio_token');
        $twilioWhatsappNumber = $this->bag->get('twilio_whatsapp_number');
        $serviceId = $this->bag->get('twilio_service_id');
        $twilio = new Client($sid, $token);

        $client = $twilio->messages
            ->create("whatsapp:+447387269221", // to
                [
                    "from" => "whatsapp:$twilioWhatsappNumber",
                    "body" => $message,
                ]
            );

//        $client->messages->create($recipient, array('from' => "whatsapp:$twilioWhatsappNumber", 'body' => $message));
        return $client;
    }

}