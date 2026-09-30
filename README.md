# PadelPoint

![Status](https://img.shields.io/badge/Status-En_Desarrollo-orange?style=for-the-badge&logo=github)

> **Proyecto en desarrollo activo:** Se continúan implementando nuevos módulos en el panel de administración y optimizando la experiencia de reservas.

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel" />
  <img src="https://img.shields.io/badge/Vue.js-35495E?style=for-the-badge&logo=vuedotjs&logoColor=4FC08D" alt="Vue.js" />
  <img src="https://img.shields.io/badge/TypeScript-007ACC?style=for-the-badge&logo=typescript&logoColor=white" alt="TypeScript" />
  <img src="https://img.shields.io/badge/Inertia.js-9553E8?style=for-the-badge&logo=inertia&logoColor=white" alt="Inertia.js" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
</p>

**PadelPoint** es una plataforma web integral diseñada para la gestión de complejos deportivos de pádel y la reserva en línea de canchas. Ofrece una experiencia fluida e interactiva tanto para jugadores como para administradores de establecimientos.

---

### Características Principales

- **Gestión de Reservas:** Sistema interactivo de turnos y disponibilidad de canchas en tiempo real.
- **Panel de Administración:** Gestión de canchas, horarios, tarifas y estado de turnos.
- **Autenticación y Roles:** Control de acceso seguro para administradores.
- **Interfaz Moderna y Responsiva:** Diseño adaptado a dispositivos móviles y de escritorio.

---

### Stack Tecnológico

#### Backend
- **Framework:** [Laravel](https://laravel.com/)
- **Base de Datos:** [MySQL](https://www.mysql.com/)
- **Adaptador SPA:** [Inertia.js](https://inertiajs.com/)

#### Frontend
- **Framework:** [Vue.js 3](https://vuejs.org/) (Composition API)
- **Lenguaje:** [TypeScript](https://www.typescriptlang.org/)
- **Estilos / UI:** [Tailwind CSS](https://tailwindcss.com/) & [Shadcn Vue](https://www.shadcn-vue.com/)
- **Iconos:** [Lucide Icons](https://lucide.dev/)

---

### Instalación y Configuración Local

Sigue estos pasos para clonar y ejecutar el proyecto en tu entorno local:

1. **Clonar el repositorio:**
   ```bash
   git clone [https://github.com/agustinaojeda/PadelPoint.git](https://github.com/agustinaojeda/PadelPoint.git)
   cd PadelPoint
   ```
2. **Instalar dependencias de PHP:**
   ```bash
   composer install
   ```
3. **Instalar dependencias de JS:**
   ```bash
   npm install
   ```
4. **Configurar las variables de entorno:**
   ```bash
   cp .env.example .env
   ```
5. **Generar la clave de la aplicación y ejecutar migraciones:**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```
6. **Iniciar el entorno de desarrollo:**
   ```bash
   composer run dev
   ```
7. **Acceder a la aplicación:**
   Abre tu navegador e ingresa a http://127.0.0.1:8000.
   
