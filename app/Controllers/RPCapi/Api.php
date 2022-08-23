<?php


namespace App\Controllers\RPCapi;

use App\Controllers\RPCapi\ErrorCodes;

class Api extends \MainController
{
    /**
     * Версия RPC
     */
    const RPC_VERSION = '2.0';

    /**
     * @return false|string
     */
    public function index()
    {
        // Получаем сообщение из POST
        $message = \Request::getPost('message');

        // Преобразуем JSON в Array
        $input = json_decode($message, true);

        // Если массив, обработать, если что-то иное вывести ошибку
        if (is_array($input)) {
            $output = $this->process($input); // По хорошему бы вынести в отдельный класс
        } else {
            $output = $this->error(0, 'Parse error', ErrorCodes::PARSE_ERROR);
        }

        // Вернуть JSON ответ
        return json_encode($output, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param array $input
     * @return array
     */
    private function process(array $input): array
    {
        // Не пустой ли массив?
        if (count($input) == 0)
            return $this->invalidRequest();

        // Если это массив с несколькими RPC, запускаем циклом
        if ($input[0] !== null)
        {
            $outputs = [];
            foreach ($input as $item)
            {
                $outputs[] = $this->makeResponse($item);
            }
            return $outputs;
        }

        return $this->makeResponse($input);
    }

    /**
     * Генерация ответа
     *
     * @param $input
     * @return array
     */
    public function makeResponse($input): array
    {
        if (!is_array($input))
            $output = $this->error(0, 'Parse error', ErrorCodes::PARSE_ERROR);

        // Получаем id'шник
        $id = 0;
        if (array_key_exists('id', $input) && (is_int($input['id']) || is_string($input['id'])))
            $id = intval($input['id']);

        // Сравниваем версии
        if ($input['jsonrpc'] !== self::RPC_VERSION)
            return $this->invalidRequest($id);

        // Проверяем что метод есть и он строка
        if (!array_key_exists('method', $input) || !is_string($input['method']))
            return $this->invalidRequest($id);


        // Получаем параметры
        $params = [];
        if (array_key_exists('params', $input)) {
            $params = $input['params'];

            if (!is_array($params)) {
                return $this->invalidRequest($id);
            }
        }

        return $this->handle($id, $input['method'], $params);;
    }

    /**
     * Вызов метода
     *
     * @param int $id
     * @param string $method
     * @param array $params
     * @return array
     */
    private function handle(int $id, string $method, array $params): array
    {
        if ($method == "getEthereumDate") {
            return $this->response($id, ['DateTime' => date('Y-m-d H:i:s'), 'UNIXtime' => time()]);
        }

        if ($method == "getParams") {
            return $this->response($id, $params);
        }

        return $this->error($id, 'Method not found', ErrorCodes::METHOD_NOT_FOUND);;
    }

    /**
     * Неверный запрос
     *
     * @param int $id
     * @return array
     */
    private function invalidRequest(int $id = 0): array
    {
        return $this->error($id, 'Invalid request', ErrorCodes::INVALID_REQUEST);
    }


    /**
     * Вывод ошибки
     * https://www.jsonrpc.org/specification#error_object
     *
     * @param int $id
     * @param string $message
     * @param int $code
     * @param array|null $data
     * @return array
     */
    private function error(int $id, string $message, int $code, array $data = null): array
    {
        // Создаём template ошибки
        $error = [
            'code' => $code,
            'message' => $message
        ];

        // Если есть data, добавим её
        if ($data !== null) {
            $error['data'] = $data;
        }


        $output = [
            'jsonrpc' => self::RPC_VERSION,
            'error' => $error
        ];

        if ($id !== 0) {
            $output['id'] = $id;
        }

        return $output;
    }

    /**
     * Вывод ответа
     * https://www.jsonrpc.org/specification#response_object
     *
     * @param int $id
     * @param array $result
     * @return array
     */
    private function response(int $id, array $result): array
    {
        $output = [
            'jsonrpc' => self::RPC_VERSION,
            'result' => $result
        ];

        if ($id !== 0) {
            $output['id'] = $id;
        }

        return $output;
    }
}
