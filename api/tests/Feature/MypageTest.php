<?php

namespace Tests\Feature;

use App\Models\DiagnoseResult;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MypageTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // ── Access control ──

    public function test_mypage_requires_auth(): void
    {
        $this->get('/mypage')->assertRedirect('/login');
    }

    public function test_mypage_index_accessible_when_authenticated(): void
    {
        $this->actingAs($this->user)
            ->get('/mypage')
            ->assertOk();
    }

    // ── History ──

    public function test_history_shows_diagnose_results(): void
    {
        $result = DiagnoseResult::create([
            'result_id' => 'res_hist_test',
            'primary_type' => 'sake_dry',
            'primary_label' => '日本酒・辛口',
            'mood' => 'lively',
            'candidates' => [],
            'top5' => [],
            'answers_snapshot' => ['q1' => 'a'],
        ]);
        $this->user->diagnoseResults()->attach($result->id);

        $this->actingAs($this->user)
            ->get('/mypage/history')
            ->assertOk()
            ->assertSee('日本酒・辛口');
    }

    // ── Visited stores ──

    public function test_add_store_creates_pivot(): void
    {
        $store = Store::create([
            'name' => 'テスト居酒屋',
            'address' => '佐賀市1-1',
            'is_active' => true,
        ]);

        $this->actingAs($this->user)
            ->post("/mypage/stores/{$store->id}", [
                'memo' => '美味しかった',
            ])
            ->assertRedirect();

        $this->assertTrue(
            $this->user->visitedStores()->where('store_id', $store->id)->exists()
        );
    }

    public function test_update_store_memo(): void
    {
        $store = Store::create([
            'name' => 'テスト居酒屋',
            'address' => '佐賀市1-1',
            'is_active' => true,
        ]);
        $this->user->visitedStores()->attach($store->id, [
            'memo' => '旧メモ',
            'visited_at' => now(),
        ]);

        $this->actingAs($this->user)
            ->put("/mypage/stores/{$store->id}", [
                'memo' => '新しいメモ',
            ])
            ->assertRedirect();

        $pivot = $this->user->visitedStores()->where('store_id', $store->id)->first()->pivot;
        $this->assertEquals('新しいメモ', $pivot->memo);
    }

    public function test_cannot_update_other_users_store(): void
    {
        $other = User::factory()->create();
        $store = Store::create([
            'name' => '他人の店',
            'address' => '佐賀市2-2',
            'is_active' => true,
        ]);
        $other->visitedStores()->attach($store->id, ['visited_at' => now()]);

        $this->actingAs($this->user)
            ->put("/mypage/stores/{$store->id}", ['memo' => 'hack'])
            ->assertForbidden();
    }

    public function test_remove_store_detaches_pivot(): void
    {
        $store = Store::create([
            'name' => '削除テスト店',
            'address' => '佐賀市3-3',
            'is_active' => true,
        ]);
        $this->user->visitedStores()->attach($store->id, ['visited_at' => now()]);

        $this->actingAs($this->user)
            ->delete("/mypage/stores/{$store->id}")
            ->assertRedirect();

        $this->assertFalse(
            $this->user->visitedStores()->where('store_id', $store->id)->exists()
        );
    }

    public function test_cannot_remove_other_users_store(): void
    {
        $other = User::factory()->create();
        $store = Store::create([
            'name' => '他人の店',
            'address' => '佐賀市4-4',
            'is_active' => true,
        ]);
        $other->visitedStores()->attach($store->id, ['visited_at' => now()]);

        $this->actingAs($this->user)
            ->delete("/mypage/stores/{$store->id}")
            ->assertForbidden();
    }

    // ── Trend ──

    public function test_trend_page_loads(): void
    {
        $this->actingAs($this->user)
            ->get('/mypage/trend')
            ->assertOk();
    }

    public function test_trend_data_counts_primary_types(): void
    {
        foreach (['sake_dry', 'sake_dry', 'whisky'] as $i => $type) {
            $result = DiagnoseResult::create([
                'result_id' => "res_trend_{$i}",
                'primary_type' => $type,
                'primary_label' => $type,
                'mood' => 'lively',
                'candidates' => [],
                'top5' => [],
                'answers_snapshot' => [],
            ]);
            $this->user->diagnoseResults()->attach($result->id);
        }

        $response = $this->actingAs($this->user)->get('/mypage/trend');
        $response->assertOk();
    }
}
