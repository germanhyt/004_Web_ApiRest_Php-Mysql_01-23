<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API - Test - Ecommerce</title>
    <link rel="stylesheet" href="assets/styles.css" type="text/css">
</head>

<body>

    <div class="container">
        <h1>Api de Pruebas</h1>
        <div class="divbody">
            <h3>Auth - login</h3>
            <code>
                POST /auth
                <br>
                {
                <br>
                "email" :"", -> REQUERIDO
                <br>
                "password": "" -> REQUERIDO
                <br>
                }

            </code>
        </div>
        <div class="divbody">
            <h3>Users</h3>
            <code>
                GET /users?page=$numeroPagina
                <br>
                GET /users?id=$idUser
            </code>

            <code>
                POST /Users
                <br>
                {
                <br>
                "name" : "", -> REQUERIDO
                <br>
                "lastname" : "", 
                <br>
                "email":"", -> REQUERIDO
                <br>
                "phone" :"",
                <br>
                "password" : "", -> REQUERIDO
                <br>
                "image" : "",
                <br>
                "created_at" : "",
                <br>
                "updated_at" : "",
                <br>
                "token" : "" -> REQUERIDO
                <br>
                }

            </code>
            <code>
                PUT /Users
                <br>
                {
                <br>
                "name" : "",
                <br>
                "lastname" : "",
                <br>
                "email":"",
                <br>
                "phone" :"",
                <br>
                "password" : "",
                <br>
                "image" : "",
                <br>
                "updated_at" : "",
                <br>
                "token" : "" , -> REQUERIDO
                <br>
                "userId" : "" -> REQUERIDO
                <br>
                }

            </code>
            <code>
                DELETE /Users
                <br>
                {
                <br>
                "token" : "", -> REQUERIDO
                <br>
                "userId" : "" -> REQUERIDO
                <br>
                }

            </code>
        </div>

    </div>

</body>

</html>