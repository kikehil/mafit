# Solución de Errores Docker

## 1. Error de Puerto (MySQL)
Ya actualicé el archivo `docker-compose.yml` en el repositorio para usar el puerto **3308** y evitar conflicto con el MySQL que ya tienes en tu servidor.

## 2. Configurar Base de Datos (.env)
Para que el contenedor de la aplicación pueda conectar con la base de datos de Docker, necesitas editar tu archivo `.env` en el servidor:

```ini
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=mafit
DB_USERNAME=mafit
DB_PASSWORD=root
```
*Nota: Es importante que `DB_HOST` diga `mysql` (el nombre del servicio en docker) y no `127.0.0.1`.*

## 3. Arreglar Permisos (Error "vendor does not exist")
Este error ocurre porque el usuario de Docker no tiene permiso para escribir en tu carpeta. Ejecuta esto en el servidor (en la carpeta mafit):

```bash
# Cambiar el dueño de la carpeta al usuario www-data (que usa Docker)
chown -R www-data:www-data /var/www/mafit
chmod -R 775 /var/www/mafit
```

## 4. Reintentar Despliegue
Ahora, ejecuta los pasos en orden nuevamente:

```bash
# 1. Bajar el cambio del puerto
git pull origin main

# 2. Reiniciar contenedores
docker-compose down
docker-compose up -d --build

# 3. Instalar todo (como usuario root dentro de docker para evitar errores de permiso extras)
docker-compose exec -u root app composer install --optimize-autoloader --no-dev
docker-compose exec -u root app php artisan key:generate
docker-compose exec -u root app php artisan migrate --force

# 4. Compilar assets
docker-compose exec -u root app npm install
docker-compose exec -u root app npm run build
```
