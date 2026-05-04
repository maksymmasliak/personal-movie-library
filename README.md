# Personal Movie Library

A PHP + PDO portfolio application for managing a personal movie collection. This project was built to practice core backend skills, secure data handling, and raw database interactions without relying on external PHP frameworks.

## Stack
- PHP (Core)
- MySQL + PDO
- Vanilla JS
- Choices.js (for genre multiselect)

## Features
- CRUD operations: Add, edit, delete movies, and write reviews.
- Soft Deletes & Trash Bin: Movies are moved to a Trash section before permanent deletion.
- Poster Uploads: MIME-type validation and unique file name generation.
- Filter by genre, rating, year.
- Custom pagination logic.

## Security
The basics are covered:
- Prepared Statements (PDO) to prevent SQL injection.
- htmlspecialchars() for XSS prevention.
- CSRF token validation on forms.
- File upload validation.

## Setup
1. Clone the repository.
2. Create an empty database and import `database/database.sql`.
3. Rename `.env.example` to `.env` and add your local database credentials.
4. Make sure the `uploads/` directory is writable.
5. Run on your local server (e.g., Laragon or XAMPP).

## Project Structure
├── database/     # SQL dump with seed data
├── config/       # Genres dictionary
├── helpers/      # Validation, upload, and core logic
├── js/           # Client-side validation & Choices.js init
├── partials/     # Header, footer, select components
├── style/        # CSS
├── uploads/      # Poster images
└── views/        # HTML templates