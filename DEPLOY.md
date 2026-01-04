# Guía de Despliegue en cPanel

## 1. Archivos

Sube todo el contenido de tu proyecto a la carpeta de tu servidor (ej. `/home/usuario/repositories/api_test`).

**Importante:**
La carpeta `public` debe ser la única visible desde la web.

- Opción A: Configura el "Document Root" de tu dominio/subdominio apuntando a `/projects/api_test/public`.
- Opción B (Si no puedes cambiar el root): Copia el contenido de `public` a `public_html`, pero tendrás que editar `index.php` para apuntar correctamente a `../src` y `../vendor` (esto es más sucio).

## 2. Base de Datos

1. Crea una base de datos MySQL en cPanel.
2. Ve a PHPMyAdmin e importa el archivo `database.sql` que te he generado en la raíz del proyecto.
3. Crea un Usuario MySQL y asígnale permisos a esa base de datos.
4. Anota: Nombre de DB, Usuario y Contraseña.

## 3. Configuración (.env)

En tu servidor, crea o edita el archivo `.env` en la raíz del proyecto (¡NO en public_html!).

```env
DB_HOST=localhost
DB_NAME=tu_base_de_datos_cpanel
DB_USER=tu_usuario_cpanel
DB_PASS=tu_password_cpanel

# Genera una clave segura y LARGA para JWT (min 32 chars)
JWT_SECRET=Un_String_Muy_Largo_Y_Seguro_Para_Produccion
JWT_EXPIRES_IN=2h
```

## 4. Dependencias

Si tienes acceso SSH (Terminal) en cPanel, entra a la carpeta y ejecuta:

```bash
composer install --no-dev --optimize-autoloader
```

Si no tienes SSH, tendrás que subir la carpeta `vendor` desde tu local (asegúrate de que tu versión de PHP local sea compatible con la del servidor).

## 5. Permisos

Asegúrate de que la carpeta `logs/` tenga permisos de escritura (chmod 775 o 777 dependiendo del hosting).
