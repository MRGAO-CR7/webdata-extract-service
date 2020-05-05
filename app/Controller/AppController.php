<?php
/**
 * Application level Controller.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 0.2.9
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller.
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link    http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    public $components = array(
        'Paginator',
        'RequestHandler',
        'Cookie'
    );

    public function beforeFilter()
    {
        if (Configure::read('debug') == 0) {
            $this->clientName = $this->authenticate();
            if ($this->clientName) {
                return true;
            }
            return false;
        }
    }

    /**
     * Really basic Authentication of the request, expects an Authorization header in the format clientID:clientHash where
     * the client hash is an hmac_sha1 of the request body and the clients secret key.
     *
     * @return boolen true or otherwise throw an expection.
     */
    private function authenticate()
    {
        /* Extract out authorization header */
        $headers = apache_request_headers();

        if (isset($headers['Authorization'])) {
            $headers['authorization'] = $headers['Authorization'];
            $headers['origin'] = $headers['Origin'];
        }

        if (!isset($headers['authorization'])) {
            throw new UnauthorizedException('Missing Authorization');
        }

        list($clientID, $clientHash) = explode(':', $headers['authorization'], 2);

        // if ($clientID == 'POSTMAN' && $clientHash == 'proactive12') {
        //     return 'Postman';
        // }

        /* Gets the clients secret key */
        $this->loadModel('Client');
        $clientSerect = $this->Client->getClientSecret($clientID);
        if (!$clientSerect) {
            throw new UnauthorizedException('Invalid Authorization');
        }

        /* Generate the same hash the client should have based on the input and there client secret */
        $checkHash = hash_hmac('sha1', $this->request->input(), $clientSerect);

        /* Make sure the generated hash and the provied hash match */
        if ($checkHash !== $clientHash) {
            throw new UnauthorizedException('Invalid Authorization');
        }

        return $this->Client->getClientName($clientID);
    }
}
