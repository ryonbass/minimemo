<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>sticky memo</title>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- jquery -->
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{asset('css/memo/style.css')}}">
    <style>
        #img {
            background-size: cover;
            background-image: url("storage/CorkBoard.jpg");
            width: 100%;
            height: 670px;
            z-index: -1000;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Page Content -->
        <main>
            <form action="/">
                @csrf
                <div class="sticky">
                    <div class="color pink" id="pink"></div>
                    <div class="color yellow" id="yellow"></div>
                    <div class="color green" id="green"></div>
                    <div class="color white" id="white"></div>
                    <div class="color blue" id="blue"></div>
                    <div class="color orange" id="orange"></div>
                </div>
                <div class="settings" id="settings"><i class="fa-solid fa-gear"></i></div>

                <div class="bgd-img">
                    <img id="img">
                    <div id="sticky-memo-container" class="memos">
                    </div>
                </div>
            </form>
        </main>
        <script>
            let next_memo_id = 0;
            $(function() {
                let memo_data = @json($memo_data);
                showMemo(memo_data);
            });
            //  メモ作成処理
            function createMemo(color, id, content = "") {
                return '<div class="memo ' + color + '" id="memo' + id + '" style="z-index:' + id + '; position: absolute;" > ' +
                    '<div class="memo-header" id="header' + id + '">test<div  class="change-oneline" onclick="changeOneline(memo' + id + ');"><i class="fa-solid fa-caret-down"></i></div><div class="memo-close" id="close' + id + '" onclick="memoDelete(memo' + id + ',' + id + ');"><i class="fa-solid fa-xmark"></i></div><div class="memo-save" id="save' + id + '" onclick="memoSave(memo' + id + ',textarea' + id + '.value,' + id + ');"><i class="fa-solid fa-check"></i></div></div>' +
                    '<div class="memo-content" id="content' + id + '"><textarea class="' + color + '" id="textarea' + id + '">' + content + '</textarea></div>' +
                    '</div>';
            }

            function showMemo(memo_data) {
                $.each(memo_data, function(index, val) {
                    const color = val.color;
                    const memo_id = val.id;
                    const content = val.content;
                    const width = val.width;
                    const height = val.height;
                    const left = val.position_left;
                    const top = val.position_top;
                    const $memo = $(createMemo(color, memo_id, content));
                    $memo.draggable();
                    $memo.resizable({
                        minHeight: 50,
                        minWidth: 50,
                        alsoResize: "#content" + memo_id,
                    });
                    const option = {
                        "width": width + "px",
                        "height": height + "px",
                        "top": top + "px",
                        "left": left + "px",
                    }
                    setTimeout(function() {
                        $("#memo" + memo_id).css(option)
                    }, 10);
                    $('#sticky-memo-container').append($memo);
                    next_memo_id = memo_id;
                })
            }
            //付箋押下時
            $('.sticky').on('click', function(e) {
                next_memo_id += 1;
                const color = e.target.id;
                const $memo = $(createMemo(color, next_memo_id));
                $memo.draggable();
                $memo.resizable({
                    minHeight: 50,
                    minWidth: 50,
                    alsoResize: "#content" + next_memo_id,
                });
                // $("#content" + next_memo_id).resizable();
                $('#sticky-memo-container').append($memo);
            });

            //バツボタン押下処理
            function memoDelete(memo_id, delete_id) {
                memo_id.remove();
                const send_data = {
                    "id": delete_id,
                }
                databaseOperations("delete", send_data);
            }
            //▼ボタン押下処理
            function changeOneline(memo_id) {
                console.log(memo_id);
            }
            // 保存処理
            function memoSave(memo, content, id) {
                const color = $(memo).attr("class").split(" ")[1];
                const style_data = $(memo).css(['width', 'height', 'top', 'left']);

                const send_data = {
                    "id": id,
                    "color": color,
                    "content": content,
                    "width": style_data.width,
                    "height": style_data.height,
                    "left": style_data.left,
                    "top": style_data.top,
                }
                databaseOperations("save", send_data);
            }

            // DB操作
            function databaseOperations(operate, send_data) {
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                });
                $.ajax({
                        type: "post",
                        url: "/" + operate,
                        dataType: "json",
                        data: send_data,
                    })
                    .then((res) => {
                        console.log(res);
                    })
                    .fail((error) => {
                        console.log(error.statusText);
                    });
            }
        </script>
</body>

</html>