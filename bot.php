<?php
// Подключение библиотеки
use GuzzleHttp\Client;
use Telegram\Api;

//include('../bot/conf.php');


class TelegramBot
{
    //protected $token = "[x]";//tonality
    protected $token = "x";//royal
    protected $updateId;
	// Функция собирает URL
	public function query($method, $params = [])
	{
		$url = "https://api.telegram.org/bot";
		$url .= $this->token;
		$url .= "/" . $method;
		if (!empty($params))
		{
		$url .= "?" . http_build_query($params);
		}

		$client = new Client([
		'base_uri' => $url
		]);

		$result = $client->request('GET');

		return json_decode($result->getBody());
	}

	// Получаем обновления
	public function getUpdates()
	{
		$response = $this->query('getUpdates', [
		'offset' => $this->updateId + 1
		]);
		if (!empty($response->result)) {
		$this->updateId = $response->result[count($response->result) -1]->update_id;
		}
		return $response->result;
	}

	// Отправляем сообщения
	public function sendMessage($chat_id, $text)
	{
		$response = $this->query('sendMessage',[
		'chat_id' => $chat_id,
		'text' => $text
		]);
		return $response;
	}

	// Принимает ноту и количество полутонов для транспонирования
    public function transpose($chord, $transpose)
    {
        // the chords
        $chords = ["C", "C#", "D", "D#", "E", "F", "F#", "G", "G#", "A", "A#", "B"];

        // get root tone
        $root_arr = explode("/", $chord);
        $root = strtoupper($root_arr[0]);

        // the chord is the first character and a # if there is one
        $root = $root[0] . ((strpos($root, "#") !== false) ? "#" : "");

        // get any extra info
        $root_extra_info = str_replace("#", "", substr($root_arr[0], 1)); // assuming that extra info does not have any #

        // find the index on chords array
        $root_index = array_search($root, $chords);
        // transpose the values and modulo by 12 so we always point to existing indexes in our array
        $root_transpose_index = floor(($root_index + $transpose) % 12);

        if ($root_transpose_index < 0) {
            $root_transpose_index += 12;
        }

        //$result .= $chords[$root_transpose_index] . $root_extra_info;

        if (count($root_arr) > 1) {
            $result = "";
            // get the non root tone
            $non_root = $root_arr[1];
            // the chord is the first character and a # if there is one
            $non_root = strtoupper($non_root[0]) . ((strpos($non_root, "#") !== false) ? "#" : "");
            // get any extra info
            $non_root_extra_info = str_replace("#", "", substr($root_arr[1], 1)); // assuming that extra info does not have any #

            // find the index on chords array
            $non_root_index = array_search($non_root, $chords);
            // transpose the values and modulo by 12 so we always point to existing indexes in our array
            $non_root_transpose_index = floor(($non_root_index + $transpose) % 12);

            if ($non_root_transpose_index < 0) {
                $non_root_transpose_index += 12;
            }

            $result .= "/" . $chords[$non_root_transpose_index] . $non_root_extra_info;
        }

        return $result;
    }

    //формирует расписание
    public function schedule($text) {
        //помещает слова из сообщения в элементы массива
	    $names = explode(' ', $text, 50);
        $mixedText = '';
        $countNames = count($names);

        //заполняет массив именами, если их меньше, чем надо (<20)
        if ($countNames<20) {
            $least = floor(20 / $countNames);;
            for ($i = 0; $i <= $least; $i++) {
                shuffle($names);

                $mixedText .= implode(" ", $names)." ";
            }
            $names = explode(' ', $mixedText, 30);
        }

        //определяет 5 дат следующих вторников для расписания
        $date = time();
        $day = 60 * 60 * 24;
        while (date('D', $date) !== 'Tue') $date += $day;
        for($i=0; $i<=28; $i+=7)
        {
            $tue[] = date('d', $date + ($i * $day));
        }

        //пишет текущий месяц из трех букв
        $month = date("M");

        //Таблица для вывода
$table = "$month | __$tue[0]___$tue[1]___$tue[2]___$tue[3]___$tue[4]__|
лдк | $names[0] $names[4] $names[8] $names[12] $names[16]
прс | $names[1] $names[5] $names[9] $names[13] $names[17]
слв | $names[2] $names[6] $names[10] $names[14] $names[18]
двц | $names[3] $names[7] $names[11] $names[15] $names[19]";

        return ($table);
    }
}
