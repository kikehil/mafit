# 🚀 Implementación de MAFIT en VPS

Esta guía te ayudará a implementar tu aplicación Laravel MAFIT en un servidor VPS de forma rápida y sencilla.

## 📚 Documentación Disponible

- **`GUIA_RAPIDA_VPS.md`** - Guía rápida paso a paso (empieza aquí)
- **`GUIA_DESPLIEGUE_COMPLETA.md`** - Guía detallada con todos los pasos
- **`DEPLOY_VPS.md`** - Guía básica de despliegue

## ⚡ Inicio Rápido

### 1. Preparar el proyecto (Windows)

Ejecuta el script de preparación:

```batch
preparar-para-vps.bat
```

Este script:
- Exporta tu base de datos local
- Verifica que todos los archivos necesarios estén presentes
- Te muestra los comandos para subir al VPS

### 2. Subir al VPS

Usa PowerShell o WinSCP para subir los archivos:

```powershell
# Subir proyecto completo
scp -r C:\WEB\MAFIT usuario@tu-vps-ip:/var/www/mafit

# Subir script de despliegue
scp C:\WEB\MAFIT\deploy-vps.sh usuario@tu-vps-ip:/tmp/deploy-vps.sh

# Subir backup de BD (si tienes)
scp C:\WEB\MAFIT\backups\mafit_backup_*.sql usuario@tu-vps-ip:/tmp/mafit_backup.sql
```

### 3. Ejecutar despliegue automatizado

Conéctate al VPS y ejecuta:

```bash
ssh usuario@tu-vps-ip
chmod +x /tmp/deploy-vps.sh
sudo /tmp/deploy-vps.sh
```

El script instalará y configurará todo automáticamente.

## 🎯 Características del Script de Despliegue

El script `deploy-vps.sh` automatiza:

- ✅ Actualización del sistema
- ✅ Instalación de PHP 8.3 y extensiones necesarias
- ✅ Instalación de Composer
- ✅ Instalación de MySQL
- ✅ Creación de base de datos y usuario
- ✅ Instalación de Nginx
- ✅ Instalación de Node.js (opcional)
- ✅ Configuración de permisos
- ✅ Instalación de dependencias
- ✅ Configuración de `.env`
- ✅ Generación de clave de aplicación
- ✅ Ejecución de migraciones
- ✅ Compilación de assets
- ✅ Optimización para producción
- ✅ Configuración de Nginx
- ✅ Configuración de SSL (opcional)
- ✅ Configuración de cron

## 📋 Requisitos del VPS

- **OS**: Ubuntu 20.04 o superior (recomendado)
- **RAM**: Mínimo 1GB (2GB recomendado)
- **Disco**: Mínimo 10GB libres
- **Acceso**: SSH con permisos sudo

## 🔧 Stack Tecnológico

- **PHP**: 8.3
- **Base de Datos**: MySQL 8
- **Servidor Web**: Nginx
- **Framework**: Laravel 11
- **Frontend**: Vite + TailwindCSS

## 📝 Checklist de Despliegue

- [ ] VPS configurado con Ubuntu
- [ ] Acceso SSH funcionando
- [ ] Proyecto subido al VPS
- [ ] Script de despliegue ejecutado
- [ ] Base de datos importada (si aplica)
- [ ] Usuario administrador creado
- [ ] SSL configurado (si tienes dominio)
- [ ] Aplicación accesible en el navegador
- [ ] Login funcionando correctamente

## 🆘 Solución de Problemas

### El script falla

1. Verifica que tienes permisos sudo
2. Verifica que el proyecto está en `/var/www/mafit`
3. Revisa los logs del script
4. Ejecuta los pasos manualmente siguiendo `GUIA_DESPLIEGUE_COMPLETA.md`

### Error 500 después del despliegue

```bash
# Verificar permisos
sudo chown -R www-data:www-data /var/www/mafit
sudo chmod -R 775 /var/www/mafit/storage /var/www/mafit/bootstrap/cache

# Ver logs
tail -f /var/www/mafit/storage/logs/laravel.log
```

### CSS/JS no se cargan

```bash
cd /var/www/mafit
npm install
npm run build
php artisan cache:clear
```

## 🔄 Actualizaciones Futuras

Para actualizar la aplicación después del despliegue inicial:

```bash
cd /var/www/mafit
git pull origin main  # Si usas Git
# O sube los archivos nuevos manualmente

composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build  # Si cambiaste assets
```

## 📞 Soporte

Si necesitas ayuda:

1. Revisa la documentación completa en `GUIA_DESPLIEGUE_COMPLETA.md`
2. Verifica los logs: `/var/www/mafit/storage/logs/laravel.log`
3. Revisa los logs de Nginx: `/var/log/nginx/error.log`

## 🔒 Seguridad

Después del despliegue, asegúrate de:

- [ ] Configurar firewall (UFW)
- [ ] Configurar backups automáticos
- [ ] Mantener el servidor actualizado
- [ ] Usar contraseñas seguras
- [ ] No exponer archivos sensibles (.env)

---

**¡Listo para desplegar!** 🚀

Empieza con `GUIA_RAPIDA_VPS.md` para una guía paso a paso.












