<?php
// Инклуды)
use GuzzleHttp\Client;
include('vendor/autoload.php');
include('bot.php');
include('conf.php');
//include ('tonality.php');

//Получаем данные
$telegramApi = new TelegramBot();

// Вечный цикл, обработчик
while (true) {
	sleep(2);
	$updates = $telegramApi->getUpdates(); // Получаем обновление, методом getUpdates
	foreach ($updates as $update){
		if (isset($update->message->text)) { // Проверяем Update, на наличие текста

			$text = $update->message->text; // Переменная с текстом сообщения
			$chat_id = $update->message->chat->id; // Чат ID пользователя
			$first_name = $update->message->chat->first_name; //Имя пользователя
            $last_name = $update->message->chat->last_name; //Имя пользователя
			$username = $update->message->chat->username; //Юзернейм пользователя
            //$res = $telegramApi->transpose($text,3); //результат изменения пональности
            $table = $telegramApi->schedule($update->message->text);

            //$resOfReplace = str_ireplace($chords, $res, $text);

			print_r($chat_id);
			print_r($username);

			if ($text == '/start'){ // Если пользователь подключился в первый раз, ему поступит приветствие
			    $telegramApi->sendMessage($chat_id, 'Привет'. ' ' . $first_name . '!'); //Приветствует Пользователя
			} else {
			    $telegramApi->sendMessage($chat_id, $table); // Спрашивает как дела
                //echo $text;
			}
		}
	}
}
