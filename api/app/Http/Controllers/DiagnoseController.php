<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DiagnoseService;

class DiagnoseController extends Controller {
  public function __construct(private DiagnoseService $svc) {}
  public function createSession(Request $req){
    $session = $this->svc->createSession($req->integer('seed'));
    $all = collect(config('diagnose.questions'))->keyBy('id');
    return response()->json([
      'seed'=>$session['seed'],
      'questions'=>collect($session['question_ids'])->map(fn($id)=>$all[$id])->values()->all()
    ]);
  }
  public function answer(Request $req){
    $answers = $req->validate([
      'answers'=>'required|array|size:5',
      'answers.*.id'=>'required|string',
      'answers.*.label'=>'required|string',
    ])['answers'];
    $res = $this->svc->score($answers);
    $labels = ['SA-K'=>'日本酒・辛口','SA-A'=>'日本酒・甘口','SH-I'=>'焼酎・芋','SH-M'=>'焼酎・麦','SH-K'=>'焼酎・米','RW'=>'赤ワイン','WW'=>'白ワイン','SP'=>'スパークリング','CB'=>'クラフトビール','CT'=>'甘いカクテル'];
    arsort($res['scores']); $chart=[];
    foreach(array_slice($res['scores'],0,3,true) as $code=>$v){ $chart[]=['label'=>$labels[$code]??$code,'value'=>$v]; }
    return response()->json([
      'type'=>$res['primary'],
      'candidates'=>$res['candidates'],
      'scores'=>$res['scores'],
      'chartData'=>$chart,
      'mood'=>$res['mood'],
    ]);
  }
}
