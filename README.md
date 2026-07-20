# 🎧 Customer Service Dashboard

Isang Laravel-based na Customer Service Management System — dashboard para sa mga support agent na mag-manage ng tickets, self-service articles, SLA tracking, at communication logs. Ginawa bilang class project, kung saan pinagsama ang 5 modules mula sa magkakaibang miyembro ng grupo.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

---

## ✨ Features

- **📊 Dashboard** — Overview ng mga key metrics at activity logs
- **🛠️ Ticket Management** — Paggawa, pag-assign, at pagsubaybay ng support tickets
- **📚 Self-Service Portal** — Knowledge base / articles para sa mga customer
- **⏱️ SLA Tracking** — Pagmo-monitor ng service level agreements at compliance
- **💬 Communication History** — Log ng mga naging usapan/interaction sa bawat ticket

## 🧱 Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel (PHP) |
| Frontend | Blade Templating + Tailwind CSS |
| Database | MySQL (Laragon local / Aiven production) |
| Design Reference | Figma |

## 📁 Project Structure

```
app/
 ├── Http/Controllers/   → ActivityLog, Agent, Article, BillingItem, Dashboard, Sla, Ticket
 ├── Models/             → ActivityLog, Agent, Article, BillingItem, CalendarSetting, SlaRule, Ticket, User
database/
 ├── migrations/
 ├── seeders/
resources/
 └── views/
      ├── dashboard.blade.php
      ├── Ticketmanagement.blade.php
      ├── selfserviceportal.blade.php
      ├── SLA.blade.php
      ├── agents.blade.php
      ├── logs.blade.php
      └── layouts/ & partials/
```

## 🚀 Installation

```bash
# I-clone ang repo
git clone https://github.com/KyleDom09/Customer-Service.git
cd Customer-Service

# I-install ang dependencies
composer install

# I-setup ang environment
cp .env.example .env
php artisan key:generate

# I-configure ang database sa .env (MySQL)
DB_DATABASE=customer_service

# I-migrate at i-seed ang database
php artisan migrate --seed

# Patakbuhin ang dev server
php artisan serve
```

## ✅ Module Status

| Module | Status |
|---|:---:|
| Dashboard | ✅ Done |
| Self-Service Portal | ✅ Done |
| Ticket Management | ✅ Done |
| SLA Tracking | ✅ Done |
| Communication History | ✅ Done |

## 👥 Contributors

Class project na ginawa ng 4 na contributors, sinasama ang kani-kanilang modules sa iisang base repository.

## 📄 License

Ginawa para sa academic purposes lamang.
