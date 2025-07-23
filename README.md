# Proyecto Laravel TDD

Este es un proyecto desarrollado con Laravel, enfocado en la metodología de Desarrollo Guiado por Pruebas (TDD). La aplicación permite gestionar tareas y productos.

## ✨ Características

*   **Autenticación de usuarios:** Sistema de registro e inicio de sesión.
*   **Gestión de Tareas:** Funcionalidades para crear, leer, actualizar y eliminar (CRUD) tareas.
*   **Gestión de Productos:** Funcionalidades CRUD para productos.
*   **Desarrollo TDD:** El código está respaldado por pruebas para garantizar su fiabilidad.

## 🚀 Cómo empezar

Sigue estos pasos para tener una copia local del proyecto funcionando.

### Prerrequisitos

Asegúrate de tener instalado lo siguiente:
*   PHP >= 8.1
*   Composer
*   Node.js & npm
*   Una base de datos (como MySQL, PostgreSQL, o SQLite)

### Instalación

1.  **Clona el repositorio:**
    ```sh
    git clone https://github.com/jeancgarciaq/laraveltdd.git
    cd laraveltdd
    ```

2.  **Instala las dependencias de Composer:**
    ```sh
    composer install
    ```

3.  **Configura tu entorno:**
    Copia el archivo de ejemplo `.env.example` a `.env` y configura tus variables de entorno, especialmente la conexión a la base de datos.
    ```sh
    cp .env.example .env
    ```

4.  **Genera la clave de la aplicación:**
    ```sh
    php artisan key:generate
    ```

5.  **Ejecuta las migraciones de la base de datos:**
    ```sh
    php artisan migrate
    ```
    *(Opcional) Si tienes seeders, puedes ejecutarlos con:*
    ```sh
    php artisan db:seed
    ```

6.  **Instala las dependencias de Node.js:**
    ```sh
    npm install
    ```

7.  **Compila los assets:**
    ```sh
    npm run dev
    ```

8.  **Inicia el servidor de desarrollo:**
    ```sh
    php artisan serve
    ```

¡Ahora puedes visitar `http://127.0.0.1:8000` en tu navegador!

## ✅ Ejecutando las pruebas

Para ejecutar el conjunto de pruebas automatizadas, utiliza el siguiente comando:

```sh
php artisan test
```

## 🛠️ Construido con

*   [Laravel](https://laravel.com/) - El framework web utilizado.
*   [PHP](https://www.php.net/) - El lenguaje de programación.
*   [Tailwind CSS](https://tailwindcss.com/) - El framework de CSS.
*   [Vite](https://vitejs.dev/) - La herramienta de construcción de frontend.

---
*Este README fue generado con la ayuda de GitHub Copilot.*
