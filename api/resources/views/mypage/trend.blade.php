@extends('layouts.app')

@section('title', '好み傾向 - マイページ')

@section('content')
<div class="mypage">
    <style>
        .mypage {
            min-height: 100vh;
            background: var(--bg-base, #fbf3e8);
            padding: 24px 16px 48px;
        }

        .mypage-container {
            max-width: 500px;
            margin: 0 auto;
        }

        .mypage-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-sub);
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .mypage-back:hover {
            color: var(--brand-main);
        }

        .mypage-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 20px;
        }

        .trend-card {
            background: var(--card-bg, #fff);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-bottom: 20px;
        }

        .trend-chart-wrap {
            position: relative;
            width: 100%;
            max-width: 300px;
            margin: 0 auto 24px;
        }

        .trend-total {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .trend-total-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--brand-main);
        }

        .trend-total-label {
            font-size: 12px;
            color: var(--text-sub);
        }

        .trend-legend {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .trend-legend-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .trend-legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }

        .trend-legend-label {
            flex: 1;
            font-size: 14px;
            color: var(--text-main);
        }

        .trend-legend-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
        }

        .trend-empty {
            text-align: center;
            padding: 48px 24px;
        }

        .trend-empty-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .trend-empty-text {
            color: var(--text-sub);
            margin-bottom: 16px;
        }

        .trend-empty-btn {
            display: inline-block;
            padding: 12px 24px;
            background: var(--brand-main);
            color: #fff;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 600;
        }

        .trend-insight {
            background: var(--bg-soft);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
        }

        .trend-insight-title {
            font-size: 14px;
            color: var(--text-sub);
            margin-bottom: 8px;
        }

        .trend-insight-text {
            font-size: 18px;
            font-weight: 700;
            color: var(--brand-main);
        }
    </style>

    <div class="mypage-container">
        <a href="{{ route('mypage') }}" class="mypage-back">← マイページに戻る</a>
        <h1 class="mypage-title">📈 好み傾向</h1>

        <div class="trend-card">
            @if($trendData['total'] > 0)
                <div class="trend-chart-wrap">
                    <canvas id="trendChart"></canvas>
                    <div class="trend-total">
                        <div class="trend-total-value">{{ $trendData['total'] }}</div>
                        <div class="trend-total-label">回診断</div>
                    </div>
                </div>

                <div class="trend-legend" id="trendLegend"></div>
            @else
                <div class="trend-empty">
                    <div class="trend-empty-icon">📈</div>
                    <p class="trend-empty-text">診断履歴がないため<br>傾向を分析できません</p>
                    <a href="{{ route('diagnose') }}" class="trend-empty-btn">診断してみる</a>
                </div>
            @endif
        </div>

        @if($trendData['total'] > 0 && count($trendData['labels']) > 0)
            <div class="trend-insight">
                <div class="trend-insight-title">あなたの一番好きなお酒は...</div>
                <div class="trend-insight-text">{{ $trendData['labels'][0] ?? '不明' }} 🍶</div>
            </div>
        @endif
    </div>

    @if($trendData['total'] > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const labels = @json($trendData['labels']);
            const values = @json($trendData['values']);

            // カラーパレット（5akeMeカラー系）
            const colors = [
                '#9c3f2e', // brand-main
                '#e07b5f', // 明るめ
                '#f4a261', // オレンジ
                '#e9c46a', // 黄色
                '#2a9d8f', // ティール
                '#264653', // ダーク
                '#8c6d57', // 茶色
                '#bc6c25', // ブラウン
                '#606c38', // オリーブ
                '#283618', // ダークグリーン
            ];

            const ctx = document.getElementById('trendChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors.slice(0, labels.length),
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // カスタム凡例を生成
            const legendContainer = document.getElementById('trendLegend');
            labels.forEach((label, index) => {
                const item = document.createElement('div');
                item.className = 'trend-legend-item';
                item.innerHTML = `
                    <div class="trend-legend-color" style="background: ${colors[index]}"></div>
                    <div class="trend-legend-label">${label}</div>
                    <div class="trend-legend-value">${values[index]}回</div>
                `;
                legendContainer.appendChild(item);
            });
        </script>
    @endif
</div>
@endsection
