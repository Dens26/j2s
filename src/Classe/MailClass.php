<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class MailClass
{
    public function resetPassword(string $email, string $name, string $subject, $content)
    {
        // Maijet test
        $mj = new Client(
            $_ENV['MJ_APIKEY_PUBLIC'],
            $_ENV['MJ_APIKEY_PRIVATE'],
            true,
            [
                'version' => 'v3.1',
                'curlOptions' => [
                    CURLOPT_CAINFO => $_ENV['CACERT_PATH'],
                ],
            ]
        );

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $_ENV['SENDER_EMAIL'],
                        'Name' => "J2S"
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' => $name
                        ]
                    ],
                    'TemplateID' => 6630751,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                        'name' => $name
                    ]
                ]
            ]
        ];

        $mj->post(Resources::$Email, ['body' => $body]);
    }
}
