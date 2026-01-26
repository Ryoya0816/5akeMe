<?php

namespace App\Http\Controllers;

use App\Models\StoreReport;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreReportController extends Controller
{
    /**
     * 店舗情報の報告を保存
     */
    public function store(Request $request, int $storeId)
    {
        // 店舗の存在確認
        $store = Store::findOrFail($storeId);

        // バリデーション
        $validated = $request->validate([
            'update_types'   => 'nullable|array|max:10',
            'update_types.*' => 'string|max:50|alpha_dash',
            'detail'         => 'required|string|min:5|max:2000',
        ]);

        // 報告を保存
        StoreReport::create([
            'store_id'     => $store->id,
            'update_types' => $validated['update_types'] ?? [],
            'detail'       => $validated['detail'],
            'status'       => 'pending',
        ]);

        return redirect()
            ->route('store.detail', $store->id)
            ->with('reported', true);
    }
}
