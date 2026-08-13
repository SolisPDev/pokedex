# 📦 PokéDex Manager - Backend API (Laravel 11)

Este es el servidor API REST principal de **PokéDex Manager** desarrollado en Laravel 11.

## 🛠️ Requisitos e Instalación

1. Asegúrate de tener instalado **PHP 8.2+** y **Composer**.
2. Renombra el archivo `.env.configurado` como `.env`:
   ```bash
   mv .env.configurado .env
   ```
3. Instala las dependencias del proyecto:
   ```bash
   composer install
   ```
4. Inicializa la base de datos local SQLite:
   ```bash
   php artisan migrate
   ```


## 🚀 Ejecución del Servidor

Inicia el servidor local de desarrollo de Laravel:
```bash
php artisan serve
```
La API estará disponible en `http://localhost:8000/api/v1`.

## 🧪 Pruebas Unitarias y Funcionales

El backend cuenta con una completa suite de pruebas unitarias y funcionales para todos los flujos principales (Autenticación, PokéAPI Proxy con Caché, Colección de Favoritos e Integración Híbrida de IA con Fallback).

Para ejecutar los tests, corre:
```bash
php artisan test
```
