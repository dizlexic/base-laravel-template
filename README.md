# Laravel App Starter

## Overview

This project is a **Laravel**-based application designed to deliver a robust, scalable, and modular architecture for web
development. Leveraging Laravel’s expressive syntax and powerful features, this project integrates various models,
controllers, middlewares, and requests to provide a cohesive framework for handling user authentication, morph
relationships, resource management, background processing, and more.

### Core Features:

- **User Authentication System** utilizing Laravel's built-in authentication capabilities, extended for registration,
  login, email verification, and password resets.
- **Resource Management** with controllers and request validation for `Posts`, `Comments`, `Uploads`, `Tags`, and other
  important entities.
- **Role and Permission Handling** via `spatie/permission` package.
- **Morph Relationships** enable dynamic relationships for features like tagging or commenting.
- **Inertia.js Integration** for building modern SPAs (Single Page Applications) with a Vue.js frontend.
- Modular and extensible code structure with simple domain-specific features, such as:
    - Tagging System
    - Account/Profile Management
    - File Upload functionality
- **Job Queue support** with database-based queue driver (MariaDB).
- Scalability for enhancing business logic with well-designed Models, Factories, and Form Requests.

---

## Project Structure

This project follows Laravel’s best practices for organizing files.

### Models:

1. **Core Models:**
    - `User`: Defines users and includes role-based permissions, email verification, and relationships with the
      verifier.
    - `Post`: Adds functionality for user-created posts, comments, and tags.
    - `Tag`: Handles relationships with tagged entities (e.g., posts, users, etc.).
    - `Comment`: A model representing user comments with relationships for morphing to different entities.
    - `Upload`: Defines files uploaded by users and tracks the verifier.
    - `Role` and `Permission`: Extends Spatie’s implementation for role-based access control.

2. **Morphs:**
    - `Taggable`, `Commentable`, and `Attachable`: Models facilitating the implementation of polymorphic relationships.

---

### Controllers:

This project uses both resource and specific controllers for application logic:

1. **Authentication:**
    - `RegisteredUserController`, `AuthenticatedSessionController`, `PasswordController`, `VerifyEmailController`, and
      others manage registration, login, password management, and email verification workflows.
2. **Resource Controllers:**
    - Controllers like `CommentController`, `TagController`, `UploadController`, and `AnswerController` handle CRUD
      operations for specific resources.
3. **Custom Features:**
    - `ProfileController`: Handles user-specific tasks like updating profile data or deleting accounts.
    - `AssignRoles`: A console command for assigning roles dynamically via Laravel Prompts.

---

### Middlewares:

1. **Authentication Middleware:** Ensures proper user access to restricted areas.
2. **HandleInertiaRequests:** Mediates requests between the Laravel backend and Vue.js SPA views with shared properties
   like authentication state and routing data.

---

### Requests:

FormRequest validation plays a critical role in request validation:

- `LoginRequest`, `ProfileUpdateRequest`, `StorePostRequest`, etc. validate incoming data for user authentication and
  resource-specific logic, preventing malicious inputs.
- Modular FormRequest design ensures reusability and central validation.

---

### Vue.js and Inertia.js:

The project integrates frontend SPAs using **Inertia.js** with Vue v3. This provides a modern, seamless user experience
powered by reactive components and server-side rendering.

---

## Technology Stack:

### Backend:

- **Laravel v12.2.0**: The primary framework for backend processing and API handling.
- **MariaDB**: Used for relational database management, including queues and migrations.
- **PHP 8.2**: The server-side programming language.

### Frontend:

- **Vue.js 3.4.0 with Vuetify (UI Framework)** for a material design-inspired interface.
- **Inertia.js** for bridging Laravel resources with Vue components.

### Development Utilities:

- **Queue management:** Configured queue connection using the database driver.
- **Spatie permission package** to support role-and-permission-based authorization.
- **PHPUnit**: Unit testing framework.
- **Linting:** ESLint and Prettier for code quality.

---

## Setup Instructions:

1. Clone the repository:

```shell script
git clone <repository-url>
   cd <repository-folder>
```

2. Install PHP dependencies using **Composer**:

```shell script
composer install
```

3. Install JavaScript dependencies using **npm**:

```shell script
npm install
```

4. Configure the `.env` file:
    - Copy the `.env.example` file:

```shell script
cp .env.example .env
```

- Update database, mail, and queue configurations as per environment.

5. Generate the project key:

```shell script
php artisan key:generate
```

6. Migrate the database:

```shell script
php artisan migrate
```

7. (Optional) Install roles and permissions:

```shell script
php artisan assign:roles
```

8. Start the development server:

```shell script
php artisan serve
```

9. Run the queue worker in a separate terminal:

```shell script
php artisan queue:work
```

