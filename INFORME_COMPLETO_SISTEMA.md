# 📑 INFORME TÉCNICO Y EJECUTIVO COMPLETO DE DESARROLLO Y MEJORAS DEL SISTEMA

**Proyecto**: SolicitudProsegur / I-Stock Almacén e Inventario  
**Cliente / Empresa**: INEES / Prosegur  
**Fecha de Generación**: 19 de Agosto de 2026  
**Estado General**: **100% OPERATIVO, COMPROBADO Y VERIFICADO EN RUNTIME**

---

## 📌 RESUMEN DE ACTIVIDADES REALIZADAS

Se realizó una intervención integral en el sistema **SolicitudProsegur / I-Stock**, abarcando correcciones en infraestructura, optimización de base de datos MySQL, rediseño de interfaz de usuario, migración de librerías de generación de reportes, envío automático de notificaciones por correo SMTP, configuración del perfil y seguridad para el **Rol Técnico** y desarrollo completo del módulo de **Recuperación de Contraseña**.

---

## 🛠️ 1. MÓDULO DE NOTIFICACIONES Y ALERTAS EN TIEMPO REAL

### Diagnóstico:
El archivo de navegación `app/partials/navbar_menu.php` se encontraba corrupto debido a un código incompleto, lo que bloqueaba la navegación del sistema.

### Acciones Realizadas:
1. **Restauración del Controlador de Notificaciones**:
   - Reubicación y estructuración en `app/controllers/notificacion/NotificacionController.php`.
2. **Widget Interactivo de Campanita (`plantillaVista.php`)**:
   - Posicionado en la barra superior al lado del perfil de usuario.
   - **Insignia Dinámica (Badge)**: Contador animado (`animate-pulse`) que muestra las notificaciones pendientes no leídas.
   - **Descuento Inmediato vía AJAX**: Al presionar `✓`, la notificación cambia a leída en MySQL (`leida = 1`) y descuenta el contador sin recargar la página.
3. **Envío Automático de Alertas de Stock Crítico por Correo (SMTP SSL)**:
   - Configuración de **PHPMailer** con el servidor SMTP de Gmail (`smtp.gmail.com`, puerto 465 SSL).
   - Envío verificado de alertas con diseño HTML corporativo al correo **`Juanes01vargas@gmail.com`**.
   - Registro permanente en `ALERTAS_CORREO_DESTINO` (`app/config/config_global.php`).

---

## 📊 2. REDISEÑO EJECUTIVO DEL DASHBOARD Y ESTANDARIZACIÓN DE RUTAS

### Acciones Realizadas:
1. **Header Prominente y Exportador PDF**:
   - Saludo personalizado al usuario autenticado y botón directo de **Exportar Reporte PDF**.
2. **Grid de 4 Tarjetas KPI (Métricas en Tiempo Real)**:
   - **Stock Total**: Total de unidades físicas en el inventario.
   - **Stock Crítico**: Artículos por debajo del umbral mínimo configurado.
   - **Por Aprobar**: Solicitudes pendientes de evaluación.
   - **Catálogo Activo**: Variedad de productos registrados.
3. **Gráficos Interactivos (Chart.js)**:
   - Visualización de áreas y curvas suaves (*Entradas / Compras* vs *Salidas / Despachos*).
4. **Barras de Nivel de Criticidad**:
   - Comparación visual dinámica de `cantidad_disponible` vs `stock_minimo`.
5. **Estandarización de Rutas a `/inventario`**:
   - Corrección de enlaces viejos `inventarioVer` reemplazados por la ruta oficial **`inventario`** en vistas, controladores y botones.

---

## 📑 3. SUSTITUCIÓN DEL MOTOR DE REPORTES (Dompdf → mPDF & PhpSpreadsheet)

### Acciones Realizadas:
1. **Migración a mPDF (`mpdf/mpdf`)**:
   - Se reemplazó `Dompdf\Dompdf` por **`\Mpdf\Mpdf`** en `dashboardControlador.php` y `solicitudesControlador.php`.
   - Soporte para formatos A4 Landscape (horizontal) y tipografías vectoriales sin errores de margen.
2. **Exportador Excel Nátivo (`phpoffice/phpspreadsheet`)**:
   - Implementación de reportes descargables en formato `.xlsx` mediante `PhpOffice\PhpSpreadsheet`.

---

## 🗄️ 4. CORRECCIÓN EN BASE DE DATOS: PRIORIDADES DE SOLICITUDES

### Diagnóstico:
La columna `prioridad` en la tabla `solicitudes` era un `ENUM('baja','media','urgente')`, lo que provocaba que la opción *"Alta"* guardara un valor vacío `""`.

### Acciones Realizadas:
1. **Alteración SQL de la Estructura**:
   ```sql
   ALTER TABLE solicitudes MODIFY COLUMN prioridad ENUM('baja','media','alta','urgente') NOT NULL DEFAULT 'media';
   ```
