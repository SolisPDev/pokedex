FROM node:20-alpine

# Configurar directorio de trabajo
WORKDIR /app

# Exponer puerto de vite
EXPOSE 5173

# El comando se ejecutará asumiendo que package.json y node_modules existen (gracias al volumen)
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