10. Compile frontend assets:

```shell script
npm run dev
```

---

## Features in Detail:

### Authentication:

- Mechanisms for account creation, login, email verification, password confirmation, and password reset are included.

### Roles and Permissions:

- Fully integrated with **Spatie Laravel Permission**, roles can be dynamically assigned, validated, and have
  fine-grained access control for resources.

### Morph Relationships:

- **Tagging:** Attach tags to multiple models, such as posts or candidates.
- **Commenting:** Comments can be associated with various entities (e.g., posts) via `Commentable`.

### Uploads:

- Users can upload files, and administrators can verify uploads.

---

## Contributing

If you want to contribute to this project:

- Fork the repository and create a new branch.
- Submit contributions following the Laravel style guide.
- Make pull requests with clear commit messages of changes.

---

## Testing

Run the PHPUnit test suite:

```shell script
php artisan test
```

---

## License

This project is open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).

2. **Morphs:**
    - `Taggable`, `Commentable`, and `Attachable`: Models facilitating the implementation of polymorphic relationships.

---

### Controllers:

This project uses both resource and specific controllers for application logic:

1. **Authentication:**
    - `RegisteredUserController`, `AuthenticatedSessionController`, `PasswordController`, `VerifyEmailController`, and
      others manage registration, login, password management, and email verification workflows.
2. **Resource Controllers:**
    - Controllers like `CommentController`, `TagController`, `UploadController`, and `AnswerController` handle CRUD
      operations for specific resources.
3. **Custom Features:**
    - `ProfileController`: Handles user-specific tasks like updating profile data or deleting accounts.
    - `AssignRoles`: A console command for assigning roles dynamically via Laravel Prompts.

---

### Middlewares:

1. **Authentication Middleware:** Ensures proper user access to restricted areas.
2. **HandleInertiaRequests:** Mediates requests between the Laravel backend and Vue.js SPA views with shared properties
   like authentication state and routing data.

---

### Requests:

FormRequest validation plays a critical role in request validation:

- `LoginRequest`, `ProfileUpdateRequest`, `StorePostRequest`, etc. validate incoming data for user authentication and
  resource-specific logic, preventing malicious inputs.
- Modular FormRequest design ensures reusability and central validation.

---

### Vue.js and Inertia.js:

The project integrates frontend SPAs using **Inertia.js** with Vue v3. This provides a modern, seamless user experience
powered by reactive components and server-side rendering.

---

## Technology Stack:

### Backend:

- **Laravel v12.2.0**: The primary framework for backend processing and API handling.
- **MariaDB**: Used for relational database management, including queues and migrations.
- **PHP 8.2**: The server-side programming language.

### Frontend:

- **Vue.js 3.4.0 with Vuetify (UI Framework)** for a material design-inspired interface.
- **Inertia.js** for bridging Laravel resources with Vue components.

### Development Utilities:

- **Queue management:** Configured queue connection using the database driver.
- **Spatie permission package** to support role-and-permission-based authorization.
- **PHPUnit**: Unit testing framework.
- **Linting:** ESLint and Prettier for code quality.

---

## Setup Instructions:

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd <repository-folder>
   ```

2. Install PHP dependencies using **Composer**:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies using **npm**:
   ```bash
   npm install
   ```

4. Configure the `.env` file:
    - Copy the `.env.example` file:
      ```bash
      cp .env.example .env
      ```
    - Update database, mail, and queue configurations as per environment.

5. Generate the project key:
   ```bash
   php artisan key:generate
   ```

6. Migrate the database:
   ```bash
   php artisan migrate
   ```

7. (Optional) Install roles and permissions:
   ```bash
   php artisan assign:roles
   ```

8. Start the development server:
   ```bash
   php artisan serve
   ```

9. Run the queue worker in a separate terminal:
   ```bash
   php artisan queue:work
   ```

10. Compile frontend assets:
    ```bash
    npm run dev
    ```

---

## Features in Detail:

### Authentication:

- Mechanisms for account creation, login, email verification, password confirmation, and password reset are included.

### Roles and Permissions:

- Fully integrated with **Spatie Laravel Permission**, roles can be dynamically assigned, validated, and have
  fine-grained access control for resources.

### Morph Relationships:

- **Tagging:** Attach tags to multiple models, such as posts or candidates.
- **Commenting:** Comments can be associated with various entities (e.g., posts) via `Commentable`.

### Uploads:

- Users can upload files, and administrators can verify uploads.

---

## Contributing

If you want to contribute to this project:

- Fork the repository and create a new branch.
- Submit contributions following the Laravel style guide.
- Make pull requests with clear commit messages of changes.

---

## Testing

Run the PHPUnit test suite:

```bash
php artisan test
```

---

## License

This project is open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).