2. **Actualización de Registros Previos**: Limpieza e imputación de prioridades vacías.
3. **Badges Visuales por Prioridad**:
   - `Urgente`: Rojo (**Extrabold**)
   - `Alta`: Naranja (**Bold**)
   - `Media`: Amarillo (**Medium**)
   - `Baja`: Gris / Slate (**Normal**)

---

## 🧑‍🔧 5. CONFIGURACIÓN COMPLETA DEL ROL TÉCNICO

### Acciones Realizadas:
1. **Matriz RBAC en BD (`rol_permisos`)**:
   - **Rutas Autorizadas**: `/inicio`, `/inventario`, `/solicitudes` y `/notificacion`.
   - **Ruta Restringida**: `/dashboard` (**Bloqueado automáticamente** por `index.php`).
2. **Menú Acotado para Técnico**:
   - En versión de escritorio y móvil se muestran únicamente: *Inicio*, *Inventario Almacén* e *Historial y Solicitudes*.
3. **Credenciales del Usuario de Prueba**:
   - **Usuario**: `tecnico.prueba`
   - **Contraseña**: `Tecnico2026*`
   - **Email**: `Juanes01vargas@gmail.com`
   - **Rol**: Técnico (`idTipoUsuario = 2`)
4. **Permisos en Flujo de Solicitudes**:
   - Los **Técnicos** y **Administradores** pueden Aprobar, Rechazar, Iniciar Proceso y Finalizar solicitudes.
5. **Cierre Automático de Opciones**:
   - Al alcanzar un estado finalizado (**"Listo para Entrega"** / `atendida`, **"Rechazada"** o **"Cancelada"**), los botones de cambio de estado **se ocultan automáticamente**.

---

## 🔑 6. MÓDULO DE RECUPERACIÓN DE CONTRASEÑA (`resetPassword`)

### Acciones Realizadas:
1. **Creación de la Tabla `password_reset`**:
   - Diseñada en MySQL para guardar códigos de 6 dígitos encriptados con hash Bcrypt (`codigo_hash`), la hora de caducidad (`expira_en`) y la bandera de uso (`usado`).
2. **Registro de Rutas Públicas**:
   - Se insertaron en la tabla `rutas`: `/solicitarCodigo`, `/resetPassword`, `/enviarCodigo` y `/mensajeEnviado`.
3. **Solución al Desfase de Zona Horaria (PHP vs MySQL)**:
   - Se detectó que PHP operaba en hora UTC (7 horas adelantadas a Bogotá), haciendo que cualquier código expirara inmediatamente.
   - **Solución**: Se actualizaron `guardarCodigoReset` y `verificarCodigoReset` en `loginModelo.php` para usar la hora nativa de MySQL **`expira_en > NOW()`** y **`DATE_ADD(NOW(), INTERVAL 15 MINUTE)`**.
4. **Envío de Código por Correo Electrónico**:
   - Se implementó la función para enviar correos corporativos HTML con el código de 6 dígitos al correo del usuario.
   - **Último código activo generado**: `410259` para `Juanes01vargas@gmail.com`.

---

## 🧪 7. MATRIZ DE PRUEBAS DE VERIFICACIÓN

| Módulo / Prueba | Descripción | Resultado |
| :--- | :--- | :---: |
| **Sintaxis PHP (`php -l`)** | Verificación de sintaxis en todos los controladores y vistas creados/editados. | **PASÓ (0 errores)** |
| **Notificaciones AJAX** | Marcado de notificaciones como leídas en tiempo real. | **PASÓ** |
| **Envío Alertas SMTP** | Despacho de correo HTML de stock crítico a `Juanes01vargas@gmail.com`. | **PASÓ** |
| **mPDF & PhpSpreadsheet** | Generación de reportes PDF y Excel descargables. | **PASÓ** |
| **Ruta `/inventario`** | Resolución correcta del botón "Ver Inventario Completo". | **PASÓ (200 OK)** |
| **Bloqueo Dashboard Técnico** | Intento de ingreso directo a `/dashboard` con rol Técnico. | **PASÓ (Bloqueado)** |
| **Aprobaciones Técnico** | Aprobar/Rechazar en rol Técnico vs Bloqueo en rol Cliente. | **PASÓ** |
| **Ocultamiento Botones** | Ocultar panel de acciones al llegar a "Listo para Entrega". | **PASÓ** |
| **Reset Password (NOW)** | Inserción, envío por email y verificación de código de 6 dígitos. | **PASÓ (Éxito)** |

---

## 📁 UBICACIÓN DE LOS ARCHIVOS REPORTE GENERADOS

- **Archivo PDF Oficial**: [`c:/xampp/htdocs/solicitudProsegur/reporte_cambios_sistema.pdf`](file:///c:/xampp/htdocs/solicitudProsegur/reporte_cambios_sistema.pdf)
- **Archivo Markdown Completo**: [`c:/xampp/htdocs/solicitudProsegur/INFORME_COMPLETO_SISTEMA.md`](file:///c:/xampp/htdocs/solicitudProsegur/INFORME_COMPLETO_SISTEMA.md)
