# Changelog

All notable changes to the Anesi Kassa project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-01-01

### Added
- Complete Laravel 11 migration from legacy system
- Vue.js 3 SPA frontend with Composition API
- RESTful API with Laravel Sanctum authentication
- Role-based access control with Spatie Laravel Permission
- Payment management system with commission calculation
- Currency exchange functionality (USD/UZS)
- Credit management with repayment tracking
- Cash collection (Incash) operations
- Exchange rate management
- Comprehensive API documentation
- Deployment guides for CPanel hosting
- CI/CD pipeline with GitHub Actions
- Unit and Feature tests
- Docker containerization support
- API versioning structure
- Security headers middleware
- Centralized API response handling
- Health check endpoint
- Structured logging channels

### Changed
- Migrated from procedural PHP to Laravel framework
- Updated database schema with proper relationships
- Improved security with modern authentication
- Enhanced UI/UX with modern Vue.js components
- Optimized database queries with Eloquent ORM

### Security
- Implemented Laravel Sanctum for API authentication
- Added CORS protection
- Input validation with Form Requests
- SQL injection prevention through Eloquent
- XSS protection with Vue.js
- CSRF protection for web routes
- Security headers middleware

### Documentation
- Complete API documentation
- Deployment guides (CPanel, general)
- Migration guide from legacy system
- Frontend development guide
- Quick start guide
- Production readiness checklist

## [1.0.0] - 2025-12-01

### Initial Release
- Legacy PHP application
- Basic payment processing
- Manual currency exchange
- Simple credit tracking
