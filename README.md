# Sistema Conecta - Laravel 12 + Vue.js 3

## Descripción

Sistema web de autenticación y gestión de recompensas con integración a Web Services SOAP y API REST desarrollado en Laravel 12 y Vue.js 3.

## Stack Tecnológico

- **Backend:** Laravel 12 (PHP 8.3+)
- **Frontend:** Vue.js 3 + Vite
- **Base de Datos:** MySQL 8.0
- **Estilos:** SASS
- **Analítica:** Google Tag Manager + Google Analytics 4
- **SOAP Client:** PHP SOAP Extension

## Prerrequisitos

- PHP >= 8.2 o 8.3
- Composer 2.x
- Node.js >= 22.x
- MySQL >= 8.0
- PHP Extensions: soap, pdo_mysql, mbstring, xml, curl

## Instalación

### 1. Clonar el repositorio en tu carpeta www de (laragon o xampp)

```bash
git clone https://github.com/bryanjbsjuego/prueba-tecnica-fidelity.git
cd prueba-tecnica-fidelity
```

### 2. Configurar Backend (Laravel)

```bash
# Instalar dependencias PHP
composer install

# Copiar archivo de configuración
cp .env.example .env

# Generar key de aplicación
php artisan key:generate

# Configurar JWT
php artisan jwt:secret
```

### 3. Ejemplo de .env

Editar `.env`:

```env
APP_NAME=Conecta
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=America/Mexico_City
APP_URL=http://localhost


APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_FAKER_LOCALE=es_MX

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=conecta
DB_USERNAME=root
DB_PASSWORD=tu_password

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
# VARIABLE PARA LA URL DE LA API
VITE_API_URL=http://localhost:8000/api


# SOAP Web Services
WS_TERMINAL_URL=https://dev.testfidely.net/fnet3web/proxy/wsdl.php?v=01.95
WS_TERMINAL_SERIAL=47294
WS_TERMINAL_USER=opTemporalEBBBS
WS_TERMINAL_PASSWORD=qM6iNHN7etfhKS2

WS_CA_URL=https://dev.testfidely.net/fnet3web/proxy/wsdl_ca.php?v=01.90
WS_CA_KIND=3
WS_CA_CAMPAIGN_ID=2005

# JWT
JWT_SECRET=tu_secreto_jwt
JWT_TTL=60
JWT_REFRESH_TTL=20160


# Analítica
VITE_GTM_ID=
VITE_GA_ID=


AUTH_GUARD=api
```

```bash
# Crear base de datos
mysql -u root -p -e "CREATE DATABASE conecta CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed
```

### Configurar Frontend (Vue)

```bash
# Instalar dependencias Node
npm install

# Compilar assets
npm run dev
```

## Ejecución

### Modo Desarrollo

Terminal 1 - Laravel:

```bash
php artisan serve
# Backend: http://localhost:8000
```

Terminal 2 - Vite (Assets):

```bash
npm run dev
# Hot reload assets
```

### Modo Producción

```bash
# Build frontend
npm run build

```

## Credenciales de Prueba

### Login Usuario Final

- **Email:** bryan.solis@correo.com
- **Password:** Dz0NY2ghhbiGqne

### API REST - Operador

- **Usuario:** operador1
- **Password:** Password123

## Probar Endpoints API

## Configuración Inicial

### Base URL

```
 http://localhost:8000/api/
```

---

## 1. Health Check

Verificar que el servicio está funcionando.

**Endpoint:** `Método GET /health`

**Headers:** Ninguno requerido

**Response esperado:**

```json
{
    "status": "ok",
    "service": "Conecta",
    "version": "1.0.0",
    "timestamp": "2025-11-26T..."
}
```

---

## 2. Autenticación Usuario Final

### Login Usuario Final

**Endpoint:** `Método POST /login`

**Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Body (JSON):**

```json
{
    "email": "bryan.solis@correo.com",
    "password": "Dz0NY2ghhbiGqne"
}
```

**Response esperado:**

```json
{
    "success": true,
    "message": "Autenticación exitosa",
    "session": "session_id_aqui",
    "customer": { ... },
    "userName": "..."
}
```

**⚠️ Importante:** Guarda el valor de `session y de customer->category ` para usarlo en las siguientes peticiones de premios y alianzas.

---

## 3. Premios (Requiere sesión)

### Listar Premios

**Endpoint:** `Método GET /premios`

**Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Query Parameters:**

```
session: [SESSION_DEL_LOGIN]
page: 1
limit: 8
search: (opcional) 
sort_by: (opcional)
order: (opcional) asc|desc
```

**Ejemplo URL completa:**

```
GET /premios?session=abc123&page=1&limit=8&search=&sort_by=name&order=asc
```

**Response esperado:**

