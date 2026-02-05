# Guía Rápida: Subir Proyecto a Git

## Comandos Básicos

### 1. Verificar estado
```powershell
git status
```

### 2. Agregar todos los archivos
```powershell
git add .
```

### 3. Crear commit
```powershell
git commit -m "Descripción de los cambios"
```

### 4. Subir a GitHub
```powershell
git push origin main
```

## Comando Completo (Todo en uno)

```powershell
cd C:\WEB\MAFIT
git add .
git commit -m "Actualización del proyecto"
git push origin main
```

## Verificar Repositorio Remoto

```powershell
git remote -v
```

Debería mostrar:
```
origin  https://github.com/kikehil/mafit.git (fetch)
origin  https://github.com/kikehil/mafit.git (push)
```

## Si hay errores

### Error: "fatal: not a git repository"
```powershell
git init
git remote add origin https://github.com/kikehil/mafit.git
```

### Error: "Permission denied"
- Verifica tus credenciales de GitHub
- Puede necesitar un token de acceso personal

### Error: "Updates were rejected"
```powershell
git pull origin main
git push origin main
```





