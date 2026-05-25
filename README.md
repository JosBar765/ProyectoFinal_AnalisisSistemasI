# Specialisterne - Sistema de Gestión de Testing

Sistema web desarrollado como prototipo académico para la gestión centralizada de proyectos de testing, ejecución de casos de prueba y reporte de errores dentro de la organización Specialisterne.

## Descripción

El proyecto tiene como objetivo mejorar la organización, trazabilidad y control del proceso de pruebas de software mediante una plataforma centralizada que permita administrar usuarios, proyectos, asignaciones, ejecución de pruebas y reportes de errores.

El sistema fue desarrollado como parte del curso de Análisis de Sistemas I utilizando tecnologías web tradicionales orientadas al desarrollo rápido de prototipos.

---

## Características principales

- Inicio de sesión y autenticación de usuarios.
- Gestión de roles:
  - Administrador
  - Supervisor
  - Consultor
- Gestión de proyectos y fases.
- Registro de manuales y documentación de prueba.
- Creación y administración de casos de prueba.
- Ejecución de pruebas por consultores.
- Registro de errores y evidencias.
- Dashboard de seguimiento para supervisores.
- Asignación de proyectos según perfil y habilidades del consultor.
- Visualización de avance y estado de proyectos.

---

## Tecnologías utilizadas

### Backend
- PHP
- MySQL

### Frontend
- HTML5
- CSS3
- JavaScript

### Herramientas y servicios
- XAMPP
- Git y GitHub
- Cloudinary (almacenamiento de evidencias)

---

## Estructura general del proyecto

```bash
ProyectoFinal_AnalisisSistemasI/
│
├── pages/
│   ├── admin/
│   ├── supervisor/
│   └── consultor/
│
├── php/
│   ├── auth/
│   ├── proyectos/
│   ├── casos/
│   ├── reportes/
│   └── db.php
│
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
│
└── database/
```

---

## Base de datos

La base de datos está diseñada para gestionar:

- Usuarios y roles.
- Consultores y perfiles de trabajo.
- Proyectos y fases.
- Casos de prueba.
- Ejecución de pruebas.
- Reportes de errores.
- Evidencias multimedia.

---

## Instalación

### Requisitos

- PHP 8+
- MySQL
- XAMPP o entorno similar

### Pasos

1. Clonar el repositorio:

```bash
git clone https://github.com/JosBar765/ProyectoFinal_AnalisisSistemasI.git
```

2. Mover el proyecto al directorio `htdocs` de XAMPP.

3. Crear la base de datos en MySQL.

4. Importar el script SQL correspondiente.

5. Configurar las credenciales de conexión en:

```php
/php/db.php
```

6. Iniciar Apache y MySQL desde XAMPP.

7. Abrir en el navegador:

```bash
http://localhost/ProyectoFinal_AnalisisSistemasI
```

---

## Objetivo del prototipo

El sistema busca demostrar la viabilidad de una plataforma centralizada para la gestión de procesos de testing dentro de Specialisterne, mejorando:

- Organización de información.
- Seguimiento de avances.
- Trazabilidad de pruebas.
- Control de errores.
- Gestión de consultores y proyectos.

---

## Estado del proyecto

Proyecto académico en desarrollo 🚧

Actualmente el sistema continúa en fase de mejoras, corrección de errores y ampliación de funcionalidades.

---

## Equipo de desarrollo

- Josué Abraham Barrios Ramírez
- Equipo de Análisis de Sistemas I

---

## Repositorio oficial

Repositorio disponible en GitHub:

[ProyectoFinal_AnalisisSistemasI](https://github.com/JosBar765/ProyectoFinal_AnalisisSistemasI)

---

## Licencia

Proyecto desarrollado con fines académicos.
