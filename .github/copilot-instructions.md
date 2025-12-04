# GitHub Copilot Instructions for Inboxy Email Campaign Management System

## Project Overview
This is a Laravel + Inertia.js + Vue.js based email campaign management system inspired by Sendy. The goal is to clone Sendy's functionality while maintaining its simplicity and elegance. All code should follow Laravel/PHP best practices and maintain consistent, professional design patterns throughout.

### System Architecture
- **Self-Hosted**: Designed to run on user's own infrastructure (Laravel + MySQL)
- **Unlimited Subscribers**: No per-list charging or artificial limits on subscriber counts
- **Amazon SES Integration**: Primary email sending through AWS SES for ultra-low-cost delivery
- **SMTP Support**: Fallback option to send via any SMTP provider if not using SES
- **Queue-Based Processing**: Use Laravel's queue system with cron job scheduling for background email sending
- **Scalable Design**: Handle large subscriber lists and high-volume campaigns efficiently

### Key Design Philosophy
- **Simplicity First**: Keep the UI/UX simple and intuitive like Sendy
- **Elegant Solutions**: Prefer clean, straightforward implementations over complex ones
- **Complete Features**: Every feature must be fully implemented with all necessary fields and proper data structures
- **No Half-Measures**: All database tables, forms, and API endpoints must include complete field sets
- **Cost-Effective**: Optimize for minimal server resource usage and low email delivery costs

---

## Laravel & PHP Coding Standards

### General PHP Standards
- Follow PSR-12 coding style standards
- Use strict typing: declare(strict_types=1) in all PHP files
- Type hint all method parameters and return types
- Use meaningful variable and method names in camelCase
- Keep methods focused and single-purpose (max 20-30 lines)
- Always use dependency injection over facades where possible

### Laravel Best Practices
- Use Laravel's built-in features (Eloquent, Collections, Validation, etc.)
- Follow RESTful conventions for routes and controllers
- Use Form Requests for validation logic
- Implement Repository pattern for complex data access
- Use Laravel Events and Listeners for decoupled logic
- Leverage Laravel Jobs for queue-able tasks (email sending, imports, etc.)
- Use Laravel's queue system with database driver for reliable job processing
- Implement Laravel Scheduler for cron-based queue workers
- Use Laravel's notification system for email notifications
- Implement Laravel Policies for authorization
- Use Inertia.js for seamless server-side routing with client-side rendering
- Return Inertia responses from controllers using `Inertia::render()`
- Use route model binding with Inertia
- Store configuration in .env and config files, never hardcode
- **Amazon SES Integration**: Use AWS SDK for PHP to send emails via SES
- **SMTP Fallback**: Support standard SMTP configuration as alternative to SES

### Code Organization
```
app/
├── Http/
│   ├── Controllers/     # Thin controllers, delegate to services
│   ├── Requests/        # Form validation requests
│   ├── Resources/       # API resources for transforming models
│   └── Middleware/
├── Models/              # Eloquent models with relationships
├── Services/            # Business logic layer
├── Repositories/        # Data access layer (if needed)
├── Jobs/                # Queue-able jobs
├── Events/              # Application events
├── Listeners/           # Event listeners
└── Policies/            # Authorization policies
```

### Database & Models
- **Complete Schema Design**: Every table must include ALL necessary fields from the start
- Include proper timestamps, status fields, and metadata columns
- Use migrations for all database changes
- Add indexes for foreign keys and frequently queried columns
- Use Eloquent relationships instead of manual joins
- Implement soft deletes where appropriate
- Use database transactions for multi-step operations
- Add model factories for testing
- Use seeders for initial/test data
- **Never skip fields**: If a feature requires 10 fields, implement all 10 - no placeholders
- Use database queue driver for Laravel's queue system (store jobs in database)
- Design subscriber tables to handle unlimited contacts efficiently

### Example Controller Pattern (Inertia)
```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampaignRequest;
use App\Services\CampaignService;
use Inertia\Inertia;
use Inertia\Response;

class CampaignController extends Controller
{
    public function __construct(
        private readonly CampaignService $campaignService
    ) {}

    public function index(): Response
    {
        return Inertia::render('Campaign/Index', [
            'campaigns' => $this->campaignService->getPaginated(),
        ]);
    }

    public function store(StoreCampaignRequest $request)
    {
        $campaign = $this->campaignService->create($request->validated());
        
        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign created successfully.');
    }
}
```

---

## Inertia.js Integration

