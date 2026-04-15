@php
    // 先に必要な変数を定義
    $primaryType  = data_get($result, 'primary_type');
    $primaryLabel = data_get($result, 'primary_label');
    $master = config('diagnose_results', []);
    $detail = $primaryType && isset($master[$primaryType]) ? $master[$primaryType] : [];
    $pairingLabel = $detail['pairing_label'] ?? $detail['name'] ?? $primaryLabel ?? '○○ × ○○';
    
    // ペアリング情報
    $snacks = $detail['snacks'] ?? [];
    $catchCopy = $detail['catch_copy'] ?? '';
    $onePhrase = $detail['one_phrase'] ?? '';
    
    $shareUrl = url('/diagnose/result/' . ($result->result_id ?? ''));
    $shareTitle = '【5akeMe診断結果】私にぴったりのお酒は「' . $pairingLabel . '」でした！';
    $shareDescription = '5つの質問に答えるだけで、あなたにぴったりのお酒が見つかる！あなたも診断してみよう🍶';
@endphp

@extends('layouts.app')

@section('title', '診断結果 - ' . $pairingLabel)

@section('og_type', 'article')
@section('og_title', $shareTitle)
@section('og_description', $shareDescription)

@section('content')
<div class="diagnose-result-page">

    @include('diagnose-result.partials._styles')

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
        </section>

        {{-- 食事ペアリング --}}
        @include('diagnose-result.partials._pairing')

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

        {{-- SNSシェア --}}
        @include('diagnose-result.partials._share')

        {{-- おすすめ店舗 --}}
        @include('diagnose-result.partials._stores')

        {{-- フィードバック --}}
        @include('diagnose-result.partials._feedback')
    </div>

    @include('diagnose-result.partials._scripts')
</div>
@endsection
