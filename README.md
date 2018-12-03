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
1. Get all articles: http://127.0.0.1:8000/api/articles  -method "GET"
2. Get article by id: http://127.0.0.1:8000/api/articles/{id}  -method "GET"
3. Update article by id: http://127.0.0.1:8000/api/articles/{id}  -method "PUT" put body {"title":"","content":"","authorId":{id}}
4. Create article: http://127.0.0.1:8000/api/articles/create  -method "POST"  post body {"title":"","content":"","authorId":{id}}
5. Delete article: http://127.0.0.1:8000/api/articles/{id}  -method "DELETE"
