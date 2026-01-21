# Configuración de Correo SMTP

Para que el sistema de notificaciones por correo funcione correctamente, debes agregar las siguientes variables a tu archivo `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=enrique.gilzara@gmail.com
MAIL_PASSWORD=jetbfuugctsaiavl
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=enrique.gilzara@gmail.com
MAIL_FROM_NAME="MAFIT"
```

## Notas importantes:

1. El sistema enviará correos automáticamente a los usuarios asignados a cada tienda cuando se cierre un inventario.
2. El correo incluirá:
   - Nombre de la tienda
   - Usuario que realizó el inventario
   - Fecha y hora del inventario
   - Notas adicionales (si fueron agregadas)

3. Si un usuario no tiene correo electrónico asignado, se omitirá el envío para ese usuario.

4. Si hay un error al enviar el correo, se registrará en los logs pero no afectará el guardado del inventario.

