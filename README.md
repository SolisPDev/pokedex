# 📱 PokéDex Manager

¡Bienvenido a **PokéDex Manager**! Esta es una aplicación web SPA full-stack construida bajo la metodología **Desarrollo Guiado por Especificaciones (Spec-Driven Development - SDD)**. Permite a los usuarios buscar y gestionar su colección personal de Pokémon mediante la integración de la PokéAPI oficial, optimizada con caché, y potenciada por inteligencia artificial.

---

## 🚀 Características

### 1. Alcance Core (Obligatorio)
- **Autenticación segura:** Registro e inicio de sesión integrados mediante Tokens (Laravel Sanctum).
- **Proxy PokéAPI oficial:** Consumo y filtrado en tiempo real con paginación integrada.
- **Caché en Backend:** Almacenamiento temporal en caché (24 horas) para optimizar consultas frecuentes a PokéAPI y reducir la latencia de red.
- **Colección de Favoritos (CRUD completo):** 
  - Registrar Pokémon favoritos.
  - Editar notas estratégicas del entrenador.
  - Eliminar Pokémon de la colección.
- **Diseño Premium:** Interfaz responsive, moderna, con fuentes estéticas y animaciones suaves utilizando Vue 3 y Tailwind CSS.

### 2. Funcionalidades de Inteligencia Artificial (Bonus)
- **Reconocimiento de Pokémon por Imagen (LMM Visión):** 
  - Sube una imagen o captura de pantalla de un Pokémon.
  - La inteligencia artificial identifica al Pokémon con porcentaje de confianza y te sugiere cómo utilizarlo.
- **Asesor Pokémon de Colección (Insights de IA):**
  - Un consejero interactivo analiza tu colección actual y te aconseja sobre debilidades de tipos y recomendaciones estratégicas de equipo.
- **Revisión y Moderación de Textos:**
  - Las notas de favoritos ingresadas por el usuario son validadas en el backend contra lenguaje ofensivo o inapropiado antes de guardarse.
- **Resiliencia con Arquitectura Híbrida de Contingencia (Fallback):**
  - El backend gestiona de forma transparente la conmutación entre **OpenAI (GPT-4o-mini / Moderations)** y **Google Gemini (gemini-3.5-flash)**. Si el proveedor prioritario se queda sin cuota o reporta fallas de red, el sistema migra automáticamente de forma transparente.
  - Toda la interfaz del usuario es **neutral**, devolviendo mensajes genéricos sin revelar qué proveedor de IA procesó la solicitud.

---

## 🛠️ Stack Tecnológico

- **Backend:** [Laravel 11](https://laravel.com/) (PHP 8.2+)
- **Frontend:** [Vue 3](https://vuejs.org/) + [Vite](https://vite.dev/) + [Tailwind CSS v4](https://tailwindcss.com/) + [Pinia](https://pinia.vuejs.org/)
- **Base de Datos:** SQLite (Entorno de desarrollo local ligero)
- **Proveedor Cognitivo Principal:** OpenAI API
- **Proveedor de Contingencia:** Google Gemini API (v1beta / `gemini-3.5-flash`)

---

## ⚙️ Requisitos Previos

Asegúrate de tener instalados en tu máquina:
- **PHP 8.2+** y **Composer**
- **Node.js 18+** y **npm**
- Una clave API de OpenAI y/o una clave API de Google Gemini (v1beta)

---

## 📦 Configuración e Instalación Rápida

Sigue estos sencillos pasos para tener el proyecto corriendo localmente de forma nativa:

### 1. Levantar el Backend (Laravel 11)

1. Entra a la carpeta `backend/` y duplica el archivo `.env.example` como `.env`:
   ```bash
   cd backend
   cp .env.example .env
   ```
2. Instala las dependencias de PHP y genera la clave de la aplicación:
   ```bash
   composer install
   php artisan key:generate
   ```
3. Configura tus claves de API correspondientes en el archivo `.env`:
   ```env
   # API Keys de Inteligencia Artificial (Configura al menos una para habilitar las funciones cognitivas)
   OPENAI_API_KEY=tu_openai_api_key_aqui
   GEMINI_API_KEY=tu_gemini_api_key_aqui
   ```
4. Ejecuta las migraciones para inicializar la base de datos SQLite:
   ```bash
   php artisan migrate
   ```
5. Inicia el servidor de desarrollo:
   ```bash
   php artisan serve
   ```
   El backend estará disponible en: **`http://localhost:8000`**

### 2. Levantar el Frontend (Vue 3)

1. Abre una nueva terminal, entra a la carpeta `frontend/` e instala las dependencias de Node:
   ```bash
   cd frontend
   npm install
   ```
2. Inicia el servidor de desarrollo de Vite:
   ```bash
   npm run dev
   ```
   El frontend estará disponible en: **`http://localhost:5174`** (o `:5173` si el puerto está libre).

---

## 📝 Resumen de Endpoints de la API

La aplicación expone los siguientes endpoints HTTP principales bajo el prefijo `/api/v1`:

| Método | Endpoint | Descripción | Requiere Autenticación |
| :--- | :--- | :--- | :--- |
| **POST** | `/auth/register` | Registro de nuevos entrenadores | No |
| **POST** | `/auth/login` | Inicio de sesión (Retorna Sanctum token) | No |
| **POST** | `/auth/logout` | Cierre de sesión (Revoca el token actual) | Sí |
| **GET** | `/pokemon` | Lista paginada y filtrada de PokéAPI con caché | No |
| **GET** | `/pokemon/{idOrName}` | Detalle de un Pokémon específico con caché | No |
| **GET** | `/collection` | Obtener la lista personal de favoritos | Sí |
| **POST** | `/collection` | Añadir un Pokémon a la colección con notas y moderación de texto | Sí |
| **PUT** | `/collection/{id}` | Editar notas estratégicas del favorito con moderación de texto | Sí |
| **DELETE** | `/collection/{id}` | Eliminar Pokémon de la colección | Sí |
| **POST** | `/ia/identify-pokemon` | Escanear imagen para identificar Pokémon con fallback híbrido | Sí |
| **POST** | `/ia/chat-insights` | Obtener recomendaciones del Profesor Pokémon con fallback híbrido | Sí |
