# OneMember

A professional SaaS application built with Laravel 13, Bootstrap 5, and Bootstrap Icons.

---

## Requirements

| Tool | Version |
|------|---------|
| PHP | 8.3+ |
| Composer | 2.x |
| Node.js | 20+ |
| npm | 10+ |

---

## Local Development Setup

### 1. Clone the repository

```bash
git clone <repo-url> onemember
cd onemember
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your local database, mail, and other credentials.

### 4. Create the SQLite database (default for local dev)

```bash
touch database/database.sqlite
php artisan migrate
```

### 5. Install Node dependencies and build assets

```bash
npm install
npm run dev
```

### 6. Start the development server

```bash
php artisan serve
```

Visit [http://localhost:8000](http://localhost:8000).

---

## Useful Commands

| Command | Description |
|---------|-------------|
| `php artisan serve` | Start the local development server |
| `npm run dev` | Start Vite with hot module replacement |
| `npm run build` | Build production assets |
| `php artisan migrate` | Run pending database migrations |
| `php artisan migrate:fresh --seed` | Wipe and re-seed the database |
| `php artisan tinker` | Open an interactive REPL |
| `composer test` | Run the PHPUnit test suite |
| `./vendor/bin/pint` | Fix code style with Laravel Pint |
| `php artisan route:list` | List all registered routes |

---

## Running Everything at Once

The `composer dev` script starts the server, queue worker, log watcher, and Vite together:

```bash
composer dev
```

---

## Project Documentation

All project documentation lives in the [`/docs`](./docs/) folder:

| File | Contents |
|------|----------|
| [01-Business-Requirements.md](docs/01-Business-Requirements.md) | Goals, personas, functional requirements |
| [02-Architecture.md](docs/02-Architecture.md) | Stack, directory structure, design decisions |
| [03-Database.md](docs/03-Database.md) | Schema conventions, entity overview |
| [04-UI-UX.md](docs/04-UI-UX.md) | Design system, layouts, component conventions |
| [05-Roadmap.md](docs/05-Roadmap.md) | Phased feature plan |
| [06-Coding-Standards.md](docs/06-Coding-Standards.md) | PHP, Blade, JS, CSS, Git conventions |
| [07-Deployment.md](docs/07-Deployment.md) | Server requirements, deploy checklist |
| [CHANGELOG.md](docs/CHANGELOG.md) | Version history |

---

## Tech Stack

- **Framework:** Laravel 13
- **Frontend:** Bootstrap 5.3 + Bootstrap Icons
- **Bundler:** Vite 8
- **Database:** SQLite (dev) / MySQL or PostgreSQL (production)
- **Testing:** PHPUnit 12
