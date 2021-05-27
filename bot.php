<?php
// Подключение библиотеки
use GuzzleHttp\Client;
use Telegram\Api;
include('conf.php');

class TelegramBot
{
	protected $updateId;
	// Функция собирает URL
	protected function query($method, $params = [])
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

}