### Inertia Best Practices
- Use `Inertia::render()` in controllers to return Inertia responses
- Pass data as props to Vue components via the second parameter
- Use `Inertia::share()` in middleware for globally shared data
- Leverage Inertia's form helper for form submissions with validation
- Use `router.visit()` for programmatic navigation
- Implement `preserveState` and `preserveScroll` for better UX
- Use `only` and `except` options to optimize data loading
- Handle redirects and flash messages through Inertia

### Inertia Response Pattern
```php
// Controller
return Inertia::render('Campaign/Edit', [
    'campaign' => $campaign,
    'lists' => $lists,
]);
```

### Inertia Form Handling
```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  subject: '',
  from_email: '',
});

const submit = () => {
  form.post(route('campaigns.store'), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  });
};
</script>
```

---

## Vue.js Frontend Standards

### Component Structure
- Use Composition API with `<script setup>` syntax
- Keep components small and focused (max 150-200 lines)
- Use TypeScript for type safety
- Follow single-responsibility principle
- Extract reusable logic into composables
- Use Inertia props (`defineProps`) for data from backend
- Use `Link` component for navigation instead of `<a>` tags
- Use `router` for programmatic navigation
- Use `useForm` for form handling with built-in validation

### Component Organization
```
resources/js/
├── Pages/               # Inertia page components (route views)
│   ├── Campaign/        # Campaign pages (Index, Create, Edit, Show)
│   ├── Contact/         # Contact management pages
│   ├── List/            # List management pages
│   └── Dashboard/       # Dashboard pages
├── Components/
│   ├── Layout/          # Layout components (AppLayout, AuthLayout)
│   ├── Common/          # Reusable UI components
│   └── Form/            # Form-related components
├── Composables/         # Reusable composition functions
├── Types/               # TypeScript type definitions
└── Utils/               # Utility functions
```

### Example Page Component Pattern (Inertia)
```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import type { Campaign } from '@/Types';

interface Props {
  campaign: Campaign;
  lists: Array<{ id: number; name: string }>;
}

const props = defineProps<Props>();

const form = useForm({
  name: props.campaign.name,
  subject: props.campaign.subject,
  from_email: props.campaign.from_email,
  list_id: props.campaign.list_id,
});

const submit = () => {
  form.put(route('campaigns.update', props.campaign.id), {
    preserveScroll: true,
  });
};
</script>

<template>
  <AppLayout title="Edit Campaign">
    <form @submit.prevent="submit">
      <!-- Form fields -->
      <button type="submit" :disabled="form.processing">
        Update Campaign
      </button>
    </form>
  </AppLayout>
</template>
```

### State Management
- Inertia handles most state through props from the server
- Use composables for shared reactive logic
- Keep component-level state in components with `ref()` and `reactive()`
- Use Inertia's shared data for globally needed data (auth user, flash messages)
- Avoid complex client-side state management - leverage Inertia's server-driven approach
- Use Pinia only for truly global client-side state (rare in Inertia apps)

---

## Design & UI/UX Guidelines

### Design Principles
- **Sendy-Inspired Simplicity**: Mirror Sendy's clean, minimal, and functional interface design
- **Consistency**: Use the same design patterns, colors, spacing, and components throughout
- **No Clutter**: Clean, uncluttered interfaces with clear visual hierarchy - avoid unnecessary elements
- **Functional First**: Prioritize functionality and usability over decorative elements
- **Professional**: Business-appropriate styling that feels lightweight and fast
- **Accessibility**: WCAG 2.1 AA compliant (semantic HTML, ARIA labels, keyboard navigation)

### UI Component Library
- Use a consistent component library (Headless UI, Radix Vue, or similar)
- Create a design system with reusable components:
  - Buttons (primary, secondary, danger, ghost)
  - Forms (inputs, selects, checkboxes, radio buttons)
  - Cards and containers
  - Tables with sorting/filtering
  - Modals and dialogs
  - Alerts and notifications
  - Loading states and skeletons

### Color Scheme
Define a consistent color palette:
```css
:root {
  /* Primary colors */
  --color-primary-50: #f0f9ff;
  --color-primary-500: #0ea5e9;
  --color-primary-600: #0284c7;
  --color-primary-700: #0369a1;
  
  /* Neutral colors */
  --color-gray-50: #f9fafb;
  --color-gray-100: #f3f4f6;
  --color-gray-500: #6b7280;
  --color-gray-900: #111827;
  
  /* Semantic colors */
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-error: #ef4444;
  --color-info: #3b82f6;
}
```

