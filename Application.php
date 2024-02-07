<?php
namespace juanignaso\phpmvc\framework;

use juanignaso\phpmvc\framework\db\DataBase;
use juanignaso\phpmvc\framework\Token;

// use app\models\Usuario; <-- Define aquí o usa aquí la clase que tengas de Usuario

/**
 * Class Application
 * 
 * @package juanignaso\phpmvc\framework
 */
class Application
{

    public static string $ROOT_DIR; //para evitar que se sobreescriba

    public string $layout = 'main';

    public string $userClass;

    public Router $router;

    public Request $request;

    public Response $response;

    public Session $session;

    public DataBase $db;

    public static Application $app;

    public ?Controller $controller = null;

    #Define o escribe aquí la case de Usuario, en este caso sería un Modelo que manipulase la tabla con los datos del usuario
    #El usuario aquí extiende de DBModel
    public ?Usuario $user;

    public View $view;

    public Cookie $cookie;

    public Token $Token;


    public function __construct($rootPath, array $config)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->userClass = $config['userClass'];
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();
        $this->cookie = new Cookie();
        $this->db = new DataBase($config['db']);
        $this->Token = new Token();

        $this->recoverUserSesion(); //Recupera la sesión si la cookie 'remember me' existe en el navegador

        //Devuelve el usuario entre página y página, para poder acceder a él en cualquier punto de la aplicación.
        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            /*No deja llamar métodos no estáticos de forma estática*/
            $c = new $this->userClass;
            $primaryKey = $c->primaryKey();
            $this->user = $c->findOne([$primaryKey => $primaryValue]);
        } else {
            $this->user = NULL;
        }


    }

    /**
     * Inicia la aplicación haciendo uso del router.
     */
    public function run()
    {
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('error_page', [
                'exception' => $e,
            ]);

        }
    }

    /**
     * Recupera la sesión del usuario cada vez que se abra el navegador
     * o inicie el servidor, si este ha marcado la casilla de 'remember_me'
     */
    public function recoverUserSesion()
    {
        #se comprueba que existe la cookie
        if (isset($_COOKIE['remember_me'])) {
            $usuario = $this->Token->encontrarUsrPorToken($_COOKIE['remember_me']);
            /*
            si el resultado de 'encontrarUsrPorToken()' es distinto de falso, osea que el usuario 
            tiene token, se inicia sesión.
            */
            if ($usuario != false) {
                $userModel = new Usuario(); //Asignas a esta variable la clase del Usuario
                $usuario = $userModel->findOne(['id' => $usuario['id']]);
                $this->login($usuario);
            }
        }
    }

    public function login(Usuario $user)
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }

    // /**
    //  * Borrar la sesión actual dentro de aplicación
    //  */
    public function logout()
    {

        if ($this->isUserLoggedIn()) {
            //borrar el token del usuario
            $this->Token->borrarTokensUsuario($this->user->id);
            $this->user = NULL;
            $this->session->remove('user');
            $this->cookie->delete('remember_me');
        }


    }

    public function isUserLoggedIn(): bool
    {
        //Comprobar que el usuario tiene sesion iniciada
        if (self::$app->user != null) {
            return true;
        }

        //Comprobar el token de remember me
        $token = filter_input(INPUT_COOKIE, 'remember_me', FILTER_SANITIZE_STRING);

        if ($token && self::$app->Token->isTokenValido($token)) {
            $usuario = self::$app->Token->encontrarUsrPorToken($token);
            if ($usuario) {
                return $this->login($usuario);
            }
        }
        return false;
    }

    // /**
    //  * Determinar si el usuario está o no logueado en la web
    //  */
    public static function isGuest()
    {
        return self::$app->user == null;
    }

}


?>