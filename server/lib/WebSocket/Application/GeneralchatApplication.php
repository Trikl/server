<?php

namespace WebSocket\Application;

/**
 * Websocket-Server demo and test application.
 * 
 * @author Simon Samtleben <web@lemmingzshadow.net>
 */
class GeneralchatApplication extends Application
{
    private $_clients = array();
	private $_filename = '';
	public $_users;

	public function onConnect($client, $uid = false)
    {
		$id = $client->getClientId();
        $this->_clients[$id] = $client;
        		$this->_users[$id] =  array(
					"connid" => $id,
					"userid" => $this->_clients[$id]->userid,
				);
    }

    public function onDisconnect($client)
    {
        $id = $client->getClientId();		
		unset($this->_clients[$id]);
		unset($this->_users[$id]);
		
    }

    public function onData($data, $client)
    {		
    	
        $decodedData = $this->_decodeData($data);		
		if($decodedData === false)
		{
			// @todo: invalid request trigger error...
		}
			$this->log('sending message from uid: ' . $client->userid . ' to uid: ' . $decodedData['to'] );
			$this->_actionEcho($decodedData['action'], strip_tags($decodedData['data']), $decodedData['to']);
    }
	
	private function _actionEcho($action, $text, $to)
	{		
		$encodedData = $this->_encodeData($action, $text, $to);

		
			
			foreach ($this->_users as $user) {
				if ($action === $user['userid'] || $to === $user['userid']) {
					$sendmessage = $this->_clients[$user['connid']];
					$sendmessage->send($encodedData);
				}
			}
	    

		

 
	}
	
    public function log($message)
    {
        echo date('Y-m-d H:i:s') . ' [message] ' .  $message . PHP_EOL;
    }
    
    public function messageDirection($uid, $to)
    {
	    if ($uid === $to) {
		    $this->log("for you");
	    } else {
		    $this->log("not for you");
	    }
    }
    
    
    //	public function onConnect($client, $uid = false)
    //{
	//	$id = $client->getClientId();
    /*   $this->_clients[$id] = $client;
        $this->_clients['userid'] = $uid;		
		
		$this->clientinfo[] = array(
			'userid' => $uid,
			'otherid' => $this->_clients[$id],
		);
		
		var_dump($this->clientinfo);
		$this->log('done');


        
    }*/

}