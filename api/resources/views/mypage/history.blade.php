@extends('layouts.app')

@section('title', '診断履歴 - マイページ')

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

        .history-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .history-card {
            background: var(--card-bg, #fff);
            border-radius: var(--radius-md, 16px);
            padding: 16px;
            box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.04));
            display: flex;
            align-items: center;
            gap: 16px;
            text-decoration: none;
            transition: all var(--transition-normal, 200ms ease-out);
        }

        .history-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .history-icon {
            width: 50px;
            height: 50px;
            border-radius: var(--radius-sm, 8px);
            background: var(--bg-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .history-content {
            flex: 1;
        }

        .history-type {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .history-mood {
            font-size: 12px;
            color: var(--text-sub);
        }

        .history-date {
            font-size: 12px;
            color: var(--text-sub);
        }

        .history-empty {
            text-align: center;
            padding: 48px 24px;
            background: var(--card-bg);
            border-radius: var(--radius-md, 16px);
        }

        .history-empty-icon {
            display: flex;
            justify-content: center;
            color: var(--brand-main, #9c3f2e);
            margin-bottom: 16px;
        }

        .history-empty-text {
            color: var(--text-sub);
            margin-bottom: 16px;
        }

        .history-empty-btn {
            display: inline-block;
            padding: 12px 24px;
            background: var(--brand-main);
            color: #fff;
            border-radius: var(--radius-full, 999px);
            text-decoration: none;
            font-weight: 600;
        }

        .pagination-wrap {
            margin-top: 24px;
            display: flex;
            justify-content: center;
        }
    </style>

    <div class="mypage-container">
        <a href="{{ route('mypage') }}" class="mypage-back">← マイページに戻る</a>
        <h1 class="mypage-title"><x-svg-icon name="bar-chart" size="22" /> 診断履歴</h1>

        @if($results->count() > 0)
            <div class="history-list">
                @foreach($results as $result)
                    <a href="{{ route('diagnose.result', $result->result_id) }}" class="history-card">
                        <div class="history-icon"><x-svg-icon name="wine" size="24" /></div>
                        <div class="history-content">
                            <div class="history-type">{{ $result->primary_label }}</div>
                            <div class="history-mood">
                                @if($result->mood)
                                    {{ match($result->mood) {
                                        'lively' => '🎉 わいわい',
                                        'chill' => '🍵 しっとり',
                                        'silent' => '🌙 静かに',
                                        'light' => '🍃 サクッと',
                                        'strong' => '🔥 ガッツリ',
                                        default => $result->mood
                                    } }}
                                @endif
                            </div>
                        </div>
                        <div class="history-date">{{ $result->pivot->created_at->format('Y/m/d') }}</div>
                    </a>
                @endforeach
            </div>

            <div class="pagination-wrap">
                {{ $results->links() }}
            </div>
        @else
            <div class="history-empty">
                <div class="history-empty-icon"><x-svg-icon name="bar-chart" size="48" /></div>
                <p class="history-empty-text">まだ診断履歴がありません</p>
                <a href="{{ route('diagnose') }}" class="history-empty-btn">診断してみる</a>
            </div>
        @endif
    </div>
</div>
@endsection
