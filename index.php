<?php
ob_start();
error_reporting(0);
date_Default_timezone_set('Asia/Tashkent');


define('API_KEY', "<TOKEN>");
$admin = "<adminid>";


function bot($method, $datas = [])
{
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
    } else {
        return json_decode($res);
    }
}


$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$cid = $message->chat->id ?? $update->callback_query->message->chat->id;
$mid = $message->message_id ?? $update->callback_query->message->message_id;
$text = $message->text;
$fid = $message->from->id;
$type = $message->chat->type;
$name = $message->from->first_name;
$username = $message->from->username;
$callback = $update->callback_query;
$data = $update->callback_query->data;
$qid = $update->callback_query->id;
$cid2 = $update->callback_query->message->chat->id;
$query = $update->inline_query->query;
$query_id = $update->inline_query->from->id;
$cid2 = $update->callback_query->message->chat->id;
$ccid = $update->callback_query->message->chat->id;
$mid2 = $update->callback_query->message->message_id;
$cmid = $update->callback_query->message->message_id;
$callname = $update->callback_query->from->first_name;
$calluser = $update->callback_query->from->username;
$fid2 = $update->callback_query->from->id;
$familya = $message->from->last_name;
$nameru = "$name $familya";
$time = date('Y-m-d H:i');
$joinchatid = $update->chat_join_request->chat->id;
$jcid = $update->chat_join_request->from->id;



@mkdir("data");


$start_id = file_get_contents("data/message.id");
$join_id = file_get_contents("data/message1.id");
$step = file_get_contents("data/step.id");

$userlar = file_get_contents("data/stat.txt");
$stat = substr_count($userlar, "\n")+1;

$botname = bot('getme', ['bot'])->result->username;


if (isset($message)) {
    $baza = file_get_contents("data/stat.txt");
    if (mb_stripos($baza, $cid) !== false) {
    } else {
        $txt = "
 $cid";
        $file = fopen("data/stat.txt", "a");
        fwrite($file, $txt);
        fclose($file);
    }
}


$back = json_encode([
    'inline_keyboard' => [
        [
            ['text' => "⬅️ Back", 'callback_data' => "back"]
        ],
    ]
]);


$share = json_encode([
    'inline_keyboard' => [
        [
            ['text' => "➕ Foydalanish", 'url' => "t.me/$botname?startgroup=uz"]
        ],
    ]
]);

$panel = json_encode([
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => "✉  Xabarnoma"], ['text' => "📊 Statistika"]],
        [['text' => "✍️ start xabar"], ['text' => "✍️ join xabar"]],
        [['text' => "◀ Back"]],
    ]
]);

if ($text == '/start' || $text == "◀ Back") {
    bot('forwardMessage', [
        'chat_id' => $cid,
        'from_chat_id' => $admin,
        'message_id' => $start_id

    ]);
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>✅ Kanal va Guruhlarga yuborilgan so'rovlarni avtomatik tarzda qabul qiluvchi bot! 
Bot ishlashi uchun kanalga qo'shib admin qilishingiz kerak👇</b>",
        'parse_mode' => 'html',
        'reply_markup' => $share
    ]);
    exit();
}




if (isset($update->chat_join_request)) {
    $joinchatid = $update->chat_join_request->chat->id;
    $cid = $update->chat_join_request->from->id;

    $response = bot("approveChatJoinRequest", [
        "chat_id" => $joinchatid,
        "user_id" => $cid,
    ]);
    bot('forwardMessage', [
        'chat_id' => $jcid,
        'from_chat_id' => $admin,
        'message_id' => $join_id
    ]);
    if ($response && $response->ok) {
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "<b>✅ So'rovingiz qabul qilindi!</b>",
            'parse_mode' => 'html',
            'reply_markup' => $share
        ]);
    } else {
        // bot('sendMessage', [
        //     'chat_id' => $admin,
        //     'text' => "<b>Xato: </b>".$response->description,
        //     'parse_mode' => 'html',
        //     'reply_markup' => $panel
        // ]);
    }
}


if ($data == 'back') {
    bot("deleteMessage", ['chat_id' => $cid, 'message_id' => $mid]);
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>🧑‍💻 Admin panelga xush kelibsiz!</b>",
        'parse_mode' => 'html',
        'reply_markup' => $panel
    ]);
    exit();
}

if ($text == '/panel' and $cid == $admin) {
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>🧑‍💻 Admin panelga xush kelibsiz!</b>",
        'parse_mode' => 'html',
        'reply_markup' => $panel
    ]);
    exit();
}
if ($text === '📊 Statistika' and $cid = $admin) {
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>📊 Obunachilar soni: $stat ta</b>",
        'parse_mode' => 'html',
        'reply_markup' => $back
    ]);
    exit();
}
if ($text === '✉  Xabarnoma' and $cid = $admin) {
    file_put_contents("data/step.id", 'message');

    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>📊 Obunachilar soni: $stat ta</b>",
        'parse_mode' => 'html',
        'reply_markup' => $back
    ]);
    exit();
}


if ($text === '✍️ start xabar' and $cid = $admin) {
    file_put_contents("data/step.id", 'start');
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>Xabaringizni yuboring...</b>",
        'parse_mode' => 'html',
    ]);
    exit();
}

if ($text === '✍️ join xabar' and $cid = $admin) {
    file_put_contents("data/step.id", 'join');
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>Xabaringizni yuboring...</b>",
        'parse_mode' => 'html',
    ]);
    exit();
}

if ($step == 'start' and $cid == $admin) {
    unlink("data/step.id");
    file_put_contents('data/message.id', $mid);
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>✅</b>",
        'parse_mode' => 'html',
        'reply_markup' => $back
    ]);
} elseif ($step == 'join' and $cid == $admin) {
    unlink("data/step.id");
    file_put_contents('data/message1.id', $mid);
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>✅</b>",
        'parse_mode' => 'html',
        'reply_markup' => $back
    ]);
} elseif ($step == 'message' and $cid == $admin) {
    unlink("data/step.id");
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>✅ Xabar yuborish boshlandi!</b>",
        'parse_mode' => 'html',
    ]);
    $users = explode("\n", file_get_contents("data/stat.txt"));
    foreach ($users as $user) {
        bot('sendMessage', [
            'chat_id' => $user,
            'text' => "$text",
            'parse_mode' => 'html',
        ]);
    }
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>✅ Xabar yuborish tugatildi!</b>",
        'parse_mode' => 'html',
    ]);
    exit();
}
