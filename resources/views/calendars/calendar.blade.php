<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FullCalendar</title>
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.jsx', 'resources/js/calendar.js'])
</head>
<body >
    <div id="mic-icon-container" style="display: flex; justify-content: center; align-items: center;">
        <img id="mic-icon" src="/images/Group 38.png" alt="Mic Icon" style="width: 50px; cursor: pointer;" />
    </div>

    <div id='calendar' ></div>

    <!-- 予定追加モーダル -->
    <div id="modal-add" class="modal">
        <div class="modal-contents">
            <form method="POST" action="{{ route('create') }}">
                @csrf
                <input id="new-id" type="hidden" name="id" />
                <label for="event_title">タイトル</label>
                <input id="new-event_title" class="input-title" type="text" name="event_title" placeholder="イベントのタイトル" />
                <label for="start_date">開始日時</label>
                <input id="new-start_date" class="input-date" type="date" name="start_date" />
                <label for="end_date">終了日時</label>
                <input id="new-end_date" class="input-date" type="date" name="end_date" />
                <label for="event_body">内容</label>
                <textarea id="new-event_body" name="event_body" rows="4" placeholder="イベントの詳細"></textarea>
                <label for="event_color">背景色</label>
                <select id="new-event_color" name="event_color">
                    <option value="blue" selected>青</option>
                    <option value="green">緑</option>
                </select>
                <div class="modal-actions">
                    <button type="button" class="modal-btn cancel-btn" onclick="closeAddModal()">キャンセル</button>
                    <button type="submit" class="modal-btn submit-btn">決定</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 予定編集モーダル -->
    <div id="modal-update" class="modal">
        <div class="modal-contents">
        <h2>予定を編集</h2>
        <form method="POST" action="{{ route('update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" id="id" name="id" />

            <label for="event_title">タイトル</label>
            <input class="input-title" type="text" id="event_title" name="event_title" placeholder="イベントのタイトル" />

            <label for="start_date">開始日時</label>
            <input class="input-date" type="date" id="start_date" name="start_date" />

            <label for="end_date">終了日時</label>
            <input class="input-date" type="date" id="end_date" name="end_date" />

            <label for="event_body">内容</label>
            <textarea id="event_body" name="event_body" rows="3" placeholder="イベントの詳細"></textarea>

            <label for="event_color">背景色</label>
            <select id="event_color" name="event_color">
                <option value="blue">青</option>
                <option value="green">緑</option>
            </select>

            <div class="modal-actions">
                <button type="button" class="modal-btn cancel-btn" onclick="closeUpdateModal()">キャンセル</button>
                <button type="submit" class="modal-btn submit-btn">決定</button>
            </div>
        </form>

        <form id="delete-form" method="POST" action="{{ route('delete') }}">
            @csrf
            @method('DELETE')
            <input type="hidden" id="delete-id" name="id" />
            <button class="delete-btn" type="button" onclick="deleteEvent()">削除</button>
        </form>
        </div>
    </div>

    <!-- 感情分析モーダル -->
    <div id="modal-analyze" class="modal">
        <div class="modal-contents">
            <h2>感情を分析</h2>
            <form method="POST" action="{{ route('analyze') }}">
                @csrf
                <label for="emotion_text">感情を記録する文章:</label>
                <textarea id="emotion_text" name="text" rows="4" required></textarea>
                <label for="emotion_date">日付:</label>
                <input id="emotion_date" name="date" type="date" value="{{ now()->format('Y-m-d') }}" required />
                <div class="modal-actions">
                    <button type="button" class="modal-btn cancel-btn" onclick="closeAnalyzeModal()">キャンセル</button>
                    <button type="submit" class="modal-btn submit-btn">分析して保存</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 録音モーダル -->
    <div id="modal-record" class="modal">
        <div class="modal-contents">
            <h2>音声録音</h2>
            <button id="record-toggle-btn" onclick="toggleSpeechRecognition()" class="modal-btn record-btn">🎙️ 録音開始</button>
            <p id="record-modal-status">録音待機中...</p>
            <canvas id="audio-visualizer" width="500" height="100"></canvas>
            <button type="button" class="modal-btn close-btn" onclick="closeRecordModal()">閉じる</button>
        </div>
    </div>

    <footer style="text-align: center; margin-bottom: 20px;">
        <nav>
            <a href="{{ route('dashboard') }}" style="margin-right: 15px;">Dashboard</a>
        </nav>
    </footer>
</body>
</html>

<style scoped>

body {
        background-color: #b4eeb4; /* 背景を緑色に設定 */
        font-family: 'Nunito', sans-serif;
        margin: 0;
        padding: 0;
    }

#calendar {
        background-color: #b4eeb4; /* カレンダー全体にも緑色の背景を設定 */
    }

.modal {
    display: none;
    justify-content: center;
    align-items: center;
    position: absolute;
    z-index: 10;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    height: 100%;
    width: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    transition: opacity 0.3s ease;
}

.modal-contents {
    background-color: #f7e74b;
    height: 500px;
    width: 600px;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

input, textarea, select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
}

.input-title, .input-date, textarea {
    display: block;
    width: 100%;
    margin-bottom: 20px;
}

.textarea {
    resize: none;
}

select {
    width: 100%;
    margin-bottom: 20px;
}

.modal-actions {
    display: flex;
    justify-content: space-between;
}

.modal-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.cancel-btn {
    background-color: #ddd;
}

.submit-btn {
    background-color: #4CAF50;
    color: white;
}

.cancel-btn:hover {
    background-color: #bbb;
}

.submit-btn:hover {
    background-color: #45a049;
}

.record-btn {
    background-color: #f39c12;
    color: white;
    font-size: 18px;
}

.record-btn:hover {
    background-color: #e67e22;
}

.close-btn {
    background-color: #e74c3c;
    color: white;
}

.close-btn:hover {
    background-color: #c0392b;
}

#audio-visualizer {
    border: 2px solid #eee;
    margin-top: 10px;
}

.delete-btn {
        background-color: #e74c3c; /* 削除ボタンの背景色（赤） */
        color: white; /* ボタンの文字色 */
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease; /* ホバー時に背景色が変わる効果 */
    }

    .delete-btn:hover {
        background-color: #c0392b; /* ホバー時に暗くなる赤色 */
    }
</style>
