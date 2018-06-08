<?php

namespace Stephanie\Handlers;

use Stephanie\Interfaces\SessionInterface;

/**
 * Classe se chargeant de definir les messages flash
 */
class Flash implements \Countable {

    private $session;

    public function __construct(SessionInterface $session = null){
        if ($session == null){
            $this->session = new Session();
        } else {
            $this->session = $session;
        }
    }

    /**
     * Definition du message flash
     * @param string $message Le message a definir
     * @param string $type Le type de message
     * @param string $title Le titre de la notification
     */
    public function set($message, $title = 'Notification', $type = 'default'){
        $this->session->set(FLASH_KEY, [
            'message' => $message,
            'type'    => $type,
            'title'   => $title
        ]);
    }

    /**
     * Obtenir le message flash contenu dans la session
     */
    public function get(){
        $flash = $this->session->get(FLASH_KEY);
        $this->session->delete(FLASH_KEY);
        return $flash;
    }

    public function success($message = null, $title = 'Bravo !'){
        $this->set($message, $title, 'success');
    }

    public function error($message = null, $title = 'Erreur !'){
        $this->set($message, $title, 'error');
    }

    public function warning($message = null, $title = 'Avertissement !'){
        $this->set($message, $title, 'warning');
    }
	
	public function count() {
		return count($this->session->get(FLASH_KEY));
	}
}
