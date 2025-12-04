# Inboxy - Email Campaign Management System

A self-hosted email campaign management system built with Laravel, Inertia.js, and Vue.js. Inspired by Sendy's simplicity and elegance.

## About Inboxy

Inboxy is a self-hosted email campaign management system that clones Sendy's functionality while maintaining its elegant simplicity. Built on modern technologies, it provides:

- **Self-Hosted**: Complete control over your data and infrastructure
- **Unlimited Subscribers**: No per-list charging or artificial limitations
- **Amazon SES Integration**: Ultra-low-cost email delivery
- **SMTP Support**: Works with any SMTP provider as fallback
- **Queue-Based Processing**: Efficient background email sending
- **Complete Tracking**: Opens, clicks, bounces, complaints, unsubscribes
- **Modern Stack**: Laravel 12, Inertia.js, Vue 3, TypeScript, Tailwind CSS

## System Requirements

- PHP 8.2+
- Composer
- Node.js 18+ & npm
- MySQL 8.0+ or SQLite
- (Optional) AWS Account for SES

## Installation

### 1. Install Dependencies

```bash
composer install
npm install --legacy-peer-deps
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure Database

Edit `.env` file:

**For MySQL:**
```env
DB_CONNECTION=mysql
DB_DATABASE=inboxy
DB_USERNAME=root
DB_PASSWORD=your_password
```

**For SQLite (default):**
```env
DB_CONNECTION=sqlite
```

### 4. Configure Email Sending

**For Amazon SES:**
```env
MAIL_DRIVER=ses
AWS_SES_KEY=your_aws_access_key
AWS_SES_SECRET=your_aws_secret_key
AWS_SES_REGION=us-east-1
```

**For SMTP:**
```env
MAIL_DRIVER=smtp
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USERNAME=your_username
SMTP_PASSWORD=your_password
SMTP_ENCRYPTION=tls
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Build Frontend Assets

```bash
npm run build
```

For development:
```bash
npm run dev
```

### 7. Start the Application

```bash
php artisan serve
```

Visit: `http://localhost:8000`

## Queue Worker Setup

For email campaign processing:

```bash
php artisan queue:work --queue=emails
```

### Cron Job Configuration

Add to crontab:

```bash
* * * * * cd /path/to/inboxy && php artisan schedule:run >> /dev/null 2>&1
```

## Database Schema

### Core Tables

- **lists** - Email lists with configuration and branding
- **subscribers** - Unlimited contact database with custom fields
- **campaigns** - Email campaigns with complete tracking metrics
- **campaign_subscriber** - Campaign delivery status and errors
- **email_tracking** - Detailed event tracking (opens, clicks, bounces, complaints)

## Architecture

### Backend (Laravel 12)
- Inertia.js for seamless SPA experience
- Service layer for business logic
- Queue jobs for background email sending
- AWS SDK for SES integration
- Database queue driver for reliability

### Frontend (Vue 3 + TypeScript)
- Composition API with `<script setup>`
- TypeScript for type safety
- Tailwind CSS for styling
- Responsive, mobile-first design
- Inertia.js forms with validation

## Configuration

Key configuration files:

- `.env` - Environment variables
- `config/inboxy.php` - Custom email campaign settings
- `config/mail.php` - Laravel mail configuration
- `.github/copilot-instructions.md` - Development guidelines

## Development

### Running Development Environment

```bash
php artisan serve
npm run dev
```

### Code Standards

Follow PSR-12 and project guidelines:

```bash
./vendor/bin/pint
```

### Contributing

1. Follow `.github/copilot-instructions.md`
2. Maintain Sendy-like simplicity
3. Implement complete features with all fields
4. Write clean, self-documenting code
5. Add tests for new features

## Roadmap

- [ ] Campaign templates library
- [ ] Advanced segmentation
- [ ] A/B testing
- [ ] Drag & drop email designer
- [ ] Detailed analytics dashboard
- [ ] REST API for integrations
- [ ] Multi-user support
- [ ] Webhooks for events
- [ ] Automation workflows

## License

[Your License Here]

## Credits

Built with Laravel, Inertia.js, and Vue.js
