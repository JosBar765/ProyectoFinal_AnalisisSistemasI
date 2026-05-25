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

2. Mover el proyecto `specialisterne\` al directorio `htdocs` de XAMPP.

3. Crear la base de datos en MySQL.

4. Importar el script SQL correspondiente: `Specialisterne.sql`.

5. Configurar las credenciales de conexión en:

```php
/keys.env.example
```
6. Remover la extensión `.example` del archivo `keys.env`

7. Iniciar Apache y MySQL desde XAMPP.

8. Abrir en el navegador:

```bash
http://localhost/specialisterne
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

## Equipo de desarrollo

- Josué Barrios 090-23-4777
- Andrés Ixcajoc 090-23-19115
- Christopher Leal 9390-23-19975

