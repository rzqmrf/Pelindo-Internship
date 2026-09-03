<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $driver;
    protected ?string $token;
    protected string $apiUrl;

    public function __construct()
    {
        $this->driver = config('services.whatsapp.driver', env('WA_DRIVER', 'log'));
        $this->token = config('services.whatsapp.token', env('WA_API_TOKEN', ''));
        $this->apiUrl = config('services.whatsapp.url', env('WA_API_URL', 'https://api.fonnte.com/send'));
    }

    /**
     * Format phone number to standard international format (62xxx)
     */
    public function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Send WhatsApp message to target phone
     */
    public function sendMessage(?string $targetPhone, string $message): bool
    {
        if (empty($targetPhone)) {
            Log::info("WhatsAppService: No target phone provided.");
            return false;
        }

        $formattedPhone = $this->formatPhoneNumber($targetPhone);

        if ($this->driver === 'log' || empty($this->token)) {
            Log::info("WhatsApp Notification [LOG DRIVER] to {$formattedPhone}: {$message}");
            return true;
        }

        try {
            if ($this->driver === 'fonnte') {
                $response = Http::withHeaders([
                    'Authorization' => $this->token,
                ])->post($this->apiUrl, [
                    'target' => $formattedPhone,
                    'message' => $message,
                ]);

                return $response->successful();
            }

            if ($this->driver === 'wablas') {
                $response = Http::withHeaders([
                    'Authorization' => $this->token,
                ])->post($this->apiUrl, [
                    'phone' => $formattedPhone,
                    'message' => $message,
                ]);

                return $response->successful();
            }

            // Fallback HTTP generic POST
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                'target' => $formattedPhone,
                'message' => $message,
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("WhatsAppService exception sending to {$formattedPhone}: " . $e->getMessage());
            return false;
        }
    }
}
