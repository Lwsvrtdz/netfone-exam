# Laravel - GraphQL

This application is built on top of Laravel 10.x and utilizes the GraphQL API, powered by [Lighthouse](https://lighthouse-php.com/).

## Technical Exam for Netfone - Backend Software Engineer

As part of the technical exam for Netfone, I have created a GraphQL API endpoint accessible through the `/graphql` endpoint. 
## Laravel Sanctum is used for authentication on each endpoint.

## Features

- **Login**
- **Logout**
- **Create Contact**
- **View Contact**
- **List Contact**
- **Update Contact**
- **Delete Contact**

## Unit Testing

I have also written unit tests for this application using PHPUnit. Before running the tests, make sure to properly set up the `phpunit.xml` or `.env.testing` file. SQLite is used as the testing database.

To run the tests, execute the following command:

```bash
php artisan test
