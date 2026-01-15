@extends('layouts.app')

@section('title', '診断結果')

@section('content')
<div class="diagnose-result-page">

    <style>
        /* =========================================
           結果ページ専用スタイル（最小変更で“共通カラー”に寄せる）
           ※ app.css の :root（--bg-base など）を前提にしています
           ========================================= */

        .diagnose-result-page {
            display: flex;
            justify-content: center;
            padding: 32px 8px 48px;

            /* 以前：background: #fafafa;
               → 共通の和紙色に寄せる（統一） */
            background: var(--bg-base, #fbf3e8);
        }

        .dr-page {
            width: 100%;
            max-width: 800px;

            /* 以前：background: #fff;
               → 白を残しつつ、境界と影を“共通トーン”へ */
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--line-soft, #f1dfd0);

            padding: 24px 16px 40px;
            border-radius: 16px;
            box-shadow: var(--shadow, 0 4px 20px rgba(0,0,0,0.04));
        }

        .dr-title {
            text-align: center;
            font-size: 20px;
            margin-bottom: 16px;

            /* タイトルもブランド寄せ */
            color: var(--brand-main, #9c3f2e);
            font-weight: 700;
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

            /* 以前：border: 2px solid #000;
               → ブランド色に寄せる */
            border: 2px solid var(--brand-main, #9c3f2e);

            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: var(--brand-main, #9c3f2e);
            background: var(--bg-soft, #fff7ee);
        }

        .dr-step-label {
            text-align: center;
            font-size: 14px;
            margin-bottom: 4px;
            color: var(--text-sub, #8c6d57);
        }

        .dr-arrow {
            text-align: center;
            font-size: 20px;
            margin-bottom: 16px;
            color: var(--text-sub, #8c6d57);
        }

        .dr-hex-section {
            display: flex;
            justify-content: center;
            margin-bottom: 28px;
        }

        .dr-hex-wrap {
            position: relative;
            width: 468px;
            height: 468px;

            /* チャート周りの“台座”を追加して統一感UP（最小の見栄え改善） */
            background: var(--bg-soft, #fff7ee);
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: 18px;
            box-shadow: 0 10px 18px rgba(0,0,0,0.05);
            padding: 10px;
        }

        /* チャートだけ中央に表示 */
        #diagnose-chart {
            position: absolute;
            inset: 10px;
            margin: auto;
        }

        .dr-result-main {
            text-align: center;
            margin-bottom: 24px;
            line-height: 1.7;
        }

        .dr-result-main .dr-main-text {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--brand-main, #9c3f2e);
        }

        .dr-result-main .dr-sub-text {
            font-size: 15px;
            color: var(--text-main, #3f3f3f);
        }

        .dr-mood-text {
            font-size: 13px;
            color: var(--text-sub, #8c6d57);
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

            /* 以前：background: #222;
               → ブランド赤茶 */
            background: var(--brand-main, #9c3f2e);

            color: #fff;
            font-size: 15px;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.08s ease, box-shadow 0.08s ease, background 0.12s ease;
        }

        .dr-btn:hover {
            background: var(--brand-text, #8a3a28);
            box-shadow: 0 4px 12px rgba(0,0,0,0.18);
            transform: translateY(-1px);
        }

        .dr-btn-secondary {
            background: var(--bg-soft, #fff7ee);
            color: var(--brand-main, #9c3f2e);
            border: 1px solid var(--line-soft, #f1dfd0);
        }

        .dr-btn-secondary:hover {
            background: #f7eadf;
        }

        .dr-btn small {
            font-size: 12px;
            margin-right: 6px;
            opacity: 0.85;
        }

        .dr-note {
            margin-top: 20px;
            font-size: 12px;
            color: var(--text-sub, #8c6d57);
            text-align: center;
        }

        @media (max-width: 600px) {
            .diagnose-result-page {
                padding: 16px 4px 32px;
            }

            .dr-hex-wrap {
                width: 396px;
                height: 396px;
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
         *   - top5           (type/score/label の配列)  ← ★追加：チャートはこっちを使う
         */

        // モデルでも配列でも data_get で安全に取れるようにしておく
        $primaryType  = data_get($result, 'primary_type');
        $primaryLabel = data_get($result, 'primary_label');
        $mood         = data_get($result, 'mood');

        // ★候補（テキスト表示などに使うなら残す）
        $candidates   = data_get($result, 'candidates', []);

        // ★チャート用：上位5（無ければ candidates から5件フォールバック）
        $top5 = data_get($result, 'top5', []);
        if (!is_array($top5) || empty($top5)) {
            // candidatesから上位5件を取得（既にスコア降順でソートされている想定）
            if (is_array($candidates) && !empty($candidates)) {
                // スコアでソート（念のため）
                $sortedCandidates = $candidates;
                usort($sortedCandidates, function($a, $b) {
                    $scoreA = $a['score'] ?? 0;
                    $scoreB = $b['score'] ?? 0;
                    return $scoreB <=> $scoreA; // 降順
                });
                $top5 = array_slice($sortedCandidates, 0, 5);
            } else {
                $top5 = [];
            }
        }

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

        // -----------------------------------------
        // レーダーチャート用データ
        // 1. マスタに chart_labels / chart_values があればそちら優先
        // 2. なければ top5 を使う（★仕様どおり）
        // -----------------------------------------
        if (!empty($detail['chart_labels']) && !empty($detail['chart_values'])) {
            $chartLabels = $detail['chart_labels'];
            $chartValues = $detail['chart_values'];
        } else {
            $chartLabels = [];
            $chartValues = [];

            if (!empty($top5) && is_array($top5)) {
                foreach ($top5 as $row) {
                    if (is_array($row)) {
                        $chartLabels[] = $row['label'] ?? ($row['type'] ?? 'タイプ');
                        $chartValues[] = isset($row['score']) ? round((float)$row['score'], 1) : 0;
                    }
                }
            }

            // 万が一何もない場合のフォールバック
            if (empty($chartLabels) || empty($chartValues)) {
                // candidatesから再度試行
                if (is_array($candidates) && !empty($candidates)) {
                    $chartLabels = [];
                    $chartValues = [];
                    $sortedCandidates = $candidates;
                    usort($sortedCandidates, function($a, $b) {
                        $scoreA = $a['score'] ?? 0;
                        $scoreB = $b['score'] ?? 0;
                        return $scoreB <=> $scoreA;
                    });
                    foreach (array_slice($sortedCandidates, 0, 5) as $row) {
                        if (is_array($row)) {
                            $chartLabels[] = $row['label'] ?? ($row['type'] ?? 'タイプ');
                            $chartValues[] = isset($row['score']) ? round((float)$row['score'], 1) : 0;
                        }
                    }
                }
                
                // それでも空の場合はデフォルト値
                if (empty($chartLabels)) {
                    $chartLabels = ['タイプA', 'タイプB', 'タイプC', 'タイプD', 'タイプE'];
                    $chartValues = [3, 4, 2, 5, 3];
                }
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
            ※ グラフは、あなたの回答から算出した「上位5種類のお酒タイプ」をチャートで表示しています。
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

                        /* 見た目も“世界観”に寄せる（色はCSS変数から取得） */
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 3,

                        /* Chart.js はCSS変数を直接は読めないのでJSで読む */
                        borderColor: getComputedStyle(document.documentElement).getPropertyValue('--brand-main').trim() || '#9c3f2e',
                        backgroundColor: 'rgba(156, 63, 46, 0.12)',
                        pointBackgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--brand-main').trim() || '#9c3f2e',
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
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
                            },
                            angleLines: {
                                color: 'rgba(0,0,0,0.08)'
                            },
                            pointLabels: {
                                font: { size: 12 },
                                color: '#6b7280'
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
