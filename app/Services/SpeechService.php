<?php

namespace App\Services;

use OpenAI;
use Illuminate\Http\Request;

class SpeechService
{
    protected $openai;

    public function __construct()
    {
        $this->openai = OpenAI::client(env('OPENAI_API_KEY'));
    }

    public function transcribeAudio(Request $request)
    {
        // 音声データ取得
        $uploadedFile = $request->file('audio');

        try {
            $response = $this->openai->audio()->transcribe([
                'model' => 'whisper-1',
                'file' => fopen($uploadedFile->getRealPath(), 'r'),
                'response_format' => 'text',
            ]);

            // デバッグ: レスポンス全体を確認
            echo "OpenAI API レスポンス:\n";
            var_dump($response);  // これでレスポンスの全内容を確認
            echo "\n";

            // テキスト部分を取得
            $transcribedText = $response->text ?? 'Undefined response text';
            echo "文字起こし結果: $transcribedText\n";

            return $transcribedText;
        } catch (\Exception $e) {
            // エラーが発生した場合のログ出力
            echo "OpenAI API との通信中にエラーが発生しました:\n";
            echo $e->getMessage();
            return response()->json(['error' => '音声解析中にエラーが発生しました。'], 500);
        }
    }
}
