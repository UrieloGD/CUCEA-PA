# 🎓 Sistema Integral de Programación Académica (SIPA) - CUCEA

[![PHP Version](https://img.shields.io/badge/PHP-8.2.20-blue)](https://www.php.net/)
[![Database](https://img.shields.io/badge/MySQL-8.0.35-orange)](https://dev.mysql.com/)
[![License](https://img.shields.io/badge/Licencia-MIT-green)](LICENSE)

**SIPA** es una plataforma tecnológica diseñada para optimizar la Programación Académica (PA) en el CUCEA, centralizando la asignación de aulas, horarios, docentes y recursos, y facilitando la colaboración interdepartamental.

**URL de Producción**: [pa.cucea.udg.mx](http://pa.cucea.udg.mx)  
**Repositorio**: [CUCEA-PA en GitHub](https://github.com/UrieloGD/CUCEA-PA)

---

## 📜 Tabla de Contenidos
- [Introducción](#-introducción)
- [Tecnologías](#-tecnologías)
- [Requerimientos](#-requerimientos)
- [Instalación Local](#-instalación-local)
- [Estructura de Usuarios](#-estructura-de-usuarios)
- [Funcionalidades Clave](#-funcionalidades-clave)
- [Contribución](#-contribución)
- [Licencia](#-licencia)
- [Contacto](#-contacto)

---

## 🌟 Introducción
### Objetivo del Proyecto
El SIPA surge como respuesta a los desafíos de la **Programación Académica (PA)** en el CUCEA:
- Distribución eficiente de aulas y horarios.
- Coordinación entre áreas administrativas (Jefes de Departamento, Control Escolar, etc.).
- Automatización de procesos para reducir errores humanos.
- Acceso centralizado a información en tiempo real.

### ¿Qué resuelve?
- Elimina la comunicación deficiente entre departamentos.
- Simplifica la asignación de recursos (docentes, espacios, horarios).
- Garantiza que los estudiantes de primer ingreso tengan horarios asignados.

---

## 💻 Tecnologías
- **Frontend**: 
  - JavaScript Vanilla
  - CSS3 (Sin frameworks)
- **Backend**: 
  - PHP 8.2.20
  - Librerías: `TCPDF` (generación de PDFs), `Composer` (gestión de dependencias)
- **Base de Datos**: 
  - MySQL 8.0.35
  - Gestor: phpMyAdmin 5.2.1
- **Entorno de Desarrollo**: 
  - WAMP (Windows) / MAMP (macOS)
- **Control de Versiones**: 
  - Git + GitHub

---

## ⚙️ Requerimientos
- **Equipo**: 
  - Conexión a internet.
  - Navegador web (Chrome/Edge recomendados).
- **Credenciales**: 
  - Acceso autorizado por el CUCEA (ver [Inicio de Sesión](#-uso)).
- **Servidor Local (Desarrollo)**: 
  - PHP 8.2+, MySQL 8.0+, Apache/Nginx.

---

## 🛠️ Instalación Local
### Pasos para Configurar el Proyecto
1. **Clonar el Repositorio**:
   ```bash
   git clone https://github.com/UrieloGD/CUCEA-PA.git
   cd CUCEA-PA