<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SnsCheckCommand extends Command
{
    protected $signature = 'sns:check';

    protected $description = 'SNSログイン（Google/LINE/X）の設定状況とコールバックURLを表示';

    public function handle(): int
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $providers = [
            'Google' => [
                'id' => config('services.google.client_id'),
                'secret' => config('services.google.client_secret'),
                'callback' => $baseUrl . '/auth/google/callback',
                'console' => 'https://console.cloud.google.com/apis/credentials',
                'label' => 'Google Cloud Console → 認証情報 → OAuth 2.0 クライアント',
            ],
            'LINE' => [
                'id' => config('services.line.client_id'),
                'secret' => config('services.line.client_secret'),
                'callback' => $baseUrl . '/auth/line/callback',
                'console' => 'https://developers.line.biz/console/',
                'label' => 'LINE Developers → チャネル → LINE Login 設定 → Callback URL',
            ],
            'X (Twitter)' => [
                'id' => config('services.twitter.client_id'),
                'secret' => config('services.twitter.client_secret'),
                'callback' => $baseUrl . '/auth/twitter/callback',
                'console' => 'https://developer.x.com/en/portal/dashboard',
                'label' => 'X Developer Portal → アプリ → User authentication set up → Callback URI',
            ],
        ];

        $this->info('');
        $this->info('=== SNSログイン 設定チェック ===');
        $this->info('APP_URL: ' . $baseUrl);
        $this->info('');

        foreach ($providers as $name => $p) {
            $idOk = !empty($p['id']);
            $secretOk = !empty($p['secret']);
            $status = ($idOk && $secretOk) ? '<fg=green>✓ 設定済み</>' : '<fg=yellow>要設定</>';
            $this->line("<fg=cyan>{$name}</> {$status}");
            $this->line('  コールバックURL（開発者コンソールにこのURLを登録）:');
            $this->line('  ' . $p['callback']);
            if (!$idOk) {
                $this->line('  → ' . $p['label']);
                $this->line('  → ' . $p['console']);
            }
            $this->line('');
        }

        $this->info('上記のコールバックURLを各開発者コンソールに登録し、.env に CLIENT_ID / CLIENT_SECRET を設定してください。');
        $this->info('詳細: docs/SNS_LOGIN.md');
        $this->info('');

        return self::SUCCESS;
    }
}
