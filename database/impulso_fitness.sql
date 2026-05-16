-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-05-2026 a las 20:33:17
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `impulso_fitness`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_estados_membresias` ()   BEGIN
    UPDATE socio_membresia
    SET activa = 0
    WHERE fecha_fin <= NOW();

    UPDATE socios s
    SET s.estado = 'inactivo'
    WHERE s.estado <> 'suspendido'
      AND NOT EXISTS (
        SELECT 1
        FROM socio_membresia sm
        WHERE sm.socio_id = s.id
          AND sm.activa = 1
          AND sm.fecha_inicio <= NOW()
          AND sm.fecha_fin > NOW()
    );

    UPDATE socios s
    SET s.estado = 'activo'
    WHERE s.estado <> 'suspendido'
      AND EXISTS (
        SELECT 1
        FROM socio_membresia sm
        WHERE sm.socio_id = s.id
          AND sm.activa = 1
          AND sm.fecha_inicio <= NOW()
          AND sm.fecha_fin > NOW()
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_socio_completo` (IN `p_id` INT, IN `p_nombres` VARCHAR(100), IN `p_apellido_paterno` VARCHAR(80), IN `p_apellido_materno` VARCHAR(80), IN `p_fecha_nacimiento` DATE, IN `p_telefono` VARCHAR(20), IN `p_email` VARCHAR(100), IN `p_genero` VARCHAR(20), IN `p_contacto_emergencia_nombre` VARCHAR(100), IN `p_contacto_emergencia_telefono` VARCHAR(20), IN `p_calle` VARCHAR(120), IN `p_numero` VARCHAR(20), IN `p_colonia` VARCHAR(120), IN `p_codigo_postal` CHAR(5), IN `p_notas` TEXT)   BEGIN
    UPDATE socios
    SET
        nombres = p_nombres,
        apellido_paterno = p_apellido_paterno,
        apellido_materno = p_apellido_materno,
        fecha_nacimiento = p_fecha_nacimiento,
        telefono = p_telefono,
        email = p_email,
        genero = p_genero,
        contacto_emergencia_nombre = p_contacto_emergencia_nombre,
        contacto_emergencia_telefono = p_contacto_emergencia_telefono,
        calle = p_calle,
        numero = p_numero,
        colonia = p_colonia,
        codigo_postal = p_codigo_postal,
        notas = p_notas
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_buscar_socios_para_membresia` (IN `p_busqueda` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci)   BEGIN
    DECLARE v_busqueda VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

    SET v_busqueda = IFNULL(p_busqueda, _utf8mb4'' COLLATE utf8mb4_general_ci);

    SELECT 
        s.id,
        CONCAT_WS(_utf8mb4' ' COLLATE utf8mb4_general_ci, s.nombres, s.apellido_paterno, s.apellido_materno) AS nombre,
        s.telefono,
        s.email,
        s.estado
    FROM socios s
    WHERE 
        v_busqueda = _utf8mb4'' COLLATE utf8mb4_general_ci
        OR CONCAT_WS(_utf8mb4' ' COLLATE utf8mb4_general_ci, s.nombres, s.apellido_paterno, s.apellido_materno)
            LIKE CONCAT(_utf8mb4'%' COLLATE utf8mb4_general_ci, v_busqueda, _utf8mb4'%' COLLATE utf8mb4_general_ci)
        OR s.email
            LIKE CONCAT(_utf8mb4'%' COLLATE utf8mb4_general_ci, v_busqueda, _utf8mb4'%' COLLATE utf8mb4_general_ci)
        OR CAST(s.id AS CHAR)
            LIKE CONCAT(_utf8mb4'%' COLLATE utf8mb4_general_ci, v_busqueda, _utf8mb4'%' COLLATE utf8mb4_general_ci)
    ORDER BY nombre ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_desactivar_socio` (IN `p_id` INT)   BEGIN
    UPDATE socios
    SET estado = 'suspendido'
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_historial_pagos_membresia` (IN `p_busqueda` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci, IN `p_fecha_inicio` DATE, IN `p_fecha_fin` DATE)   BEGIN
    DECLARE v_busqueda VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

    SET v_busqueda = IFNULL(p_busqueda, _utf8mb4'' COLLATE utf8mb4_general_ci);

    SELECT 
        pm.id,
        CONCAT_WS(_utf8mb4' ' COLLATE utf8mb4_general_ci, s.nombres, s.apellido_paterno, s.apellido_materno) AS socio_nombre,
        pm.fecha_pago,
        pm.metodo_pago,
        m.nombre AS membresia_nombre,
        pm.monto
    FROM pagos_membresia pm
    INNER JOIN socios s ON pm.socio_id = s.id
    INNER JOIN membresias m ON pm.membresia_id = m.id
    WHERE (
        v_busqueda = _utf8mb4'' COLLATE utf8mb4_general_ci
        OR CONCAT_WS(_utf8mb4' ' COLLATE utf8mb4_general_ci, s.nombres, s.apellido_paterno, s.apellido_materno)
            LIKE CONCAT(_utf8mb4'%' COLLATE utf8mb4_general_ci, v_busqueda, _utf8mb4'%' COLLATE utf8mb4_general_ci)
        OR s.email
            LIKE CONCAT(_utf8mb4'%' COLLATE utf8mb4_general_ci, v_busqueda, _utf8mb4'%' COLLATE utf8mb4_general_ci)
        OR CAST(s.id AS CHAR)
            LIKE CONCAT(_utf8mb4'%' COLLATE utf8mb4_general_ci, v_busqueda, _utf8mb4'%' COLLATE utf8mb4_general_ci)
    )
    AND (p_fecha_inicio IS NULL OR DATE(pm.fecha_pago) >= p_fecha_inicio)
    AND (p_fecha_fin IS NULL OR DATE(pm.fecha_pago) <= p_fecha_fin)
    ORDER BY pm.fecha_pago DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_socio_completo` (IN `p_nombres` VARCHAR(100), IN `p_apellido_paterno` VARCHAR(80), IN `p_apellido_materno` VARCHAR(80), IN `p_fecha_nacimiento` DATE, IN `p_telefono` VARCHAR(20), IN `p_email` VARCHAR(100), IN `p_genero` VARCHAR(20), IN `p_contacto_emergencia_nombre` VARCHAR(100), IN `p_contacto_emergencia_telefono` VARCHAR(20), IN `p_calle` VARCHAR(120), IN `p_numero` VARCHAR(20), IN `p_colonia` VARCHAR(120), IN `p_codigo_postal` CHAR(5), IN `p_notas` TEXT)   BEGIN

    INSERT INTO socios (
        nombres,
        apellido_paterno,
        apellido_materno,
        fecha_nacimiento,
        telefono,
        email,
        genero,
        contacto_emergencia_nombre,
        contacto_emergencia_telefono,
        calle,
        numero,
        colonia,
        codigo_postal,
        notas,
        estado,
        fecha_registro
    )
    VALUES (
        p_nombres,
        p_apellido_paterno,
        p_apellido_materno,
        p_fecha_nacimiento,
        p_telefono,
        p_email,
        p_genero,
        p_contacto_emergencia_nombre,
        p_contacto_emergencia_telefono,
        p_calle,
        p_numero,
        p_colonia,
        p_codigo_postal,
        p_notas,
        'inactivo',
        CURDATE()
    );

    SELECT LAST_INSERT_ID() AS id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_inventario_movimientos_listar` (IN `p_busqueda` VARCHAR(100))   BEGIN
    SELECT 
        im.id,
        im.producto_id,
        p.nombre AS producto,
        p.codigo,
        p.categoria,
        im.tipo,
        im.cantidad,
        im.fecha,
        im.referencia,
        im.observaciones
    FROM inventario_movimientos im
    INNER JOIN productos p ON p.id = im.producto_id
    WHERE 
        p_busqueda IS NULL OR p_busqueda = ''
        OR p.nombre LIKE CONCAT('%', p_busqueda, '%')
        OR p.codigo LIKE CONCAT('%', p_busqueda, '%')
        OR im.tipo LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY im.fecha DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_inventario_registrar_movimiento` (IN `p_producto_id` INT, IN `p_usuario_id` INT, IN `p_tipo` ENUM('entrada','salida','ajuste'), IN `p_cantidad` INT, IN `p_referencia` VARCHAR(100), IN `p_observaciones` TEXT)   BEGIN
    DECLARE v_stock_actual INT DEFAULT 0;
    DECLARE v_activo TINYINT DEFAULT 0;

    SELECT stock, activo
    INTO v_stock_actual, v_activo
    FROM productos
    WHERE id = p_producto_id
    LIMIT 1;

    IF v_activo IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El producto no existe.';
    END IF;

    IF v_activo = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No se pueden registrar movimientos de un producto inactivo.';
    END IF;

    IF p_tipo NOT IN ('entrada', 'salida', 'ajuste') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Tipo de movimiento inválido.';
    END IF;

    IF p_tipo IN ('entrada', 'salida') AND p_cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cantidad debe ser mayor a 0.';
    END IF;

    IF p_tipo = 'ajuste' AND p_cantidad < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El ajuste no puede dejar stock negativo.';
    END IF;

    IF p_tipo = 'salida' AND v_stock_actual < p_cantidad THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stock insuficiente para realizar la salida.';
    END IF;

    IF p_tipo = 'entrada' THEN
        UPDATE productos
        SET stock = stock + p_cantidad
        WHERE id = p_producto_id;

    ELSEIF p_tipo = 'salida' THEN
        UPDATE productos
        SET stock = stock - p_cantidad
        WHERE id = p_producto_id;

    ELSEIF p_tipo = 'ajuste' THEN
        UPDATE productos
        SET stock = p_cantidad
        WHERE id = p_producto_id;
    END IF;

    INSERT INTO inventario_movimientos (
        producto_id,
        usuario_id,
        tipo,
        cantidad,
        referencia,
        observaciones
    )
    VALUES (
        p_producto_id,
        p_usuario_id,
        p_tipo,
        p_cantidad,
        NULLIF(TRIM(p_referencia), ''),
        p_observaciones
    );

    SELECT LAST_INSERT_ID() AS movimiento_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_membresias` ()   BEGIN
    SELECT 
        id,
        nombre,
        duracion_dias,
        precio,
        descripcion
    FROM membresias
    ORDER BY precio ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_socios` ()   BEGIN
    SELECT
        id,
        nombres,
        apellido_paterno,
        apellido_materno,
        CONCAT_WS(' ', nombres, apellido_paterno, apellido_materno) AS nombre,
        telefono,
        email,
        fecha_nacimiento,
        estado,
        fecha_registro,
        genero,
        contacto_emergencia_nombre,
        contacto_emergencia_telefono,
        calle,
        numero,
        colonia,
        codigo_postal,
        CONCAT_WS(' ', calle, numero, colonia, codigo_postal) AS direccion,
        notas
    FROM socios
    ORDER BY id DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_historial_reciente_pagos_socio` (IN `p_socio_id` INT)   BEGIN
    SELECT 
        pm.id,
        pm.socio_id,
        pm.membresia_id,
        m.nombre AS membresia_nombre,
        pm.monto,
        pm.metodo_pago,
        pm.fecha_pago,
        pm.referencia
    FROM pagos_membresia pm
    INNER JOIN membresias m 
        ON pm.membresia_id = m.id
    WHERE pm.socio_id = p_socio_id
    ORDER BY pm.fecha_pago DESC
    LIMIT 5;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_huella_socio` (IN `p_socio_id` INT)   BEGIN
    SELECT id, socio_id, huella_hash, fecha_registro
    FROM socio_biometria
    WHERE socio_id = p_socio_id
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_membresia_activa_socio` (IN `p_socio_id` INT)   BEGIN
    SELECT 
        sm.id,
        sm.socio_id,
        sm.membresia_id,
        sm.fecha_inicio,
        sm.fecha_fin,
        sm.activa,
        m.nombre AS membresia_nombre,
        m.precio,
        m.duracion_dias,
        DATEDIFF(sm.fecha_fin, NOW()) AS dias_restantes
    FROM socio_membresia sm
    INNER JOIN membresias m ON sm.membresia_id = m.id
    WHERE sm.socio_id = p_socio_id
      AND sm.activa = 1
      AND sm.fecha_inicio <= NOW()
      AND sm.fecha_fin > NOW()
    ORDER BY sm.fecha_fin DESC
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_membresia_por_nombre` (IN `p_nombre` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci)   BEGIN
    SELECT 
        id,
        nombre,
        duracion_dias,
        precio,
        descripcion
    FROM membresias
    WHERE nombre COLLATE utf8mb4_general_ci = p_nombre
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_recibo_membresia` (IN `p_pago_id` INT)   BEGIN
    SELECT 
        pm.id AS pago_id,
        pm.fecha_pago,
        pm.metodo_pago,
        pm.monto,
        pm.referencia,

        s.id AS socio_id,
        CONCAT_WS(' ', s.nombres, s.apellido_paterno, s.apellido_materno) AS socio,
        s.telefono,
        s.email,
        s.estado,

        m.nombre AS membresia,
        m.duracion_dias,
        m.precio,

        sm.fecha_inicio,
        sm.fecha_fin,
        sm.activa
    FROM pagos_membresia pm
    INNER JOIN socios s 
        ON pm.socio_id = s.id
    INNER JOIN membresias m 
        ON pm.membresia_id = m.id
    LEFT JOIN socio_membresia sm
        ON sm.socio_id = pm.socio_id
        AND sm.membresia_id = pm.membresia_id
    WHERE pm.id = p_pago_id
    ORDER BY sm.id DESC
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_socio_por_id` (IN `p_id` INT)   BEGIN
    SELECT
        id,
        nombres,
        apellido_paterno,
        apellido_materno,
        CONCAT_WS(' ', nombres, apellido_paterno, apellido_materno) AS nombre,
        fecha_nacimiento,
        telefono,
        email,
        estado,
        fecha_registro,
        genero,
        contacto_emergencia_nombre,
        contacto_emergencia_telefono,
        calle,
        numero,
        colonia,
        codigo_postal,
        CONCAT_WS(' ', calle, numero, colonia, codigo_postal) AS direccion,
        notas
    FROM socios
    WHERE id = p_id
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_productos_listar` (IN `p_busqueda` VARCHAR(100))   BEGIN
    SELECT 
        id,
        codigo,
        nombre,
        categoria,
        descripcion,
        costo_compra,
        precio_venta,
        stock,
        stock_minimo,
        icono,
        activo,
        CASE 
            WHEN stock <= 0 THEN 'Agotado'
            WHEN stock <= stock_minimo THEN 'Por agotarse'
            ELSE 'Stock OK'
        END AS estado_stock
    FROM productos
    WHERE activo = 1
      AND (
        p_busqueda IS NULL OR p_busqueda = ''
        OR nombre LIKE CONCAT('%', p_busqueda, '%')
        OR codigo LIKE CONCAT('%', p_busqueda, '%')
        OR categoria LIKE CONCAT('%', p_busqueda, '%')
      )
    ORDER BY nombre ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_productos_stock_bajo` ()   BEGIN
    SELECT 
        id,
        codigo,
        nombre,
        categoria,
        stock,
        stock_minimo,
        icono,
        CASE 
            WHEN stock <= 0 THEN 'Agotado'
            ELSE 'Por agotarse'
        END AS estado_stock
    FROM productos
    WHERE activo = 1
      AND stock <= stock_minimo
    ORDER BY stock ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_baja_logica` (IN `p_id` INT)   BEGIN
    DECLARE v_existe INT DEFAULT 0;

    SELECT COUNT(*)
    INTO v_existe
    FROM productos
    WHERE id = p_id
      AND activo = 1;

    IF v_existe = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El producto no existe o ya está inactivo.';
    END IF;

    UPDATE productos
    SET activo = 0
    WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_guardar` (IN `p_id` INT, IN `p_codigo` VARCHAR(30), IN `p_nombre` VARCHAR(100), IN `p_categoria` VARCHAR(100), IN `p_descripcion` TEXT, IN `p_costo_compra` DECIMAL(10,2), IN `p_precio_venta` DECIMAL(10,2), IN `p_stock` INT, IN `p_stock_minimo` INT, IN `p_icono` VARCHAR(50))   BEGIN
    DECLARE v_nuevo_codigo VARCHAR(30);
    DECLARE v_existe INT DEFAULT 0;

    IF p_nombre IS NULL OR TRIM(p_nombre) = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El nombre del producto es obligatorio.';
    END IF;

    IF p_costo_compra < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El costo de compra no puede ser negativo.';
    END IF;

    IF p_precio_venta <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El precio de venta debe ser mayor a 0.';
    END IF;

    IF p_stock_minimo < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El stock mínimo no puede ser negativo.';
    END IF;

    IF p_id IS NULL OR p_id = 0 THEN

        IF p_stock < 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El stock inicial no puede ser negativo.';
        END IF;

        SELECT CONCAT('PR', LPAD(COALESCE(MAX(id), 0) + 1, 3, '0'))
        INTO v_nuevo_codigo
        FROM productos;

        INSERT INTO productos (
            codigo,
            nombre,
            categoria,
            descripcion,
            costo_compra,
            precio_venta,
            stock,
            stock_minimo,
            icono,
            activo
        )
        VALUES (
            v_nuevo_codigo,
            TRIM(p_nombre),
            NULLIF(TRIM(p_categoria), ''),
            p_descripcion,
            p_costo_compra,
            p_precio_venta,
            p_stock,
            p_stock_minimo,
            IFNULL(NULLIF(TRIM(p_icono), ''), 'bi-box-seam'),
            1
        );

        SELECT LAST_INSERT_ID() AS producto_id;

    ELSE

        SELECT COUNT(*)
        INTO v_existe
        FROM productos
        WHERE id = p_id;

        IF v_existe = 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El producto no existe.';
        END IF;

        UPDATE productos
        SET 
            nombre = TRIM(p_nombre),
            categoria = NULLIF(TRIM(p_categoria), ''),
            descripcion = p_descripcion,
            costo_compra = p_costo_compra,
            precio_venta = p_precio_venta,
            stock_minimo = p_stock_minimo,
            icono = IFNULL(NULLIF(TRIM(p_icono), ''), 'bi-box-seam')
        WHERE id = p_id;

        SELECT p_id AS producto_id;

    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_obtener` (IN `p_id` INT)   BEGIN
    SELECT 
        id,
        codigo,
        nombre,
        categoria,
        descripcion,
        costo_compra,
        precio_venta,
        stock,
        stock_minimo,
        icono,
        activo,
        fecha_creacion,
        fecha_actualizacion
    FROM productos
    WHERE id = p_id
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_huella_socio` (IN `p_socio_id` INT, IN `p_huella_hash` VARBINARY(512))   BEGIN
    INSERT INTO socio_biometria (socio_id, huella_hash)
    VALUES (p_socio_id, p_huella_hash)
    ON DUPLICATE KEY UPDATE
        huella_hash = p_huella_hash,
        fecha_registro = CURRENT_TIMESTAMP;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_pago` (IN `p_socio_id` INT, IN `p_membresia_id` INT, IN `p_fecha_inicio` DATETIME, IN `p_monto` DECIMAL(10,2), IN `p_metodo` VARCHAR(50))   BEGIN
    DECLARE v_duracion INT;
    DECLARE v_fecha_inicio DATETIME;
    DECLARE v_fecha_fin DATETIME;
    DECLARE v_fecha_fin_actual DATETIME;
    DECLARE v_estado_socio VARCHAR(20);
    DECLARE v_pago_id INT;
    DECLARE v_referencia VARCHAR(50);

    SELECT estado
    INTO v_estado_socio
    FROM socios
    WHERE id = p_socio_id
    LIMIT 1;

    IF v_estado_socio IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El socio no existe.';
    END IF;

    IF v_estado_socio = 'suspendido' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El socio está suspendido. No se puede registrar pago.';
    END IF;

    SELECT duracion_dias
    INTO v_duracion
    FROM membresias
    WHERE id = p_membresia_id
    LIMIT 1;

    IF v_duracion IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La membresía no existe.';
    END IF;

    SELECT MAX(fecha_fin)
    INTO v_fecha_fin_actual
    FROM socio_membresia
    WHERE socio_id = p_socio_id
      AND activa = 1
      AND fecha_fin > NOW();

    SET v_fecha_inicio = IFNULL(p_fecha_inicio, NOW());

    IF v_fecha_fin_actual IS NOT NULL AND v_fecha_fin_actual > v_fecha_inicio THEN
        SET v_fecha_inicio = v_fecha_fin_actual;
    END IF;

    SET v_fecha_fin = DATE_ADD(v_fecha_inicio, INTERVAL v_duracion DAY);

    IF v_fecha_inicio <= NOW() THEN
        UPDATE socio_membresia
        SET activa = 0
        WHERE socio_id = p_socio_id
          AND activa = 1;
    END IF;

    INSERT INTO pagos_membresia (
        socio_id,
        membresia_id,
        monto,
        metodo_pago
    )
    VALUES (
        p_socio_id,
        p_membresia_id,
        p_monto,
        p_metodo
    );

    SET v_pago_id = LAST_INSERT_ID();

    SET v_referencia = CONCAT(
        'REC-',
        DATE_FORMAT(NOW(), '%Y%m%d'),
        '-',
        LPAD(v_pago_id, 6, '0')
    );

    UPDATE pagos_membresia
    SET referencia = v_referencia
    WHERE id = v_pago_id;

    INSERT INTO socio_membresia (
        socio_id,
        membresia_id,
        fecha_inicio,
        fecha_fin,
        activa
    )
    VALUES (
        p_socio_id,
        p_membresia_id,
        v_fecha_inicio,
        v_fecha_fin,
        CASE
            WHEN v_fecha_inicio <= NOW() AND v_fecha_fin > NOW()
            THEN 1
            ELSE 0
        END
    );

    UPDATE socios
    SET estado = CASE
        WHEN v_fecha_inicio <= NOW() AND v_fecha_fin > NOW()
        THEN 'activo'
        ELSE estado
    END
    WHERE id = p_socio_id;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accesos`
