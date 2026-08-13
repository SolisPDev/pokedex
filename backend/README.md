# 📦 PokéDex Manager - Backend API (Laravel 11)

Este es el servidor API REST principal de **PokéDex Manager** desarrollado en Laravel 11.

## 🛠️ Requisitos e Instalación

Para la instalación rápida, requisitos previos y puesta en marcha (incluyendo renombrar el archivo `.env.configurado` como `.env`), sigue los pasos unificados descritos en la sección **[Configuración e Instalación Rápida](../README.md#-configuración-e-instalación-rápida)** del README de la raíz.



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
