# Pizza Family Ratings App

A Laravel application for tracking and voting on pizza places as a family.

## Tech Stack

- Laravel 12
- Livewire 3 (Volt)
- MySQL
- Tailwind CSS

## Project Principles

- **Single-file components**: Use Volt for all interactive UI
- **Convention over configuration**: Stick to Laravel defaults
- **Simple data model**: Restaurants, Ratings, Dimensions
- **Mobile-first**: Designed for use at restaurants
- **No over-engineering**: Build exactly what's needed

## Core Features

1. **Restaurant Management**
    - Add/edit pizza places (name, location, notes)
    - View all restaurants with average scores

2**Voting System**
    - Rate on multiple dimensions (taste, service, atmosphere, value)
    - 1-5 star rating per dimension
    - Add visit notes
    - Name requried for voting on unauthed page
    - Ability to open and closing voting form and create a custom url based to vote from
    - Track date of visit

3**Scoring**
    - Calculate average per dimension
    - Overall restaurant score
    - Visit history per restaurant

## Pages
- Unauth welcome page that shows results (with charts) and offers a log in page
- Authenticated dashboard for managing restaurants and viewing detailed scores (with any needed extra edit pages)
- Restaurant detail page with voting form and visit history

## Database Schema
```
restaurants: id, name, location, created_at
ratings: id, restaurant_id, user_id, dimension, score, notes, visited_at
dimensions: taste, service, atmosphere, value
```

## File Structure Priority

Focus development on:
- `resources/views/livewire/` - Volt components
- `app/Models/` - Restaurant, Rating
- `database/migrations/` - Schema
- `routes/web.php` - Simple routing

## Development Notes

- Keep it simple - this is a family tool, not a SaaS
- Mobile-responsive by default
- Use Alpine.js (included with Livewire) for micro-interactions
- SQLite for portability

## Commands Reference
```bash
php artisan make:volt restaurant-list
php artisan make:volt voting-form
php artisan make:model Restaurant -m
```

---

When building features, prefer:
- Functional Volt components over class-based
- Blade components for repeated UI elements
- Simple Eloquent relationships
- Minimal JavaScript
