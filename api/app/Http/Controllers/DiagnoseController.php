<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DiagnoseService;

class DiagnoseController extends Controller
{
    public function __construct(private DiagnoseService $svc) {}

    /** GET /api/diagnose/questions */
    public function questions()
    {
        // config('diagnose.questions') が空なら Service の defaultConf が使われる想定
        $all = $this->svc->getQuestions();
        return response()->json(['questions' => $all]);
    }

    /** POST /api/diagnose/session  { seed?: int } */
    public function createSession(Request $req)
    {
        $session = $this->svc->createSession($req->integer('seed'));
        // フロントは ID 配列を期待
        return response()->json([
            'seed' => $session['seed'],
            'question_ids' => $session['question_ids'],
        ]);
    }

    /** POST /api/diagnose/score { answers: [{id,label}*5] } */
    public function score(Request $req)
    {
        $answers = $req->validate([
            'answers' => ['required','array','size:5'],
            'answers.*.id' => ['required','string'],
            'answers.*.label' => ['required','string'],
        ])['answers'];

        $res = $this->svc->score($answers);

        // ついでにトップ3のチャート用データも返す（既存ロジックを流用）
        $labels = [
            'SA-K'=>'日本酒・辛口','SA-A'=>'日本酒・甘口','SH-I'=>'焼酎・芋','SH-M'=>'焼酎・麦','SH-K'=>'焼酎・米',
            'RW'=>'赤ワイン','WW'=>'白ワイン','SP'=>'スパークリング','CB'=>'クラフトビール','CT'=>'甘いカクテル'
        ];
        arsort($res['scores']); $chart = [];
        foreach (array_slice($res['scores'], 0, 3, true) as $code => $v) {
            $chart[] = ['label' => $labels[$code] ?? $code, 'value' => $v];
        }

        return response()->json([
            'scores'      => $res['scores'],
            'candidates'  => $res['candidates'],
            'primary'     => $res['primary'],
            'mood'        => $res['mood'],
            'chartData'   => $chart,
        ]);
    }
}
