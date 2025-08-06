# Etapa de build
FROM node:18 as build
WORKDIR /usr/src/app
COPY . .
RUN npm install
RUN npm run build

# Etapa de produção
FROM node:18-alpine
RUN npm install -g serve
WORKDIR /app
COPY --from=build /usr/src/app/dist ./dist
EXPOSE 8080
CMD ["serve", "-s", "dist", "-l", "8080"]
