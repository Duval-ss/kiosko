-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 16-10-2026 a las 17:35:26
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
-- Base de datos: `kiosko_junior`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas`
--

CREATE TABLE `cajas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `monto_inicial` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_ventas` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monto_cierre` decimal(10,2) DEFAULT NULL,
  `fecha_apertura` datetime NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `estado` enum('ABIERTA','CERRADA') NOT NULL DEFAULT 'ABIERTA',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cajas`
--

INSERT INTO `cajas` (`id`, `usuario_id`, `monto_inicial`, `total_ventas`, `monto_cierre`, `fecha_apertura`, `fecha_cierre`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 50.00, 51.50, NULL, '2026-09-28 00:27:57', NULL, 'ABIERTA', '2026-09-28 05:27:57', '2026-10-15 21:23:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('PRODUCTO','COMIDA') NOT NULL DEFAULT 'PRODUCTO',
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `tipo`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Golosinas', 'PRODUCTO', 'Chocolates, caramelos y productos similares', 1, '2026-09-26 05:04:57', '2026-09-26 05:04:57'),
(2, 'Comida', 'PRODUCTO', NULL, 1, '2026-09-28 18:57:58', '2026-09-28 18:57:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `numero_compra` varchar(255) NOT NULL,
  `proveedor` varchar(255) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` enum('REGISTRADA','ANULADA') NOT NULL DEFAULT 'REGISTRADA',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `user_id`, `numero_compra`, `proveedor`, `fecha`, `total`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 'COM-20261002-0001', 'Inka Kola', '2026-10-02 16:23:59', 30.00, 'REGISTRADA', '2026-10-02 21:23:59', '2026-10-02 21:23:59'),
(2, 1, 'COM-20261002-0002', 'Inka Cola', '2026-10-02 16:27:43', 30.00, 'REGISTRADA', '2026-10-02 21:27:44', '2026-10-02 21:27:44'),
(3, 1, 'COM-20261002-0003', 'Alicorp', '2026-10-02 16:31:50', 14.00, 'REGISTRADA', '2026-10-02 21:31:51', '2026-10-02 21:31:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compras`
--

CREATE TABLE `detalle_compras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `compra_id` bigint(20) UNSIGNED NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad_paquetes` int(11) NOT NULL,
  `unidades_por_paquete` int(11) NOT NULL,
  `unidades_ingresadas` int(11) NOT NULL,
  `precio_paquete` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_compras`
--

INSERT INTO `detalle_compras` (`id`, `compra_id`, `producto_id`, `cantidad_paquetes`, `unidades_por_paquete`, `unidades_ingresadas`, `precio_paquete`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 1, 12, 12, 30.00, 30.00, '2026-10-02 21:23:59', '2026-10-02 21:23:59'),
(2, 2, 5, 1, 12, 12, 30.00, 30.00, '2026-10-02 21:27:44', '2026-10-02 21:27:44'),
(3, 3, 2, 2, 1, 2, 7.00, 14.00, '2026-10-02 21:31:51', '2026-10-02 21:31:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `venta_id` bigint(20) UNSIGNED NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(10) UNSIGNED NOT NULL,
  `precio_compra_unitario` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id`, `venta_id`, `producto_id`, `cantidad`, `precio_compra_unitario`, `precio_unitario`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 2.00, 3.50, 3.50, '2026-09-26 05:07:12', '2026-09-26 05:07:12'),
