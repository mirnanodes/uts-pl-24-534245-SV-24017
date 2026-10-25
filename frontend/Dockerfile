# =============================================================================
# Stage 1: build the Vue app with Node.js
# =============================================================================
FROM node:22-alpine AS build

WORKDIR /app

# Install dependencies first so this layer is cached until package*.json change.
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY . .

# API base URL is configuration, injected at build time (not hard-coded).
ARG VITE_API_URL=/api
ENV VITE_API_URL=${VITE_API_URL}

RUN npm run build

# =============================================================================
# Stage 2: serve the static build with Nginx (no Node.js, no node_modules)
# =============================================================================
FROM nginx:1.30-alpine AS runtime

# Where Nginx forwards /api requests (Compose service name of the backend).
ENV API_UPSTREAM=http://backend:8000

# The official image renders templates with envsubst into conf.d/ at startup.
RUN rm -f /etc/nginx/conf.d/default.conf
COPY nginx/default.conf.template /etc/nginx/templates/default.conf.template

COPY --from=build /app/dist /usr/share/nginx/html

EXPOSE 80
