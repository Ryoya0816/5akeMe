<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MypageController extends Controller
{
    /**
     * マイページトップ
     */
    public function index()
    {
        $user = Auth::user();
        
        // 最新の診断履歴5件
        $recentResults = $user->diagnoseResults()
            ->orderByPivot('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // 行ったお店の数
        $visitedStoresCount = $user->visitedStores()->count();
        
        // 傾向データ（円グラフ用）
        $trendData = $this->calculateTrend($user);
        
        return view('mypage.index', compact('user', 'recentResults', 'visitedStoresCount', 'trendData'));
    }

    /**
     * 診断履歴一覧
     */
    public function history()
    {
        $user = Auth::user();
        
        $results = $user->diagnoseResults()
            ->orderByPivot('created_at', 'desc')
            ->paginate(10);
        
        return view('mypage.history', compact('results'));
    }

    /**
     * 行ったお店一覧
     */
    public function stores()
    {
        $user = Auth::user();
        
        $stores = $user->visitedStores()
            ->orderByPivot('visited_at', 'desc')
            ->paginate(10);
        
        return view('mypage.stores', compact('stores'));
    }

    /**
     * お店を追加
     */
    public function addStore(Request $request, Store $store)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'memo' => 'nullable|string|max:1000',
            'visited_at' => 'nullable|date',
        ]);
        
        // 既に登録済みでなければ追加
        if (!$user->visitedStores()->where('store_id', $store->id)->exists()) {
            $user->visitedStores()->attach($store->id, [
                'memo' => $validated['memo'] ?? null,
                'visited_at' => $validated['visited_at'] ?? now(),
            ]);
        }
        
        return back()->with('success', 'お店を追加しました！');
    }

    /**
     * お店のメモを更新
     */
    public function updateStore(Request $request, Store $store)
    {
        $user = Auth::user();
        
        // 所有権チェック：ユーザーが登録したお店のみ更新可能
        if (!$user->visitedStores()->where('store_id', $store->id)->exists()) {
            abort(403, 'このお店を更新する権限がありません。');
        }
        
        $validated = $request->validate([
            'memo' => 'nullable|string|max:1000',
            'visited_at' => 'nullable|date',
        ]);
        
        $user->visitedStores()->updateExistingPivot($store->id, [
            'memo' => $validated['memo'] ?? null,
            'visited_at' => $validated['visited_at'] ?? null,
        ]);
        
        return back()->with('success', 'メモを更新しました！');
    }

    /**
     * お店を削除
     */
    public function removeStore(Store $store)
    {
        $user = Auth::user();
        
        // 所有権チェック：ユーザーが登録したお店のみ削除可能
        if (!$user->visitedStores()->where('store_id', $store->id)->exists()) {
            abort(403, 'このお店を削除する権限がありません。');
        }
        
        $user->visitedStores()->detach($store->id);
        
        return back()->with('success', 'お店を削除しました。');
    }

    /**
     * 傾向グラフ（円グラフ）
     */
    public function trend()
    {
        $user = Auth::user();
        
        $trendData = $this->calculateTrend($user);
        
        return view('mypage.trend', compact('trendData'));
    }

    /**
     * ユーザーの診断傾向を計算
     */
    private function calculateTrend($user): array
    {
        $results = $user->diagnoseResults()->get();
        
        if ($results->isEmpty()) {
            return [
                'labels' => [],
                'values' => [],
                'total' => 0,
            ];
        }
        
        // primary_typeでグループ化してカウント
        $counts = $results->groupBy('primary_type')
            ->map(fn($items) => $items->count())
            ->sortDesc();
        
        // ラベルの日本語化
        $labels = config('diagnose_results', []);
        
        return [
            'labels' => $counts->keys()->map(function ($type) use ($labels) {
                return $labels[$type]['label'] ?? $type;
            })->values()->toArray(),
            'values' => $counts->values()->toArray(),
            'total' => $results->count(),
        ];
    }
}
