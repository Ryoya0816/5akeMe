@extends('layouts.app')

@section('title', '診断結果')

@section('content')
<div class="diagnose-result-page">

    <style>
        .diagnose-result-page {
            display: flex;
            justify-content: center;
            padding: 32px 8px 48px;
            background: #fafafa;
        }

        .dr-page {
            width: 100%;
            max-width: 800px;
            background: #fff;
            padding: 24px 16px 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        }

        .dr-title {
            text-align: center;
            font-size: 20px;
            margin-bottom: 16px;
        }

        /* 一番上の酒名（⭕️⭕️のイメージ） */
        .dr-name-pill {
            display: flex;
            justify-content: center;
            margin-bottom: 8px;
        }

        .dr-name-pill-inner {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 24px;
            border-radius: 999px;
            border: 2px solid #000;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 0.08em;
        }

        .dr-step-label {
            text-align: center;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .dr-arrow {
            text-align: center;
            font-size: 20px;
            margin-bottom: 16px;
        }

        .dr-hex-section {
            display: flex;
            justify-content: center;
            margin-bottom: 32px;
        }

        .dr-hex-wrap {
            position: relative;
            width: 260px;
            height: 260px;
        }

        /* 六角形は削除して、チャートだけ中央に表示 */
        #diagnose-chart {
            position: absolute;
            inset: 0;
            margin: auto;
        }

        .dr-result-main {
            text-align: center;
            margin-bottom: 24px;
            line-height: 1.7;
        }

        .dr-result-main .dr-main-text {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .dr-result-main .dr-sub-text {
            font-size: 15px;
        }

        .dr-mood-text {
            font-size: 13px;
            color: #777;
            margin-bottom: 12px;
        }

        .dr-actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            margin-top: 8px;
            margin-bottom: 8px;
        }

        .dr-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 260px;
            padding: 10px 20px;
            border-radius: 999px;
            border: none;
            background: #222;
            color: #fff;
            font-size: 15px;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.08s ease, box-shadow 0.08s ease, background 0.12s ease;
        }

        .dr-btn:hover {
            background: #000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.18);
            transform: translateY(-1px);
        }

        .dr-btn-secondary {
            background: #f5f5f5;
            color: #222;
            border: 1px solid #ddd;
        }

        .dr-btn-secondary:hover {
            background: #eaeaea;
        }

        .dr-btn small {
            font-size: 12px;
            margin-right: 4px;
            opacity: 0.8;
        }

        .dr-note {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }

        @media (max-width: 600px) {
            .diagnose-result-page {
                padding: 16px 4px 32px;
            }

            .dr-hex-wrap {
                width: 220px;
                height: 220px;
            }

            #diagnose-chart {
                width: 100%;
                height: 100%;
            }

            .dr-btn {
                min-width: 220px;
                width: 100%;
                max-width: 320px;
            }
        }
    </style>

    @php
        /**
         * $result は今こういう想定：
         * - Eloquentモデル App\Models\DiagnoseResult
         *   - primary_type   (例: sake_dry)
         *   - primary_label  (例: 日本酒・辛口)
         *   - mood           (lively/chill/silent/light/strong)
         *   - candidates     (type/score/label の配列)
         *
         * さらに、config/diagnose_results.php で
         * タイプごとの詳細マスタを持っている想定：
         *
         * return [
         *   'sake_dry' => [
         *      'pairing_label'   => '日本酒・辛口 × 刺身',
         *      'pairing_message' => 'キリッと辛口で刺身の旨みを引き立てるタイプです。',
         *      'chart_labels'    => [...],
         *      'chart_values'    => [...],
         *   ],
         *   ...
         * ];
         */

        // モデルでも配列でも data_get で安全に取れるようにしておく
        $primaryType  = data_get($result, 'primary_type');
        $primaryLabel = data_get($result, 'primary_label');
        $mood         = data_get($result, 'mood');
        $candidates   = data_get($result, 'candidates', []);

        // 診断結果マスタ（存在しなければ空配列）
        $master = config('diagnose_results', []);
        $detail = $primaryType && isset($master[$primaryType]) ? $master[$primaryType] : [];

        // 表示用ラベル
        $pairingLabel = $detail['pairing_label']
            ?? $detail['name']
            ?? $primaryLabel
            ?? '○○ × ○○';

        $pairingMessage = $detail['pairing_message']
            ?? $detail['message']
            ?? '○○ × ○○ が楽しめるお酒を紹介します！！！';

        // moodテキスト（任意）
        $moodLabels = [
            'lively' => '今日は、みんなでわいわい飲みたい気分みたい。そんなあなたに…',
            'chill'  => '今日は、少人数でしっぽり語りたい気分みたい。そんなあなたに…',
            'silent' => '今日は、ひとりで静かに飲みたい気分みたい。そんなあなたに…',
            'light'  => '今日は、サクッと軽く飲みたい気分みたい。そんなあなたに…',
            'strong' => '今日は、がっつり飲みたい気分みたい。そんなあなたに…',
        ];
        $moodText = $mood ? ($moodLabels[$mood] ?? null) : null;

        // レーダーチャート用データ
        // 1. マスタに chart_labels / chart_values があればそちら優先
        // 2. なければ candidates の上位6件を使う
        if (!empty($detail['chart_labels']) && !empty($detail['chart_values'])) {
            $chartLabels = $detail['chart_labels'];
            $chartValues = $detail['chart_values'];
        } else {
            // candidates から上位6件を抽出
            $top = is_array($candidates) ? array_slice($candidates, 0, 6) : [];
            $chartLabels = [];
            $chartValues = [];

            foreach ($top as $row) {
                $chartLabels[] = $row['label'] ?? ($row['type'] ?? 'タイプ');
                $chartValues[] = isset($row['score']) ? round((float)$row['score'], 1) : 0;
            }

            // 万が一何もない場合のフォールバック
            if (empty($chartLabels)) {
                $chartLabels = ['タイプA', 'タイプB', 'タイプC', 'タイプD', 'タイプE', 'タイプF'];
                $chartValues = [3, 4, 2, 5, 3, 4];
            }
        }
    @endphp

    <div class="dr-page">
        {{-- タイトル --}}
        <h1 class="dr-title">あなたへのおすすめのお酒は、、、</h1>

        {{-- 一番上の⭕️⭕️ゾーン → 酒名を表示 --}}
        <div class="dr-name-pill">
            <div class="dr-name-pill-inner">
                {{ $pairingLabel }}
            </div>
        </div>

        <div class="dr-step-label"></div>
        <div class="dr-arrow"></div>

        {{-- チャートのみ --}}
        <section class="dr-hex-section">
            <div class="dr-hex-wrap">
                <canvas id="diagnose-chart"></canvas>
            </div>
        </section>

        <section class="dr-result-main">
            @if($moodText)
                <div class="dr-mood-text">
                    {{ $moodText }}
                </div>
            @endif

            <div class="dr-main-text">
                ペアリングのおすすめは、 {{ $pairingLabel }}
            </div>
            <div class="dr-sub-text">
                {{ $pairingMessage }}
            </div>
        </section>

        <div class="dr-actions">
            <button type="button" class="dr-btn" id="btn-show-stores">
                <small>②</small>
                <span>{{ $pairingLabel }} が飲めるお店を見る</span>
            </button>

            <a href="{{ url('/diagnose') }}" class="dr-btn dr-btn-secondary">
                <small>③</small>
                <span>もう一度診断する</span>
            </a>
        </div>

        <div class="dr-note">
            ※ グラフは、あなたの回答から算出した「上位6種類のお酒タイプ」をチャートで表示しています。
        </div>
    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const chartLabels = @json($chartLabels);
        const chartValues = @json($chartValues);

        const ctx = document.getElementById('diagnose-chart');

        if (ctx) {
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'お酒タイプのバランス',
                        data: chartValues,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        r: {
                            suggestedMin: 0,
                            suggestedMax: 5,
                            ticks: {
                                stepSize: 1
                            },
                            grid: {
                                circular: true
                            }
                        }
                    }
                }
            });
        }

        const btnShowStores = document.getElementById('btn-show-stores');
        if (btnShowStores) {
            btnShowStores.addEventListener('click', function () {
                // TODO: ここで「おすすめのお店リスト」画面に遷移させる
                alert('ここで「おすすめのお店リスト」を表示するように実装します！（後から作る）');
            });
        }
    </script>
</div>
@endsection