### Typography
- Use a professional font stack (Inter, System UI, or similar)
- Maintain consistent font sizes and weights
- Use clear text hierarchy (h1-h6)
- Ensure sufficient line height (1.5-1.7)
- Maintain readable text contrast (minimum 4.5:1)

### Spacing & Layout
- Use consistent spacing scale (4px, 8px, 12px, 16px, 24px, 32px, 48px, 64px)
- Implement responsive design with mobile-first approach
- Use CSS Grid or Flexbox for layouts
- Maintain consistent padding and margins
- Use max-width for content containers (1280px-1440px)

### Form Design
- **Complete Forms**: Every form must include ALL necessary fields for the feature
- Group related fields together
- Provide clear labels and placeholder text
- Show inline validation feedback
- Use appropriate input types
- Include helpful error messages
- Show loading states during submission
- Confirm destructive actions
- Match Sendy's straightforward form patterns - no over-engineering

### Dashboard & Tables
- Use data tables with sorting, filtering, and pagination
- Show loading skeletons while fetching data
- Display empty states with helpful messages
- Use badges/tags for status indicators
- Provide bulk actions where appropriate
- Implement responsive tables (stack on mobile)

### UX Best Practices
- Provide immediate feedback for user actions
- Use optimistic UI updates where appropriate
- Implement proper loading states
- Show success/error notifications
- Include confirmation dialogs for destructive actions
- Support keyboard shortcuts for power users
- Implement infinite scroll or pagination appropriately
- Cache API responses to reduce loading times

---

## Email Campaign Specific Guidelines

### Campaign Builder
- Use drag-and-drop interface for email template builder
- Provide real-time preview of emails
- Support responsive email templates
- Include template library with pre-built designs
- Allow custom HTML/CSS for advanced users

### Contact Management
- Implement efficient list segmentation
- Support CSV import/export
- Show subscription status clearly
- Handle unsubscribes automatically
- Maintain GDPR compliance features

### Analytics & Reporting
- Display metrics clearly (open rate, click rate, bounce rate)
- Use charts and visualizations (Chart.js, ApexCharts)
- Support date range filtering
- Export reports to CSV/PDF
- Show real-time campaign performance

### API Integration
- Primary interface is through Inertia.js (not traditional API)
- Use Inertia forms for data submission
- Use Laravel Sanctum only if building separate API endpoints
- For AJAX requests within Inertia pages, use axios with CSRF token
- Implement rate limiting on routes
- Use proper HTTP status codes
- Leverage Inertia's built-in error handling

---

## Testing Requirements

### Backend Testing
```php
// Feature tests for endpoints
// Unit tests for services and models
// Use factories and seeders for test data
```

### Frontend Testing
```typescript
// Unit tests for composables and utilities
// Component tests with Vue Test Utils (including Inertia mocks)
// E2E tests for critical user flows (Playwright/Cypress)
// Test Inertia page components with proper prop mocking
```

---

## Code Quality

### General Rules
- Write self-documenting code
- Add comments only for complex logic
- Keep code DRY (Don't Repeat Yourself)
- Use early returns to reduce nesting
- Handle errors gracefully
- Log errors appropriately
- Never expose sensitive data in responses

### Performance
- Eager load relationships to avoid N+1 queries
- Use Laravel query optimization (select, chunk)
- Implement caching for expensive operations
- Lazy load Vue components where appropriate
- Optimize images and assets
- Use database indexes effectively

---

## Security

- Validate all user input
- Use Laravel's CSRF protection
- Implement proper authentication and authorization
- Sanitize data before displaying
- Use parameterized queries (Eloquent does this)
- Store passwords using bcrypt
- Implement rate limiting on sensitive endpoints
- Keep dependencies updated

---

## Git Commit Messages

Follow conventional commits:
- `feat:` New feature
- `fix:` Bug fix
- `refactor:` Code refactoring
- `style:` Formatting changes
- `test:` Adding tests
- `docs:` Documentation
- `chore:` Maintenance tasks

Example: `feat: add campaign scheduling functionality`

---

## Additional Notes

When generating code:
1. Always follow these standards without exception
2. Maintain consistency with existing codebase patterns
3. Prioritize readability and maintainability
4. Consider performance implications
5. Think about edge cases and error handling
6. Keep the user experience smooth and professional
7. **Complete Implementation**: Always implement features with ALL required fields and proper data structures
8. **Sendy-like Simplicity**: Keep interfaces clean and functional like Sendy - avoid unnecessary complexity
