@extends('layouts.app')

@section('title', 'マイページ - 5akeMe')

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

        /* ヘッダー（LINEっぽいプロフィール） */
        .mypage-header {
            background: linear-gradient(135deg, var(--brand-main) 0%, #b5543f 100%);
            border-radius: 20px;
            padding: 24px;
            color: #fff;
            text-align: center;
            margin-bottom: 20px;
        }

        .mypage-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #fff;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--brand-main);
            overflow: hidden;
        }

        .mypage-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .mypage-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .mypage-provider {
            font-size: 12px;
            opacity: 0.8;
        }

        /* 統計カード */
        .mypage-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .mypage-stat-card {
            background: var(--card-bg, #fff);
            border-radius: 16px;
            padding: 20px 16px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .mypage-stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--brand-main);
            margin-bottom: 4px;
        }

        .mypage-stat-label {
            font-size: 12px;
            color: var(--text-sub);
        }

        /* メニューリスト */
        .mypage-menu {
            background: var(--card-bg, #fff);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-bottom: 20px;
        }

        .mypage-menu-item {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            text-decoration: none;
            color: var(--text-main);
            border-bottom: 1px solid var(--line-soft);
            transition: background 0.2s;
        }

        .mypage-menu-item:last-child {
            border-bottom: none;
        }

        .mypage-menu-item:hover {
            background: var(--bg-soft);
        }

        .mypage-menu-icon {
            display: flex;
            align-items: center;
            margin-right: 16px;
            color: var(--brand-main);
        }

        .mypage-menu-text {
            flex: 1;
        }

        .mypage-menu-title {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .mypage-menu-desc {
            font-size: 12px;
            color: var(--text-sub);
        }

        .mypage-menu-arrow {
            color: var(--text-sub);
            display: flex;
        }

        /* 最近の診断 */
        .mypage-section-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-sub);
            margin-bottom: 12px;
            padding-left: 4px;
        }

        .mypage-recent-list {
            background: var(--card-bg, #fff);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .mypage-recent-item {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            border-bottom: 1px solid var(--line-soft);
        }

        .mypage-recent-item:last-child {
            border-bottom: none;
        }

        .mypage-recent-type {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
            flex: 1;
        }

        .mypage-recent-date {
            font-size: 12px;
            color: var(--text-sub);
        }

        .mypage-empty {
            text-align: center;
            padding: 32px;
            color: var(--text-sub);
        }

        /* ログアウト */
        .mypage-logout {
            display: block;
            width: 100%;
            padding: 14px;
            background: transparent;
            border: 1px solid var(--line-soft);
            border-radius: 12px;
            color: var(--text-sub);
            font-size: 14px;
            text-align: center;
            cursor: pointer;
            transition: all var(--transition-normal, 200ms ease-out);
        }

        .mypage-logout:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #dc2626;
        }
    </style>

    <div class="mypage-container">
        {{-- ヘッダー --}}
        <div class="mypage-header">
            <div class="mypage-avatar">
                @if($user->avatar)
                    <img src="{{ strpos($user->avatar, 'http') === 0 ? $user->avatar : asset($user->avatar) }}" alt="{{ $user->name }}">
                @else
                    <x-svg-icon name="wine" size="36" />
                @endif
            </div>
            <div class="mypage-name">{{ $user->name }}</div>
            <div class="mypage-provider">{{ $user->provider_label }}でログイン中</div>
        </div>

        {{-- 統計 --}}
        <div class="mypage-stats">
            <div class="mypage-stat-card">
                <div class="mypage-stat-value">{{ $trendData['total'] }}</div>
                <div class="mypage-stat-label">診断回数</div>
            </div>
            <div class="mypage-stat-card">
                <div class="mypage-stat-value">{{ $visitedStoresCount }}</div>
                <div class="mypage-stat-label">行ったお店</div>
            </div>
        </div>

        {{-- メニュー --}}
        <div class="mypage-menu">
            <a href="{{ route('mypage.history') }}" class="mypage-menu-item">
                <span class="mypage-menu-icon"><x-svg-icon name="bar-chart" size="22" /></span>
                <div class="mypage-menu-text">
                    <div class="mypage-menu-title">診断履歴</div>
                    <div class="mypage-menu-desc">過去の診断結果を見る</div>
                </div>
                <span class="mypage-menu-arrow"><x-svg-icon name="chevron-right" size="18" /></span>
            </a>
            <a href="{{ route('mypage.stores') }}" class="mypage-menu-item">
                <span class="mypage-menu-icon"><x-svg-icon name="store" size="22" /></span>
                <div class="mypage-menu-text">
                    <div class="mypage-menu-title">行ったお店</div>
                    <div class="mypage-menu-desc">訪問した店舗とメモ</div>
                </div>
                <span class="mypage-menu-arrow"><x-svg-icon name="chevron-right" size="18" /></span>
            </a>
            <a href="{{ route('mypage.trend') }}" class="mypage-menu-item">
                <span class="mypage-menu-icon"><x-svg-icon name="trending-up" size="22" /></span>
                <div class="mypage-menu-text">
                    <div class="mypage-menu-title">好み傾向</div>
                    <div class="mypage-menu-desc">あなたの好みを分析</div>
                </div>
                <span class="mypage-menu-arrow"><x-svg-icon name="chevron-right" size="18" /></span>
            </a>
            <a href="{{ route('profile.edit') }}" class="mypage-menu-item">
                <span class="mypage-menu-icon"><x-svg-icon name="settings" size="22" /></span>
                <div class="mypage-menu-text">
                    <div class="mypage-menu-title">プロフィール編集</div>
                    <div class="mypage-menu-desc">名前やアイコンを変更</div>
                </div>
                <span class="mypage-menu-arrow"><x-svg-icon name="chevron-right" size="18" /></span>
            </a>
        </div>

        {{-- 最近の診断 --}}
        <div class="mypage-section-title">最近の診断</div>
        @if($recentResults->count() > 0)
            <div class="mypage-recent-list">
                @foreach($recentResults as $result)
                    <div class="mypage-recent-item">
                        <span class="mypage-recent-type">{{ $result->primary_label }}</span>
                        <span class="mypage-recent-date">{{ $result->pivot->created_at->format('m/d') }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="mypage-recent-list">
                <div class="mypage-empty">
                    まだ診断履歴がありません<br>
                    <a href="{{ route('diagnose') }}" style="color: var(--brand-main);">診断してみる →</a>
                </div>
            </div>
        @endif

        {{-- ログアウト --}}
        <form method="POST" action="{{ route('logout') }}" style="margin-top: 24px;">
            @csrf
            <button type="submit" class="mypage-logout">ログアウト</button>
        </form>
    </div>
</div>
@endsection
