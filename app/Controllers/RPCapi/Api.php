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

        $output = $this->RPCProcess($input); // По хорошему бы вынести в отдельный класс

        // Вернуть JSON ответ
        return json_encode($output, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Проверяем входящий json-массив RPC
     *
     * @param $input
     * @return array
     */
    private function RPCProcess($input): array
    {
        if (!is_array($input))
            return $this->RPCParseError();

        // Не пустой ли массив?
        if (count($input) == 0)
            return $this->RPCInvalidRequest();

        // Если это массив с несколькими RPC, запускаем циклом
        if ($input[0] !== null)
        {
            $outputs = [];
            foreach ($input as $item)
            {
                $outputs[] = $this->RPCmakeResponse($item);
            }
            return $outputs;
        }

        return $this->RPCmakeResponse($input);
    }

    /**
     * Генерация ответа RPC
     *
     * @param $input
     * @return array
     */
    public function RPCmakeResponse($input): array
    {
        if (!is_array($input))
             return $this->RPCParseError();

        // Получаем id'шник
        $id = 0;
        if (array_key_exists('id', $input) && (is_int($input['id']) || is_string($input['id'])))
            $id = intval($input['id']);

        // Сравниваем версии
        if ($input['jsonrpc'] !== self::RPC_VERSION)
            return $this->RPCInvalidRequest($id);

        // Проверяем что метод есть и он строка
        if (!array_key_exists('method', $input) || !is_string($input['method']))
            return $this->RPCInvalidRequest($id);


        // Получаем параметры
        $params = [];
        if (array_key_exists('params', $input)) {
            $params = $input['params'];

            if (!is_array($params)) {
                return $this->RPCInvalidRequest($id);
            }
        }

        return $this->RPChandle($id, $input['method'], $params);
    }

    /**
     * Вызов метода RPC
     *
     * @param int $id
     * @param string $method
     * @param array $params
     * @return array
     */
    private function RPChandle(int $id, string $method, array $params): array
    {
        if ($method == "getEthereumDate") {
            return $this->RPCResponse($id, ['DateTime' => date('Y-m-d H:i:s'), 'UNIXtime' => time()]);
        }

        if ($method == "getParams") {
            return $this->RPCResponse($id, $params);
        }

        return $this->RPCRError($id, 'Method not found', ErrorCodes::METHOD_NOT_FOUND);;
    }

    /**
     * Неверный запрос RPC
     *
     * @param int $id
     * @return array
     */
    private function RPCInvalidRequest(int $id = 0): array
    {
        return $this->RPCRError($id, 'Invalid request', ErrorCodes::INVALID_REQUEST);
    }

    /**
     * Ошибка парсинга RPC
     *
     * @return array
     */
    private function RPCParseError(): array
    {
        return $this->RPCRError(0, 'Parse error', ErrorCodes::PARSE_ERROR);
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
    private function RPCRError(int $id, string $message, int $code, array $data = null): array
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
    private function RPCResponse(int $id, array $result): array
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
