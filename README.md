# Enderchive

A comprehensive game library management system built with Laravel 12, Vue 3, and Inertia.js. Enderchive allows users to track their game collection, manage wishlists, track game progress, write reviews, and connect with friends.

## 🎮 Features

- **Game Library Management**: Add games to your library, track ownership, and manage different editions
- **Wishlist**: Keep track of games you want to play
- **Progress Tracking**: Mark milestones and track your progress through games
- **Reviews & Ratings**: Write and read reviews for games
- **Social Features**: Add friends, view their libraries, and discover new games
- **Search & Discovery**: Search for games by title, genre, platform, or tags
- **Admin Dashboard**: Manage games, platforms, genres, and tags
- **User Profiles**: Customize your profile with username and profile information

## 🛠️ Tech Stack

### Backend
- **Laravel 12** - PHP web framework
- **MySQL** - Database (not SQLite)
- **Laravel Sanctum** - API authentication
- **Laravel Inertia.js** - Server-side rendering bridge

### Frontend
- **Vue 3** - Progressive JavaScript framework
- **Inertia.js** - Modern monolith approach
- **Tailwind CSS 4** - Utility-first CSS framework
- **Vite** - Next generation frontend tooling
- **Lucide Vue Next** - Icon library

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL 5.7+ or MariaDB 10.3+
- Git

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/verdenef/Enderchive-v2.git
   cd Enderchive-v2
   ```

2. **Navigate to the Laravel project**
   ```bash
   cd enderchive-laravel
   ```

3. **Install PHP dependencies**
   ```bash
   composer install
   ```

4. **Install Node.js dependencies**
   ```bash
   npm install
   ```

5. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

6. **Configure database in `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=enderchive
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

7. **Create the database**
   ```sql
   CREATE DATABASE enderchive;
   ```

8. **Run migrations**
   ```bash
   php artisan migrate
   ```

9. **Seed the database (optional)**
   ```bash
   php artisan db:seed
   ```

10. **Build frontend assets**
    ```bash
    npm run build
    ```

## 🏃 Running the Application

### Development Mode

**Option 1: Using the provided batch script (Windows)**
```bash
# From project root
run-enderchive.bat
```

**Option 2: Manual start**
```bash
# Terminal 1 - Laravel server
cd enderchive-laravel
php artisan serve

# Terminal 2 - Vite dev server
cd enderchive-laravel
npm run dev
```

**Option 3: Using Composer script**
```bash
cd enderchive-laravel
composer run dev
```

The application will be available at `http://localhost:8000`

### Production Mode

```bash
cd enderchive-laravel
npm run build
php artisan serve
```

## 📁 Project Structure

```
enderchive-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── API/          # API controllers
│   │   │   └── ...            # Web controllers
│   │   ├── Middleware/
│   │   └── Requests/
│   └── Models/                # Eloquent models
├── database/
│   ├── migrations/            # Database migrations
│   ├── seeders/               # Database seeders
│   └── factories/             # Model factories
├── resources/
│   ├── js/
│   │   ├── Components/        # Vue components
│   │   ├── Pages/             # Inertia pages
│   │   ├── Layouts/           # Layout components
│   │   └── composables/       # Vue composables
│   └── css/
├── routes/
│   ├── api.php                # API routes
│   └── web.php                # Web routes
└── public/                    # Public assets
```

## 🗄️ Database Schema

The application uses MySQL with the following main entities:

- **Users** - User accounts and authentication
- **Games** - Game information (title, description, genre, etc.)
- **Platforms** - Gaming platforms (PC, PlayStation, Xbox, etc.)
- **Genres** - Game genres
- **Tags** - Game tags for categorization
- **UserGames** - User's game library entries
- **UserGameOwnerships** - Game ownership records (physical, digital, etc.)
- **UserGameProgress** - Progress tracking for milestones
- **UserGameStats** - Game statistics
- **Reviews** - User reviews for games
- **Friends** - Friend relationships
- **Editions** - Game editions (Standard, Deluxe, etc.)
- **GameMilestones** - Milestones for progress tracking
- **Stores** - Game stores/platforms

## 🔐 Authentication

The application uses Laravel Sanctum for API authentication and session-based authentication for web routes. Users can:

- Register new accounts
- Login/Logout
- Update profile information
- Change password
- Update username (with cooldown period)

## 📝 API Endpoints

### Public Endpoints
- `GET /api/games` - List all games
- `GET /api/games/{id}` - Get game details
- `GET /api/platforms` - List platforms
- `GET /api/genres` - List genres
- `GET /api/tags` - List tags
- `GET /api/games/{id}/reviews` - Get game reviews

### Protected Endpoints (Requires Authentication)
- `POST /api/register` - Register new user
- `POST /api/login` - Login
- `GET /api/user/games` - Get user's library
- `POST /api/user/games` - Add game to library
- `GET /api/friends` - Get friends list
- `POST /api/friends` - Send friend request
- And many more...

## 🧪 Testing

```bash
cd enderchive-laravel
php artisan test
```

## 📚 Documentation

Additional documentation can be found in the `docs/` directory:
- System Architecture Diagram
- Sequence Diagram
- User Flow Diagram
- ERD (Entity Relationship Diagram)
- Design Documentation

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👤 Author

**verdenef**

## 🙏 Acknowledgments

- Laravel framework
- Vue.js community
- Inertia.js for the amazing SPA experience
- All contributors and users

---

**Note**: This project uses **MySQL** as the database. Make sure your `.env` file is configured with `DB_CONNECTION=mysql` and proper MySQL credentials.

