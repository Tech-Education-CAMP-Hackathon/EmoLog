<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Services\SentimentAnalysisService;

class EventController extends Controller
{
    protected $sentimentService;

    public function __construct(SentimentAnalysisService $sentimentService)
    {
        $this->sentimentService = $sentimentService;
    }

    public function show()
    {
        return view("calendars/calendar");
    }

    public function create(Request $request, Event $event)
    {
        $request->validate([
            'event_title' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'event_color' => 'required',
        ]);

        $event->event_title = $request->input('event_title');
        $event->event_body = $request->input('event_body');
        $event->start_date = $request->input('start_date');
        $event->end_date = date("Y-m-d", strtotime("{$request->input('end_date')} +1 day"));
        $event->event_color = $request->input('event_color');
        $event->event_border_color = $request->input('event_color');
        $event->save();

        return redirect(route("show"));
    }

    public function get(Request $request, Event $event)
    {
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer'
        ]);

        $start_date = date('Y-m-d', $request->input('start_date') / 1000);
        $end_date = date('Y-m-d', $request->input('end_date') / 1000);

        return $event->query()
            ->select(
                'id',
                'event_title as title',
                'event_body as description',
                'start_date as start',
                'end_date as end',
                'event_color as backgroundColor',
                'event_border_color as borderColor'
            )
            ->where('end_date', '>', $start_date)
            ->where('start_date', '<', $end_date)
            ->get();
    }

    public function update(Request $request, Event $event)
    {
        $input = new Event();

        $input->event_title = $request->input('event_title');
        $input->event_body = $request->input('event_body');
        $input->start_date = $request->input('start_date');
        $input->end_date = date("Y-m-d", strtotime("{$request->input('end_date')} +1 day"));
        $input->event_color = $request->input('event_color');
        $input->event_border_color = $request->input('event_color');

        $event->find($request->input('id'))->fill($input->attributesToArray())->save();

        return redirect(route("show"));
    }

    public function delete(Request $request, Event $event)
    {
        $event->find($request->input('id'))->delete();

        return redirect(route("show"));
    }

    // 新しい感情分析メソッド
    public function analyzeAndSave(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'date' => 'required|date',
        ]);

        // OpenAIを使って感情分析を実行
        $analysisResult = $this->sentimentService->analyzeSentiment($request->input('text'));

        // 取得した感情の分類
        $emotion = $analysisResult['emotion'] ?? 'Neutral'; // デフォルトはNeutral
        $confidence = $analysisResult['confidence'] ?? 0.5;
        $intensity = $analysisResult['intensity'] ?? 0.5;

        // 感情ごとの背景色を設定
        $emotionColors = [
            '喜び' => 'orange',      // 喜び → 黄色
            '悲しみ' => 'blue',    // 悲しみ → 青
            '怒り' => 'red',       // 怒り → 赤
            '怖さ' => 'purple',     // 怖さ → 紫
            '驚き' => 'green',  // 驚き → 緑
            'Neutral' => 'gray',    // 中立 → 灰色
        ];

        // 存在しない感情が来た場合はデフォルトの灰色
        $eventColor = $emotionColors[$emotion] ?? 'gray';

        // イベントをカレンダーに保存
        Event::create([
            'event_title' => ucfirst($emotion),
            'event_body' => "信頼度: $confidence, 強度: $intensity",
            'start_date' => $request->input('date'),
            'end_date' => $request->input('date'),
            'event_color' => $eventColor,
            'event_border_color' => $eventColor,
        ]);

        return redirect(route('show'))->with('success', '感情がカレンダーに記録されました。');
    }
}
