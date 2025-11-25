# 🏢 Gestión de Comunidad de Vecinos

Aplicación web en PHP para la gestión de comunidades de vecinos. Permite la administración de usuarios (👤 vecinos, 🧑‍💼 presidente, 👨‍💼 administrador), control de accesos mediante autenticación 🔐, y gestión de información relevante de la comunidad. El objetivo es facilitar la organización y comunicación entre los miembros de la comunidad.

---

## 📑 Índice

- [📈 Estado](#estado)
- [✨ Características](#características)
- [🛠️ Requisitos](#requisitos)
- [⚙️ Instalación](#instalación)
- [🚀 Uso](#uso)
- [📁 Estructura del Proyecto](#estructura-del-proyecto)
- [🔧 Configuración](#configuración)
- [💻 Desarrollo](#desarrollo)
- [🧪 Pruebas](#pruebas)
- [🤝 Contribuir](#contribuir)
- [📄 Licencia](#licencia)
- [👤 Autor](#autor)

---

## Estado 📈
🚧 Proyecto en desarrollo. Funcionalidades principales implementadas y en pruebas.

## Características ✨
- 👥 Gestión de usuarios: vecinos, presidente y administrador
- 🔐 Autenticación y control de acceso por rol
- 🏘️ Visualización de datos de la comunidad
- ✏️ Edición y actualización de información de usuarios
- 🛡️ Seguridad básica en el acceso (login/logout)

## Requisitos 🛠️
- 💻 Sistema operativo: Windows, Linux o macOS con PHP 7.4+
- 🌐 Servidor web: Apache/Nginx recomendado
- 🐘 PHP 7.4 o superior
- 🗄️ MySQL/MariaDB

## Instalación ⚙️
1. 📥 Clona el repositorio:
    ```bash
    git clone <REPO_URL>
    cd Tarea_4_authentication
    ```
2. 🗄️ Configura la base de datos:
    - Crea una base de datos en MySQL/MariaDB.
    - Importa el script `sql/comunidad.sql` para crear las tablas y datos iniciales.
3. 🔧 Configura la conexión en `conexion.php` con tus credenciales de base de datos.
4. 🖥️ Coloca el proyecto en el directorio raíz de tu servidor web local (por ejemplo, `htdocs` en XAMPP o `www` en Laragon).

## Uso 🚀
1. 🌐 Accede a `index.php` desde tu navegador.
2. 🔑 Inicia sesión con un usuario existente (puedes consultar los usuarios en la base de datos o en el script de ejemplo).
3. 👤 Según el rol, accederás a diferentes vistas:
    - 🛠️ **Administrador**: gestión de usuarios y datos generales.
    - 🧑‍💼 **Presidente**: acceso a información relevante y gestión limitada.
    - 👥 **Vecino**: consulta de información personal y de la comunidad.
4. 🚪 Puedes cerrar sesión desde cualquier vista.

## Estructura del Proyecto 📁

