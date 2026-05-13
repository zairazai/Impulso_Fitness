<?php

/*
|--------------------------------------------------------------------------
| CONTROLADOR DE INVENTARIO
|--------------------------------------------------------------------------
| Este controlador administra toda la lógica del módulo de inventario:
|
| - Guardar productos
| - Actualizar productos
| - Baja lógica de productos
| - Registrar movimientos de inventario
| - Validar permisos por rol
|
| IMPORTANTE:
| Toda comunicación con la BD se realiza mediante el modelo Inventario.php
| y procedimientos almacenados.
*/

/*
|--------------------------------------------------------------------------
| CARGA DE DEPENDENCIAS
|--------------------------------------------------------------------------
| require_once incluye archivos necesarios para que el controlador funcione.
| __DIR__ obtiene la ruta actual del archivo y permite acceder a otros
| archivos usando rutas relativas sin importar desde dónde se ejecute.
|
| Socio.php hacer referencia al  Modelo del módulo de socios.
| app.php  es la Configuración global de la aplicación (BASE_URL).
*/

require_once __DIR__ . '/../models/Inventario.php';
require_once __DIR__ . '/../../config/app.php';

class InventarioController
{
    /*
    |--------------------------------------------------------------------------
    | RUTAS DEL SISTEMA
    |--------------------------------------------------------------------------
    | Centralizamos rutas importantes para reutilizarlas fácilmente.
    */
    private const LOGIN_VIEW = BASE_URL . '/resources/views/auth/login.php';
    private const DASHBOARD_VIEW = BASE_URL . '/resources/views/dashboard.php';

    private const PRODUCTOS_VIEW = BASE_URL . '/routes/inventario_mostrar_productos.php';
    private const ENTRADA_VIEW = BASE_URL . '/resources/views/inventario/entrada.php';
    private const MOVIMIENTOS_VIEW = BASE_URL . '/resources/views/inventario/movimientos.php';

