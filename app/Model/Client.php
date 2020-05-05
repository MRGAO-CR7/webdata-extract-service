<?php

class Client extends AppModel
{

    /**
     * Checking the CLientSecret from the client request against our DB record
     * base on the ClientID from the client's request
     *
     * @param int clientID the ClientID from Client's request data
     * @return True if ClientID exist | False otherwise
     */
    public function getClientSecret($clientID)
    {
        $client = $this->find('first', array('conditions' => array('client_id' => $clientID)));
        if (isset($client['Client']['client_secret'])) {
            return $client['Client']['client_secret'];
        }
        return false;
    }

    /**
     * Get the ClientName base on the ClientID
     *
     * @param int clientID the ClientID from Client's request data
     * @return string clientName
     */
    public function getClientName($clientID)
    {
        $client = $this->find('first', array('conditions' => array('client_id' => $clientID)));
        return $client['Client']['client_name'];
    }
}