(2, 2, 1, 1, 2.00, 3.50, 3.50, '2026-09-27 03:34:43', '2026-09-27 03:34:43'),
(3, 3, 1, 1, 2.00, 3.50, 3.50, '2026-09-27 03:54:00', '2026-09-27 03:54:00'),
(4, 4, 1, 1, 2.00, 3.50, 3.50, '2026-09-27 03:54:10', '2026-09-27 03:54:10'),
(5, 5, 1, 1, 2.00, 3.50, 3.50, '2026-09-27 04:01:35', '2026-09-27 04:01:35'),
(6, 6, 1, 1, 2.00, 3.50, 3.50, '2026-09-27 04:28:53', '2026-09-27 04:28:53'),
(7, 7, 2, 1, 0.00, 2.00, 2.00, '2026-09-27 05:07:11', '2026-09-27 05:07:11'),
(8, 8, 1, 1, 2.00, 3.50, 3.50, '2026-09-28 05:34:05', '2026-09-28 05:34:05'),
(9, 9, 1, 1, 2.00, 3.50, 3.50, '2026-09-28 18:55:24', '2026-09-28 18:55:24'),
(10, 10, 2, 2, 0.00, 2.00, 4.00, '2026-10-01 03:47:40', '2026-10-01 03:47:40'),
(11, 11, 2, 5, 0.00, 2.00, 10.00, '2026-10-01 04:18:38', '2026-10-01 04:18:38'),
(12, 12, 3, 1, 0.00, 8.00, 8.00, '2026-10-01 21:45:37', '2026-10-01 21:45:37'),
(13, 13, 1, 1, 2.00, 3.50, 3.50, '2026-10-09 20:29:51', '2026-10-09 20:29:51'),
(14, 14, 1, 1, 2.00, 3.50, 3.50, '2026-10-15 21:20:49', '2026-10-15 21:20:49'),
(15, 15, 3, 1, 0.00, 8.00, 8.00, '2026-10-15 21:23:15', '2026-10-15 21:23:15'),
(16, 15, 1, 1, 2.00, 3.50, 3.50, '2026-10-15 21:23:15', '2026-10-15 21:23:15'),
(17, 15, 5, 1, 2.50, 4.00, 4.00, '2026-10-15 21:23:15', '2026-10-15 21:23:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_08_21_162843_create_categorias_table', 1),
(5, '2026_08_21_162903_create_productos_table', 1),
(6, '2026_08_21_174740_create_movimientos_inventario_table', 1),
(7, '2026_08_24_140314_create_ventas_table', 1),
(8, '2026_08_24_140346_create_detalle_ventas_table', 1),
(9, '2026_09_04_152835_create_personal_access_tokens_table', 1),
(10, '2026_09_04_153047_add_rol_to_users_table', 1),
(11, '2026_09_25_232446_add_pos_fields_to_ventas_and_detalle_ventas_tables', 1),
(12, '2026_09_28_001043_create_cajas_table', 2),
(13, '2026_09_28_001416_create_cajas_table', 3),
(14, '2026_09_30_234758_agregar_tipo_a_productos_table', 3),
(15, '2026_09_30_235220_hacer_codigo_barras_nullable_en_productos', 3),
(16, '2026_09_30_235242_hacer_codigo_barras_nullable_en_productos', 3),
(17, '2026_10_01_000407_hacer_codigo_barras_nullable_en_productos', 4),
(18, '2026_10_01_001435_agregar_tipo_a_categorias', 5),
(19, '2026_10_02_154701_agregar_control_compra_productos', 6),
(20, '2026_10_02_160502_crear_compras_table', 7),
(21, '2026_10_02_160522_crear_detalle_compras_table', 7),
(22, '2026_10_15_152758_add_caja_id_to_ventas_table', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_inventario`
--

CREATE TABLE `movimientos_inventario` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `cantidad` int(10) UNSIGNED NOT NULL,
  `stock_anterior` int(10) UNSIGNED NOT NULL,
  `stock_nuevo` int(10) UNSIGNED NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `movimientos_inventario`
--

INSERT INTO `movimientos_inventario` (`id`, `producto_id`, `tipo`, `cantidad`, `stock_anterior`, `stock_nuevo`, `motivo`, `referencia`, `created_at`, `updated_at`) VALUES
(1, 1, 'ENTRADA', 20, 0, 20, 'Stock inicial del producto', 'REGISTRO-INICIAL', '2026-09-26 05:06:03', '2026-09-26 05:06:03'),
(2, 1, 'SALIDA', 1, 20, 19, 'Venta de producto', 'VTA-20260926-000001', '2026-09-26 05:07:12', '2026-09-26 05:07:12'),
(3, 1, 'SALIDA', 1, 19, 18, 'Venta de producto', 'VTA-20260926-000002', '2026-09-27 03:34:43', '2026-09-27 03:34:43'),
(4, 1, 'SALIDA', 1, 18, 17, 'Venta de producto', 'VTA-20260926-000003', '2026-09-27 03:54:00', '2026-09-27 03:54:00'),
(5, 1, 'SALIDA', 1, 17, 16, 'Venta de producto', 'VTA-20260926-000004', '2026-09-27 03:54:10', '2026-09-27 03:54:10'),
(6, 1, 'SALIDA', 1, 16, 15, 'Venta de producto', 'VTA-20260926-000005', '2026-09-27 04:01:35', '2026-09-27 04:01:35'),
(7, 1, 'SALIDA', 1, 15, 14, 'Venta de producto', 'VTA-20260926-000006', '2026-09-27 04:28:53', '2026-09-27 04:28:53'),
(8, 2, 'ENTRADA', 10, 0, 10, 'Stock inicial del producto', 'REGISTRO-INICIAL', '2026-09-27 04:31:20', '2026-09-27 04:31:20'),
(9, 2, 'SALIDA', 1, 10, 9, 'Venta de producto', 'VTA-20260927-000007', '2026-09-27 05:07:11', '2026-09-27 05:07:11'),
(10, 1, 'SALIDA', 1, 14, 13, 'Venta de producto', 'VTA-20260928-000008', '2026-09-28 05:34:05', '2026-09-28 05:34:05'),
(11, 1, 'SALIDA', 1, 13, 12, 'Venta de producto', 'VTA-20260928-000009', '2026-09-28 18:55:24', '2026-09-28 18:55:24'),
(12, 3, 'ENTRADA', 10, 0, 10, 'Stock inicial del producto', 'REGISTRO-INICIAL', '2026-09-28 19:00:35', '2026-09-28 19:00:35'),
(13, 2, 'SALIDA', 2, 9, 7, 'Venta de producto', 'VTA-20260930-000010', '2026-10-01 03:47:40', '2026-10-01 03:47:40'),
(14, 2, 'SALIDA', 5, 7, 2, 'Venta de producto', 'VTA-20260930-000011', '2026-10-01 04:18:38', '2026-10-01 04:18:38'),
(15, 3, 'SALIDA', 1, 10, 9, 'Venta de producto', 'VTA-20261001-000012', '2026-10-01 21:45:37', '2026-10-01 21:45:37'),
(16, 4, 'ENTRADA', 10, 0, 10, 'Stock inicial del producto', 'REGISTRO-INICIAL', '2026-10-01 21:56:54', '2026-10-01 21:56:54'),
(17, 5, 'ENTRADA', 12, 0, 12, 'Stock inicial del producto', 'REGISTRO-INICIAL', '2026-10-02 21:03:06', '2026-10-02 21:03:06'),
(18, 5, 'ENTRADA', 12, 12, 24, 'COMPRA', 'COM-20261002-0001', '2026-10-02 21:23:59', '2026-10-02 21:23:59'),
(19, 5, 'ENTRADA', 12, 24, 36, 'COMPRA', 'COM-20261002-0002', '2026-10-02 21:27:44', '2026-10-02 21:27:44'),
(20, 2, 'ENTRADA', 2, 2, 4, 'COMPRA', 'COM-20261002-0003', '2026-10-02 21:31:51', '2026-10-02 21:31:51'),
(21, 1, 'SALIDA', 1, 12, 11, 'Venta de producto', 'VTA-20261009-000013', '2026-10-09 20:29:51', '2026-10-09 20:29:51'),
(22, 1, 'SALIDA', 1, 11, 10, 'Venta de producto', 'VTA-20261015-000014', '2026-10-15 21:20:49', '2026-10-15 21:20:49'),
(23, 3, 'SALIDA', 1, 9, 8, 'Venta de producto', 'VTA-20261015-000015', '2026-10-15 21:23:15', '2026-10-15 21:23:15'),
(24, 1, 'SALIDA', 1, 10, 9, 'Venta de producto', 'VTA-20261015-000015', '2026-10-15 21:23:15', '2026-10-15 21:23:15'),
(25, 5, 'SALIDA', 1, 36, 35, 'Venta de producto', 'VTA-20261015-000015', '2026-10-15 21:23:15', '2026-10-15 21:23:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'angular-kiosko-junior', 'a0336ad2b813ae3bcba8f11ef4f2e24b69a841310291687a3bb776f9ea13691d', '[\"*\"]', '2026-09-26 05:09:04', NULL, '2026-09-26 05:03:19', '2026-09-26 05:09:04'),
(2, 'App\\Models\\User', 1, 'angular-kiosko-junior', '3babcd45322f176316ab571d578f7b86dc64d41871aa447699126978d34dda74', '[\"*\"]', '2026-09-27 04:31:57', NULL, '2026-09-27 03:32:45', '2026-09-27 04:31:57'),
(3, 'App\\Models\\User', 1, 'angular-kiosko-junior', '824693d91bb749f9f307d3b1c34b9d0db79042096dd87d102a2d4ce960b4e434', '[\"*\"]', '2026-09-27 05:07:13', NULL, '2026-09-27 05:06:21', '2026-09-27 05:07:13'),
(4, 'App\\Models\\User', 1, 'angular-kiosko-junior', '725c98c167d84294d5fda2d5863779bd4bb0a95e39398a60604946adec4e68df', '[\"*\"]', '2026-09-27 07:30:00', NULL, '2026-09-27 06:28:30', '2026-09-27 07:30:00'),
(5, 'App\\Models\\User', 1, 'angular-kiosko-junior', '1133cd8e6d85ebb4b104e29a669ba5b3863234716ef49b0a8066fec816ddbcbc', '[\"*\"]', '2026-10-01 03:15:14', NULL, '2026-09-27 07:30:12', '2026-10-01 03:15:14'),
(6, 'App\\Models\\User', 1, 'angular-kiosko-junior', 'ae78b4350a735f25eca3ea9b365d1211fbd137531e1315df85dc35b5bed28209', '[\"*\"]', '2026-09-28 19:21:27', NULL, '2026-09-28 05:26:13', '2026-09-28 19:21:27'),
(7, 'App\\Models\\User', 1, 'angular-kiosko-junior', 'f3fac0b6cecd2621f56dc6714c35e91bb69e7b27b1b198491de96b502631ab3e', '[\"*\"]', '2026-10-01 04:18:45', NULL, '2026-10-01 03:20:45', '2026-10-01 04:18:45'),
(8, 'App\\Models\\User', 1, 'angular-kiosko-junior', '09c8f1c50aa61b539e61af1783774fa97aec4664f3860596ce607a4b2b7dc155', '[\"*\"]', '2026-10-01 04:14:00', NULL, '2026-10-01 03:50:08', '2026-10-01 04:14:00'),
(9, 'App\\Models\\User', 1, 'angular-kiosko-junior', '94526bf849bb8adf7d4d1b916ef4b6af6b3acdcb1252d622c50dd00a3d5f0fb0', '[\"*\"]', '2026-10-01 21:27:57', NULL, '2026-10-01 21:27:02', '2026-10-01 21:27:57'),
(10, 'App\\Models\\User', 1, 'angular-kiosko-junior', '7c96d73f5577ecbdc963b7a106d95369143d5a2c2d9da914d1a3a28581585d77', '[\"*\"]', '2026-10-01 21:56:54', NULL, '2026-10-01 21:44:38', '2026-10-01 21:56:54'),
(11, 'App\\Models\\User', 1, 'angular-kiosko-junior', '7a28b7b2584de5341325df10f181b69f9acaca1de6434e091ed7365efc6d47ac', '[\"*\"]', '2026-10-09 20:40:11', NULL, '2026-10-02 20:38:32', '2026-10-09 20:40:11'),
(12, 'App\\Models\\User', 1, 'angular-kiosko-junior', 'abbf0b32b8f28c0a9f29723967db5d15fc5bd9ba9ef4a4cee65b9bd1eb0d34e3', '[\"*\"]', '2026-10-02 21:03:40', NULL, '2026-10-02 21:01:27', '2026-10-02 21:03:40'),
(13, 'App\\Models\\User', 1, 'angular-kiosko-junior', 'f6fb28012e8f7fd892ae9bf8649c7db8af3cce482faa46b210750a114c4dcc1c', '[\"*\"]', '2026-10-15 21:01:08', NULL, '2026-10-09 20:49:37', '2026-10-15 21:01:08'),
(14, 'App\\Models\\User', 1, 'angular-kiosko-junior', '45e6558e7ffc6c7bc23ed074c8b738a1905b66df13828d9fd15ebb0405e35b25', '[\"*\"]', '2026-10-15 21:17:51', NULL, '2026-10-15 21:03:47', '2026-10-15 21:17:51'),
(15, 'App\\Models\\User', 1, 'angular-kiosko-junior', '6842632c0908d8380d0095dbc21379ce2533c53e618db3ed244c28f8d5a6c136', '[\"*\"]', '2026-10-15 21:32:53', NULL, '2026-10-15 21:19:30', '2026-10-15 21:32:53'),
(16, 'App\\Models\\User', 1, 'angular-kiosko-junior', 'd4e4994bb079d0d7b7b29a07e1efa0a3f3e7aa451d29edb9ecacf5eeb06f0697', '[\"*\"]', '2026-10-15 21:48:57', NULL, '2026-10-15 21:33:01', '2026-10-15 21:48:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tipo` enum('PRODUCTO','COMIDA') NOT NULL DEFAULT 'PRODUCTO',
  `categoria_id` bigint(20) UNSIGNED NOT NULL,
  `codigo_barras` varchar(50) DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `unidad_compra` varchar(255) NOT NULL DEFAULT 'UNIDAD',
  `cantidad_por_paquete` int(11) NOT NULL DEFAULT 1,
  `descripcion` text DEFAULT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `costo_unitario` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_venta` decimal(10,2) NOT NULL,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `stock_minimo` int(10) UNSIGNED NOT NULL DEFAULT 5,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `tipo`, `categoria_id`, `codigo_barras`, `nombre`, `unidad_compra`, `cantidad_por_paquete`, `descripcion`, `precio_compra`, `costo_unitario`, `precio_venta`, `stock`, `stock_minimo`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'PRODUCTO', 1, '775003', 'Chocolate', 'UNIDAD', 1, 'Chocolate para venta en kiosko', 2.00, 0.00, 3.50, 9, 5, 1, '2026-09-26 05:06:03', '2026-10-15 21:23:15'),
(2, 'PRODUCTO', 1, '12345', 'Galleta', 'UNIDAD', 1, 'Galleta', 7.00, 7.00, 2.00, 4, 2, 1, '2026-09-27 04:31:20', '2026-10-02 21:31:51'),
(3, 'PRODUCTO', 2, '12456', 'aji de gallina', 'UNIDAD', 1, 'arroz papa huevo \n\n\n\n\n\n\n\n\n\n\n\n\narros papa huevo y aceituna', 0.00, 0.00, 8.00, 8, 5, 1, '2026-09-28 19:00:35', '2026-10-15 21:23:15'),
(4, 'PRODUCTO', 1, '24536467', 'yogurt', 'UNIDAD', 1, 'yogurt', 1.00, 0.00, 3.00, 10, 5, 1, '2026-10-01 21:56:54', '2026-10-01 21:56:54'),
(5, 'PRODUCTO', 1, '7750012345678', 'Inca Kola', 'PAQUETE', 12, 'Bebida gaseosa', 30.00, 2.50, 4.00, 35, 5, 1, '2026-10-02 21:03:06', '2026-10-15 21:23:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(20) NOT NULL DEFAULT 'VENDEDOR',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `rol`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin@kiosko.com', NULL, '$2y$12$leErrW5dVrWqgEPDdvR7o.BrqwRLfxswZoPIyzEGKUXUcUEvHOjZa', 'ADMINISTRADOR', NULL, '2026-09-26 05:01:37', '2026-10-09 20:49:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `caja_id` bigint(20) UNSIGNED DEFAULT NULL,
  `numero_venta` varchar(30) NOT NULL,
  `fecha` datetime NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `metodo_pago` varchar(20) NOT NULL DEFAULT 'EFECTIVO',
  `monto_recibido` decimal(12,2) NOT NULL DEFAULT 0.00,
  `vuelto` decimal(12,2) NOT NULL DEFAULT 0.00,
  `estado` varchar(20) NOT NULL DEFAULT 'COMPLETADA',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `user_id`, `caja_id`, `numero_venta`, `fecha`, `subtotal`, `total`, `metodo_pago`, `monto_recibido`, `vuelto`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'VTA-20260926-000001', '2026-09-26 00:07:12', 3.50, 3.50, 'EFECTIVO', 5.00, 1.50, 'COMPLETADA', '2026-09-26 05:07:12', '2026-09-26 05:07:12'),
(2, 1, NULL, 'VTA-20260926-000002', '2026-09-26 22:34:43', 3.50, 3.50, 'EFECTIVO', 5.00, 1.50, 'COMPLETADA', '2026-09-27 03:34:43', '2026-09-27 03:34:43'),
(3, 1, NULL, 'VTA-20260926-000003', '2026-09-26 22:54:00', 3.50, 3.50, 'YAPE', 3.50, 0.00, 'COMPLETADA', '2026-09-27 03:54:00', '2026-09-27 03:54:00'),
(4, 1, NULL, 'VTA-20260926-000004', '2026-09-26 22:54:10', 3.50, 3.50, 'EFECTIVO', 5.00, 1.50, 'COMPLETADA', '2026-09-27 03:54:10', '2026-09-27 03:54:10'),
(5, 1, NULL, 'VTA-20260926-000005', '2026-09-26 23:01:35', 3.50, 3.50, 'YAPE', 3.50, 0.00, 'COMPLETADA', '2026-09-27 04:01:35', '2026-09-27 04:01:35'),
(6, 1, NULL, 'VTA-20260926-000006', '2026-09-26 23:28:53', 3.50, 3.50, 'EFECTIVO', 5.00, 1.50, 'COMPLETADA', '2026-09-27 04:28:53', '2026-09-27 04:28:53'),
(7, 1, NULL, 'VTA-20260927-000007', '2026-09-27 00:07:11', 2.00, 2.00, 'EFECTIVO', 5.00, 3.00, 'COMPLETADA', '2026-09-27 05:07:11', '2026-09-27 05:07:11'),
(8, 1, NULL, 'VTA-20260928-000008', '2026-09-28 00:34:05', 3.50, 3.50, 'EFECTIVO', 5.00, 1.50, 'COMPLETADA', '2026-09-28 05:34:05', '2026-09-28 05:34:05'),
(9, 1, NULL, 'VTA-20260928-000009', '2026-09-28 13:55:24', 3.50, 3.50, 'EFECTIVO', 5.00, 1.50, 'COMPLETADA', '2026-09-28 18:55:24', '2026-09-28 18:55:24'),
(10, 1, NULL, 'VTA-20260930-000010', '2026-09-30 22:47:40', 4.00, 4.00, 'EFECTIVO', 5.00, 1.00, 'COMPLETADA', '2026-10-01 03:47:40', '2026-10-01 03:47:40'),
(11, 1, NULL, 'VTA-20260930-000011', '2026-09-30 23:18:38', 10.00, 10.00, 'EFECTIVO', 20.00, 10.00, 'COMPLETADA', '2026-10-01 04:18:38', '2026-10-01 04:18:38'),
(12, 1, NULL, 'VTA-20261001-000012', '2026-10-01 16:45:37', 8.00, 8.00, 'EFECTIVO', 20.00, 12.00, 'COMPLETADA', '2026-10-01 21:45:37', '2026-10-01 21:45:37'),
(13, 1, NULL, 'VTA-20261009-000013', '2026-10-09 15:29:51', 3.50, 3.50, 'EFECTIVO', 5.00, 1.50, 'COMPLETADA', '2026-10-09 20:29:51', '2026-10-09 20:29:51'),
(14, 1, 1, 'VTA-20261015-000014', '2026-10-15 16:20:49', 3.50, 3.50, 'EFECTIVO', 5.00, 1.50, 'COMPLETADA', '2026-10-15 21:20:49', '2026-10-15 21:20:49'),
(15, 1, 1, 'VTA-20261015-000015', '2026-10-15 16:23:15', 15.50, 15.50, 'EFECTIVO', 20.00, 4.50, 'COMPLETADA', '2026-10-15 21:23:15', '2026-10-15 21:23:15');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indices de la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cajas_usuario_id_foreign` (`usuario_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categorias_nombre_unique` (`nombre`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `compras_numero_compra_unique` (`numero_compra`),
  ADD KEY `compras_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detalle_compras_compra_id_foreign` (`compra_id`),
  ADD KEY `detalle_compras_producto_id_foreign` (`producto_id`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `detalle_ventas_venta_id_producto_id_unique` (`venta_id`,`producto_id`),
  ADD KEY `detalle_ventas_producto_id_foreign` (`producto_id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movimientos_inventario_producto_id_foreign` (`producto_id`),
  ADD KEY `movimientos_inventario_tipo_created_at_index` (`tipo`,`created_at`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `productos_codigo_barras_unique` (`codigo_barras`),
  ADD KEY `productos_categoria_id_foreign` (`categoria_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_rol_index` (`rol`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ventas_numero_venta_unique` (`numero_venta`),
  ADD KEY `ventas_user_id_foreign` (`user_id`),
  ADD KEY `ventas_fecha_index` (`fecha`),
  ADD KEY `ventas_estado_index` (`estado`),
  ADD KEY `ventas_caja_fecha_index` (`caja_id`,`fecha`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cajas`
--
ALTER TABLE `cajas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD CONSTRAINT `cajas_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD CONSTRAINT `detalle_compras_compra_id_foreign` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_compras_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `detalle_ventas_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD CONSTRAINT `movimientos_inventario_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_caja_id_foreign` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ventas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
