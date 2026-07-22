<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditService
{
    private string $secretKey;
    private string $baseUrl = 'https://api.xendit.co';

    public function __construct()
    {
        $this->secretKey = config('services.xendit.secret_key');
    }

    /**
     * Buat Invoice Xendit.
     * Xendit akan tampilkan halaman pembayaran dengan semua metode
     * (VA BCA/BNI/BRI/Mandiri, QRIS, OVO, DANA, ShopeePay, kartu kredit, dll).
     */
    public function createInvoice(array $params): array
    {
        try {
            $payload = [
                'external_id' => $params['external_id'],
                'amount' => (int) $params['amount'],
                'payer_email' => $params['payer_email'],
                'description' => $params['description'],
                'success_redirect_url' => $params['success_redirect_url'],
                'failure_redirect_url' => $params['failure_redirect_url'],
                'currency' => 'IDR',
                'invoice_duration' => 86400, // 24 jam
                'should_send_email' => false,
                'customer' => [
                    'given_names' => $params['customer_name'] ?? '',
                    'email' => $params['payer_email'] ?? '',
                    'mobile_number' => $params['customer_phone'] ?? '',
                ],
                'items' => $params['items'] ?? [],
            ];

            $response = Http::timeout(30)
                ->withoutVerifying()
                ->withBasicAuth($this->secretKey, '')
                ->post("{$this->baseUrl}/v2/invoices", $payload);

            $json = $response->json();

            Log::info('[Xendit] CreateInvoice', [
                'external_id' => $params['external_id'],
                'http_status' => $response->status(),
                'invoice_id' => $json['id'] ?? null,
                'invoice_url' => $json['invoice_url'] ?? null,
            ]);

            if (!$response->successful()) {
                Log::error('[Xendit] CreateInvoice gagal', [
                    'status' => $response->status(),
                    'body' => $json,
                ]);
                return ['error' => $json['message'] ?? 'Gagal membuat invoice Xendit.'];
            }

            return $json;
        } catch (\Exception $e) {
            Log::error('[Xendit] CreateInvoice exception: ' . $e->getMessage());
            return ['error' => 'Koneksi ke Xendit gagal: ' . $e->getMessage()];
        }
    }

    /**
     * Ambil status invoice dari Xendit (untuk polling di halaman waiting).
     */
    public function getInvoice(string $invoiceId): array
    {
        try {
            $response = Http::timeout(15)
                ->withoutVerifying()
                ->withBasicAuth($this->secretKey, '')
                ->get("{$this->baseUrl}/v2/invoices/{$invoiceId}");

            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error('[Xendit] GetInvoice: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Verifikasi webhook dari Xendit via header X-CALLBACK-TOKEN.
     */
    public function verifyWebhook(string $callbackToken): bool
    {
        $expected = config('services.xendit.webhook_token');
        if (empty($expected)) {
            Log::warning('[Xendit] XENDIT_WEBHOOK_TOKEN belum diset di .env!');
            return false;
        }
        return hash_equals($expected, $callbackToken);
    }
}
