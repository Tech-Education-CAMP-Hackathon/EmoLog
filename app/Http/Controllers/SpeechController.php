<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI;

class SpeechController extends Controller
{
    public function convert(Request $request)
    {
        // ファイルバリデーション
        $request->validate([
            'audio' => 'required|file|mimetypes:audio/webm,audio/wav,audio/mp4,audio/ogg,audio/mpeg'
        ]);

        // 音声ファイル情報を取得
        $uploadedFile = $request->file('audio');
        $mimeType = $uploadedFile->getMimeType();     // MIMEタイプを取得
        $extension = $uploadedFile->getClientOriginalExtension();  // 拡張子を取得

        // ターミナルに出力（デバッグ用）
        error_log("Uploaded audio file MIME type: {$mimeType}, Extension: {$extension}");

        // クライアントに形式情報を返す（デバッグ用）
        return response()->json([
            'mime_type' => $mimeType,
            'extension' => $extension
        ]);

        // 音声データの取得
        $audioContent = file_get_contents($uploadedFile->getRealPath());

        // Speech-to-Text API 呼び出し（OpenAI Whisper モデルを利用）
        $client = OpenAI::client(env('OPENAI_API_KEY'));
        $response = $client->audio()->transcribe([
            'model' => 'whisper-1',
            'file' => fopen($uploadedFile->getRealPath(), 'r'),
            'response_format' => 'text',
        ]);

        return response()->json(['text' => $response->text]);
    }
}
