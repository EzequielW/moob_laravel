# Messaging App 📬

A full-featured messaging platform built using **Laravel 12** and **Inertia.js + Vue 3**, enabling users to send messages via Telegram, WhatsApp, Discord, and Slack, complete with file attachments, job queues, and statistics.

---

## 🚀 Features

- ✅ Send messages to multiple platforms
- ✅ Send to multiple recipients
- ✅ Attach a file to your message (max size configurable, default: 10MB)
- ✅ Message history with pagination
- ✅ RESTful API endpoint to send messages
- ✅ Telegram integration (real message sending)
- ✅ Laravel queues (jobs for parallel sending)
- ✅ Caching on message list
- ✅ Chart for statistics (messages per platform)
- ✅ SSR-friendly Vue 3 + ApexCharts
- ✅ Full Docker-based development setup

---

## 📦 Tech Stack

- Laravel 12
- Laravel Breeze (with Inertia.js)
- Vue 3 + TypeScript
- Tailwind CSS
- ApexCharts
- Docker + MySQL
- Laravel Queues + Cache
- Telegram Bot API (real messages)

---

## ✅ Why Breeze + Inertia?

Laravel Breeze with Inertia.js gives a great balance between a modern frontend (Vue 3) and tight Laravel integration. It allows us to build SPAs while using Laravel routing and controllers, and simplifies auth scaffolding.

---

## 📁 Project Structure

- `app/Services/Messaging/*`: One strategy per platform (Telegram, Discord, etc.)
- `app/Factories/MessageStrategyFactory.php`: Selects appropriate strategy
- `app/Jobs/SendMessageJob.php`: Queued sending of messages
- `app/Providers/MessagingServiceProvider.php`: Service binding
- `app/Http/Controllers/MessageController.php`: Sends and lists messages
- `app/Http/Controllers/StatsController.php`: Handles grouping queries for stats generation
- `routes/web.php` & `routes/api.php`: Define app and API routes

---

## 🔄 Backend Routes

| Method | Endpoint           | Description                                   |
|--------|--------------------|-----------------------------------------------|
| GET    | `/dashboard`       | View message statistics                       |
| GET    | `/messages/sent`   | View sent messages (web)                      |
| POST   | `/messages`        | Send a message (web)                          |
| POST   | `/api/messages`    | Send a message via API                        |

---

## 🌐 Frontend Views

| View Component    | Description                          |
|-------------------|--------------------------------------|
| `Dashboard.vue`   | Statistics dashboard                 |
| `Send.vue`        | Form to send messages                |
| `Sent.vue`        | Paginated sent messages table        |

---

## 🛠 Setup Instructions

### Prerequisites

- Docker & Docker Compose

### Clone & Run

```bash
git clone <your_repo_url>
cd messaging-app
cp .env.example .env
docker compose --profile dev up -d --build
```

### Run the Migrations

```bash
docker compose up -d migrations
```

### Start the Worker Queue

```bash
docker compose up -d queue
```

### Access the App

- Frontend: [http://localhost:8080](http://localhost:8080) (user: `test@example.com`, pass: `12345678`)

---

## 🤖 Telegram Bot Setup

This project includes real Telegram integration. To send real messages via Telegram:

1. Go to [@BotFather](https://t.me/BotFather) and create a bot.
2. Copy the generated **bot token** and set it in `.env`:

```env
TELEGRAM_BOT_TOKEN=your_token_here
```

3. **Important**: You must initiate a chat with the bot before it can send you a message and your username must be set.
4. You can now send messages to that chat ID.

---

## 🧪 Testing

Basic back-end tests are included for:

- Message request validation
- File upload validation
- API endpoint

Run tests:

```bash
php artisan test
```

---

## 📌 Design Patterns Used

- **Strategy Pattern**: Abstracts message sending per platform.
- **Factory Pattern**: Chooses appropriate strategy based on platform input.
- **Service Container**: Used to bind and resolve dependencies.
- **Jobs (Queueable)**: Handles async sending.
- **Cache**: Optimizes message listing queries.
- **Service Providers**: Encapsulate bootstrapping logic.

---

## 📌 Notes

- Telegram only sends messages to users who **have initiated a conversation with the bot**.
- File upload size is configurable via `.env` (`MAX_UPLOAD_SIZE_MB`).

---

## 📬 Contact

Made for the Laravel Messaging Challenge.

You’re free to expand it further using Blade, Livewire, Jetstream, or tools of your choice.

Happy coding! 🚀
