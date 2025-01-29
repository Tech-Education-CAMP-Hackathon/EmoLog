import axios from "axios";
import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

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
            emotionRecordButton: { // 感情記録ボタン
                text: '感情を記録する',
                click: function () {
                    document.getElementById('modal-analyze').style.display = 'flex';
                }
            }
        },

        headerToolbar: {
            start: "prev,next today",
            center: "title",
            end: "dayGridMonth,timeGridWeek,eventAddButton,emotionRecordButton",
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
                .catch((error) => {
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

    calendar.render();

    window.closeAddModal = function () {
        document.getElementById('modal-add').style.display = 'none';
    }

    window.closeUpdateModal = function () {
        document.getElementById('modal-update').style.display = 'none';
    }

    window.closeAnalyzeModal = function () {
        document.getElementById('modal-analyze').style.display = 'none';
    }

    window.deleteEvent = function () {
        if (confirm('削除すると復元できません。\n本当に削除しますか？')) {
            document.getElementById('delete-form').submit();
        }
    }

    // 感情分析の送信処理
    document.getElementById("emotion-form").addEventListener("submit", function (event) {
        event.preventDefault(); // デフォルトのフォーム送信を防ぐ

        const formData = new FormData(this);

        axios.post("/calendar/analyze", formData)
            .then(response => {
                alert("感情が記録されました！");
                document.getElementById('modal-analyze').style.display = 'none';
                calendar.refetchEvents(); // カレンダーを更新
            })
            .catch(error => {
                alert("感情の記録に失敗しました。");
            });
    });
}
