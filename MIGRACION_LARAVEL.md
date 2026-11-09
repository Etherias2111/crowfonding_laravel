# CROWFONDING en Laravel

La aplicación Laravel está en `C:\xampp\htdocs\crowfonding-laravel`. El sitio PHP original `C:\xampp\htdocs\crowfonding` se conservó sin cambios. Laravel usa su propia base SQLite y sus migraciones; la base MySQL del sitio anterior no se modificó.

## Ejecutar en Windows

Abre una nueva PowerShell y entra a la carpeta Laravel:

```powershell
cd C:\xampp\htdocs\crowfonding-laravel
php -v
composer run dev
```

Usa el PHP de Herd 8.4.1 o superior en esa terminal (Laravel 13 no arranca con el PHP 8.0 que trae XAMPP). La web local se abre en <http://localhost:8000>. Si PowerShell dice que no reconoce `php` o `composer`, reiníciala para refrescar el PATH de Herd.

Las migraciones de la base SQLite nueva ya se aplicaron. Para una instalación limpia se ejecuta `php artisan migrate` desde esta carpeta.

## Primer administrador

Registra primero una cuenta normal en `/register`. Luego, en PowerShell desde esta carpeta, asígnale el rol de administrador usando el correo de esa cuenta:

```powershell
php artisan crowfonding:make-admin correo@ejemplo.com
```

Después cierra sesión e inicia de nuevo; aparecerá el enlace Administración. No existe una cuenta de administrador ni una contraseña predeterminada.

## Funciones trasladadas

- Portada, navegación adaptable, tema claro/oscuro, animaciones, catálogo y detalles de equipos.
- Registro e inicio de sesión por correo o nombre de usuario; se conservan los campos al corregir errores.
- Contraseñas de al menos 12 caracteres, con carácter especial, sin tres dígitos repetidos, secuencias de tres cifras ni pares consecutivos como 2211.
- Tickets solo para usuarios autenticados, con máximo de uno cada tres horas por cuenta.
- Panel de cliente, edición de perfil y avatar; administración de tickets, equipos y listado de usuarios.
- Notificaciones por correo mediante el sistema de correo de Laravel. En esta copia local el `.env` mantiene el mailer de registro (`log`), así que no se envía correo externo hasta configurar SMTP allí.

## Importar los datos del sitio anterior

El código y las imágenes se trasladaron, pero aún no se importaron las cuentas, tickets ni filas de `productos_venta`: esos datos viven en la base MySQL original y no se mezclaron con la SQLite nueva. La nueva aplicación incluye el comando de importación. Haz primero un respaldo privado de MySQL; crea en `.env` las variables `LEGACY_DB_*` del ejemplo, usando un usuario MySQL con permiso de solo lectura para esa base, y ejecuta `php artisan crowfonding:import-legacy`. El comando cancela la importación si las tablas destino ya tienen registros, mantiene los IDs y hashes de contraseña y no modifica MySQL. Retira las variables `LEGACY_DB_*` al terminar. No pegues credenciales ni el respaldo SQL en el chat.

Las imágenes de equipos y avatares que existían en `images` y `uploads` se copian a `public/images` y `public/uploads`; no copies `.htaccess` ni archivos ocultos. Los nombres de campos esperados corresponden a las tablas actuales `usuarios`, `productos_venta` y `tickets`.

## Producción

Antes de publicar, configura `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` con el dominio HTTPS y las variables de base de datos/correo del servidor en su entorno privado. Ejecuta `php artisan migrate --force` y `php artisan optimize` durante el despliegue, sirve el directorio `public` como raíz del sitio y configura HTTPS. No publiques `.env`, `database/database.sqlite`, registros ni respaldos.
