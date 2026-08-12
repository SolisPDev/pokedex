# 📋 Especificación de Requerimientos: PokéDex Manager

## 1. Alcance Core (Obligatorio)
- **Autenticación:** Sistema básico de registro e inicio de sesión de usuarios mediante Tokens (Laravel Sanctum).
- **Consumo de API Externa:** Integración con PokéAPI (`https://pokeapi.co/api/v2/`) para búsqueda, filtrado, paginación y vista de detalle de Pokémon.
- **Colección Personal:** Persistencia de datos para que cada usuario pueda:
  - Agregar Pokémon a su lista personal / favoritos.
  - Asignar notas o comentarios personalizados a cada Pokémon guardado.
  - Eliminar Pokémon de su colección.
- **Interfaz UI:** Diseño adaptativo (Responsive) y limpio desarrollado en Vue 3 con Tailwind CSS.

## 2. Funcionalidades Bonus (Diferenciadores de IA)
- **Bonus 1: Reconocimiento de Imagen / Visión con LMM:**
  - El usuario puede subir una imagen de una carta o foto de un Pokémon.
  - La API procesa la imagen con **Gemini 1.5 Flash Vision** y retorna la identificación automática del Pokémon (nombre, tipo probable y sugerencia para agregarlo).
- **Bonus 2: Chat Contextual de Colección:**
  - Un asistente inteligente en la UI que analiza la colección guardada del usuario y le da insights (ej. *"Tienes muchos Pokémon tipo Fuego, te sugiero buscar uno tipo Agua"*).

## 3. Criterios de Calidad
- Ejecución local desacoplada vía **Docker Compose** (`:8000` Backend, `:5173` Frontend, `:5432` Postgres).
- Caché en backend para las peticiones a la PokéAPI y evitar latencia excesiva.
- Manejo estandarizado de excepciones (401, 404, 422, 500).