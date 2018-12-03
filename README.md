.checkout
=========

Project Features:
1. User registration
2. User authentication
3. Password and user name validation
4. Roles
5. Articles CRUD operations
6. Local image uploader
7. REST API services.

To run the project localy please do the following steps:
1. Download the project.
2. Start MySQL.
3. Run the CMD Prompt in the project directory.
4. Enter "composer install" command.
5. When the dependencies are installed enter "php bin/console doctrine:database:create" command.
6. Enter "php bin/console doctrine:schema:update --force" command
7. Enter "php bin/console server:run" command and open the shown address.

REST API Documentation:
Path: http://127.0.0.1:8000/api
Get all articles: http://127.0.0.1:8000/api/articles  -method "GET"
Get article by id: http://127.0.0.1:8000/api/articles/{id}  -method "GET"
Update article by id: http://127.0.0.1:8000/api/articles/{id}  -method "PUT" put body {"title":"","content":"","authorId":{id}}
Create article: http://127.0.0.1:8000/api/articles/create  -method "POST"  post body {"title":"","content":"","authorId":{id}}
Delete article: http://127.0.0.1:8000/api/articles/{id}  -method "DELETE"
