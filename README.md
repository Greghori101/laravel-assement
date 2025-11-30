# Laravel Advanced Assessment - User Management System

A sophisticated Laravel application demonstrating advanced full-stack development with 1 million user records, real-time search capabilities, and event-driven notifications.

## 🎯 Project Overview

This is a production-ready Laravel application implementing the Nina.care Advanced Assessment requirements:

- **Database**: 1 million user records with related address information
- **Frontend**: Vue.js 3 with Inertia.js and Tailwind CSS UI
- **Backend**: Laravel 12 with complete REST API endpoints
- **Search**: Full-text search across 1M user records
- **Notifications**: Event-driven architecture for user updates
- **CRUD**: Complete user lifecycle management

---

## 📋 Core Features Implemented

### 1. Database Setup

#### Schema Design

The application implements a **User-Address one-to-one relationship** with UUID primary keys for optimal scalability.

**Users Table** - Stores user credentials and profile information
**Addresses Table** - Stores user location data with cascading foreign key

#### 1 Million Record Seeding

Uses **parallel CSV processing** with Laravel Concurrency for efficient data loading:

**Data Source:** [Excel Bi Analytics Sample Data Sets](https://excelbianalytics.com/wp/downloads-16-sample-csv-files-data-sets-for-testing/)

- File: `users-1M.csv` (1 million employee records)
- Location: `database/data/` folder
- CSV Column Mapping:
    - Column 2 → First Name
    - Column 4 → Last Name
    - Column 6 → Email
    - Column 29 → Street
    - Column 30 → Country
    - Column 31 → City
    - Column 33 → Post Code

**Implementation Details:**

- 10 parallel processes for concurrent CSV parsing
- 1000 records per batch insert
- Direct database insertion (bypasses ORM)
- Each process maintains independent database connection

---

### 2. Frontend Dashboard

Responsive Vue.js 3 interface built with Inertia.js and Tailwind CSS components.

### 3. Search Functionality

#### Optimized Search Implementation

Implements multi-field full-text search with pagination:

```php
// app/Http/Controllers/UserController.php
public function index(Request $request)
{
    $query = User::query();
    $search = $request->input('search');
    $perPage = $request->input('per_page', 10);

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    $users = $query
        ->orderBy('created_at', 'desc')
        ->paginate($perPage)
        ->withQueryString();

    return Inertia::render('users/Index', [
        'users' => $users,
        'search' => $search,
    ]);
}
```

**Search Capabilities:**

- Multi-field search: first_name, last_name, email
- Pagination with query string persistence
- Default 10 records per page
- Ordered by creation date (newest first)

**Recommended Database Indexes:**

```sql
CREATE INDEX idx_first_name ON users(first_name);
CREATE INDEX idx_last_name ON users(last_name);
CREATE INDEX idx_email ON users(email);
```

---

### 4. User Details View

Users can view complete user information including related address data via the show endpoint.

### 5. Create User Form

Form with validation for creating new users with address information in a single transaction.

### 6. Edit User Form

Edit interface allowing modification of user credentials and address with optional password reset.

### 7. Event-Driven Notifications

#### Job - SendUserUpdatedNotification

Asynchronous queue job for broadcasting user updates:

```php
// app/Jobs/SendUserUpdatedNotification.php
class SendUserUpdatedNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(private $user) {}

    public function handle(): void
    {
        User::chunkById(1000, function ($users) {
            Notification::send($users, new UserUpdated($this->user));
        });
    }
}
```

#### Notification - UserUpdated

```php
// app/Notifications/UserUpdated.php
class UserUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private $user) {}

    public function via(object $notifiable): array
    {
        return ['broadcast', 'database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "User {$this->user->first_name} {$this->user->last_name} has been updated.",
            'user_id' => $this->user->id,
        ];
    }
}
```

**Implementation:**

- Asynchronous queue processing
- Broadcasts via Laravel Reverb WebSocket
- Persists to notifications database table
- Chunks users in sets of 1000
- Triggered on user update event

---

## 🚀 Setup Instructions

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- npm or yarn
- SQLite or MySQL

### Installation

1. **Clone the repository:**

```bash
git clone <repository-url>
cd user-management
```

2. **Install PHP dependencies:**

```bash
composer install
```

3. **Install Node dependencies:**

```bash
npm install
```

4. **Set up environment file:**

```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure database** (in `.env`):

```env
DB_CONNECTION=sqlite
# OR for MySQL:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=user_management
DB_USERNAME=root
DB_PASSWORD=
```

6. **Run migrations:**

```bash
php artisan migrate
```

7. **Seed database with 1M records:**

```bash
php artisan db:seed --class=UsersCsvSeeder
```

This command:

- Reads from `database/data/users-1M.csv` (or `users-1k.csv` for testing)
- Uses 10 parallel processes
- Batch inserts 1000 records at a time
- Estimated time: 5-15 minutes depending on system specs

**Note:** Ensure CSV files are placed in `database/data/` folder before running the seeder.

### Running the Application

**Development:**

```bash
composer run dev
```

Starts Laravel server, queue listener, and Vite dev server.

**Production:**

```bash
npm run build
php artisan serve
```

---

## 📁 Project Structure

```
user-management/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── UserController.php          # Main user CRUD controller
│   │   └── Requests/
│   │       ├── CreateUserRequest.php       # Create validation rules
│   │       └── UpdateUserRequest.php       # Update validation rules
│   ├── Jobs/
│   │   └── SendUserUpdatedNotification.php # Async notification job
│   ├── Models/
│   │   ├── User.php                        # User model with UUID
│   │   └── Address.php                     # Address model
│   └── Notifications/
│       └── UserUpdated.php                 # User update notification
│
├── database/
│   ├── data/
│   │   ├── users-1k.csv                    # 1K sample data (testing)
│   │   └── users-1M.csv                    # 1M production data (from Excel Bi Analytics)
│   ├── factories/
│   │   ├── UserFactory.php
│   │   └── AddressFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   └── 2025_11_29_165027_create_addresses_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── UsersCsvSeeder.php
│
├── resources/
│   └── js/
│       └── pages/
│           └── users/
│               ├── Index.vue               # User listing with search
│               ├── Show.vue                # User details
│               ├── Create.vue              # Create user form
│               └── Edit.vue                # Edit user form
│
├── routes/
│   └── web.php                             # User routes definition
│
├── composer.json                           # PHP dependencies
├── package.json                            # JavaScript dependencies
└── README.md                               # This file
```

---

## 🔑 Key Technologies & Stack

| Layer               | Technology     | Version |
| ------------------- | -------------- | ------- |
| **Backend**         | Laravel        | 12.0    |
| **Frontend**        | Vue.js         | 3.5     |
| **UI Framework**    | Tailwind CSS   | 4.1     |
| **Router**          | Inertia.js     | 2.1     |
| **Real-time**       | Laravel Reverb | 1.0     |
| **Database**        | SQLite/MySQL   | -       |
| **Build Tool**      | Vite           | 7.0     |
| **Package Manager** | Composer/npm   | -       |

---

## 📊 Database Schema

### Users Table

| Column              | Type        | Notes           |
| ------------------- | ----------- | --------------- |
| `id`                | UUID        | Primary key     |
| `first_name`        | String(255) | Required        |
| `last_name`         | String(255) | Required        |
| `email`             | String(255) | Unique, indexed |
| `email_verified_at` | Timestamp   | Nullable        |
| `password`          | String(255) | Nullable        |
| `remember_token`    | String(100) | Nullable        |
| `created_at`        | Timestamp   | Auto            |
| `updated_at`        | Timestamp   | Auto            |

### Addresses Table

| Column       | Type        | Notes                |
| ------------ | ----------- | -------------------- |
| `id`         | Integer     | Primary key          |
| `user_id`    | UUID        | Foreign key, indexed |
| `country`    | String(255) | Required             |
| `city`       | String(255) | Required             |
| `post_code`  | String(255) | Required             |
| `street`     | String(255) | Required             |
| `created_at` | Timestamp   | Auto                 |
| `updated_at` | Timestamp   | Auto                 |

---

## 🔄 API Endpoints

All endpoints require authentication (`auth` and `verified` middleware).

| Method   | Endpoint           | Controller Action | Description                         |
| -------- | ------------------ | ----------------- | ----------------------------------- |
| `GET`    | `/users`           | `index`           | List users with search/pagination   |
| `GET`    | `/users/create`    | `create`          | Show create form                    |
| `POST`   | `/users`           | `store`           | Create new user                     |
| `GET`    | `/users/{id}`      | `show`            | Display user details                |
| `GET`    | `/users/{id}/edit` | `edit`            | Show edit form                      |
| `PATCH`  | `/users/{id}`      | `update`          | Update user (triggers notification) |
| `DELETE` | `/users/{id}`      | `destroy`         | Delete user                         |

---

## 🧪 Testing

```bash
composer run test
```

---

## 🚢 Performance Specifications

### Database Indexes

```sql
CREATE INDEX idx_first_name ON users(first_name);
CREATE INDEX idx_last_name ON users(last_name);
CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_created_at ON users(created_at DESC);
```

### Queue Configuration

```env
QUEUE_CONNECTION=database
```

For production, configure Redis:

```env
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
```

---

## 🔐 Security Features

- Laravel Fortify authentication with two-factor support
- Server-side input validation on all endpoints
- CSRF token protection
- Bcrypt password hashing
- Parameterized queries via Eloquent ORM
- Email verification enforcement
- Route middleware protection

---

## 📝 Development Notes

### Adding New User Fields

1. Create migration: `php artisan make:migration add_field_to_users --table=users`
2. Add column definition
3. Update `$fillable` in User model
4. Add to validation rules in CreateUserRequest/UpdateUserRequest
5. Update Vue form components

### Adding New Notifications

1. Create notification: `php artisan make:notification MyNotification`
2. Create job: `php artisan make:job SendMyNotification`
3. Dispatch from controller: `MyNotification::dispatch($data)`
4. Configure via() channels

---

## 🐛 Troubleshooting

| Issue                     | Resolution                                                                           |
| ------------------------- | ------------------------------------------------------------------------------------ |
| Search returns no results | Verify CSV loaded, check database indexes, run `php artisan tinker` to test queries  |
| Notifications not sent    | Verify queue running with `php artisan queue:listen`, check QUEUE_CONNECTION setting |
| Slow search on 1M records | Add database indexes, use Redis for caching and queue                                |
| Styles not loading        | Run `npm run dev` (development) or `npm run build` (production), clear cache         |
| CSV file not found        | Ensure files in `database/data/` directory before running seeder                     |

---

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Inertia.js Guide](https://inertiajs.com/)
- [Vue.js 3 Guide](https://vuejs.org/)
- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [Laravel Reverb](https://laravel.com/docs/reverb)
- [Excel Bi Analytics Sample Data](https://excelbianalytics.com/wp/downloads-16-sample-csv-files-data-sets-for-testing/) - CSV data source used in this project

---

## 👥 Collaborators

Project collaborators:

- **Alepop2** - [GitHub Profile](https://github.com/Alepop2)
- **Anatolyduzenko** - [GitHub Profile](https://github.com/anatolyduzenko)

---

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

---

## 🎓 Assessment Completion

This project fulfills all Nina.care Advanced Level requirements:

✅ **Database Setup**: 1M user records with addresses via parallel CSV seeding
✅ **Frontend Dashboard**: Vue.js 3 + Inertia.js responsive interface
✅ **Search Functionality**: Multi-field search across 1M users with pagination
✅ **User Details View**: Clickable results showing complete user/address data
✅ **Create Users**: Form with validation for new users and addresses
✅ **Edit Users**: Update functionality for existing records
✅ **Event Notifications**: Asynchronous job dispatch with broadcast/database channels
