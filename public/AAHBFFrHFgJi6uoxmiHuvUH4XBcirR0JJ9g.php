<?php
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
    exit;
}

if (isset($update["message"]) || isset($update['my_chat_member']) || isset($update['callback_query'])) {
    $json = json_encode($update);
    file_put_contents("storage/templates/telegram_updates/update_".date('Y-m-d-H-i-s')."_".rand().".json", $json);
}
