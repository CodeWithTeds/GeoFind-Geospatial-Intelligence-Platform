# <p align="center"><img src="public/images/oxbit.png" width="120" alt="Oxbit Logo"><br>Oxbit GIS & Gamified Learning Platform</p>

<p align="center">
    <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel 12">
    <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php" alt="PHP 8.2">
    <img src="https://img.shields.io/badge/Livewire-4.x-4e56a6?style=for-the-badge&logo=livewire" alt="Livewire 4">
    <img src="https://img.shields.io/badge/Tailwind_CSS-4.0-38B2AC?style=for-the-badge&logo=tailwind-css" alt="Tailwind 4">
    <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License MIT">
</p>

---

**Oxbit** is a high-performance, modular web application that seamlessly integrates advanced **Geographic Information Systems (GIS)** with a **Gamified Learning Platform**. Built on the latest Laravel 12 ecosystem, it offers robust location-based intelligence and interactive educational experiences.

## 🚀 Key Features

### 🌍 Advanced GIS Intelligence
*   **Geocoding & Reverse Geocoding**: Powered by OpenStreetMap's Nominatim API.
*   **Geometric Calculations**: Calculate bearings, distances, and convex hulls.
*   **Location Visualizations**: Dynamic heatmaps, geofencing, and location clustering.
*   **Real-time Environmental Data**: Integrated weather forecasts (Open-Meteo) and elevation data (Open-Elevation).
*   **Points of Interest (POI)**: Dynamic fetching of nearby amenities, tourism spots, and landmarks via Overpass API.
*   **Spatial Indexing**: Efficient geohashing for fast location querying.

### 🎮 Gamified Learning
*   **Interactive Levels**: Progressive learning paths for users.
*   **Question Engine**: Robust CRUD management for educational content.
*   **Real-time Leaderboards**: Compete with other users in a dynamic ranking system.
*   **Play Session Management**: Secure answer submission and verification.

### 🛠️ Core Infrastructure
*   **Role-Based Access Control**: Dedicated Admin and Client portals.
*   **IP-Controlled Security**: Middleware for restricted access control.
*   **Modular Architecture**: Clean separation of concerns for scalability.
*   **Modern Frontend**: Built with Livewire 4 for a smooth, SPA-like experience without leaving Laravel.

---

## 🛠 Tech Stack

| Component | Technology |
| :--- | :--- |
| **Backend** | [Laravel 12](https://laravel.com), [PHP 8.2+](https://php.net) |
| **Frontend** | [Livewire 4](https://livewire.laravel.com), [Tailwind CSS 4](https://tailwindcss.com) |
| **Build Tool** | [Vite](https://vitejs.dev) |
| **Database** | MySQL / SQLite |
| **GIS APIs** | Nominatim, Overpass API, Open-Meteo, Open-Elevation |

---

## ⚙️ Installation

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- XAMPP / Laravel Sail

### Setup Steps

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/yourusername/oxbit.git
    cd oxbit
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    npm install
    ```

3.  **Environment Configuration**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Database Migration & Seeding**
    ```bash
    # Create the database file if using SQLite
    touch database/database.sqlite
    
    # Run migrations and seed the database
    php artisan migrate --seed
    ```

5.  **Compile Assets**
    ```bash
    npm run build
    ```

6.  **Start Development Server**
    ```bash
    npm run dev
    ```

---

## 📁 Directory Structure Highlights

*   `app/Services/GeocodingService.php`: Core logic for GIS calculations and API integrations.
*   `app/Livewire/`: Frontend components for both Admin and Client portals.
*   `routes/web.php`: Defined routes for authentication, GIS tools, and game mechanics.
*   `public/images/`: Assets including the Oxbit logo and UI elements.

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:
1. Fork the project.
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`).
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4. Push to the Branch (`git push origin feature/AmazingFeature`).
5. Open a Pull Request.

---

## 📜 License

Distributed under the MIT License. See `LICENSE` for more information.

---

<p align="center">Made with ❤️ by the Oxbit Team</p>
