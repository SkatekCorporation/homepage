<?php
/**
* App.php
* @author Souvenance <skavunga@gmail.com>
* @version 1.1
* @importance Les fonctions communs des tout les controllers de l'application
* Tout les controllers doivent heriter cette Classe
*/

    namespace Stephanie\Controller;

	use App\View\AppView;
	use Stephanie\Router\Router;
	use Stephanie\Handlers\Flash;
	use Stephanie\Handlers\Session;

	/**
	 * Classe generique de tout les controllers. Tout les controllers doivent
	 * heriter de cette class
	 */
    class Controller {

		protected $view;
		protected $flash;
		protected $request;
		protected $session;

		private static $className;

    	public function __construct()
    	{
			self::$className = substr(get_class($this), 15, 35);
			$this->view      = new AppView(get_class($this));

			$this->request   = new \Stephanie\Request();
			$this->session   = new Session();
			$this->flash     = new Flash($this->session);
    	}
		
		/**
		 * Page d'accueil par defaut
		 */
		public function debut($tmp = 'debut', array $options = []){
			if(empty($options['title'])) {
				$options['title'] = "Page de test du FrameWork";
			}
			if (empty($options['application'])) {
				$options['application'] = 'Skatek Corporation';
			}
			$this->view->render($tmp, $options);
		}
		
		/**
		 * Pour rendre la vue 
		 */
		public function render($file = null, array $options = [])
		{
			return $this->view->render($file, $options);
		}
		
		/**
		 * Pour rediriger vers une page donner
		 */
		public function redirect($params = null)
		{
			return header('Location: ' . Router::buildUrl($params));
		}

		public static function buildUrl($params = null, $type = null)
		{
			return Router::buildUrl($params, $type);
		}

		public function e404(Type $var = null)
		{
			# code...
		}
		
	/**
	 * Obtenir la view et ses parametres
	 */
	public function View() 		{ return $this->view; }
	public function Session()	{ return $this->session; }
	public function Request() 	{ return $this->request; }
	public function Flash($message = null, $type = "success") { $this->flash->$type($message); }
	
	public function generateUnique($n = null){
		if ($n == null) { return time(); }
        if ($n == 'min') { return sprintf('%03x-%03x', mt_rand(0, 65535), mt_rand(0, 65535)); }
        else if ($n == 'med') { return sprintf('%04x%04x%04x', date('Ym'), mt_rand(0, 65535), mt_rand(0, 65535)); }
        else { return sprintf('%04x%05x%05x', date('Ymd'), mt_rand(0, 65535), mt_rand(0, 65535)); }
    }
}