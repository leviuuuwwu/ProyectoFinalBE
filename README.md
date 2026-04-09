<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

# 🏥 API REST - Sistema de Turnos y Citas Médicas

Backend robusto desarrollado en **Laravel 11** para la gestión integral de disponibilidad, reservación de citas, roles de usuario y reportes para clínicas y servicios médicos. Diseñado con un enfoque estricto en ciberseguridad, buenas prácticas de arquitectura y escalabilidad.

## 👥 Equipo de Desarrollo

Este proyecto fue estructurado dividiendo el sistema en micro-dominios bajo la responsabilidad de:

* **Leví Guerra (Coordinador):** Módulo de Autenticación (Sanctum), Catálogos Base (Especialidades/Servicios) y Arquitectura Core.
* **René Morataya:** Módulo de Gestión de Usuarios y Permisos Atómicos (Spatie).
* **Carla Navas:** Motor Lógico de Disponibilidad y Gestión de Horarios/Bloqueos.
* **Fernando Solorzano:** Módulo Transaccional (Gestión del ciclo de vida de Citas Médicas).
* **Benjamin Trabanino:** Historial Clínico, Reportes de Admin y Notificaciones/Recordatorios.

---

## 🚀 Características y Arquitectura Aplicada

El proyecto cumple estrictamente con principios **SOLID** y los requerimientos de la cátedra:

* **Ciberseguridad (UUIDs):** Todas las tablas principales (`users`, `citas`, `especialidades`, etc.) utilizan UUIDs en lugar de IDs secuenciales para prevenir enumeración de recursos.
* **Autenticación y Autorización:** Implementación de tokens mediante **Laravel Sanctum** y gestión granular de Roles y Permisos usando **Spatie**.
* **Controladores Atómicos:** Uso de *Single Action Controllers* (`__invoke`) en módulos clave para respetar el Principio de Responsabilidad Única (SRP).
* **Bases de Datos:** Uso nativo de **PostgreSQL** para mayor integridad relacional y soporte robusto.
* **Soft Deletes:** Eliminación lógica de usuarios e historiales para mantener la integridad de los reportes.

---

## ⚙️ Requisitos Previos

Asegúrese de tener instalados los siguientes componentes en su entorno local:
* **PHP:** >= 8.2
* **Composer:** Instalado globalmente.
* **Base de Datos:** PostgreSQL (pgAdmin).

---

## 🛠️ Instrucciones de Instalación y Levantamiento

Siga estos pasos exactos para configurar el entorno de desarrollo local sin errores.

### 1. Clonar el repositorio e instalar dependencias
git clone https://github.com/leviuuuwwu/ProyectoFinalBE
cd ProyectoFinalBE
composer install

### 2. Configuración de PostgreSQL (pgAdmin)
Abra pgAdmin y conéctese a su servidor local.

Cree una nueva base de datos llamada EXACTAMENTE: Backend_DB (El framework se encargará de crear las tablas automáticamente).

### 3. Configuración del entorno (.env)
Copie el archivo de ejemplo y renómbrelo:

cp .env.example .env
Abra el archivo .env, localice la configuración de la base de datos y ajústela con sus credenciales de PostgreSQL locales:

Fragmento de código
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=Backend_DB
DB_USERNAME=postgres
DB_PASSWORD=su_contraseña_aqui

### 4. ⚠️ Configuración de PHP en Windows (Obligatorio)
Para que PHP pueda comunicarse con PostgreSQL en Windows, debe habilitar los drivers nativos:

Navegue a la carpeta de instalación de PHP (ej. C:\php).

Abra el archivo php.ini con un editor de texto.

Busque y descomente (quite el punto y coma ; al inicio) las siguientes líneas:

extension=pdo_pgsql
extension=pgsql
Guarde el archivo.

### 5. Migraciones, Seeders y Ejecución
Con la conexión establecida, genere la clave de la aplicación y siembre la base de datos con los catálogos y el súper-administrador por defecto:

php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
El servidor estará disponible en: http://127.0.0.1:8000

### 6. 📚 Documentación Interactiva de la API (Swagger)
El proyecto cuenta con documentación OpenAPI generada automáticamente y siempre sincronizada con el código fuente.

Para probar los endpoints (Request/Response) de forma visual, una vez levantado el servidor, ingrese a:

UI Interactiva (Swagger): http://127.0.0.1:8000/docs/api

Especificación JSON: http://127.0.0.1:8000/docs/api.json

### 7. 🧪 Ejecución de Pruebas (Testing)
El sistema cuenta con una suite completa de pruebas (Feature y Unit tests) desarrolladas con el framework Pest / PHPUnit, validando rutas protegidas, lógicas de negocio, validaciones y roles.

Para ejecutar la batería de pruebas, corra el siguiente comando:

php artisan test

    Desarrollado para el Proyecto de Cátedra - Diseño y Desarrollo de API Rest.