<?php

namespace App\Support;

use GuzzleHttp\Client;
use Exception;

class GoogleDriveService
{
    protected Client $http;

    private string $raw;
    private string|null $title = null;
    private string|null $start_time = null;
    private string|null $accessToken = null;
    private string|null $fileId = null;

    public function __construct(string $raw)
    {
        $this->http = new Client();
        $this->raw = $raw;

        $payload = json_decode($this->raw, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($payload)) {
            $this->title = $payload['title'] ?? null;
            $this->start_time = $payload['start_time'] ?? null;
        }
    }

    public function getRaw(): string
    {
        return $this->raw;
    }

    public function getTitle(): string|null
    {
        return $this->title;
    }

    public function getStartTime(): string|null
    {
        return $this->start_time;
    }

    public function getFileId(): string|null
    {
        return $this->fileId;
    }

    protected function fetchAccessToken(): string
    {
        $clientId = config('_google.client_id');
        $clientSecret = config('_google.client_secret');
        $refreshToken = config('_google.refresh_token');

        if (! $clientId || ! $clientSecret || ! $refreshToken) {
            throw new Exception('Credenciais do Google não configuradas corretamente.');
        }

        $response = $this->http->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (! isset($data['access_token'])) {
            throw new Exception('Não foi possível obter o access_token do Google.');
        }

        $this->accessToken = $data['access_token'];

        return $this->accessToken;
    }

    public function createTranscriptFile(): string
    {
        $token = $this->accessToken ?? $this->fetchAccessToken();

        // Sem defaults: se não tiver start_time OU title, é erro
        if (! $this->start_time || ! $this->title) {
            throw new Exception('start_time e title são obrigatórios para criar o nome do arquivo.');
        }

        // Monta o nome do arquivo: [start_time]_[title].json
        $cleanTitle = preg_replace('/[^\w\-]+/u', '_', $this->title);
        $cleanTitle = trim((string) $cleanTitle, '_');

        if ($cleanTitle === '') {
            throw new Exception('Título inválido após sanitização.');
        }

        $fileName = $this->start_time . '_' . $cleanTitle . '.json';

        // 1) Cria o arquivo (metadata) no Drive
        $response = $this->http->post('https://www.googleapis.com/drive/v3/files', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                "name" => $fileName,
                "mimeType" => "application/json",
                "parents" => [
                    "1ofeqYiODQwmFtTdN6Ibvkgtjriti6fMD",
                ],
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $this->fileId = $data['id'] ?? null;

        // Sem defaults: se não tiver fileId, é erro
        if (! $this->fileId) {
            throw new Exception('Não foi possível obter o ID do arquivo criado no Google Drive.');
        }

        // 2) Atualiza o conteúdo do arquivo com o RAW do webhook
        $uploadUrl = sprintf(
            'https://www.googleapis.com/upload/drive/v3/files/%s?uploadType=media',
            $this->fileId
        );

        $this->http->patch($uploadUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                // pode trocar para 'text/plain' se quiser exatamente como no curl original
                'Content-Type'  => 'application/json',
            ],
            'body' => $this->raw,
        ]);

        return $this->fileId;
    }
}
