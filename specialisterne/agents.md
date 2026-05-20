# agents.md — Specialisterne Testing Platform Prototype

## Contexto del Proyecto

**Empresa:** Specialisterne — empresa que integra consultores con Trastorno del Espectro Autista (ASD) en procesos de testing de software manual.

**Objetivo del prototipo:** Maqueta visual (HTML/CSS/JS estático) del sistema centralizado de gestión de testing. Solo diseño, sin lógica de backend. Tecnología: PHP + HTML + CSS + JS + MySQL.

**Actores del sistema:**
- **Administrador** — gestiona usuarios, roles y consultores
- **Supervisor** — gestiona proyectos, casos de prueba y dashboard
- **Consultor** — ejecuta pruebas y reporta errores
- **Sistema** — control de acceso, progreso automático, permisos por rol

---

## Principios de Diseño (NON-NEGOCIABLES)

Derivados del requerimiento no funcional de **accesibilidad sensorial** para consultores con ASD:

| Principio | Regla concreta |
|---|---|
| Minimalismo | Sin animaciones agresivas, sin banners, sin carruseles |
| Paleta de colores | Tonos neutros/fríos: blanco, gris claro (#F5F6FA), azul desaturado (#4A6FA5), acentos verdes suaves (#5BAD91) |
| Tipografía | Sans-serif legible: Inter o Roboto, tamaño mínimo 14px, alto contraste |
| Iconografía | Íconos simples y consistentes (Lucide / Feather Icons). Siempre acompañados de etiqueta de texto |
| Espaciado | Generoso, sin saturación visual. Máximo 3 acciones primarias visibles a la vez |
| Notificaciones | No intrusivas: banners discretos en la parte superior, sin popups modales de alerta |
| Navegación | Sidebar fija con secciones claramente etiquetadas. Sin menús hamburguesa en desktop |
| Estados | Siempre indicar estado actual (badge de color): Pendiente (gris), En progreso (azul), Completado (verde), Fallido (rojo suave), Bloqueado (amarillo) |

---

## Estructura General de Pantallas

### Pantallas a implementar (17 en total)

```
01. Login
02. Admin — Dashboard (resumen de usuarios y consultores)
03. Admin — Gestión de Usuarios (listado + formulario crear/editar)
04. Admin — Gestión de Consultores (listado + perfil de trabajo)
05. Supervisor — Dashboard del Proyecto (avance, errores, estado por fase)
06. Supervisor — Lista de Proyectos
07. Supervisor — Crear / Editar Proyecto (formulario + fases)
08. Supervisor — Manuales de Prueba (listado + subida de archivo)
09. Supervisor — Casos de Prueba (listado por fase, crear/editar)
10. Supervisor — Asignación de Consultores a Proyecto
11. Supervisor — Errores Reportados (historial con severidad)
12. Consultor — Mis Proyectos Asignados
13. Consultor — Casos de Prueba del Proyecto (listado + instrucciones)
14. Consultor — Ejecutar Caso de Prueba (formulario de ejecución)
15. Consultor — Reportar Error (formulario + adjuntar evidencia)
16. Consultor — Mis Tareas (estado: pendiente / en progreso / completada / bloqueada)
17. 403 / Sin Permisos (pantalla genérica para acceso no autorizado)
```

---

## Layout Base (todas las pantallas post-login)

```
┌─────────────────────────────────────────────┐
│  HEADER: Logo Specialisterne | Rol | Avatar  │
├──────────┬──────────────────────────────────┤
│          │                                  │
│ SIDEBAR  │       ÁREA PRINCIPAL             │
│ (fija,   │  - Breadcrumb                    │
│  220px)  │  - Título de sección             │
│          │  - Contenido                     │
│          │                                  │
└──────────┴──────────────────────────────────┘
```

**Sidebar por rol:**

| Admin | Supervisor | Consultor |
|---|---|---|
| Dashboard | Dashboard | Mis Proyectos |
| Usuarios | Proyectos | Mis Tareas |
| Consultores | Manuales | — |
| — | Casos de Prueba | — |
| — | Asignaciones | — |
| — | Errores | — |

---

## Detalle de Pantallas

### 01. Login
- Centrado vertical y horizontal, fondo gris muy claro
- Card blanca con logo, título "Specialisterne Testing Platform"
- Campos: Correo, Contraseña
- Botón primario: "Iniciar Sesión"
- Mensaje de error inline (sin modal) en caso de credenciales inválidas
- Sin registros públicos — solo el admin crea usuarios

---

### 02. Admin — Dashboard
- 3 tarjetas de resumen: Total Usuarios, Total Consultores Activos, Proyectos en Curso
- Tabla simple: últimos usuarios creados (nombre, rol, fecha)
- Sin gráficas complejas

---

### 03. Admin — Gestión de Usuarios
**Listado:**
- Tabla: Nombre | Correo | Rol (badge) | Estado (activo/inactivo) | Acciones (editar/eliminar)
- Botón "Nuevo Usuario" (esquina superior derecha)
- Filtro por Rol (dropdown)

**Formulario Crear/Editar (modal o página):**
- Campos: Nombre, Correo, Contraseña, Rol (select), Estado (toggle)
- Validación inline: correo único, campos requeridos
- Botones: Guardar | Cancelar

---

### 04. Admin — Gestión de Consultores
**Listado:**
- Tabla: Nombre (del usuario vinculado) | Perfil de Trabajo | Habilidades (chips/tags) | Acciones
- Botón "Registrar Consultor"

**Formulario:**
- Campos: Usuario (select de usuarios con rol Consultor), Perfil de Trabajo (textarea), Habilidades (campo de tags)

---

### 05. Supervisor — Dashboard del Proyecto
> Pantalla central para el supervisor. Es el "tablero de control".

**Componentes:**
- Selector de proyecto activo (dropdown en la parte superior)
- Barra de progreso general del proyecto (% de casos ejecutados vs total)
- 4 tarjetas de estado: Casos Pendientes | En Progreso | Completados | Fallidos
- Tabla de Fases del Proyecto con mini-barra de progreso por fase
- Sección "Errores Recientes" — lista de últimos 5 errores con severidad badge:
  - CRÍTICO (rojo), ALTO (naranja), MEDIO (amarillo), BAJO (gris)
- Sección "Consultores asignados" — lista con nombre y casos completados

---

### 06. Supervisor — Lista de Proyectos
- Cards de proyecto (no tabla): Nombre, descripción corta, fechas, estado badge, botón "Ver"
- Estados de proyecto: Activo (verde), En pausa (amarillo), Finalizado (gris), Cancelado (rojo)
- Botón "Nuevo Proyecto"

---

### 07. Supervisor — Crear / Editar Proyecto
**Formulario:**
- Nombre del proyecto, Descripción, Fecha de inicio, Fecha fin, Estado
- Sección "Fases del Proyecto" — lista dinámica:
  - Cada fase: Nombre + Descripción + botón eliminar
  - Botón "Agregar Fase"
- Botones: Guardar Proyecto | Cancelar

---

### 08. Supervisor — Manuales de Prueba
- Listado de manuales del proyecto seleccionado
- Tabla: Título | Descripción | URL/Archivo | Fecha de subida | Acciones
- Formulario de subida: Título, Descripción, URL o archivo adjunto
- Los manuales son referencia para los consultores

---

### 09. Supervisor — Casos de Prueba
- Navegación por Fase (tabs o acordeón lateral)
- Por cada fase: listado de casos de prueba
  - Tabla: Título | Descripción corta | Estado | Acciones (ver/editar)
- Formulario Crear/Editar Caso de Prueba:
  - Fase (select), Título, Descripción, Instrucciones (textarea), Resultado Esperado (textarea), Estado inicial
- Estado inicial siempre: Pendiente

---

### 10. Supervisor — Asignación de Consultores
- Proyecto seleccionado (breadcrumb)
- Panel izquierdo: Consultores disponibles (con perfil y habilidades)
- Panel derecho: Consultores asignados al proyecto
- Acción: botón "Asignar" / "Remover"
- Fecha de asignación se registra automáticamente (mostrar en vista)

---

### 11. Supervisor — Errores Reportados
- Tabla: ID | Título | Descripción corta | Severidad (badge) | Caso de Prueba | Consultor | Fecha | Ver detalle
- Filtros: por severidad, por fase, por estado
- Vista detalle de error:
  - Descripción completa
  - Severidad
  - Ejecución vinculada (resultado, observaciones, fecha)
  - Galería de imágenes/evidencias adjuntas

---

### 12. Consultor — Mis Proyectos Asignados
- Cards de proyectos que el sistema filtró según perfil del consultor
- Cada card: nombre del proyecto, fechas, estado, "Ver Tareas" (botón)
- Si no hay proyectos asignados: mensaje claro "No tienes proyectos asignados actualmente."

---

### 13. Consultor — Casos de Prueba del Proyecto
- Proyecto seleccionado en header/breadcrumb
- Tabs por Fase
- Listado de casos: Título | Estado (badge) | Instrucciones cortas | Botón "Ejecutar"
- Al hacer clic en un caso: panel lateral o página con instrucciones completas y resultado esperado

---

### 14. Consultor — Ejecutar Caso de Prueba
- Información del caso (solo lectura): título, instrucciones, resultado esperado
- Formulario de ejecución:
  - Resultado (select): Aprobado | Fallido | Bloqueado
  - Observaciones (textarea)
  - Fecha/hora: se registra automáticamente (mostrar como campo read-only)
- Si el resultado es "Fallido": aparece enlace "Reportar Error" al guardar
- Botones: Guardar Ejecución | Cancelar

---

### 15. Consultor — Reportar Error
- Vinculado a una ejecución previa (mostrar caso de prueba de referencia)
- Formulario:
  - Título del error
  - Descripción detallada
  - Pasos para reproducir (textarea, puede ser numerado)
  - Severidad (select): Crítico | Alto | Medio | Bajo
  - Adjuntar evidencias (input file, múltiples imágenes)
- Botones: Registrar Error | Cancelar

---

### 16. Consultor — Mis Tareas
- Vista de todas las ejecuciones del consultor
- Tabla o cards: Caso de Prueba | Proyecto | Resultado | Fecha | Acciones
- Filtro por estado (Pendiente, Completado, Fallido, Bloqueado)

---

### 17. Pantalla 403 — Sin Permisos
- Mensaje claro y sin alarmar: "No tienes permisos para acceder a esta sección."
- Botón "Volver al inicio"
- Fondo neutro, sin iconos de peligro

---

## Mapa de Navegación (flujo por rol)

```
LOGIN
  ├── [Admin]     → Dashboard Admin → Usuarios → Consultores
  ├── [Supervisor]→ Dashboard Proyecto → Proyectos → Manuales → Casos de Prueba
  │                → Asignaciones → Errores Reportados
  └── [Consultor] → Mis Proyectos → Casos de Prueba → Ejecutar → Reportar Error
                  → Mis Tareas
```

---

## Esquema de Base de Datos (referencia para datos de prueba del mockup)

```
Rol (id, nombre)
  → valores: Administrador, Supervisor, Consultor

EstadoProyecto (id, estado)
  → valores: Activo, En Pausa, Finalizado, Cancelado

EstadoCasoPrueba (id, estado)
  → valores: Pendiente, En Progreso, Completado, Fallido, Bloqueado

SeveridadError (id, severidad_error)
  → valores: Crítico, Alto, Medio, Bajo

Usuario (id, nombre, correo, contraseña, id_rol, estado, fecha_creacion)
PerfilTrabajo (id, perfil_trabajo, habilidades)
Consultor (id, id_usuario, id_perfil_trabajo)

Proyecto (id, nombre, descripcion, fecha_inicio, fecha_fin, id_estado_proyecto)
FaseProyecto (id, id_proyecto, nombre, descripcion)
ManualPrueba (id, id_proyecto, titulo, descripcion, url, fecha_subida)
CasoPrueba (id, id_fase_proyecto, titulo, descripcion, instrucciones, resultado_esperado, id_estado_caso_prueba)

Proyecto_Consultor (id_proyecto, id_consultor, fecha_asignacion)
EjecucionPrueba (id, id_consultor, id_caso_prueba, resultado, observaciones, fecha_ejecucion)
ErrorReporte (id, id_ejecucion, titulo, descripcion, id_severidad_error)
ImagenErrorReporte (id, id_error, imagen, fecha_subida)
```

---

## Datos de Prueba para el Mockup

Usar estos valores fijos para poblar la maqueta visualmente:

**Usuarios ficticios:**
- Admin: admin@specialisterne.com
- Supervisor: supervisor@specialisterne.com
- Consultor 1: maria.garcia@specialisterne.com (perfil: "Alta atención al detalle, pruebas funcionales")
- Consultor 2: carlos.lopez@specialisterne.com (perfil: "Validación de UI, patrones repetitivos")

**Proyecto de ejemplo:**
- Nombre: "Sistema de Facturación Cliente XYZ"
- Fases: Módulo Login, Módulo Facturación, Módulo Reportes
- Estado: Activo
- Progreso: 42%

**Casos de prueba de ejemplo:**
- "Verificar login con credenciales válidas" — Completado
- "Verificar login con contraseña incorrecta" — Completado
- "Validar generación de factura" — En Progreso
- "Verificar descarga de reporte PDF" — Pendiente

**Error de ejemplo:**
- Título: "Botón 'Generar Factura' no responde en Firefox"
- Severidad: Alto
- Caso vinculado: "Validar generación de factura"

---

## Instrucciones para el Agente Generador

1. **Generar un solo archivo HTML** por pantalla (o un multi-page SPA con navegación simulada por JS).
2. **No usar frameworks pesados.** Solo HTML5 + CSS3 + JS vanilla (o Bootstrap 5 si se necesita grid).
3. **Simular navegación** con `display: none / block` entre secciones o con `<a href>` entre archivos.
4. **No conectar backend.** Todos los datos son hardcoded o generados con JS arrays.
5. **Los formularios no envían datos reales** — mostrar un mensaje de éxito simulado al hacer submit.
6. **Respetar la paleta de colores:**
   - Background: `#F5F6FA`
   - Sidebar: `#FFFFFF` con borde derecho `#E0E4EF`
   - Primario: `#4A6FA5`
   - Éxito/Completado: `#5BAD91`
   - Advertencia/Bloqueado: `#E8A838`
   - Error/Crítico: `#D64045`
   - Texto principal: `#2C3E50`
   - Texto secundario: `#6C7A89`
7. **Íconos:** usar Lucide Icons via CDN (`https://unpkg.com/lucide@latest`)
8. **Fuente:** Inter via Google Fonts
9. **Responsive básico:** funcional en 1280px+. No es prioridad mobile.
10. **Sidebar activa** resaltada con color primario en ítem actual.
11. **Cada pantalla debe mostrar el nombre del rol activo** en el header (badge pequeño).
12. **Los badges de estado** deben ser consistentes en todo el sistema (mismo color = mismo estado).

---

## Archivos a Generar

| Archivo | Descripción |
|---|---|
| `index.html` | Login |
| `admin-dashboard.html` | Dashboard del Administrador |
| `admin-usuarios.html` | Gestión de Usuarios |
| `admin-consultores.html` | Gestión de Consultores |
| `supervisor-dashboard.html` | Dashboard del Supervisor (con gráfico de avance) |
| `supervisor-proyectos.html` | Lista de Proyectos |
| `supervisor-proyecto-form.html` | Crear / Editar Proyecto |
| `supervisor-manuales.html` | Manuales de Prueba |
| `supervisor-casos.html` | Casos de Prueba por Fase |
| `supervisor-asignaciones.html` | Asignación de Consultores |
| `supervisor-errores.html` | Errores Reportados |
| `consultor-proyectos.html` | Mis Proyectos Asignados |
| `consultor-casos.html` | Casos de Prueba del Proyecto |
| `consultor-ejecutar.html` | Ejecutar Caso de Prueba |
| `consultor-reportar.html` | Reportar Error |
| `consultor-tareas.html` | Mis Tareas |
| `403.html` | Sin Permisos |
| `styles.css` | Hoja de estilos global compartida |

---

## Notas Finales

- El sistema maneja **3 roles** diferenciados con vistas completamente distintas.
- El foco de usabilidad está en **el consultor** — interfaz más simple, menos opciones, instrucciones claras.
- El foco de información está en **el supervisor** — dashboard rico, filtros, historial.
- El foco administrativo está en **el administrador** — CRUD de entidades de catálogo.
- Toda decisión de diseño debe priorizar la **baja carga cognitiva** y la **consistencia visual**.
