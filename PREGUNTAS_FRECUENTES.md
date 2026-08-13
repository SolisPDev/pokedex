# ❓ Preguntas Frecuentes (FAQ) & Arquitectura del Sistema

Este documento recopila las preguntas frecuentes y las decisiones de diseño clave tomadas durante el desarrollo del proyecto **PokéDex Manager**.

---

### 🔑 1. ¿El sistema cuenta con autenticación y de qué tipo es?
**Sí.** El backend implementa autenticación basada en **Tokens de Acceso Personal** (*Personal Access Tokens*) a través de **Laravel Sanctum**.
* **Tipo:** Autenticación de API mediante tokens `Bearer`.
* **Rutas protegidas:** Protegidas mediante el middleware nativo `auth:sanctum`. Se requiere enviar la cabecera `Authorization: Bearer <token>` para gestionar favoritos o interactuar con las funciones de IA.
* **Controlador:** [AuthController.php](file:///c:/proyectos/pokedex/backend/app/Http/Controllers/AuthController.php).

---

### 🌐 2. ¿Se integró el consumo de la API externa?
**Sí.** Se consume la API externa de **PokéAPI** (`https://pokeapi.co/api/v2`) a través del servicio [PokeApiService.php](file:///c:/proyectos/pokedex/backend/app/Services/PokeApiService.php).
* **Optimización (Caché):** Para reducir tiempos de respuesta y peticiones a la API externa, las búsquedas generales y la información de detalle de cada Pokémon se almacenan en caché por **24 horas** de forma automática en el backend.

---

### 💾 3. ¿Qué tecnología se está utilizando para la persistencia de datos?
El sistema utiliza una arquitectura de base de datos agnóstica gracias a Laravel Eloquent:
* **Entorno local:** **SQLite** (`backend/database/database.sqlite`), lo cual permite la ejecución nativa en un entorno local liviano sin necesidad de servicios externos.
* **Entorno de producción:** **PostgreSQL** administrado en la plataforma **Railway**.

---

### 📱 4. ¿La interfaz de usuario es responsiva y está integrada como una SPA?
**Sí.** El frontend está diseñado como una **SPA (Single Page Application)** construida con **Vue 3** y **Vite**.
* **Responsividad:** Implementada mediante **Tailwind CSS**, adaptando automáticamente la interfaz y la barra lateral de navegación para dispositivos móviles, tablets y ordenadores.
* **Navegación fluida:** Las vistas cambian de forma dinámica y reactiva en [App.vue](file:///c:/proyectos/pokedex/frontend/src/App.vue) sin requerir recargas de página en el navegador.

---

### 🍍 5. ¿Qué es Pinia y por qué se utiliza?
**Pinia** es la biblioteca oficial de gestión de estado global para **Vue 3**.
* Se utiliza para centralizar y compartir reactivamente datos importantes de la aplicación (como el estado de autenticación y los datos del usuario activo) entre todos los componentes de la SPA de forma segura y consistente.

---

### 🔄 6. Si mañana quisiera cambiar el frontend por Angular en lugar de Vue, ¿tendría que modificar el backend?
**No, en absoluto.**
* El backend funciona como una **API REST desacoplada**.
* Se comunica enteramente mediante formatos estructurados **JSON** y autenticación de cabecera HTTP estándar, por lo cual es 100% independiente del framework cliente. Podrías reemplazar el cliente web por Angular, React o una app móvil nativa sin alterar el backend.

---

### 🧪 7. ¿Este backend se puede poner en producción y ser consumido por cualquier programador o mediante herramientas como Insomnia?
**Sí.** Cualquier programador puede consumir las API de producción desde clientes como **Insomnia, Postman, curl** o cualquier tecnología de software, importando directamente el contrato OpenAPI documentado en [03-api-contracts.json](file:///c:/proyectos/pokedex/specs/03-api-contracts.json).

---

### 🧠 8. ¿Cómo funciona el análisis de imágenes por Inteligencia Artificial?
1. El usuario carga una foto o ilustración de un Pokémon en la interfaz.
2. El cliente convierte la imagen a **Base64** y la envía al backend.
3. El backend envía los datos binarios de la imagen y las instrucciones de formato a un **Modelo Multimodal (LMM)** como `gpt-4o-mini` (OpenAI) o `gemini-1.5-flash` (Google).
4. El modelo analiza visualmente la imagen e identifica al Pokémon devolviendo el resultado estructurado en un JSON para que el frontend lo despliegue inmediatamente.

---

### 🎨 9. ¿Cómo se puede cambiar la paleta de colores de toda la aplicación?
El frontend utiliza **Tailwind CSS v4**, lo que permite cambiar el tema de colores global de manera centralizada sin necesidad de alterar los componentes `.vue` individuales.
* **Procedimiento:**
  1. Abre el archivo de estilos globales [style.css](file:///c:/proyectos/pokedex/frontend/src/style.css).
  2. Localiza la directiva `@theme`.
  3. Modifica los códigos hexadecimales mapeados a las variables de color (ej. `--color-zinc-950` para el fondo, o `--color-red-600` para el color acentuado principal de la PokéDex).
  4. Los cambios se propagarán de manera reactiva e instantánea en todas las pantallas.

