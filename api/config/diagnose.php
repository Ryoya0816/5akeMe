<?php
return [
  'fallback_order' => ['SA-K','WW','RW','SH-I','SP','CB','SA-A','SH-M','SH-K','CT'],
  'weights' => ['q1'=>1.0,'q2'=>1.5],
  'top_band_delta' => 3,
  'mood_map' => [
    'スッキリ'=>1,'盛り上がりたい'=>1,
    'じっくり語りたい'=>2,'しっとり落ち着きたい'=>2,'静かに味わいたい'=>2,
  ],
  'questions' => [
    ['id'=>'q1','category'=>'fixed','text'=>'今の気分／テンションは？','options'=>[
      ['label'=>'スッキリ','scores'=>['SA-K'=>3,'WW'=>2,'SP'=>1,'CB'=>1]],
      ['label'=>'じっくり語りたい','scores'=>['RW'=>3,'SH-I'=>1,'SA-A'=>2,'SH-K'=>1]],
      ['label'=>'盛り上がりたい','scores'=>['SP'=>3,'CB'=>3,'WW'=>1,'CT'=>1]],
      ['label'=>'しっとり落ち着きたい','scores'=>['SA-A'=>3,'SH-K'=>2,'RW'=>1]],
      ['label'=>'静かに味わいたい','scores'=>['SH-K'=>3,'SA-A'=>2,'RW'=>1]],
    ]],
    ['id'=>'q2','category'=>'fixed','text'=>'お酒に求めるものは？','options'=>[
      ['label'=>'香り','scores'=>['WW'=>3,'SP'=>3,'CT'=>1,'SA-A'=>2]],
      ['label'=>'コク','scores'=>['RW'=>3,'SH-I'=>1,'SH-K'=>2,'SA-A'=>1]],
      ['label'=>'スッキリ感','scores'=>['SA-K'=>3,'SH-M'=>3,'WW'=>1,'CB'=>1]],
      ['label'=>'温めてほっと','scores'=>['SH-K'=>3,'SA-A'=>3,'SH-I'=>1,'SA-K'=>2]],
      ['label'=>'飲みやすさ','scores'=>['SA-A'=>3,'WW'=>2,'CT'=>2,'SP'=>1]],
    ]],
    // A/B/Cも本当は入れる。最小動作だけなら省略でもOK（まずはq1/q2だけ返せるように）
    ['id'=>'qA1','category'=>'A','text'=>'今回の主役は？','options'=>[
      ['label'=>'魚','scores'=>['SA-K'=>3,'WW'=>3,'SP'=>1,'SA-A'=>1]],
      ['label'=>'肉','scores'=>['RW'=>3,'SH-I'=>1,'CB'=>2,'SH-M'=>1]],
      ['label'=>'揚げ物','scores'=>['CB'=>3,'SP'=>3,'SA-K'=>1]],
      ['label'=>'濃い味','scores'=>['RW'=>3,'SH-I'=>1,'SA-A'=>1,'SH-K'=>2]],
      ['label'=>'野菜','scores'=>['WW'=>3,'SA-K'=>2,'SP'=>1]],
    ]],
    ['id'=>'qB1','category'=>'B','text'=>'休日の過ごし方は？','options'=>[
      ['label'=>'アウトドア','scores'=>['CB'=>3,'SA-K'=>2]],
      ['label'=>'家でまったり','scores'=>['SA-A'=>3,'SH-K'=>2]],
      ['label'=>'旅行','scores'=>['SP'=>3,'WW'=>2]],
      ['label'=>'街歩き','scores'=>['WW'=>3,'RW'=>1]],
      ['label'=>'趣味没頭','scores'=>['RW'=>2,'SA-K'=>1,'SH-K'=>1]],
    ]],
    ['id'=>'qC1','category'=>'C','text'=>'自分にとってのお酒とは？','options'=>[
      ['label'=>'ご褒美','scores'=>['SP'=>2,'CT'=>1,'SA-A'=>2]],
      ['label'=>'潤滑油','scores'=>['SH-M'=>3,'SA-K'=>1,'CB'=>1]],
      ['label'=>'趣味','scores'=>['RW'=>3,'SA-K'=>1,'SH-I'=>1]],
      ['label'=>'冒険','scores'=>['CB'=>3,'SP'=>2,'CT'=>1]],
      ['label'=>'社交','scores'=>['SP'=>2,'WW'=>1,'CB'=>1]],
    ]],
  ],
];
