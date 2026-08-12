# 🏛️ Especificación de Arquitectura e Infraestructura Local

## 1. Entorno de Ejecución (Docker Compose)
- **Base de Datos:** PostgreSQL 15 en puerto `5432` (`pokedex_db`).
- **Backend API:** Laravel 11 corriendo bajo PHP 8.2 en puerto `8000` (`http://localhost:8000/api`).
- **Frontend SPA:** Vue 3 + Vite + Tailwind CSS en puerto `5173` (`http://localhost:5173`).

## 2. Diagrama de Relación de Datos (ERD)
- **`users`**: `id`, `name`, `email`, `password`, `created_at`, `updated_at`.
- **`pokemon_collections`**: 
  - `id` (PK)
  - `user_id` (FK -> users.id)
  - `pokemon_id` (Integer - ID oficial de PokéAPI)
  - `pokemon_name` (String)
  - `pokemon_type` (String)
  - `custom_notes` (Text, Nullable)
  - `created_at`, `updated_at`

## 3. Variables de Entorno Requeridas (`.env.example`)
```env
# Backend / Database
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=pokedex_db
DB_USERNAME=postgres
DB_PASSWORD=secret

# Integración PokéAPI
POKEAPI_BASE_URL=[https://pokeapi.co/api/v2](https://pokeapi.co/api/v2)

# Bonus de IA (Gemini API)
GEMINI_API_KEY=tu_api_key_aqui