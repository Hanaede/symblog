<?php
    require '../bootstrap.php';
    use App\Controllers\IndexController;
    use App\Controllers\BlogController;
    use App\Controllers\UserController;
    use App\Controllers\AuthController;
    use App\Controllers\AdminController;
    use Aura\Router\RouterContainer;

    session_start();

    if (!isset($_SESSION["profile"])) {
        $_SESSION["user"] = "Invitado";
        $_SESSION["profile"] = "Invitado";
    }

    // Define los atributos que se pueden asignar masivamente
    $request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals(
        $_SERVER,
        $_GET,
        $_POST,
        $_COOKIE,
        $_FILES
    );

    // Crea una instancia del enrutador y obtiene el mapa de rutas
    $router = new RouterContainer();
    $map = $router->getMap();

    // Define las rutas GET, asociándolas con controladores y acciones específicas
    $map->get("home", "/", ['controller'=>IndexController::Class, 'action'=>"IndexAction"]);
    $map->get("about", "/about", ['controller'=>IndexController::Class, 'action'=>"AboutAction"]);
    $map->get("contact", "/contact", ['controller'=>IndexController::Class, 'action'=>"ContactAction", 'auth' => true]);
    $map->get("newBlog", "/addBlog", ['controller'=>BlogController::Class, 'action'=>"NewAction", 'auth' => true]);
    $map->get("showBlog", "/show", ['controller'=>BlogController::Class, 'action'=>"ShowAction"]);
    $map->get("newUser", "/addUser", ['controller'=>UserController::Class, 'action'=>"NewAction", 'auth' => true]);
    $map->get("getLogin", "/login", ['controller'=>AuthController::Class, 'action'=>"GetLoginAction"]);
    $map->get("admin", "/admin", ['controller'=>AdminController::Class, 'action'=>"AdminAction", 'auth' => true]);
    $map->get("logOut", "/logout", ['controller'=>AuthController::Class, 'action'=>"LogOutAction", 'auth' => true]);

   
    // Define las rutas POST, asociándolas con controladores y acciones específicas
    $map->post("addBlog", "/addBlog", ['controller'=>BlogController::Class, 'action'=>"AddAction"]);
    $map->post("addUser", "/addUser", ['controller'=>UserController::Class, 'action'=>"AddAction", 'auth' => true]);
    $map->post("postLogin", "/login", ['controller'=>AuthController::Class, 'action'=>"PostLoginAction"]);
    $map->post("addComment", "/show", ['controller'=>BlogController::Class, 'action'=>"AddCommentAction"]);


    // Obtiene la ruta de la solicitud
    $route = $_GET["route"] ?? "";

    // Obtiene el matcher del enrutador y trata de hacer coincidir la solicitud con una ruta
    $matcher = $router->getMatcher();
    $route=$matcher->match($request);

    // Si no se encuentra una ruta coincidente, muestra "No route"
    if (!$route) {
        echo "No route";
    } else {
        // Obtiene los datos del manejador (controlador y acción) de la ruta coincidente
        $handlerData = $route->handler;

        $controllerName = $handlerData["controller"];
        $actionName = $handlerData["action"];
        $needsAuth = $handlerData["auth"] ?? false;
        $sessionProfile = $_SESSION["profile"] ?? "Invitado";

        // Verifica si la ruta requiere autenticación y si el usuario está autenticado
        if ($needsAuth && $sessionProfile === "Invitado") {
            // Si se requiere autenticación y el usuario no está autenticado, redirige a la página de login
            header("Location: /login");
        } else {
            $controller = new $controllerName;
            $response = $controller->$actionName($request);
            
            // Envía las cabeceras de la respuesta
            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header(sprintf("%s: %s", $name, $value), false);
                }
            }
            echo $response->getBody();
        }
    }