import axios from "axios";
import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

// グローバル変数をwindowに登録
window.isRecording = false;
window.mediaRecorder = null;
window.audioChunks = [];
window.audioContext = null;
window.animationId = null;

const calendarEl = document.getElementById("calendar");

function formatDate(date, pos) {
    const dt = new Date(date);
    if (pos === "end") {
        dt.setDate(dt.getDate() - 1);
    }
    return dt.getFullYear() + '-' + ('0' + (dt.getMonth() + 1)).slice(-2) + '-' + ('0' + dt.getDate()).slice(-2);
}

if (calendarEl) {
    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: "dayGridMonth",
        customButtons: {
            eventAddButton: {
                text: '予定を追加',
                click: function () {
                    document.getElementById("new-id").value = "";
                    document.getElementById("new-event_title").value = "";
                    document.getElementById("new-start_date").value = "";
                    document.getElementById("new-end_date").value = "";
                    document.getElementById("new-event_body").value = "";
                    document.getElementById("new-event_color").value = "blue";
                    document.getElementById('modal-add').style.display = 'flex';
                }
            },
            emotionRecordButton: {
                text: '感情を記録する',
                click: function () {
                    openAnalyzeModal();
                }
            },
            openRecordButton: {
                text: '録音モードを開く',
                click: function () {
                    openRecordModal();
                }
            }
        },

        headerToolbar: {
            start: "prev,next today",
            center: "title",
            end: "dayGridMonth,timeGridWeek,eventAddButton,emotionRecordButton,openRecordButton",
        },
        height: "auto",
        selectable: true,
        select: function (info) {
            document.getElementById("new-id").value = "";
            document.getElementById("new-event_title").value = "";
            document.getElementById("new-start_date").value = formatDate(info.start);
            document.getElementById("new-end_date").value = formatDate(info.end, "end");
            document.getElementById("new-event_body").value = "";
            document.getElementById("new-event_color").value = "blue";
            document.getElementById('modal-add').style.display = 'flex';
        },

        events: function (info, successCallback, failureCallback) {
            axios.post("/calendar/get", {
                start_date: info.start.valueOf(),
                end_date: info.end.valueOf(),
            })
                .then((response) => {
                    calendar.removeAllEvents();
                    successCallback(response.data);
                })
                .catch(() => {
                    alert("登録に失敗しました。");
                });
        },

        eventClick: function (info) {
            document.getElementById("id").value = info.event.id;
            document.getElementById("delete-id").value = info.event.id;
            document.getElementById("event_title").value = info.event.title;
            document.getElementById("start_date").value = formatDate(info.event.start);
            document.getElementById("end_date").value = formatDate(info.event.end, "end");
            document.getElementById("event_body").value = info.event.extendedProps.description;
            document.getElementById("event_color").value = info.event.backgroundColor;
            document.getElementById('modal-update').style.display = 'flex';
        },
    });

    window.deleteEvent = function () {
        'use strict'

        if (confirm('削除すると復元できません。\n本当に削除しますか？')) {
            document.getElementById('delete-form').submit();
        }
    }


    calendar.render();

    window.closeAddModal = function () {
        document.getElementById('modal-add').style.display = 'none';
    };

    window.closeUpdateModal = function () {
        document.getElementById('modal-update').style.display = 'none';
    };

    window.openAnalyzeModal = function () {
        document.getElementById('modal-analyze').style.display = 'flex';
    };

    window.closeAnalyzeModal = function () {
        document.getElementById('modal-analyze').style.display = 'none';
    };

    window.openRecordModal = function () {
        document.getElementById('modal-record').style.display = 'flex';
    };

    window.closeRecordModal = function () {
        document.getElementById('modal-record').style.display = 'none';
    };

    window.toggleSpeechRecognition = async function () {
        const recordStatus = document.getElementById('record-modal-status');
        const canvas = document.getElementById('audio-visualizer');
        const ctx = canvas.getContext('2d');
        let silenceTimer = null;
        const SILENCE_THRESHOLD = 2000;  // 無音継続時間（ミリ秒）

        // 音声認識の初期化
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            recordStatus.textContent = 'このブラウザは音声認識に対応していません。';
            return;
        }

        const recognition = new SpeechRecognition();
        recognition.lang = 'ja-JP';
        recognition.interimResults = true;
        recognition.maxAlternatives = 1;

        // 無音タイマーをリセット
        function resetSilenceTimer() {
            if (silenceTimer) clearTimeout(silenceTimer);
            silenceTimer = setTimeout(() => {
                recognition.stop();
                recordStatus.textContent = '無音状態が続いたため録音を停止しました。';
            }, SILENCE_THRESHOLD);
        }

        // キャンバスのクリア
        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        // キャンバスに文字を描画
        function drawTextOnCanvas(text) {
            clearCanvas();
            ctx.font = '20px Arial';
            ctx.fillStyle = 'black';
            ctx.fillText(text, 10, 50);
        }

        recognition.onstart = function () {
            recordStatus.textContent = '音声認識中...';
            resetSilenceTimer();  // 開始時にタイマーをセット
        };

        recognition.onresult = function (event) {
            const transcript = event.results[0][0].transcript;
            drawTextOnCanvas(transcript);  // キャンバスに文字を描画
            document.getElementById('emotion_text').value = transcript;  // テキストエリアにも反映
            resetSilenceTimer();  // 音声を認識したらタイマーをリセット
        };

        recognition.onerror = function (event) {
            console.error('音声認識エラー:', event.error);
            recordStatus.textContent = '音声認識エラー: ' + event.error;
        };

        recognition.onend = function () {
            console.log('音声認識が終了しました。');
        };

        recognition.start();  // 音声認識の開始
    };

    window.sendAudioToSpeechAPI = async function (audioBlob) {
        const formData = new FormData();
        formData.append('audio', audioBlob);

        try {
            const response = await axios.post('/api/speech-to-text', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            const text = response.data.text;
            document.getElementById('emotion_text').value = text;
            document.getElementById('record-modal-status').textContent = '音声解析完了。結果を入力欄に表示しました。';

        } catch (error) {
            console.error('音声解析エラー:', error);
            const errorMessage = error.response?.data?.message || '解析に失敗しました。';
            document.getElementById('record-modal-status').textContent = `エラー: ${errorMessage}`;
        }
    };

    window.setupAudioMeter = function (stream) {
        window.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const source = window.audioContext.createMediaStreamSource(stream);

        const analyser = window.audioContext.createAnalyser();
        analyser.fftSize = 256;
        source.connect(analyser);

        const meterDisplay = document.createElement('div');
        meterDisplay.id = 'audio-meter';
        meterDisplay.style.height = '20px';
        meterDisplay.style.backgroundColor = '#4caf50';
        meterDisplay.style.width = '0%';
        meterDisplay.style.marginTop = '10px';
        document.getElementById('record-modal-status').after(meterDisplay);

        function updateMeter() {
            const dataArray = new Uint8Array(analyser.frequencyBinCount);
            analyser.getByteFrequencyData(dataArray);
            const maxVolume = Math.max(...dataArray);
            meterDisplay.style.width = (maxVolume / 255) * 100 + '%';
            window.animationId = requestAnimationFrame(updateMeter);
        }

        updateMeter();
    };

    window.handleMicIconClick = async function () {
        const recordStatus = document.getElementById('record-modal-status');
        const originalStatusText = recordStatus.textContent;
        let silenceTimer = null;
        const SILENCE_THRESHOLD = 3000;

        if (window.isRecording) {
            console.log("既に録音中です。");
            recordStatus.textContent = 'すでに録音中です。';
            return;
        }

        window.isRecording = true;
        console.log("録音を開始します。");

        try {
            openRecordModal();
            console.log("録音モーダルを開きました。");

            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            console.log("オーディオストリームを取得しました。");

            window.mediaRecorder = new MediaRecorder(stream);
            const audioChunks = [];
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const source = audioContext.createMediaStreamSource(stream);
            const analyser = audioContext.createAnalyser();
            analyser.fftSize = 256;
            source.connect(analyser);

            window.mediaRecorder.ondataavailable = (event) => {
                audioChunks.push(event.data);
                console.log("録音データチャンクを追加しました。");
            };

            function resetSilenceTimer() {
                if (silenceTimer) clearTimeout(silenceTimer);
                silenceTimer = setTimeout(() => {
                    console.log("無音が続いたため録音を停止します。");
                    window.mediaRecorder.stop();
                    recordStatus.textContent = '無音が続いたため録音を停止しました。';
                }, SILENCE_THRESHOLD);
                console.log("無音タイマーをリセットしました。");
            }

            function monitorAudioLevel() {
                const dataArray = new Uint8Array(analyser.frequencyBinCount);
                analyser.getByteFrequencyData(dataArray);
                const maxVolume = Math.max(...dataArray);

                if (maxVolume > 20) {
                    resetSilenceTimer();
                }
                window.animationId = requestAnimationFrame(monitorAudioLevel);
            }

            let finalTranscript = "";

            // 音声認識設定
            const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
            recognition.lang = 'ja-JP';
            recognition.interimResults = true;

            recognition.onresult = function (event) {
                finalTranscript = Array.from(event.results)
                    .map(result => result[0].transcript)
                    .join('');

                // 認識結果をコンソールに出力
                console.log("リアルタイム音声認識結果:", finalTranscript);

                // テキストエリアにも反映
                document.getElementById('emotion_text').value = finalTranscript;
            };

            recognition.onend = async function () {
                console.log("音声認識が終了しました。");
                if (finalTranscript.trim() !== "") {
                    console.log("文字データをAPIに送信します。内容:", finalTranscript);  // 送信する文字データをログ出力
                    await sendTextToAPI(finalTranscript);
                } else {
                    console.log("無効な文字データのため送信をキャンセルしました。");
                    recordStatus.textContent = '無効な文字のため送信しませんでした。';
                }
            };

            recognition.onerror = function (event) {
                console.error('音声認識エラー:', event.error);
                recordStatus.textContent = `音声認識エラー: ${event.error}`;
            };

            window.mediaRecorder.onstop = () => {
                console.log("録音を停止しました。");
                window.isRecording = false;
                cancelAnimationFrame(window.animationId);
                stream.getTracks().forEach(track => track.stop());
                recognition.stop();
            };

            window.mediaRecorder.start();
            recognition.start();
            recordStatus.textContent = '録音待機中...';
            resetSilenceTimer();
            monitorAudioLevel();

        } catch (error) {
            console.error('録音エラー:', error);
            recordStatus.textContent = 'マイクのアクセスに失敗しました。';
            window.isRecording = false;
        } finally {
            window.addEventListener('modal-record-closed', () => {
                console.log("モーダルが閉じられました。");
                recordStatus.textContent = originalStatusText;
            });
        }
    };

    // 文字データをAPIに送信する関数
    window.sendTextToAPI = async function (text) {
        const recordStatus = document.getElementById('record-modal-status');

        try {
            console.log("感情データをAPIに送信中...データ:", text);  // 送信する文字データをログ出力
            const emotionFormData = new FormData();
            emotionFormData.append('text', text);
            emotionFormData.append('date', new Date().toISOString().split('T')[0]);

            showWaitingScreen();

            await axios.post('/calendar/analyze', emotionFormData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            console.log("感情データが正常に記録されました。");
            calendar.refetchEvents();
            alert("感情がカレンダーに記録されました！");
        } catch (error) {
            console.error('APIエラー:', error);
            const errorMessage = error.response?.data?.message || '解析に失敗しました。';
            recordStatus.textContent = `エラー: ${errorMessage}`;
        } finally {
            hideWaitingScreen();
        }
    };





    // 待機画面を表示する関数
    function showWaitingScreen() {
        document.getElementById('waiting-screen').style.display = 'block';
        console.log("Waiting screen displayed");
    }

    // 待機画面を非表示にする関数
    function hideWaitingScreen() {
        document.getElementById('waiting-screen').style.display = 'none';
        console.log("Waiting screen hidden");
    }

    function closeRecordModal() {
        document.getElementById('modal-record').style.display = 'none';
        if (window.isRecording) {
            console.log("Closing record modal, stopping recording if active");
            window.isRecording = false;
            cancelAnimationFrame(window.animationId);
        }
    }

    document.getElementById("mic-icon").addEventListener("click", function () {
        handleMicIconClick();
    });

    document.getElementById("emotion-form").addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(this);

        axios.post("/calendar/analyze", formData)
            .then(() => {
                alert("感情が記録されました！");
                closeAnalyzeModal();
                calendar.refetchEvents();
            })
            .catch(() => {
                alert("感情の記録に失敗しました。");
            });
    });
}
