# 📱 PokéDex Manager

¡Bienvenido a **PokéDex Manager**! Esta es una aplicación web SPA full-stack construida bajo la metodología **Desarrollo Guiado por Especificaciones (Spec-Driven Development - SDD)**. Permite a los usuarios buscar y gestionar su colección personal de Pokémon mediante la integración de la PokéAPI oficial, optimizada con caché, y potenciada por inteligencia artificial (Gemini 1.5 Flash).

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
- **Reconocimiento de Pokémon por Visión (Gemini Vision LMM):** 
  - Sube una imagen o captura de pantalla de un Pokémon.
  - La inteligencia artificial de Gemini identifica al Pokémon con porcentaje de confianza y te sugiere cómo utilizarlo o por qué agregarlo.
- **Asesor Pokémon de Colección (Insights de IA):**
  - Un consejero interactivo analiza tu colección actual y te aconseja sobre debilidades de tipos y recomendaciones estratégicas de equipo.

---

## 🛠️ Stack Tecnológico

- **Backend:** [Laravel 11](https://laravel.com/) (PHP 8.2)
- **Frontend:** [Vue 3](https://vuejs.org/) + [Vite](https://vite.dev/) + [Tailwind CSS v4](https://tailwindcss.com/) + [Pinia](https://pinia.vuejs.org/)
- **Base de Datos:** [PostgreSQL 15](https://www.postgresql.org/)
- **Orquestación de Entorno:** [Docker Compose](https://www.docker.com/)
- **Modelo LMM:** [Gemini 1.5 Flash API](https://ai.google.dev/)

---

## ⚙️ Requisitos Previos

Asegúrate de tener instalados en tu máquina:
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (con WSL2 habilitado en Windows)
- Una clave API de Gemini válida ([Obtener API Key](https://aistudio.google.com/))

---

## 📦 Configuración e Instalación Rápida

Sigue estos sencillos pasos para tener el proyecto corriendo localmente en menos de 5 minutos:

1. **Configurar el archivo `.env` del Backend:**
   Duplica el archivo `.env.example` en la carpeta `backend/` como `.env` o créalo directamente con el siguiente contenido:
   ```env
   APP_NAME=PokedexManager
   APP_ENV=local
   APP_KEY=base64:SkJeV11x9g82ZsEV2N6FwTpFkxpE5s5nj5+aM0s3JsU=
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   DB_CONNECTION=pgsql
   DB_HOST=db
   DB_PORT=5432
   DB_DATABASE=pokedex_db
   DB_USERNAME=postgres
   DB_PASSWORD=secret

   POKEAPI_BASE_URL=https://pokeapi.co/api/v2
   GEMINI_API_KEY=tu_api_key_de_gemini_aqui
   ```
   > [!IMPORTANT]
   > Asegúrate de reemplazar `tu_api_key_de_gemini_aqui` por tu clave de API de Gemini válida para habilitar el funcionamiento del escáner y del consejero de IA.

2. **Levantar los Contenedores Docker:**
   En la raíz del proyecto, ejecuta el siguiente comando en la terminal:
   ```bash
   docker compose up --build -d
   ```

3. **Ejecutar las Migraciones de la Base de Datos:**
   Una vez que los contenedores estén levantados, ejecuta las migraciones para inicializar la base de datos en Postgres:
   ```bash
   docker compose exec backend php artisan migrate
   ```

4. **Acceder a la Aplicación:**
   - **Frontend (Vue 3 SPA):** [http://localhost:5173](http://localhost:5173)
   - **Backend (Laravel API):** [http://localhost:8000/api/v1](http://localhost:8000/api/v1)

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
| **POST** | `/collection` | Añadir un Pokémon a la colección con notas | Sí |
| **PUT** | `/collection/{id}` | Editar notas estratégicas del favorito | Sí |
| **DELETE** | `/collection/{id}` | Eliminar Pokémon de la colección | Sí |
| **POST** | `/ia/identify-pokemon` | Escanear imagen para identificar Pokémon | Sí |
| **POST** | `/ia/chat-insights` | Obtener recomendaciones del Profesor Pokémon | Sí |
