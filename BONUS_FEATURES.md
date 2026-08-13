# 🌟 Funcionalidades Bonus de Inteligencia Artificial: PokéDex Manager

Este documento detalla el propósito, arquitectura y uso de las características avanzadas de Inteligencia Artificial integradas en el proyecto **PokéDex Manager**.

---

## 📸 1. Reconocimiento de Pokémon por Imagen (LMM Visión)
* **Propósito:** Permitir al usuario identificar un Pokémon cargando una foto o ilustración, facilitando su catalogación rápida.
* **Flujo de Operación:**
  1. El usuario carga una imagen en la interfaz (sección "Escáner & Asesor IA").
  2. El frontend convierte la imagen a formato **Base64** y la transmite en un JSON payload al endpoint `POST /api/v1/ia/identify-pokemon`.
  3. El backend envía los datos binarios al modelo multimodal (como `gpt-4o-mini` o `gemini-1.5-flash`), solicitando la identificación en formato JSON plano con los campos `name` (nombre oficial en minúsculas), `confidence` (precisión de 0.0 a 1.0), `type` (tipo o tipos) y `suggestion` (sugerencia en español).
  4. La interfaz procesa el JSON de respuesta y renderiza una tarjeta con los resultados del escaneo, permitiendo al usuario agregarlo a su colección de forma directa.

---

## 💬 2. Asesor Pokémon de Colección (Insights de IA)
* **Propósito:** Generar recomendaciones y consejos estratégicos sobre el equipo actual de Pokémon guardados por el usuario.
* **Flujo de Operación:**
  1. Al presionar el botón "Obtener consejos del Profesor Pokémon", el backend consulta la colección de favoritos del usuario y serializa su contenido en un string estructurado que lista nombres, tipos y notas del entrenador.
  2. Este listado se inyecta dinámicamente en el prompt del chatbot en el backend: `POST /api/v1/ia/chat-insights`.
  3. El modelo analiza el balance elemental del equipo, detectando vulnerabilidades, fortalezas y recomendando qué tipos de Pokémon debería capturar a continuación para balancear el equipo.
  4. La respuesta generada con tono amigable es enviada y mostrada en un chat interactivo.

---

## 🛡️ 3. Revisión y Moderación de Textos
* **Propósito:** Asegurar que los comentarios, notas y mensajes de los usuarios pasen por un filtro ético antes de ser guardados en la base de datos local SQLite o enviados a los modelos generativos.
* **Flujo de Operación:**
  1. Cada vez que se crea o edita un favorito (en los endpoints `POST /api/v1/collection` y `PUT /api/v1/collection/{id}`), el backend intercepta el campo `custom_notes`.
  2. Llama a la API de moderación de OpenAI (o fallback a Gemini) para evaluar el contenido.
  3. Si el texto infringe las políticas de contenido (lenguaje ofensivo, acoso, etc.), el backend interrumpe la ejecución del controlador y retorna una respuesta de validación HTTP `422 Unprocessable Content`.

---

## 🔄 4. Arquitectura Híbrida de Contingencia (Fallback) e Identidad Neutral
* **Propósito:** Brindar alta disponibilidad y resiliencia a fallos de red, caídas de servicio o límites de cuota (`insufficient_quota`).
* **Flujo de Operación:**
  1. El backend delega la lógica de IA en el [AiManagerService.php](file:///c:/proyectos/pokedex/backend/app/Services/AiManagerService.php).
  2. Por defecto, todas las peticiones de IA intentan resolverse a través de **OpenAI Service**.
  3. Si OpenAI devuelve un error de cuota (HTTP 429), fallo de red u otra excepción, el servicio captura la excepción, genera una advertencia en el log del sistema, y automáticamente conmuta la misma petición hacia **Google Gemini Service** (usando la clave `GEMINI_API_KEY`).
  4. **Identidad Neutral:** Con el fin de evitar confusión en el usuario final o fugas de información interna de infraestructura, toda respuesta de error devuelta por la API es neutra (ej. *"El servicio de revisión de textos no está disponible"* en lugar de *"OpenAI API Exception"*).