--

CREATE TABLE `accesos` (
  `id` int(11) NOT NULL,
  `socio_id` int(11) NOT NULL,
  `membresia_id` int(11) DEFAULT NULL,
  `resultado` enum('permitido','denegado') NOT NULL,
  `motivo` varchar(150) DEFAULT NULL,
  `fecha_hora` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instructores`
--

CREATE TABLE `instructores` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `horas_diarias` int(11) DEFAULT 8,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instructor_horarios`
--

CREATE TABLE `instructor_horarios` (
  `id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `dia_semana` enum('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') DEFAULT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_movimientos`
--

CREATE TABLE `inventario_movimientos` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `tipo` enum('entrada','salida','ajuste') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `referencia` varchar(100) DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventario_movimientos`
--

INSERT INTO `inventario_movimientos` (`id`, `producto_id`, `usuario_id`, `tipo`, `cantidad`, `fecha`, `referencia`, `observaciones`) VALUES
(1, 1, 1, 'entrada', 2, '2026-05-09 00:28:21', 'Compra proveedor', 'Entrada de recepción 08-05-2026'),
(2, 1, 1, 'entrada', 3, '2026-05-16 08:58:37', 'PRUEBA-ENTRADA', 'Prueba de entrada manual'),
(3, 1, 1, 'salida', 1, '2026-05-16 08:58:54', 'PRUEBA-SALIDA', 'Prueba de salida manual'),
(4, 4, 1, 'entrada', 10, '2026-05-16 18:21:22', 'Ajuste fisico', 'Prueba'),
(5, 3, 1, 'salida', 5, '2026-05-16 18:28:39', 'Ajuste fisico', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membresias`
--

CREATE TABLE `membresias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `duracion_dias` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `membresias`
--

INSERT INTO `membresias` (`id`, `nombre`, `duracion_dias`, `precio`, `descripcion`) VALUES
(1, 'Pase Diario', 1, 50.00, 'Acceso por 1 día'),
(2, 'Pase Semanal', 7, 200.00, 'Acceso por 7 días'),
(3, 'Pase Mensual', 30, 500.00, 'Acceso por 30 días');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `socio_id` int(11) DEFAULT NULL,
  `tipo` enum('membresia_por_vencer','membresia_vencida','inventario_bajo') NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `leida` tinyint(1) DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_membresia`
--

CREATE TABLE `pagos_membresia` (
  `id` int(11) NOT NULL,
  `socio_id` int(11) NOT NULL,
  `membresia_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','tarjeta','transferencia') NOT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp(),
  `referencia` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos_membresia`
--

INSERT INTO `pagos_membresia` (`id`, `socio_id`, `membresia_id`, `monto`, `metodo_pago`, `fecha_pago`, `referencia`) VALUES
(1, 2, 1, 50.00, 'efectivo', '2026-04-20 04:18:48', NULL),
(2, 9, 1, 50.00, 'tarjeta', '2026-04-20 04:38:29', NULL),
(3, 7, 3, 500.00, 'transferencia', '2026-04-20 04:41:30', NULL),
(4, 10, 3, 500.00, 'transferencia', '2026-04-21 00:20:14', NULL),
(5, 11, 2, 200.00, 'efectivo', '2026-04-21 01:15:14', NULL),
(6, 12, 1, 50.00, 'efectivo', '2026-04-21 02:09:06', NULL),
(7, 13, 1, 50.00, 'transferencia', '2026-04-21 03:22:03', NULL),
(8, 14, 3, 500.00, 'transferencia', '2026-04-27 07:24:05', NULL),
(9, 15, 1, 50.00, 'efectivo', '2026-04-27 08:28:18', NULL),
(10, 16, 3, 500.00, 'transferencia', '2026-04-27 08:30:26', NULL),
(11, 5, 2, 200.00, 'tarjeta', '2026-04-27 08:31:20', NULL),
(12, 17, 2, 200.00, 'transferencia', '2026-04-27 15:27:42', NULL),
(13, 12, 1, 50.00, 'efectivo', '2026-04-27 20:40:29', NULL),
(14, 11, 1, 50.00, 'tarjeta', '2026-04-27 20:41:29', NULL),
(15, 13, 3, 500.00, 'transferencia', '2026-04-27 20:42:16', NULL),
(16, 12, 1, 50.00, 'tarjeta', '2026-04-27 20:42:58', NULL),
(17, 4, 1, 50.00, 'efectivo', '2026-04-27 20:45:20', NULL),
(18, 11, 1, 50.00, 'efectivo', '2026-04-27 21:25:44', 'REC-20260427-000018'),
(19, 11, 3, 500.00, 'transferencia', '2026-05-09 04:04:38', 'REC-20260508-000019'),
(20, 20, 3, 500.00, 'transferencia', '2026-05-14 07:55:31', 'REC-20260514-000020'),
(21, 12, 1, 50.00, 'efectivo', '2026-05-15 04:21:08', 'REC-20260514-000021'),
(22, 21, 2, 200.00, 'transferencia', '2026-05-15 04:25:32', 'REC-20260514-000022'),
(23, 22, 1, 50.00, 'tarjeta', '2026-05-15 06:41:05', 'REC-20260514-000023'),
(24, 23, 1, 50.00, 'tarjeta', '2026-05-15 17:37:19', 'REC-20260515-000024'),
(25, 23, 1, 50.00, 'tarjeta', '2026-05-16 06:29:07', 'REC-20260515-000025'),
(26, 20, 1, 50.00, 'tarjeta', '2026-05-16 07:04:47', 'REC-20260516-000026'),
(27, 21, 2, 200.00, 'tarjeta', '2026-05-16 07:14:06', 'REC-20260516-000027'),
(28, 6, 1, 50.00, 'efectivo', '2026-05-16 07:29:15', 'REC-20260516-000028'),
(29, 19, 1, 50.00, 'transferencia', '2026-05-16 07:51:21', 'REC-20260516-000029'),
(30, 18, 1, 50.00, 'efectivo', '2026-05-16 07:54:40', 'REC-20260516-000030');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `codigo` varchar(30) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `costo_compra` decimal(10,2) DEFAULT 0.00,
  `precio_venta` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `stock_minimo` int(11) DEFAULT 5,
  `icono` varchar(50) DEFAULT 'bi-box-seam',
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `codigo`, `nombre`, `categoria`, `descripcion`, `costo_compra`, `precio_venta`, `stock`, `stock_minimo`, `icono`, `activo`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'PR001', 'Proteína Whey', 'Suplementos', '', 600.00, 1200.00, 8, 2, 'bi-capsule', 1, '2026-05-08 16:55:12', '2026-05-16 08:58:54'),
(2, 'PR002', 'Proteína Itholate', 'Suplementos', '', 800.00, 1300.00, 3, 1, 'bi-capsule', 1, '2026-05-09 00:32:12', '2026-05-09 00:32:12'),
(3, 'PR003', 'Agua', 'Bebidas', '', 15.00, 25.00, 95, 8, 'bi-cup-straw', 1, '2026-05-09 04:10:08', '2026-05-16 18:28:39'),
(4, 'PR004', 'Producto prueba', 'Prueba', 'Producto temporal para validar SP', 10.00, 20.00, 15, 2, 'bi-box-seam', 1, '2026-05-16 08:57:59', '2026-05-16 18:21:22'),
(5, 'PR005', 'Monster Energy', 'Bebidas', '', 35.00, 45.00, 12, 6, 'bi-lightning-charge', 1, '2026-05-16 16:52:12', '2026-05-16 16:52:12'),
(6, 'PR006', 'Producto equis', 'Suplementos', '', 100.00, 200.00, 8, 2, 'bi-capsule', 0, '2026-05-16 18:31:08', '2026-05-16 18:31:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `socios`
--

CREATE TABLE `socios` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellido_paterno` varchar(80) DEFAULT NULL,
  `apellido_materno` varchar(80) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `estado` enum('activo','inactivo','suspendido') NOT NULL DEFAULT 'inactivo',
  `fecha_registro` date NOT NULL,
  `genero` varchar(20) DEFAULT NULL,
  `contacto_emergencia_nombre` varchar(100) DEFAULT NULL,
  `contacto_emergencia_telefono` varchar(20) DEFAULT NULL,
  `calle` varchar(120) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `colonia` varchar(120) DEFAULT NULL,
  `codigo_postal` char(5) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `socios`
--

INSERT INTO `socios` (`id`, `user_id`, `nombres`, `apellido_paterno`, `apellido_materno`, `telefono`, `email`, `fecha_nacimiento`, `estado`, `fecha_registro`, `genero`, `contacto_emergencia_nombre`, `contacto_emergencia_telefono`, `calle`, `numero`, `colonia`, `codigo_postal`, `direccion`, `notas`) VALUES
(1, NULL, 'Juan Perez López', NULL, NULL, '6691272667', 'juanperez@gmail.com', '2000-09-10', 'inactivo', '2026-04-16', 'Masculino', 'Juan Ramos', '4444444444', 'San Matias', NULL, NULL, NULL, 'San Matias', 'ninguna'),
(2, NULL, 'Miguel Lizarraga Osuna', NULL, NULL, '6692481620', 'cris00@gmail.com', '2006-03-19', 'inactivo', '2026-04-17', 'Masculino', 'Alondra Hernandez', '6691666339', 'San Juan 23', NULL, NULL, NULL, 'San Juan 23', 'ninguna'),
(3, NULL, 'Juan', 'Ramos', 'Hernandez', '8889001231', 'juaito@live.com.mx', '1966-07-16', 'inactivo', '2026-04-18', 'Masculino', 'Fanny Cañedo', '3784920204', 'San Matias', NULL, NULL, NULL, 'San Matias', 'OKK'),
(4, NULL, 'Gabriela', 'Mora', 'Ramirez', '6690000000', 'gabo@gmail.com', '2005-10-19', 'inactivo', '2026-04-19', 'Femenino', '6690000001', '6690000001', 'San Jose #1244 Azul Marino, Mazatlan', NULL, NULL, NULL, 'San Jose #1244 Azul Marino, Mazatlan', 'Este socio no registro ninguna nota adicional.'),
(5, NULL, 'Maria Navarro Lopez', NULL, NULL, '6691666335', 'mari@live.com.mx', '1980-07-08', 'inactivo', '2026-04-19', 'Femenino', 'Alondra Hernandez', '6691666335', 'San Martin 89 Real Pacifico, Mazatlan', NULL, NULL, NULL, 'San Martin 89 Real Pacifico, Mazatlan', 'Este socio tiene una lesión en el hombro derecho'),
(6, NULL, 'Juan Manuel Silva', NULL, NULL, '0000000000', 'juan2@outlook.com', '2002-04-12', 'activo', '2026-04-19', 'Masculino', '7892289202', '7892289202', 'Calle 78 #456 Terrranova, Mazatlan', NULL, NULL, NULL, 'Calle 78 #456 Terrranova, Mazatlan', 'Sin notas adicionales'),
(7, NULL, 'Jose Hernandez Cañedo', NULL, NULL, '1111111111', 'jose@live.com', '2000-02-10', 'activo', '2026-04-19', 'Masculino', 'Alondra Hernandez', '2222222222', 'Calle Juan Grijalva #389', NULL, NULL, NULL, 'Calle Juan Grijalva #389', 'Ninguna'),
(8, NULL, 'Ivana', 'Ramos', 'De la Cruz', '6691666339', 'ivana@gmail.com', '1990-09-13', 'inactivo', '2026-04-19', 'Femenino', 'Juan Ramos', '6691272560', 'San Matias', '67', 'Real Pacifico', '82124', 'San Matias 67 Real Pacifico', 'NINGUNA'),
(9, NULL, 'Oscar Torres Olivier', NULL, NULL, '6692497367', 'oscar3@outlook.com', '2006-01-23', 'inactivo', '2026-04-19', 'Masculino', 'Cristobal Lizarraga', '6699248162', 'San Marcos #45 Real Del Valle', NULL, NULL, NULL, 'San Marcos #45 Real Del Valle', 'niguno'),
(10, NULL, 'Zaira', NULL, NULL, '6682345678', 'zai@gmail.com', '2002-06-23', 'activo', '2026-04-20', 'Femenino', 'Juan Ramos', '1111111111', 'San Matias #123 Colinas', NULL, NULL, NULL, 'San Matias #123 Colinas', 'Ninguna'),
(11, NULL, 'Carlos', 'Camacho', 'Sanchez', '3333333333', 'carcar@outlook.com', '1990-07-30', 'activo', '2026-04-20', 'Masculino', 'Maria Tejeda', '9999999999', 'Calle Ola #345, Fracc. Haciendas, Mazatlán', NULL, NULL, NULL, 'Calle Ola #345, Fracc. Haciendas, Mazatlán', 'Este socio tiene una lesión en el hombro izquierdo'),
(12, NULL, 'Angel Urias Lopez', NULL, NULL, '0000000000', 'angel1@hotmail.com', '2001-01-01', 'activo', '2026-04-20', 'Masculino', 'Zaira Ramos', '2222222222', 'CALLE 2 , FRACC REAL , MOCHIS', NULL, NULL, NULL, 'CALLE 2 , FRACC REAL , MOCHIS', 'NINGUNA'),
(13, NULL, 'Fanny de la Cruz', NULL, NULL, '8888888888', 'fan@hotmail.com', '2005-01-01', 'activo', '2026-04-20', 'Femenino', 'Juan Rene', '4444444458', 'Calle 78 San Rafael', NULL, NULL, NULL, 'Calle 78 San Rafael', 'niguna'),
(14, NULL, 'Doralin', 'Zavala', 'Partida', '7777777777', 'dora@hotmail.com', '2000-05-12', 'activo', '2026-04-27', 'Femenino', 'Juan Rene', '0000000000', 'morelos', '893', 'hagdkcjhcd', '91038', 'Calle 3 #1344 San Rafael', 'Todo ok'),
(15, NULL, 'Eduardo', 'Osuna', 'Roa', '6692543219', 'edu@hotmail.com', '1980-02-01', 'inactivo', '2026-04-27', 'Otro', 'Maria Rendon', '4444444444', 'Calle 2', '234', 'Real Pacifico', '98819', 'Calle 2 #345 Real Pacifico', 'NINGUNA'),
(16, NULL, 'Roberto Juarez Mojica', NULL, NULL, '4444444444', 'rober@live.com.mx', '1998-11-30', 'activo', '2026-04-27', 'Masculino', 'Ivana Ramos', '5555555555', 'Calle 6 #45 Colinas, Mazatlán', NULL, NULL, NULL, 'Calle 6 #45 Colinas, Mazatlán', 'NINGUNA'),
(17, NULL, 'Zaira', 'Ra', 'C', '6687123646', 'zaizair@live.com.mx', '2002-08-23', 'inactivo', '2026-04-27', 'Femenino', 'Juan Perez', '6691666335', 'San Matias 78 Lagos', NULL, NULL, NULL, 'San Matias 78 Lagos', 'ninguna'),
(18, NULL, 'Alejandro', 'Bustamante', 'Hernandez', '1234567890', 'alejo@gmail.com', '1990-09-19', 'activo', '2026-05-14', 'Masculino', 'Zaira Ramos', '0000000000', 'San Jose', '1', 'Real del Valle', '82124', NULL, 'Este socio es entrenador personal.'),
(19, NULL, 'Alfredo', 'Gomez', 'Díaz', '7388278492', 'alf345@outlook.com', '1980-11-04', 'activo', '2026-05-14', 'Masculino', 'Sheyla Gomez', '2222222222', 'Insurgentes', '89', 'Centro', '82100', NULL, ''),
(20, NULL, 'Gael', 'Muñoz', 'Estrada', '0000000000', 'gaelm34@outlook.com', '2026-05-15', 'suspendido', '2026-05-14', 'Otro', 'Esthela Zamora', '2874207478', 'Catalina', 'jjuimh', 'hgg', '82124', NULL, 'jhhj'),
(21, NULL, 'Zai', 'Ramos', 'De la Cruz', '6691272667', 'arizazaira23@gmail.com', '2002-06-23', 'activo', '2026-05-14', 'Femenino', 'Ivana Ramos de la Cruz', '6692543213', 'San Matías', 'hola', 'Real del Valle', '82124', NULL, ''),
(22, NULL, 'Olivier', 'Juarez', 'Perez', '6692218518', 'ollie69@gmail.com', '2006-06-06', 'inactivo', '2026-05-14', 'Femenino', 'Oscar Armando Lizarraga', '3679204082', 'Catalina', '2874', 'Hacienda de Seminario', '82242', NULL, 'lol'),
(23, NULL, 'Oscar', 'Liz', 'uhfnv', '5555555555', 'jashsis@gamil.com', '2007-02-11', 'activo', '2026-05-15', 'Masculino', 'Maria Rendon', '4444444444', 'Palos Prietos', '234', 'Francisco Villa', '53522', NULL, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `socio_biometria`
--

CREATE TABLE `socio_biometria` (
  `id` int(11) NOT NULL,
  `socio_id` int(11) NOT NULL,
  `huella_hash` varbinary(512) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `socio_biometria`
--

INSERT INTO `socio_biometria` (`id`, `socio_id`, `huella_hash`, `fecha_registro`) VALUES
(1, 4, 0x32353862373031356266373633353336626266313262636566636638653737666437396637313139316163363031333065353162316638626536373262393139, '2026-04-19 21:27:42'),
(2, 5, 0x34346232343032613162656263333561333138623136663831343736636234323866376665313631313635643466356633303933656438333365643932653937, '2026-04-20 01:09:39'),
(3, 6, 0x35653934646565363735646264396438383931363262643031633931366633376433613835613630336533363466396365663835333861653239363438393265, '2026-04-19 23:33:10'),
(4, 7, 0x32623538396131636339383039666633656236663136626535613632393732366233356432326638386536343166353738613437363035356365343163323433, '2026-04-19 23:59:00'),
(5, 8, 0x35653530373336356536636130643233383133323863346563353139643664333663363530623639646238323433363263393035613564303034343434653537, '2026-04-20 00:07:07'),
(7, 2, 0x36356532373334386531373864363262616263333130393434343565393536383632656564356236383364396238326434633530633966323934356638356632, '2026-04-20 02:08:14'),
(8, 9, 0x34653162643334356234323634336135663432396537386465636663333330303637373462626432363637373964633536636331663163633030303162623237, '2026-04-20 04:37:37'),
(9, 10, 0x62323532313665616663333764313536613364366634396563383539383031383335366664646163316166303866383264616131353862376363366263326235, '2026-04-21 00:19:51'),
(10, 11, 0x31636139323136323832333039376135333538313632306137316239373339626530626237303638616132613030383530616331613533373364343137356231, '2026-04-21 01:14:49'),
(11, 12, 0x61336333663566366636306361643662353965643033316166396630666437336662326631393939323130623161663963393464623233623764646231393232, '2026-04-21 02:07:46'),
(12, 13, 0x63303461396161356334396437646131623937393966336138303736326663336665373139343130346337623266303062373534393364333661336264353930, '2026-04-21 03:21:23'),
(13, 14, 0x65386634386264633833343534306234393731653166343665323030303466633035323830353665613862643134363732653536313438646465643633356632, '2026-04-27 07:23:48'),
(14, 15, 0x63306261326438663865313834326636633061633734316665653065363430306562323635633733346136333131323062643261363739323866396135303238, '2026-04-27 08:01:39'),
(15, 16, 0x66343830386563333231653933366537323034616538383132613762653630316538646463326366653661633831623137356664643437333863646535656464, '2026-04-27 08:30:15'),
(16, 17, 0x63383135306432633332373162663734643739356262313230333438616634326661366538383439313337336234326462393937333366393730633765616439, '2026-04-27 15:27:26'),
(17, 19, 0x37616135383834373137353238363339663331666262303538623162613863353366383662323035306161303062393061353131333836333137313061396631, '2026-05-14 07:45:44'),
(18, 20, 0x62376465303435633834633036383166313830636661653231373664356138303136643236313564616166303535383933376635363665303761616136316333, '2026-05-14 07:54:56'),
(19, 21, 0x35663239346330333361613933306639323131333739623562366663373233376465663835643335633364303737663837333735633237373335653734306135, '2026-05-15 04:24:57'),
(20, 22, 0x37366639636161366137303363303333393463636231613265323039363530653130353237363631393764303565373233336166333334376631353965386233, '2026-05-15 06:40:39'),
(21, 23, 0x31646165306364343465396538396266663866306532353562323032666433313364303133366532656630353330323763666536616164366235363133396637, '2026-05-15 17:36:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `socio_instructor_sesiones`
--

CREATE TABLE `socio_instructor_sesiones` (
  `id` int(11) NOT NULL,
  `socio_id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `asistio` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `socio_membresia`
--

CREATE TABLE `socio_membresia` (
  `id` int(11) NOT NULL,
  `socio_id` int(11) NOT NULL,
  `membresia_id` int(11) NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `activa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `socio_membresia`
--

INSERT INTO `socio_membresia` (`id`, `socio_id`, `membresia_id`, `fecha_inicio`, `fecha_fin`, `activa`) VALUES
(1, 2, 1, '2026-04-20 00:00:00', '2026-04-21 00:00:00', 0),
(2, 9, 1, '2026-04-21 00:00:00', '2026-04-22 00:00:00', 0),
(3, 7, 3, '2026-04-21 00:00:00', '2026-05-21 00:00:00', 1),
(4, 10, 3, '2026-04-22 00:00:00', '2026-05-22 00:00:00', 1),
(5, 11, 2, '2026-04-20 00:00:00', '2026-04-27 00:00:00', 0),
(6, 12, 1, '2026-04-20 00:00:00', '2026-04-21 00:00:00', 0),
(7, 13, 1, '2026-04-20 00:00:00', '2026-04-21 00:00:00', 0),
(8, 14, 3, '2026-04-27 00:00:00', '2026-05-27 00:00:00', 1),
(9, 15, 1, '2026-04-27 00:00:00', '2026-04-28 00:00:00', 0),
(10, 16, 3, '2026-04-27 00:00:00', '2026-05-27 00:00:00', 1),
(11, 5, 2, '2026-04-27 00:00:00', '2026-05-04 00:00:00', 0),
(12, 17, 2, '2026-04-27 00:00:00', '2026-05-04 00:00:00', 0),
(13, 12, 1, '2026-04-26 05:00:00', '2026-04-27 05:00:00', 0),
(14, 11, 1, '2026-04-27 22:41:00', '2026-04-28 22:41:00', 0),
(15, 13, 3, '2026-04-26 10:41:00', '2026-05-26 10:41:00', 1),
(16, 12, 1, '2026-04-27 13:42:00', '2026-04-28 13:42:00', 0),
(17, 4, 1, '2026-04-27 13:30:00', '2026-04-28 13:30:00', 0),
(18, 11, 1, '2026-04-27 08:25:00', '2026-04-28 08:25:00', 0),
(19, 11, 3, '2026-05-09 06:04:00', '2026-06-08 06:04:00', 1),
(20, 20, 3, '2026-05-14 09:54:00', '2026-06-13 09:54:00', 1),
(21, 12, 1, '2026-05-15 21:20:00', '2026-05-16 21:20:00', 1),
(22, 21, 2, '2026-05-15 21:25:00', '2026-05-22 21:25:00', 1),
(23, 22, 1, '2026-05-14 08:40:00', '2026-05-15 08:40:00', 0),
(24, 23, 1, '2026-05-15 09:36:00', '2026-05-16 09:36:00', 0),
(25, 23, 1, '2026-05-15 20:28:00', '2026-05-16 20:28:00', 1),
(26, 20, 1, '2026-06-13 09:54:00', '2026-06-14 09:54:00', 0),
(27, 21, 2, '2026-05-22 21:25:00', '2026-05-29 21:25:00', 0),
(28, 6, 1, '2026-05-16 00:29:00', '2026-05-17 00:29:00', 1),
(29, 19, 1, '2026-05-16 00:51:00', '2026-05-17 00:51:00', 1),
(30, 18, 1, '2026-05-16 00:00:00', '2026-05-17 00:00:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('Admin','Recepcion','Instructor','Socio') NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `email`, `role`, `active`, `created_at`) VALUES
(1, 'admin', '$2y$10$mj38z2gP55q6owo.DG/yuej.tkqxWhtHtPd30fbFOHctIxqdkG3ge', 'admin@impulso.com', 'Admin', 1, '2026-04-17 05:52:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_biometrics`
--

CREATE TABLE `user_biometrics` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `biometric_hash` varbinary(512) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `socio_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','tarjeta','transferencia') NOT NULL DEFAULT 'efectivo',
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_detalle`
--

CREATE TABLE `venta_detalle` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesos`
--
ALTER TABLE `accesos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_accesos_membresia` (`membresia_id`),
  ADD KEY `idx_socio_fecha` (`socio_id`,`fecha_hora`);

--
-- Indices de la tabla `instructores`
--
ALTER TABLE `instructores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indices de la tabla `instructor_horarios`
--
ALTER TABLE `instructor_horarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `instructor_id` (`instructor_id`);

--
-- Indices de la tabla `inventario_movimientos`
--
ALTER TABLE `inventario_movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `membresias`
--
ALTER TABLE `membresias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `socio_id` (`socio_id`);

--
-- Indices de la tabla `pagos_membresia`
--
ALTER TABLE `pagos_membresia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `socio_id` (`socio_id`),
  ADD KEY `membresia_id` (`membresia_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `socios`
--
ALTER TABLE `socios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indices de la tabla `socio_biometria`
--
ALTER TABLE `socio_biometria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `socio_id` (`socio_id`);

--
-- Indices de la tabla `socio_instructor_sesiones`
--
ALTER TABLE `socio_instructor_sesiones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `socio_id` (`socio_id`,`fecha`),
  ADD KEY `instructor_id` (`instructor_id`);

--
-- Indices de la tabla `socio_membresia`
--
ALTER TABLE `socio_membresia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `socio_id` (`socio_id`),
  ADD KEY `membresia_id` (`membresia_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `user_biometrics`
--
ALTER TABLE `user_biometrics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `socio_id` (`socio_id`);

--
-- Indices de la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesos`
--
ALTER TABLE `accesos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `instructores`
--
ALTER TABLE `instructores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `instructor_horarios`
--
ALTER TABLE `instructor_horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario_movimientos`
--
ALTER TABLE `inventario_movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `membresias`
--
ALTER TABLE `membresias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos_membresia`
--
ALTER TABLE `pagos_membresia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `socios`
--
ALTER TABLE `socios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `socio_biometria`
--
ALTER TABLE `socio_biometria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `socio_instructor_sesiones`
--
ALTER TABLE `socio_instructor_sesiones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `socio_membresia`
--
ALTER TABLE `socio_membresia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `user_biometrics`
--
ALTER TABLE `user_biometrics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accesos`
--
ALTER TABLE `accesos`
  ADD CONSTRAINT `accesos_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`),
  ADD CONSTRAINT `accesos_ibfk_2` FOREIGN KEY (`membresia_id`) REFERENCES `membresias` (`id`),
  ADD CONSTRAINT `fk_accesos_membresia` FOREIGN KEY (`membresia_id`) REFERENCES `membresias` (`id`),
  ADD CONSTRAINT `fk_accesos_socio` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`);

--
-- Filtros para la tabla `instructores`
--
ALTER TABLE `instructores`
  ADD CONSTRAINT `instructores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `instructor_horarios`
--
ALTER TABLE `instructor_horarios`
  ADD CONSTRAINT `instructor_horarios_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `instructores` (`id`);

--
-- Filtros para la tabla `inventario_movimientos`
--
ALTER TABLE `inventario_movimientos`
  ADD CONSTRAINT `inventario_movimientos_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`);

--
-- Filtros para la tabla `pagos_membresia`
--
ALTER TABLE `pagos_membresia`
  ADD CONSTRAINT `pagos_membresia_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`),
  ADD CONSTRAINT `pagos_membresia_ibfk_2` FOREIGN KEY (`membresia_id`) REFERENCES `membresias` (`id`);

--
-- Filtros para la tabla `socios`
--
ALTER TABLE `socios`
  ADD CONSTRAINT `socios_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `socio_biometria`
--
ALTER TABLE `socio_biometria`
  ADD CONSTRAINT `socio_biometria_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`);

--
-- Filtros para la tabla `socio_instructor_sesiones`
--
ALTER TABLE `socio_instructor_sesiones`
  ADD CONSTRAINT `socio_instructor_sesiones_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`),
  ADD CONSTRAINT `socio_instructor_sesiones_ibfk_2` FOREIGN KEY (`instructor_id`) REFERENCES `instructores` (`id`);

--
-- Filtros para la tabla `socio_membresia`
--
ALTER TABLE `socio_membresia`
  ADD CONSTRAINT `socio_membresia_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`),
  ADD CONSTRAINT `socio_membresia_ibfk_2` FOREIGN KEY (`membresia_id`) REFERENCES `membresias` (`id`);

--
-- Filtros para la tabla `user_biometrics`
--
ALTER TABLE `user_biometrics`
  ADD CONSTRAINT `user_biometrics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`);

--
-- Filtros para la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle`
  ADD CONSTRAINT `venta_detalle_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`),
  ADD CONSTRAINT `venta_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