    /*
    |--------------------------------------------------------------------------
    | VALIDAR SESIÓN
    |--------------------------------------------------------------------------
    | Verifica que exista un usuario autenticado.
    */
    private function validarSesion(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header("Location: " . self::LOGIN_VIEW);
            exit;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAR ACCESO A INVENTARIO
    |--------------------------------------------------------------------------
    | Roles permitidos:
    | - Admin
    | - Recepcion
    */
    private function validarRolesInventario(): void
    {
        $this->validarSesion();

        $rol = $_SESSION['user']['role'] ?? '';

        $rolesPermitidos = ['Admin', 'Recepcion'];

        if (!in_array($rol, $rolesPermitidos)) {
            header("Location: " . self::DASHBOARD_VIEW . "?error=sin_permiso");
            exit;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAR SOLO ADMIN
    |--------------------------------------------------------------------------
    | Acciones sensibles:
    | - Crear productos
    | - Editar productos
    | - Baja lógica
    */
    private function validarSoloAdmin(): void
    {
        $this->validarSesion();

        $rol = $_SESSION['user']['role'] ?? '';

        if ($rol !== 'Admin') {
            header("Location: " . self::PRODUCTOS_VIEW . "?error=sin_permiso");
            exit;
        }
    }

    /*
|--------------------------------------------------------------------------
| MOSTRAR PRODUCTOS
|--------------------------------------------------------------------------
| Obtiene la lista de productos desde el modelo y carga la vista.
| De esta forma la vista solo muestra la información y no consulta
| directamente al modelo.
*/
public function productos(): void
{
    $this->validarRolesInventario();

    /*
    |--------------------------------------------------------------------------
    | OBTENER BÚSQUEDA
    |--------------------------------------------------------------------------
    */
    $busqueda = trim($_GET['buscar'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | LLAMAR MODELO
    |--------------------------------------------------------------------------
    */
    $modelo = new Inventario();

    $productos = $modelo->listarProductos($busqueda);

    /*
    |--------------------------------------------------------------------------
    | CARGAR VISTA
    |--------------------------------------------------------------------------
    | Usamos ruta física porque require_once necesita ubicar el archivo
    | real en el servidor.
    */
    require_once __DIR__ . '/../../resources/views/inventario/productos.php';
}

    /*
    |--------------------------------------------------------------------------
    | GUARDAR PRODUCTO
    |--------------------------------------------------------------------------
    | Este método sirve para:
    | - Agregar productos nuevos
    | - Actualizar productos existentes
    |
    | Todo se realiza mediante:
    | sp_producto_guardar
    */
    public function store(): void
    {
        $this->validarSoloAdmin();

        /*
        |--------------------------------------------------------------------------
        | VALIDAR MÉTODO POST
        |--------------------------------------------------------------------------
        */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . self::PRODUCTOS_VIEW);
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | OBTENER DATOS DEL FORMULARIO
        |--------------------------------------------------------------------------
        */
        $id = (int)($_POST['id'] ?? 0);

        $codigo = trim($_POST['codigo'] ?? '');

            if ($id === 0) {
                $codigo = '';
            }
        $nombre = trim($_POST['nombre'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        $costoCompra = (float)($_POST['costo_compra'] ?? 0);
        $precioVenta = (float)($_POST['precio_venta'] ?? 0);

        $stock = (int)($_POST['stock'] ?? 0);
        $stockMinimo = (int)($_POST['stock_minimo'] ?? 0);

        $icono = trim($_POST['icono'] ?? 'bi-box-seam');

        /*
        |--------------------------------------------------------------------------
        | VALIDACIONES BÁSICAS
        |--------------------------------------------------------------------------
        */
        if (

            $nombre === '' ||
            $categoria === '' ||
            $precioVenta <= 0 ||
            $stock < 0 ||
            $stockMinimo < 0
        ) {
            header("Location: " . self::PRODUCTOS_VIEW . "?error=datos");
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | LLAMAR MODELO
        |--------------------------------------------------------------------------
        */
        $modelo = new Inventario();

        $ok = $modelo->guardarProducto(
            $id,
            $codigo,
            $nombre,
            $categoria,
            $descripcion,
            $costoCompra,
            $precioVenta,
            $stock,
            $stockMinimo,
            $icono
        );

        /*
        |--------------------------------------------------------------------------
        | REDIRECCIONES
        |--------------------------------------------------------------------------
        */
        if ($ok) {

            /*
            |--------------------------------------------------------------------------
            | SI EL ID ES MAYOR A 0 = ACTUALIZACIÓN
            |--------------------------------------------------------------------------
            */
            if ($id > 0) {
                header("Location: " . self::PRODUCTOS_VIEW . "?updated=1");
            } else {
                header("Location: " . self::PRODUCTOS_VIEW . "?created=1");
            }

        } else {
            header("Location: " . self::PRODUCTOS_VIEW . "?error=guardar");
        }

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | BAJA LÓGICA DE PRODUCTO
    |--------------------------------------------------------------------------
    | No eliminamos registros físicamente.
    | Solo cambiamos:
    | activo = 0
    */
    public function delete(): void
    {
        $this->validarSoloAdmin();

        /*
        |--------------------------------------------------------------------------
        | VALIDAR MÉTODO POST
        |--------------------------------------------------------------------------
        */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . self::PRODUCTOS_VIEW);
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | OBTENER ID
        |--------------------------------------------------------------------------
        */
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            header("Location: " . self::PRODUCTOS_VIEW . "?error=id");
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | LLAMAR MODELO
        |--------------------------------------------------------------------------
        */
        $modelo = new Inventario();

        $ok = $modelo->bajaLogicaProducto($id);

        /*
        |--------------------------------------------------------------------------
        | REDIRECCIÓN
        |--------------------------------------------------------------------------
        */
        if ($ok) {
            header("Location: " . self::PRODUCTOS_VIEW . "?deleted=1");
        } else {
            header("Location: " . self::PRODUCTOS_VIEW . "?error=delete");
        }

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTRAR MOVIMIENTO DE INVENTARIO
    |--------------------------------------------------------------------------
    | Tipos:
    | - entrada
    | - salida
    | - ajuste
    |
    | Roles permitidos:
    | - Admin
    | - Recepcion
    */
    public function movimientoStore(): void
    {
        $this->validarRolesInventario();

        /*
        |--------------------------------------------------------------------------
        | VALIDAR MÉTODO POST
        |--------------------------------------------------------------------------
        */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . self::ENTRADA_VIEW);
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | OBTENER DATOS
        |--------------------------------------------------------------------------
        */
        $productoId = (int)($_POST['producto_id'] ?? 0);

        $tipo = trim($_POST['tipo'] ?? 'entrada');

        $cantidad = (int)($_POST['cantidad'] ?? 0);

        $referencia = trim($_POST['referencia'] ?? '');

        $observaciones = trim($_POST['observaciones'] ?? '');

        /*
        |--------------------------------------------------------------------------
        | OBTENER USUARIO EN SESIÓN
        |--------------------------------------------------------------------------
        */
        $usuarioId = $_SESSION['user']['id'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | VALIDACIONES
        |--------------------------------------------------------------------------
        */
        if (
            $productoId <= 0 ||
            $cantidad <= 0 ||
            !in_array($tipo, ['entrada', 'salida', 'ajuste'])
        ) {
            header("Location: " . self::ENTRADA_VIEW . "?error=datos");
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | LLAMAR MODELO
        |--------------------------------------------------------------------------
        */
        $modelo = new Inventario();

        $ok = $modelo->registrarMovimiento(
            $productoId,
            $usuarioId ? (int)$usuarioId : null,
            $tipo,
            $cantidad,
            $referencia,
            $observaciones
        );

        /*
        |--------------------------------------------------------------------------
        | REDIRECCIONES
        |--------------------------------------------------------------------------
        */
        if ($ok) {
            header("Location: " . self::MOVIMIENTOS_VIEW . "?movimiento=1");
        } else {
            header("Location: " . self::ENTRADA_VIEW . "?error=movimiento");
        }

        exit;
    }
}