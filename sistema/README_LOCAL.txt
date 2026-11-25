EJECUCIÓN LOCAL CON LARAGON
1. Copia esta carpeta sistema dentro de C:\laragon\www\farmacia.
2. Inicia Laragon y crea una base MySQL llamada farmacia.
3. En la terminal, dentro de C:\laragon\www\farmacia, ejecuta:
   copy .env.example .env
   composer install
   php artisan key:generate
4. Verifica el .env: DB_CONNECTION=mysql, DB_HOST=127.0.0.1, DB_PORT=3306,
   DB_DATABASE=farmacia, DB_USERNAME=root, DB_PASSWORD= (vacío si corresponde).
5. Ejecuta: php artisan optimize:clear
             php artisan migrate
             php artisan serve
6. Abre http://127.0.0.1:8000.
CRUD: medicamentos, categorías y clientes (listar, buscar donde aplica, crear, editar y eliminar).
La eliminación está protegida cuando hay relaciones con medicamentos o ventas.
IMPORTANTE: migrate:fresh borra todos los datos; solo usar en una base de pruebas vacía.
