# Laravel Authentication API

A robust authentication microservice built with Laravel, providing JWT-based authentication for the microservices ecosystem.

## 🚀 Features

- **JWT Authentication** - Secure token-based authentication
- **User Registration & Login** - Complete user management
- **Password Reset** - Secure password recovery
- **Profile Management** - User profile CRUD operations
- **Health Monitoring** - Built-in health check endpoints
- **API Documentation** - Comprehensive API docs

## 📋 Requirements

- PHP 8.1+
- Composer
- MySQL 8.0+
- Redis (for caching and sessions)

## 🛠️ Local Development

```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Start development server
php artisan serve
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test --testsuite=Feature
```

## 📡 API Endpoints

### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `POST /api/auth/refresh` - Refresh JWT token
- `GET /api/auth/profile` - Get user profile

### Health & Monitoring
- `GET /api/healthz` - Health check endpoint
- `GET /api/status` - Service status information

## 🔧 Configuration

Key environment variables:
- `DB_HOST` - Database host
- `DB_DATABASE` - Database name
- `DB_USERNAME` - Database username
- `DB_PASSWORD` - Database password
- `REDIS_HOST` - Redis host
- `JWT_SECRET` - JWT signing secret

## 🚀 Deployment

This service is automatically deployed via the CI/CD infrastructure repository. The deployment pipeline:

1. Pulls source code from this repository
2. Runs tests and quality checks
3. Builds Docker image
4. Deploys to Kubernetes cluster
5. Runs health checks

## 📊 Monitoring

- Health endpoint: `/api/healthz`
- Metrics endpoint: `/api/metrics` (if enabled)
- Logs: Available via Kubernetes logging

## 🤝 Contributing

1. Fork the repository
2. Create feature branch
3. Make changes with tests
4. Submit pull request

## 📝 License

MIT License - see LICENSE file for details. 