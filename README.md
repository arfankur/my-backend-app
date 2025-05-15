# Laravel REST API with Authentication

A robust REST API built with Laravel featuring authentication, item management, and persistent login functionality.

## Features

- 🔐 Authentication with Laravel Sanctum
- 📝 Item CRUD operations
- 🔍 Search and Pagination
- 🎯 Authorization with Policies
- 🍪 Persistent Login with Remember Me
- ✅ Comprehensive Test Coverage

## Requirements

- PHP >= 8.1
- Composer
- MySQL/PostgreSQL
- Node.js & NPM (for frontend)

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd <project-directory>
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run migrations:
```bash
php artisan migrate
```

7. Start the development server:
```bash
php artisan serve
```

## API Endpoints

### Authentication

#### Register
```http
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password",
    "remember_me": true
}
```

#### Check Authentication Status
```http
GET /api/check-auth
Authorization: Bearer <token>
```

#### Logout
```http
POST /api/logout
Authorization: Bearer <token>
```

### Items

#### List Items
```http
GET /api/items
Authorization: Bearer <token>

# Optional Query Parameters:
?page=1
?per_page=15
?search=laptop
?sort_by=price
?sort_direction=asc
```

#### Create Item
```http
POST /api/items
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "Laptop",
    "description": "High-end gaming laptop",
    "price": 1500.00,
    "stock": 10
}
```

#### Get Item
```http
GET /api/items/{id}
Authorization: Bearer <token>
```

#### Update Item
```http
PUT /api/items/{id}
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "Updated Laptop",
    "description": "Updated description",
    "price": 1600.00,
    "stock": 15
}
```

#### Delete Item
```http
DELETE /api/items/{id}
Authorization: Bearer <token>
```

## Features in Detail

### Authentication
- Token-based authentication using Laravel Sanctum
- Persistent login with "Remember Me" functionality
- Token expiration management
- Secure cookie handling

### Item Management
- Full CRUD operations
- User-specific items
- Authorization using Policies
- Input validation

### Search & Pagination
- Search by name and description
- Pagination with customizable page size
- Sorting by any field
- Direction control (asc/desc)

## Testing

Run the test suite:
```bash
php artisan test
```

## Security

- All endpoints are protected with authentication
- CSRF protection enabled
- Input validation on all requests
- Secure password hashing
- Token-based authentication
- Authorization policies for resource access

## Error Handling

The API returns appropriate HTTP status codes and error messages:

- 200: Success
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 500: Server Error

## Frontend Integration

Example of frontend integration using Axios:

```javascript
import axios from 'axios';

// Configure axios
axios.defaults.baseURL = 'http://your-api-url';
axios.defaults.withCredentials = true;

// Login example
const login = async (credentials) => {
    try {
        const response = await axios.post('/api/login', {
            ...credentials,
            remember_me: true
        });
        return response.data;
    } catch (error) {
        throw error.response.data;
    }
};

// Get items with search and pagination
const getItems = async (params) => {
    try {
        const response = await axios.get('/api/items', { params });
        return response.data;
    } catch (error) {
        throw error.response.data;
    }
};
```

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
