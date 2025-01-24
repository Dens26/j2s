<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TranslatorService
{
    private HttpClientInterface $client;
    private int $usageLimit;
    private string $apiKey;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiKey = $_ENV['DEEPL_APIKEY'];
        $this->usageLimit = (int) ($_ENV['USAGE_LIMIT'] ?? 0);
    }

    /**
     * Check current API usage
     * @return int
     */
    public function getUsageCount(): int
    {
        $response = $this->client->request('GET', 'https://api-free.deepl.com/v2/usage', [
            'headers' => [
                'Authorization' => 'DeepL-Auth-Key ' . $this->apiKey
            ]
        ]);

        $data = $response->toArray();
        return $data['character_count'] ?? 0;
    }

    /**
     * Function that translate to french only if usage limit is not exceeded
     * @param string $description
     * 
     * @return string
     */
    public function checkIfQuotaAvailable(string $text): string
    {
        $currentUsage = $this->getUsageCount();

        // Return the original text if usage limit exceeded
        if ($currentUsage >= $this->usageLimit && $currentUsage > strlen($text) + 1000) {
            return false;
        }

        // Return the translated text
        return true;
    }

    public function translateOK(string $text): string
    {
        $currentUsage = $this->getUsageCount();

        // Return the original text if usage limit exceeded
        if ($currentUsage >= $this->usageLimit && $currentUsage > strlen($text)) {
            return false;
        }

        // Return the translated text
        return true;
    }


    /**
     * Function that use DeepL Api to translate the text to French
     * @param string $text
     * 
     * @return string
     */
    public function translateDescription(string $text): string
    {
        if ($this->checkIfQuotaAvailable($text)) {
            $response = $this->client->request('POST', 'https://api-free.deepl.com/v2/translate', [
                'headers' => [
                    'Authorization' => 'DeepL-Auth-Key ' . $this->apiKey,
                ],
                'body' => [
                    'text' => $text,
                    'target_lang' => 'FR'
                ],
            ]);

            $data = $response->toArray();
            return $data['translations'][0]['text'] ?? $text;
        }
        return $text;
    }

    public function translateOthers(string $text): string
    {
        if ($this->translateOK($text)) {
            $response = $this->client->request('POST', 'https://api-free.deepl.com/v2/translate', [
                'headers' => [
                    'Authorization' => 'DeepL-Auth-Key ' . $this->apiKey,
                ],
                'body' => [
                    'text' => $text,
                    'target_lang' => 'FR'
                ],
            ]);

            $data = $response->toArray();
            return $data['translations'][0]['text'] ?? 'Traduction non disponible';
        }
        return $text;
    }
}
