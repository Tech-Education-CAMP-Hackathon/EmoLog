<?php

namespace App\Services;

use OpenAI;

class SentimentAnalysisService
{
    protected $openai;

    public function __construct()
    {
        $this->openai = OpenAI::client(env('OPENAI_API_KEY'));
    }

    public function analyzeSentiment($text)
    {
        $response = $this->openai->chat()->create([
            'model' => 'gpt-3.5-turbo', // ✅ 修正: 適切なチャットモデルを使用
            'messages' => [
                ['role' => 'system', 'content' => 'あなたは文章の感情分析を行うAIです。'],
                ['role' => 'user', 'content' => "以下の文章の感情を推定し、次のJSON形式で返してください:\n\n文章: \"$text\"\n\n結果形式:\n{\n    \"emotion\": \"感情名\",\n    \"confidence\": 感情の信頼度 (0-1),\n    \"intensity\": 感情の強さ (0-1)\n}"]
            ],
            'max_tokens' => 100,
            'temperature' => 0.7,
        ]);


        // レスポンスの解析
        $result = json_decode($response['choices'][0]['message']['content'], true);

        return $result;
    }
}
