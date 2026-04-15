<?php

namespace Tests\Unit;

use App\Services\DiagnoseService;
use Tests\TestCase;

class DiagnoseServiceTest extends TestCase
{
    private DiagnoseService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DiagnoseService();
    }

    // ── createSession ──

    public function test_create_session_returns_seed_and_questions(): void
    {
        $session = $this->service->createSession();

        $this->assertArrayHasKey('seed', $session);
        $this->assertArrayHasKey('questions', $session);
        $this->assertNotNull($session['seed']);
    }

    public function test_create_session_returns_five_questions(): void
    {
        $session = $this->service->createSession();

        $this->assertCount(5, $session['questions']);
    }

    public function test_create_session_always_includes_fixed_questions(): void
    {
        $session = $this->service->createSession(42);
        $ids = array_column($session['questions'], 'id');

        $this->assertContains('q1', $ids);
        $this->assertContains('q2', $ids);
    }

    public function test_create_session_questions_have_required_fields(): void
    {
        $session = $this->service->createSession(42);

        foreach ($session['questions'] as $q) {
            $this->assertArrayHasKey('id', $q);
            $this->assertArrayHasKey('text', $q);
            $this->assertArrayHasKey('choices', $q);
            $this->assertNotEmpty($q['choices']);
        }
    }

    public function test_create_session_same_seed_returns_same_questions(): void
    {
        $a = $this->service->createSession(12345);
        $b = $this->service->createSession(12345);

        $this->assertEquals(
            array_column($a['questions'], 'id'),
            array_column($b['questions'], 'id'),
        );
    }

    public function test_create_session_no_duplicate_question_ids(): void
    {
        $session = $this->service->createSession(99);
        $ids = array_column($session['questions'], 'id');

        $this->assertCount(count($ids), array_unique($ids));
    }

    public function test_create_session_includes_one_from_each_category(): void
    {
        $session = $this->service->createSession(42);
        $ids = array_column($session['questions'], 'id');

        $nonFixed = array_filter($ids, fn ($id) => !str_starts_with($id, 'q'));

        $categories = array_map(fn ($id) => substr($id, 0, 1), $nonFixed);

        $this->assertContains('A', $categories);
        $this->assertContains('B', $categories);
        $this->assertContains('C', $categories);
    }

    // ── score ──

    public function test_score_returns_expected_structure(): void
    {
        $answers = ['q1' => 'a', 'q2' => 'a', 'A1' => 'a', 'B1' => 'a', 'C1' => 'a'];
        $result = $this->service->score($answers);

        $this->assertArrayHasKey('primary', $result);
        $this->assertArrayHasKey('candidates', $result);
        $this->assertArrayHasKey('top5', $result);
        $this->assertArrayHasKey('scores_map', $result);
        $this->assertArrayHasKey('mood', $result);
    }

    public function test_score_primary_is_not_null(): void
    {
        $answers = ['q1' => 'a', 'q2' => 'c', 'A1' => 'b', 'B1' => 'c', 'C1' => 'a'];
        $result = $this->service->score($answers);

        $this->assertNotNull($result['primary']);
    }

    public function test_score_primary_is_valid_type(): void
    {
        $types = config('diagnose.types');
        $answers = ['q1' => 'a', 'q2' => 'c', 'A1' => 'b', 'B1' => 'c', 'C1' => 'a'];
        $result = $this->service->score($answers);

        $this->assertContains($result['primary'], $types);
    }

    public function test_score_top5_has_five_entries(): void
    {
        $answers = ['q1' => 'a', 'q2' => 'a', 'A1' => 'a', 'B1' => 'a', 'C1' => 'a'];
        $result = $this->service->score($answers);

        $this->assertCount(5, $result['top5']);
    }

    public function test_score_top5_entries_have_required_fields(): void
    {
        $answers = ['q1' => 'a', 'q2' => 'a', 'A1' => 'a', 'B1' => 'a', 'C1' => 'a'];
        $result = $this->service->score($answers);

        foreach ($result['top5'] as $entry) {
            $this->assertArrayHasKey('type', $entry);
            $this->assertArrayHasKey('score', $entry);
            $this->assertArrayHasKey('label', $entry);
        }
    }

    public function test_score_candidates_are_sorted_desc(): void
    {
        $answers = ['q1' => 'b', 'q2' => 'b', 'A1' => 'c', 'B1' => 'b', 'C1' => 'b'];
        $result = $this->service->score($answers);

        $scores = array_column($result['candidates'], 'score');
        $sorted = $scores;
        rsort($sorted);

        $this->assertEquals($sorted, $scores);
    }

    public function test_score_primary_matches_first_candidate(): void
    {
        $answers = ['q1' => 'a', 'q2' => 'c', 'A1' => 'e', 'B1' => 'e', 'C1' => 'e'];
        $result = $this->service->score($answers);

        if (!empty($result['candidates'])) {
            $this->assertEquals($result['primary'], $result['candidates'][0]['type']);
        }
    }

    public function test_score_candidates_within_width_of_max(): void
    {
        $width = config('diagnose.scoring.candidate_width', 4);
        $answers = ['q1' => 'a', 'q2' => 'a', 'A1' => 'a', 'B1' => 'a', 'C1' => 'a'];
        $result = $this->service->score($answers);

        if (empty($result['candidates'])) {
            $this->assertTrue(true);
            return;
        }

        $maxScore = $result['candidates'][0]['score'];
        foreach ($result['candidates'] as $c) {
            $this->assertLessThanOrEqual($width, $maxScore - $c['score']);
        }
    }

    // ── mood ──

    public function test_mood_lively_when_q1_is_a(): void
    {
        $result = $this->service->score(['q1' => 'a', 'q2' => 'a', 'A1' => 'a', 'B1' => 'a', 'C1' => 'a']);
        $this->assertEquals('lively', $result['mood']);
    }

    public function test_mood_chill_when_q1_is_b(): void
    {
        $result = $this->service->score(['q1' => 'b', 'q2' => 'a', 'A1' => 'a', 'B1' => 'a', 'C1' => 'a']);
        $this->assertEquals('chill', $result['mood']);
    }

    public function test_mood_silent_when_q1_is_c(): void
    {
        $result = $this->service->score(['q1' => 'c', 'q2' => 'a', 'A1' => 'a', 'B1' => 'a', 'C1' => 'a']);
        $this->assertEquals('silent', $result['mood']);
    }

    public function test_mood_light_when_q1_is_d(): void
    {
        $result = $this->service->score(['q1' => 'd', 'q2' => 'a', 'A1' => 'a', 'B1' => 'a', 'C1' => 'a']);
        $this->assertEquals('light', $result['mood']);
    }

    public function test_mood_strong_when_q1_is_e(): void
    {
        $result = $this->service->score(['q1' => 'e', 'q2' => 'a', 'A1' => 'a', 'B1' => 'a', 'C1' => 'a']);
        $this->assertEquals('strong', $result['mood']);
    }

    public function test_mood_null_when_q1_missing(): void
    {
        $result = $this->service->score(['q2' => 'a', 'A1' => 'a', 'B1' => 'a', 'C1' => 'a']);
        $this->assertNull($result['mood']);
    }

    // ── q2 multiplier ──

    public function test_q2_multiplier_amplifies_scores(): void
    {
        $onlyQ2 = $this->service->score(['q2' => 'c']);
        $onlyB1 = $this->service->score(['B1' => 'c']);

        $q2Max = max($onlyQ2['scores_map'] ?: [0]);
        $b1Max = max($onlyB1['scores_map'] ?: [0]);

        $this->assertGreaterThan($b1Max, $q2Max, 'q2 should produce higher scores due to multiplier');
    }

    // ── scores_map ──

    public function test_scores_map_contains_all_types(): void
    {
        $types = config('diagnose.types');
        $answers = ['q1' => 'a', 'q2' => 'a', 'A1' => 'a', 'B1' => 'a', 'C1' => 'a'];
        $result = $this->service->score($answers);

        foreach ($types as $type) {
            $this->assertArrayHasKey($type, $result['scores_map']);
        }
    }

    public function test_score_empty_answers_returns_null_primary_gracefully(): void
    {
        $result = $this->service->score([]);

        $this->assertNotNull($result['primary']);
        $this->assertIsArray($result['top5']);
    }

    // ── different answer patterns produce different results ──

    public function test_different_answers_produce_different_primaries(): void
    {
        $lively = $this->service->score(['q1' => 'a', 'q2' => 'a', 'A1' => 'a', 'B1' => 'a', 'C1' => 'a']);
        $dark = $this->service->score(['q1' => 'e', 'q2' => 'c', 'A1' => 'e', 'B1' => 'e', 'C1' => 'e']);

        $this->assertNotEquals(
            $lively['primary'],
            $dark['primary'],
            'Completely different answer patterns should yield different primaries'
        );
    }
}
