# Pizza Votes - Family Pizza Rating App

A web application for tracking and voting on pizza places as a family. Built with Laravel and Livewire, this app makes it easy to rate pizza restaurants across multiple dimensions and share voting sessions with family members.

**Built with ❤️ by Claude (Anthropic AI)**

## Tech Stack

- **Backend**: Laravel 12 with PHP
- **Frontend**: Livewire 3 (Volt) - Reactive components without writing JavaScript
- **Styling**: Tailwind CSS + Dark Mode support
- **Database**: MySQL (configured via `.env`)
- **Server**: Laravel Herd (local development)
- **JavaScript**: Alpine.js (included with Livewire) for client-side interactions

## Core Features

### 1. Restaurant Management
- Add and edit pizza restaurants with name, location, and notes
- View all restaurants with average scores across all dimensions
- Track visit history and individual ratings

### 2. Private Voting Sessions
- **Authenticated users only** can create voting sessions
- Each session is tied to a specific restaurant
- Generate unique shareable links for family members
- Voters don't need to log in - they just use the shared link
- Sessions can be opened or closed to control when voting is allowed
- Only active sessions show the copy-to-clipboard button

### 3. Multi-Dimensional Rating System
Each vote rates a restaurant on four key dimensions:
- **Taste** - How good the pizza tastes
- **Service** - Quality of service at the restaurant
- **Atmosphere** - Ambiance and environment
- **Value** - Price vs. quality ratio

Each dimension is rated on a 1-5 scale.

### 4. Aggregate Scoring
- **Overall Score**: Average of all dimensions for a restaurant
- **Per-Dimension Averages**: Breakdown by taste, service, atmosphere, value
- **Session vs. All Votes**: Compare how a specific session's votes stack up against all other votes for that restaurant

### 5. Home Page Analytics
Non-authenticated users can see:
- Top pizza place (by overall score)
- Total submissions across all sessions
- Average score by dimension across all restaurants
- Sign in/Sign up buttons to create their own sessions

## Architecture Decisions

### Why Voting Sessions?
Instead of allowing anonymous voting directly on restaurants, we implemented voting sessions because:
- **Family-focused**: Keeps votes organized by group/time
- **Control**: Organizers can close voting at any time
- **Privacy**: Sessions are private to their creator
- **Comparison**: Compare how your family voted vs. others

### Single-File Components (Volt)
All interactive UI uses Laravel Volt's functional component syntax:
- Cleaner, more maintainable code
- No separate JavaScript files needed
- State management is straightforward
- Blade + PHP in one file

### Database Design
```
restaurants: id, name, location, notes, user_id, created_at
users: standard Laravel auth users
voting_sessions: id, user_id, restaurant_id, name, slug, description, is_active, created_at
ratings: id, voting_session_id, restaurant_id, dimension, score, notes, visited_at, created_at
```

### Key Design Patterns

**Authentication**:
- Voting requires a session link (public) - no login needed
- Session creation requires authentication
- Restaurant management is per-user

**State Management**:
- Livewire handles reactive state (session toggles, form submissions)
- Alpine.js handles micro-interactions (copy button feedback, keyboard events)

**Styling**:
- Dark mode enabled by default for all users
- Mobile-first responsive design using Tailwind CSS
- Consistent spacing and component styling

## Routes Overview

```
/                              - Home page (public, shows aggregated stats)
/vote/{slug}/{session_slug}    - Vote on a session (public, no login required)
/dashboard                     - Main dashboard for authenticated users
/restaurants                   - View all restaurants (authenticated)
/restaurants/create            - Add a new restaurant (authenticated)
/restaurants/{id}              - View restaurant details & results (authenticated)
/restaurants/{id}/edit         - Edit restaurant (authenticated)
/sessions                      - Manage your voting sessions (authenticated)
/sessions/create               - Create a new voting session (authenticated)
```

## Key Implementation Highlights

### Copy-to-Clipboard
Uses Alpine.js with the Clipboard API. Displays "✓ Copied!" feedback for 2 seconds instead of browser alerts.

**Note**: Must use `herd secure` to enable HTTPS, which is required by modern browsers for clipboard access.

### Session Filtering
Restaurant detail pages show:
1. Results filtered by selected session
2. All other votes (excluding current session)
3. Side-by-side comparison with progress bars

### Recent Submissions
Grouped by voter, showing all votes from one person together, sorted by most recent.

### Dynamic Vote Counts
Calculates submissions as unique combinations of (voter_name + created_at timestamp) to properly count multi-part votes.

## Database Migrations

Key migrations include:
- `create_voting_sessions_table` - Session management
- `add_voting_session_id_to_ratings_table` - Link ratings to sessions
- `add_user_id_to_restaurants_table` - User ownership of restaurants

## Getting Started

```bash
# Install dependencies
composer install
npm install

# Run migrations
php artisan migrate

# Seed demo data (optional)
php artisan db:seed

# Start development server
npm run dev
./vendor/bin/sail up  # or use Herd
```

## Development Notes

- Keep components simple - this is a family tool, not enterprise software
- Mobile-responsive by default - designed for use at restaurants
- Use Alpine.js for micro-interactions, Livewire for complex state
- Follow Laravel conventions - no over-engineering
- All new features should include appropriate validation and authorization checks

## Future Enhancement Ideas

- **Participant Tracking**: Track who voted in each session
- **Session Analytics**: Detailed breakdown of session voting patterns
- **Photo Uploads**: Attach pizza photos to votes
- **Comparison Export**: Export session results as PDF
- **Real-time Updates**: Live vote counts as family members vote
- **Custom Ratings**: Let users create custom rating dimensions

---

**Built with Claude Code** - Anthropic's AI coding assistant
