# Subir Nexora a Hostinger

Guía paso a paso para poner este proyecto en producción en Hostinger. La
base de datos local ya quedó lista para esto: solo tiene el Plan Único de
Cuentas (PUC) y un usuario administrador (`Nexora` / `Nexora`) — todo lo
demás (productos, mesas, cajas, facturas...) se crea desde cero en
producción, usando la app real.

> **Cambia la contraseña `Nexora` apenas entres por primera vez** —
> quedó así solo para que puedas iniciar sesión de inmediato.

## 1. Prepara la base de datos en Hostinger

1. En hPanel → **Bases de datos → Bases de datos MySQL**, crea una base
   nueva (anota el nombre, usuario y contraseña que te asigna Hostinger —
   suelen llevar el prefijo de tu cuenta, ej. `u123456789_nexora`).
2. Abre **phpMyAdmin** desde esa misma pantalla, entra a la base que
   creaste, y en la pestaña **Importar** sube el archivo
   `app_system_LIMPIO_para_hostinger.sql` que te dejé (es el volcado ya
   limpio: solo PUC + usuario Nexora). Esto crea todas las tablas y deja
   la base exactamente como la tienes en local ahora mismo.

## 2. Sube los archivos del proyecto

**Con Git (recomendado, si tu plan lo permite):**
```bash
git push
```
y luego clona/actualiza desde SSH en el servidor. **Con File Manager /
FTP** (planes sin SSH): comprime todo el proyecto **excepto** `.git/`,
`node_modules/`, `scratch/` y este mismo `.env.production.example`
convertido ya en `.env`, y súbelo a tu carpeta del dominio.

## 3. El punto más importante: la carpeta pública

Laravel sirve la app desde `public/`, no desde la raíz del proyecto.
Hostinger, según tu plan:

- **Si tu plan permite elegir la raíz del documento** (hPanel → Sitios
  web → tu dominio → Configuración avanzada): apunta el dominio a la
  carpeta `public/` del proyecto. Es la forma más limpia.
- **Si no te deja elegir raíz** (hosting compartido básico, dominio debe
  servir desde `public_html/`): sube el proyecto completo a una carpeta
  **fuera** de `public_html` (ej. `nexora-app/`), y luego:
  1. Copia el **contenido** de `nexora-app/public/` dentro de
     `public_html/`.
  2. Edita `public_html/index.php`: cambia las dos líneas que apuntan a
     `__DIR__.'/../vendor/autoload.php'` y
     `__DIR__.'/../bootstrap/app.php'` para que en su lugar apunten a
     `__DIR__.'/../nexora-app/vendor/autoload.php'` y
     `__DIR__.'/../nexora-app/bootstrap/app.php'`.

## 4. Configura el `.env` en el servidor

Copia `.env.production.example` a `.env` **en el servidor** (nunca lo
subas desde tu máquina, `.env` no va en git) y completa:
- `APP_URL` con tu dominio real.
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` con los datos exactos que
  Hostinger te dio en el paso 1.
- Genera un `APP_KEY` propio para producción con
  `php artisan key:generate --force` (si tienes SSH/Compositor) — no
  reutilices el de tu entorno local.

## 5. Instala dependencias y deja todo listo

Si tienes **SSH** (planes Business/Cloud) o el **Compositor** de hPanel:
```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```
Si tu plan **no tiene SSH ni Composer**, corre esos mismos comandos en tu
máquina local apuntando a una copia del proyecto (con un `.env` de
prueba), y sube la carpeta `vendor/` ya generada junto con el resto de
archivos.

## 6. Permisos (si tienes acceso por SSH/Terminal de hPanel)

```bash
chmod -R 775 storage bootstrap/cache
```

## 7. Verifica

Entra a tu dominio, inicia sesión con `Nexora` / `Nexora`, cambia la
contraseña, y empieza a configurar tu negocio real: Datos del Emisor,
Bodegas, Cajas, Impresoras, Mesas y Zonas, Productos — en ese orden
suele ser más cómodo, porque cada uno depende un poco del anterior.
