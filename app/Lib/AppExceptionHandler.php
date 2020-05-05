<?php

App::uses('CakeLog', 'Log');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('Router', 'Routing');

class AppExceptionHandler
{

    /**
     * Exception handler for REST APIs
     *
     * Logs the messages with details about the error and
     * then responds with json in the format below to the client.
     *
     * {
     *      "status": "error",
     *      "code": 400,
     *      "data": {
     *          "message": "No customer profile found."
     *      }
     * }
     *
     * @return json A json string of the error.
     */
    public static function handleException($exception)
    {
        $request = Router::getRequest();
        $message = sprintf(
            "[%s] %s\nFile: %s\nLine: %s\nUrl: %s\nInput: %s\nIP: %s\n",
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $request ? $request->here() : '',
            $request ? $request->input() : '',
            $request ? $request->clientIp() : ''
        );
        $message .= "Stack Trace:\n" . $exception->getTraceAsString() . "\n";

        $logName = 'error';

        if (get_class($exception) == 'BadRequestException') {
            $logName = 'bad_requests';
        }

        if (strpos($exception->getMessage(), 'Unroutable') !== false) {
            $logName = 'routable';
        }

        CakeLog::write($logName, $message);

        $response = new CakeResponse();
        $response->statusCode(400);
        $response->type('json');
        $response->send();
        echo json_encode(array(
            'status' => 'error',
            'code' => $exception->getCode(),
            'data' => array(
                'message' => $exception->getMessage()
            )
        ));
    }
}
