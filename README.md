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
```bash
git clone <url-del-repositorio>
cd ProyectoFinalBE
composer install