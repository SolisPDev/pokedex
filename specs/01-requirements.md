# 📋 Especificación de Requerimientos: PokéDex Manager

## 1. Alcance Core (Obligatorio)
- **Autenticación:** Sistema básico de registro e inicio de sesión de usuarios mediante Tokens (Laravel Sanctum).
- **Consumo de API Externa:** Integración con PokéAPI (`https://pokeapi.co/api/v2/`) para búsqueda, filtrado, paginación y vista de detalle de Pokémon.
- **Colección Personal:** Persistencia de datos para que cada usuario pueda:
  - Agregar Pokémon a su lista personal / favoritos.
  - Asignar notas o comentarios personalizados a cada Pokémon guardado.
  - Eliminar Pokémon de su colección.
- **Interfaz UI:** Diseño adaptativo (Responsive) y limpio desarrollado en Vue 3 con Tailwind CSS.
- **Sección 'About' (Información del Desarrollador):**
  - Una vista dedicada dentro de la navegación de la SPA para presentar la información profesional del desarrollador (Nombre, rol, enlaces a GitHub/LinkedIn y resumen del stack del proyecto).


## 2. Funcionalidades Bonus (Diferenciadores de IA)
- **Bonus 1: Reconocimiento de Imagen / Visión con LMM:**
  - El usuario puede subir una imagen de una carta o foto de un Pokémon.
  - La API procesa la imagen con un modelo multimodal cognitivo y retorna la identificación automática del Pokémon (nombre, tipo probable y sugerencia para agregarlo).
- **Bonus 2: Chat Contextual de Colección:**
  - Un asistente inteligente en la UI que analiza la colección guardada del usuario y le da insights (ej. *"Tienes muchos Pokémon tipo Fuego, te sugiero buscar uno tipo Agua"*).
- **Bonus 3: Revisión y Moderación de Textos de Usuario:**
  - Todo texto ingresado por el usuario (como las notas de la colección personal `custom_notes` o mensajes del chat contextual) debe ser revisado antes de procesarse.
- **Bonus 4: Arquitectura Híbrida de Contingencia (Fallback) e Identidad Neutral:**
  - El backend debe priorizar el uso de OpenAI para el procesamiento de imágenes, moderación e insights.
  - En caso de fallo de red, cuota excedida (`insufficient_quota`) u otros errores de OpenAI, el sistema deberá conmutar automáticamente a la API de Google Gemini como plan de respaldo (fallback).
  - Los mensajes devueltos al usuario final deben ser completamente genéricos y neutrales (e.g. *"El servicio de IA no está disponible temporalmente"*, *"La nota contiene contenido inapropiado"*). El usuario no debe enterarse de si la petición fue resuelta por OpenAI o Gemini, únicamente si hubo un error irrecuperable.

## 3. Criterios de Calidad e Infraestructura
- **Ejecución Local:** Desacoplada vía **Docker Compose** (`:8000` Backend, `:5173` Frontend, `:5432` Postgres) con aislamiento de `node_modules` para consistencia cross-platform.
- **Caché en Backend:** Almacenamiento en caché para las peticiones a la PokéAPI para optimizar la velocidad.
- **Manejo de Errores:** Manejo estandarizado de excepciones (401, 404, 422, 500) en respuestas JSON. Incluyendo la respuesta de error de moderación de OpenAI.
- **Infraestructura de Producción (Cloud):** Despliegue en producción en la plataforma **Railway** con una base de datos administrada PostgreSQL.
- **Pipeline de Integración y Despliegue Continuo (CI/CD):** Vinculación directa con un repositorio de **GitHub** para compilar y desplegar automáticamente la aplicación en cada actualización en la rama principal (`main`).