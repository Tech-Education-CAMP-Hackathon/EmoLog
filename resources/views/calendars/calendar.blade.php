<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FullCalendar</title>
    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.jsx']) <!-- vite用の記述忘れずに -->
</head>
<body>
    <!-- カレンダー表示 -->
    <div id='calendar'></div>

    <!-- 予定追加モーダル -->
    <div id="modal-add" class="modal">
        <div class="modal-contents">
            <form method="POST" action="{{ route('create') }}">
                @csrf
                <input id="new-id" type="hidden" name="id" value="" />
                <label for="event_title">タイトル</label>
                <input id="new-event_title" class="input-title" type="text" name="event_title" value="" />
                <label for="start_date">開始日時</label>
                <input id="new-start_date" class="input-date" type="date" name="start_date" value="" />
                <label for="end_date">終了日時</label>
                <input id="new-end_date" class="input-date" type="date" name="end_date" value="" />
                <label for="event_body">内容</label>
                <textarea id="new-event_body" name="event_body" rows="3"></textarea>
                <label for="event_color">背景色</label>
                <select id="new-event_color" name="event_color">
                    <option value="blue" selected>青</option>
                    <option value="green">緑</option>
                </select>
                <button type="button" onclick="closeAddModal()">キャンセル</button>
                <button type="submit">決定</button>
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
                <textarea id="emotion_text" name="text" rows="3" required></textarea>
                <label for="emotion_date">日付:</label>
                <input id="emotion_date" name="date" type="date" value="{{ now()->format('Y-m-d') }}" required />
                <button type="button" onclick="closeAnalyzeModal()">キャンセル</button>
                <button type="submit">分析して保存</button>
            </form>
        </div>
    </div>

    <!-- 予定編集モーダル -->
    <div id="modal-update" class="modal">
        <div class="modal-contents">
            <form method="POST" action="{{ route('update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" id="id" name="id" value="" />
                <label for="event_title">タイトル</label>
                <input class="input-title" type="text" id="event_title" name="event_title" value="" />
                <label for="start_date">開始日時</label>
                <input class="input-date" type="date" id="start_date" name="start_date" value="" />
                <label for="end_date">終了日時</label>
                <input class="input-date" type="date" id="end_date" name="end_date" value="" />
                <label for="event_body">内容</label>
                <textarea id="event_body" name="event_body" rows="3"></textarea>
                <label for="event_color">背景色</label>
                <select id="event_color" name="event_color">
                    <option value="blue">青</option>
                    <option value="green">緑</option>
                </select>
                <button type="button" onclick="closeUpdateModal()">キャンセル</button>
                <button type="submit">決定</button>
            </form>

            <form id="delete-form" method="post" action="{{ route('delete') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" id="delete-id" name="id" value="" />
                <button class="delete" type="button" onclick="deleteEvent()">削除</button>
            </form>
        </div>
    </div>

    <button onclick="openAnalyzeModal()">感情を記録する</button>

    <script>
        function openAnalyzeModal() {
            document.getElementById('modal-analyze').style.display = 'flex';
        }

        function closeAnalyzeModal() {
            document.getElementById('modal-analyze').style.display = 'none';
        }
    </script>

</body>
</html>

<style scoped>
/* モーダルのオーバーレイ */
.modal{
    display: none; /* モーダル開くとflexに変更 */
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
    background-color: rgba(0,0,0,0.5);
}

/* モーダル */
.modal-contents{
    background-color: white;
    height: 400px;
    width: 600px;
    padding: 20px;
}

/* 入力フィールド */
input, textarea, select{
    padding: 2px;
    border: 1px solid black;
    border-radius: 5px;
}
.input-title, .input-date, textarea {
    display: block;
    width: 80%;
    margin: 0 0 20px;
}
.input-date {
    width: 27%;
}
textarea {
    resize: none;
}
select {
    display: block;
    width: 20%;
    margin-bottom: 20px;
}
</style>
