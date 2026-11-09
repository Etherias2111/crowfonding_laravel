# Seguridad antes de publicar CROWFONDING

- Configura el servidor web con `public/` como raíz del sitio. No uses la raíz del repositorio como `DocumentRoot`.
- Prepara `.env` fuera del directorio público; usa `APP_ENV=production`, `APP_DEBUG=false`, la URL HTTPS real y una `APP_KEY` generada para producción.
- En HTTPS, conserva `SESSION_SECURE_COOKIE=true` y `SESSION_ENCRYPT=true`. La aplicación los activa de forma predeterminada cuando `APP_ENV=production`.
- Configura una base de datos de producción con un usuario de privilegios mínimos. No publiques `database/database.sqlite` ni los archivos de `storage/`.
- Genera los recursos estáticos con `npm run build`. No incluyas `public/hot` ni expongas un servidor Vite de desarrollo.
- Verifica que el transporte de correo y la verificación de cuentas estén operativos antes de depender de ellos en producción.
- Conserva HTTPS en todo el sitio; el middleware añade HSTS únicamente cuando la petición llega por HTTPS en producción.
- Antes de publicar, revisa el servidor y el proxy para que no expongan archivos de configuración, logs, respaldos, `.env` ni contenido de `vendor/`.
- Mantén los paquetes de compilación (Vite, Tailwind y Passkeys) en `devDependencies`; ejecuta `npm audit` y `composer audit` antes de cada publicación.
- Exige 2FA para las cuentas administradoras en producción. Las imágenes nuevas se guardan en almacenamiento privado y se sirven mediante una ruta que solo acepta nombres y tipos permitidos. Las imágenes antiguas de `public/uploads/` y las imágenes estáticas de `public/images/` dependen de las reglas `.htaccess`; si usas otro servidor web, configura allí una regla equivalente para impedir la ejecución de scripts.
- La CSP de producción utiliza nonce para scripts Vite/Livewire y permite `unsafe-eval` por compatibilidad con Alpine/Livewire. Revisa violaciones antes de endurecerla más o retirarlo.
