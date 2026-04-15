<?php

namespace Tests\Feature;

use App\Models\DiagnoseFeedback;
use App\Models\DiagnoseResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DiagnoseApiTest extends TestCase
{
    use RefreshDatabase;

    // ── POST /api/diagnose/start ──

    public function test_start_returns_questions(): void
    {
        $response = $this->postJson('/api/diagnose/start');

        $response->assertOk()
            ->assertJsonStructure([
                'seed',
                'questions' => [
                    '*' => ['id', 'text', 'choices'],
                ],
            ]);

        $this->assertCount(5, $response->json('questions'));
    }

    public function test_start_with_seed_returns_deterministic_questions(): void
    {
        $a = $this->postJson('/api/diagnose/start', ['seed' => 42]);
        $b = $this->postJson('/api/diagnose/start', ['seed' => 42]);

        $this->assertEquals(
            collect($a->json('questions'))->pluck('id')->toArray(),
            collect($b->json('questions'))->pluck('id')->toArray(),
        );
    }

    public function test_start_validates_seed_is_integer(): void
    {
        $response = $this->postJson('/api/diagnose/start', ['seed' => 'abc']);

        $response->assertServerError();
    }

    // ── POST /api/diagnose/score ──

    public function test_score_returns_result(): void
    {
        Http::fake(['*/score' => Http::response(null, 500)]);

        $response = $this->postJson('/api/diagnose/score', [
            'answers' => [
                'q1' => 'a',
                'q2' => 'c',
                'A1' => 'b',
                'B1' => 'c',
                'C1' => 'a',
            ],
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'result_id',
                'result' => [
                    'primary',
                    'primary_label',
                    'candidates',
                    'top5',
                    'mood',
                ],
            ]);
    }

    public function test_score_saves_to_database(): void
    {
        Http::fake(['*/score' => Http::response(null, 500)]);

        $response = $this->postJson('/api/diagnose/score', [
            'answers' => [
                'q1' => 'a',
                'q2' => 'c',
                'A1' => 'b',
                'B1' => 'c',
                'C1' => 'a',
            ],
        ]);

        $response->assertOk();

        $resultId = $response->json('result_id');
        $this->assertDatabaseHas('diagnose_results', [
            'result_id' => $resultId,
        ]);
    }

    public function test_score_requires_answers(): void
    {
        $response = $this->postJson('/api/diagnose/score', []);

        $response->assertStatus(422);
    }

    public function test_score_rejects_empty_answers(): void
    {
        $response = $this->postJson('/api/diagnose/score', [
            'answers' => [],
        ]);

        $response->assertStatus(422);
    }

    public function test_score_filters_disallowed_answer_keys(): void
    {
        Http::fake(['*/score' => Http::response(null, 500)]);

        $response = $this->postJson('/api/diagnose/score', [
            'answers' => [
                'q1' => 'a',
                'q2' => 'a',
                'A1' => 'a',
                'B1' => 'a',
                'C1' => 'a',
                'EVIL' => 'x',
            ],
        ]);

        $response->assertOk();

        $result = DiagnoseResult::where('result_id', $response->json('result_id'))->first();
        $this->assertArrayNotHasKey('EVIL', $result->answers_snapshot);
    }

    public function test_score_result_id_starts_with_res_prefix(): void
    {
        Http::fake(['*/score' => Http::response(null, 500)]);

        $response = $this->postJson('/api/diagnose/score', [
            'answers' => ['q1' => 'a', 'q2' => 'a', 'A1' => 'a', 'B1' => 'a', 'C1' => 'a'],
        ]);

        $this->assertStringStartsWith('res_', $response->json('result_id'));
    }

    public function test_score_falls_back_to_php_when_python_fails(): void
    {
        Http::fake(['*/score' => Http::response(null, 500)]);

        $response = $this->postJson('/api/diagnose/score', [
            'answers' => ['q1' => 'a', 'q2' => 'a', 'A1' => 'a', 'B1' => 'a', 'C1' => 'a'],
        ]);

        $response->assertOk();
        $this->assertNotNull($response->json('result.primary'));
    }

    // ── GET /diagnose/result/{result_id} ──

    public function test_show_result_page_renders(): void
    {
        $result = DiagnoseResult::create([
            'result_id' => 'res_testrender123',
            'primary_type' => 'sake_dry',
            'primary_label' => '日本酒・辛口',
            'mood' => 'lively',
            'candidates' => [['type' => 'sake_dry', 'score' => 10, 'label' => '日本酒・辛口']],
            'top5' => [['type' => 'sake_dry', 'score' => 10, 'label' => '日本酒・辛口']],
            'answers_snapshot' => ['q1' => 'a', 'q2' => 'c'],
        ]);

        $response = $this->get("/diagnose/result/{$result->result_id}");

        $response->assertOk();
    }

    public function test_show_result_404_for_unknown_id(): void
    {
        $response = $this->get('/diagnose/result/res_nonexistent999');

        $response->assertNotFound();
    }

    public function test_show_result_attaches_to_authenticated_user(): void
    {
        $user = User::factory()->create();

        $result = DiagnoseResult::create([
            'result_id' => 'res_attach_test',
            'primary_type' => 'craft_beer',
            'primary_label' => 'クラフトビール',
            'mood' => 'lively',
            'candidates' => [],
            'top5' => [],
            'answers_snapshot' => ['q1' => 'a'],
        ]);

        $this->actingAs($user)->get("/diagnose/result/{$result->result_id}");

        $this->assertTrue(
            $user->diagnoseResults()->where('diagnose_result_id', $result->id)->exists()
        );
    }

    // ── Feedback ──

    public function test_feedback_store_saves_rating(): void
    {
        $result = DiagnoseResult::create([
            'result_id' => 'res_fb_test',
            'primary_type' => 'whisky',
            'primary_label' => 'ウイスキー',
            'mood' => 'silent',
            'candidates' => [],
            'top5' => [],
            'answers_snapshot' => ['q1' => 'c', 'q2' => 'c'],
        ]);

        $response = $this->postJson("/api/diagnose/feedback/{$result->result_id}", [
            'rating' => 4,
            'comment' => '当たってた！',
        ]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('diagnose_feedbacks', [
            'diagnose_result_id' => $result->id,
            'rating' => 4,
        ]);
    }

    public function test_feedback_rejects_duplicate(): void
    {
        $result = DiagnoseResult::create([
            'result_id' => 'res_fb_dup',
            'primary_type' => 'cocktail',
            'primary_label' => 'カクテル',
            'mood' => 'lively',
            'candidates' => [],
            'top5' => [],
            'answers_snapshot' => ['q1' => 'a'],
        ]);

        DiagnoseFeedback::create([
            'diagnose_result_id' => $result->id,
            'rating' => 3,
            'answers_snapshot' => ['q1' => 'a'],
            'result_type' => 'cocktail',
            'mood' => 'lively',
        ]);

        $response = $this->postJson("/api/diagnose/feedback/{$result->result_id}", [
            'rating' => 5,
        ]);

        $response->assertStatus(409);
    }

    public function test_feedback_validates_rating_range(): void
    {
        $result = DiagnoseResult::create([
            'result_id' => 'res_fb_val',
            'primary_type' => 'wine_red',
            'primary_label' => '赤ワイン',
            'mood' => 'chill',
            'candidates' => [],
            'top5' => [],
            'answers_snapshot' => ['q1' => 'b'],
        ]);

        $response = $this->postJson("/api/diagnose/feedback/{$result->result_id}", [
            'rating' => 6,
        ]);

        $response->assertStatus(422);
    }

    public function test_feedback_404_for_unknown_result(): void
    {
        $response = $this->postJson('/api/diagnose/feedback/res_unknown', [
            'rating' => 3,
        ]);

        $response->assertNotFound();
    }

    public function test_feedback_check_returns_status(): void
    {
        $result = DiagnoseResult::create([
            'result_id' => 'res_fb_chk',
            'primary_type' => 'sake_dry',
            'primary_label' => '日本酒・辛口',
            'mood' => 'lively',
            'candidates' => [],
            'top5' => [],
            'answers_snapshot' => ['q1' => 'a'],
        ]);

        $response = $this->getJson("/api/diagnose/feedback/{$result->result_id}/check");
        $response->assertOk()->assertJson(['has_feedback' => false]);

        DiagnoseFeedback::create([
            'diagnose_result_id' => $result->id,
            'rating' => 5,
            'answers_snapshot' => ['q1' => 'a'],
            'result_type' => 'sake_dry',
            'mood' => 'lively',
        ]);

        $response = $this->getJson("/api/diagnose/feedback/{$result->result_id}/check");
        $response->assertOk()->assertJson(['has_feedback' => true, 'rating' => 5]);
    }
}
