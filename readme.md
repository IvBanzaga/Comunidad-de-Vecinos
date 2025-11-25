

# 🏢 Gestión de Comunidad de Vecinos


Aplicación web en PHP para la gestión de comunidades de vecinos. Permite la administración de usuarios (👤 vecinos, 🧑‍💼 presidente, 👨‍💼 administrador), control de accesos mediante autenticación 🔐, y gestión de información relevante de la comunidad. El objetivo es facilitar la organización y comunicación entre los miembros de la comunidad.



## 📑 Tabla de contenidos
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

## 📈 Estado
🚧 Proyecto en desarrollo. Funcionalidades principales implementadas y en pruebas.

## ✨ Características
- 👥 Gestión de usuarios: vecinos, presidente y administrador
- 🔐 Autenticación y control de acceso por rol
- 🏘️ Visualización de datos de la comunidad
- ✏️ Edición y actualización de información de usuarios
- 🛡️ Seguridad básica en el acceso (login/logout)

## 🛠️ Requisitos
- 💻 Sistema operativo: Windows, Linux o macOS con PHP 7.4+
- 🌐 Servidor web: Apache/Nginx recomendado
- 🐘 PHP 7.4 o superior
- 🗄️ MySQL/MariaDB

## ⚙️ Instalación
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

## 🚀 Uso
1. 🌐 Accede a `index.php` desde tu navegador.
2. 🔑 Inicia sesión con un usuario existente (puedes consultar los usuarios en la base de datos o en el script de ejemplo).
3. 👤 Según el rol, accederás a diferentes vistas:
    - 🛠️ **Administrador**: gestión de usuarios y datos generales.
    - 🧑‍💼 **Presidente**: acceso a información relevante y gestión limitada.
    - 👥 **Vecino**: consulta de información personal y de la comunidad.
4. 🚪 Puedes cerrar sesión desde cualquier vista.

## 📁 Estructura del Proyecto

```
├── 🔐 auth.php                # Lógica de autenticación y control de acceso
├── 🔗 conexion.php            # Conexión a la base de datos
├── 🛠️ functions.php           # Funciones auxiliares
├── 🏠 index.php               # Página principal/login
├── 🔑 login.php               # Formulario de inicio de sesión
├── 🚪 logout.php              # Cierre de sesión
├── 🛠️ viewAdmin.php           # Vista para el administrador
├── 🧑‍💼 viewPresidente.php      # Vista para el presidente
├── 👥 viewVecino.php           # Vista para el vecino
├── 📂 sql/                    # Scripts SQL para la base de datos
│   ├── comunidad.sql
│   ├── comunidad_update.sql
│   └── update_passwords.php
└── 📄 .gitignore              # Exclusión de archivos y carpetas sensibles
```

## 🔧 Configuración
Edita el archivo `conexion.php` para establecer los parámetros de conexión a tu base de datos:

```php
$host = 'localhost';      // 🖥️ Servidor
$usuario = 'root';        // 👤 Usuario
$password = '';           // 🔑 Contraseña
$bbdd = 'comunidad';      // 🗄️ Base de datos
```

## 💻 Desarrollo
Puedes contribuir mejorando la lógica de autenticación, añadiendo nuevas vistas o funcionalidades (por ejemplo, gestión de incidencias, notificaciones, etc.).

🔹 Recomendaciones:
- 🌱 Utiliza ramas para nuevas funcionalidades.
- 🛡️ Sigue buenas prácticas de seguridad en PHP.
- 📝 Documenta tus cambios en los comentarios del código.

## 🧪 Pruebas
Actualmente no hay una suite de pruebas automatizadas. Se recomienda probar manualmente:
- 🚪 Inicio y cierre de sesión
- 👤 Acceso a vistas según el rol
- ✏️ Edición y consulta de datos

## 🤝 Contribuir
1. 📝 Abre un issue describiendo la propuesta o bug.
2. 🍴 Haz un fork y envía un pull request con una descripción clara de los cambios.

## 📄 Licencia
MIT. Puedes usar, modificar y distribuir este proyecto libremente.

## 👤 Autor

### 👨‍💻 **Iván Bazaga**

Desarrollador web con experiencia en PHP, MySQL y gestión de proyectos de software. Este proyecto es una práctica de gestión de comunidades de vecinos, aplicando conceptos de autenticación, roles y manejo de datos.

### ☎️ Información de Contacto

| 🌐 Plataforma | 🔗 Enlace | 📋 Descripción |
|------------|--------|-------------|
| 🐙 GitHub | [@IvBanzaga](https://github.com/IvBanzaga/) | Repositorios y proyectos de código |
| 💼 LinkedIn | [Iván Bazaga](https://www.linkedin.com/in/ivan-bazaga-gonzalez/) | Perfil profesional y networking |
| ✉️ Email | [ivan.cpweb@gmail.com](mailto:ivan.cpweb@gmail.com) | Contacto directo para oportunidades |
| 🖥️ Portfolio | [Ivancodelab.com](https://Ivancodelab.com) | Showcase de proyectos y skills |

---
