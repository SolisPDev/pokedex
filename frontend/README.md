# 💻 PokéDex Manager - Frontend Client (Vue 3 + Vite)

Este es el cliente web Single Page Application (SPA) para **PokéDex Manager** desarrollado en Vue 3 con Tailwind CSS y Pinia.

## 🛠️ Requisitos e Instalación

1. Asegúrate de tener instalado **Node.js (versión 18 o superior)**.
2. Navega al directorio del cliente e instala las dependencias necesarias:
   ```bash
   npm install
   ```

## 🚀 Ejecución en Desarrollo

Para iniciar el servidor de desarrollo local de Vite y visualizar la interfaz web:
```bash
npm run dev
```

De forma predeterminada, si el puerto `5173` está libre, estará disponible en `http://localhost:5173`. Si el puerto `5173` ya está ocupado por otro proceso, Vite levantará el cliente automáticamente en:
**`http://localhost:5174`**

## ⚙️ Conexión con la API Backend

La configuración de Axios se enlaza automáticamente a la URL por defecto `http://localhost:8000/api/v1` para el desarrollo local. En producción, se lee de las variables de entorno inyectadas.
