FROM node:20-alpine

# Configurar directorio de trabajo
WORKDIR /app

# Copiar package.json y package-lock.json
COPY frontend/package*.json ./

# Instalar dependencias para Linux
RUN npm install

# Exponer puerto de vite
EXPOSE 5173

CMD npm run dev -- --host 0.0.0.0 --port ${PORT:-5173}