```json
{
    "success": true,
    "prizes": [
        {
            "id": 1,
            "name": "Premio ejemplo",
            "points": 100,
            "image": "https://...",
            "moneyValue": 50.00,
            "description": "Descripción del premio"
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 8,
        "total": 35,
        "total_pages": 5
    }
}
```

---

## 4. Autenticación Operador

### Login Operador

**Endpoint:** `Método POST /operator/login`

**Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Body (JSON):**

```json
{
    "usuario": "operador1",
    "contrasena": "Password123"
}
```

**Response esperado:**

```json
{
    "success": true,
    "message": "Autenticación exitosa",
    "uuid": "uuid-generado-aqui",
    "token": "jwt-token-aqui",
    "operador": {
        "id": 1,
        "nombre": "...",
        "email": "..."
    }
}
```

**⚠️ Importante:** Guarda tanto el `uuid` como el `token` para las siguientes peticiones.

---

## 5. Alianzas (Requiere autenticación de operador)

### Listar Alianzas

**Endpoint:** `Método GET /operator/alianzas`

**Headers:**

```
Content-Type: application/json
Accept: application/json
Authorization: Bearer [TOKEN_JWT]
```

**Query Parameters:**

```
uuid: [UUID_DEL_LOGIN]
categoria_cliente_id: 3230
page: 1
limit: 6
```

**Ejemplo URL completa:**

```
GET /operator/alianzas?uuid=abc-123&categoria_cliente_id=1&page=1&limit=6
```

**Response esperado:**

```json
{
    "success": true,
    "alianzas": [
        {
            "id": 1,
            "nombre": "Alianza ejemplo",
            "descripcion": "...",
            "activo": true
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 6,
        "total": 15,
        "total_pages": 3
    }
}
```

---

### Ver Detalle de Alianza

**Endpoint:** `Método GET /operator/alianzas/{alianza_id}`

**Headers:**

```
Content-Type: application/json
Accept: application/json
Authorization: Bearer [TOKEN_JWT]
```

**Query Parameters:**

```
uuid: [UUID_DEL_LOGIN]
```

**Ejemplo:**

```
GET /operator/alianzas/1?uuid=abc-123
```

**Response esperado:**

```json
{
    "success": true,
    "alianza": {
        "id": 1,
        "nombre": "Alianza ejemplo",
        "descripcion": "...",
        "activo": true
    }
}
```

---

### Marcar Alianza como Usada

**Endpoint:** `POST /operator/alianzas/marcar-usada`

**Headers:**

```
Content-Type: application/json
Accept: application/json
Authorization: Bearer [TOKEN_JWT]
```

**Body (JSON):**

```json
{
    "uuid": "uuid-del-login",
    "alianza_id": 1
}
```

**Response esperado:**

```json
{
    "success": true,
    "message": "Alianza obtenida",
    "alianza": {
        "id": 1,
        "nombre": "Alianza ejemplo",
	"descripcion": "Ejemplo de descripción",
        "estatus": "usado",
	"fecha_uso" : "2025-11-27 03:10:56",
	"categorias": []
    }
}
```

---

### Logout Operador

**Endpoint:** `POST /operator/logout`

**Headers:**

```
Content-Type: application/json
Accept: application/json
Authorization: Bearer [TOKEN_JWT]
```

**Body (JSON):**

```json
{
    "uuid": "uuid-del-login"
}
```

**Response esperado:**

```json
{
    "success": true,
    "message": "Sesión cerrada correctamente"
}
```

---

### Flujo Usuario Final:

1. Health Check
2. Login Usuario Final → guarda `session`
3. Listar Premios (con filtros opcionales)

### Flujo Operador:

1. Health Check
2. Login Operador → guarda `uuid` y `token`
3. Listar Alianzas
4. Ver Detalle de Alianza
5. Marcar Alianza como Usada
6. Logout Operador

---

## Códigos de Respuesta

| Código | Significado                              |
| ------- | ---------------------------------------- |
| 200     | Operación exitosa                       |
| 401     | No autenticado / Credenciales inválidas |
| 403     | Sin permisos / Cuenta no válida         |
| 404     | Recurso no encontrado                    |
| 500     | Error del servidor                       |

---

## Solución de problemas

### Error 401 en /premios

- Verifica que el parámetro `session` sea correcto
- Asegúrate de que la sesión no haya expirado

### Error 401 en rutas de operador

- Verifica que el token JWT esté en el header `Authorization: Bearer {token}`
- Asegúrate de que el token no haya expirado

### Error 404 "Alianza no encontrada"

- Verifica que el ID de la alianza sea correcto
- Asegúrate de que la alianza exista en la base de datos

### Error 500

- Revisa los logs del servidor
- Verifica la conexión con el servicio SOAP
- Comprueba la configuración de la base de datos
