# 💻 PokéDex Manager - Frontend Client (Vue 3 + Vite)

Este es el cliente web Single Page Application (SPA) para **PokéDex Manager** desarrollado en Vue 3 con Tailwind CSS y Pinia.

## 🛠️ Requisitos e Instalación

Para los requisitos previos, instalación de dependencias y puesta en marcha del frontend de la SPA, consulta los pasos detallados de la sección **[Configuración e Instalación Rápida](../README.md#-configuración-e-instalación-rápida)** del README de la raíz.


## 🚀 Ejecución en Desarrollo

Para iniciar el servidor de desarrollo local de Vite y visualizar la interfaz web:
```bash
npm run dev
```

De forma predeterminada, si el puerto `5173` está libre, estará disponible en `http://localhost:5173`. Si el puerto `5173` ya está ocupado por otro proceso, Vite levantará el cliente automáticamente en:
**`http://localhost:5174`**

## ⚙️ Conexión con la API Backend

La configuración de Axios se enlaza automáticamente a la URL por defecto `http://localhost:8000/api/v1` para el desarrollo local. En producción, se lee de las variables de entorno inyectadas.

---

## 🎨 Personalización de Colores (Tailwind CSS v4)

Este proyecto hace uso de **Tailwind CSS v4** para el manejo de estilos. Puedes cambiar la paleta de colores de toda la interfaz de forma centralizada sin editar los componentes individuales:
1. Abre el archivo [frontend/src/style.css](file:///c:/proyectos/pokedex/frontend/src/style.css).
2. Edita los códigos de color hexadecimales bajo la directiva `@theme` (por ejemplo, `--color-zinc-950` para el fondo o `--color-red-600` para el realce de la PokéDex).

---

## 📦 Compilación para Producción

Para compilar la aplicación optimizada para producción:
```bash
npm run build
```
Esto generará los archivos estáticos listos para desplegar en la carpeta `dist/`.

