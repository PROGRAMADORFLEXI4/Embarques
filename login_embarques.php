<!DOCTYPE html>

<?php 
session_start(); 
 if(isset($_SESSION['nom']) && isset($_SESSION['pass'])){
 
     
   
    echo"<script> 
    window.location.href = 'embarques.php';
    </script>"; 
     
 }
?>
<head>
    <link href="css/styles.css" rel="stylesheet" />
    <title>Login Embarques</title>
    <script src="css/jquery.js"></script>
    <link rel="shortcut icon" href="images/icono.ico" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<style>
    #h1 {
        padding-top: 10px;
        font-size: 30px;
        text-shadow: 2px 2px #000000;

    }

    body {
        background: #f9f9f9;

    }

    main {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 50%;


    }

    form {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: #68A4C4;
        min-width: 300px;
        min-height: 250px;
        max-width: 300px;
        max-height: 250px;
        border-style: groove;
        border: 2px solid gray;
        border-radius: 5px;

    }


    footer {
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        bottom: 0;
        width: 100%;
        min-height: 75px;
        margin: 0px;
        padding: 0px;
        background: #68A4C4;
        border-style: groove;
        border: 2px solid gray;
    }

    label,
    input {
        display: block;
        margin-bottom: 10px;
        font: .9rem 'Fira Sans', sans-serif;

    }


    #btn {
        margin-top: 1.5rem;

    }

    header {
        display: flex;
        width: 100%;
        min-height: 75px;
        background: #68A4C4;
        border-style: groove;
        border: 2px solid gray;
        align-items: center;
        justify-content: center;
    }

    .campos {
        justify-content: center;
        align-items: center;
    }
</style>

<body>
    <header>
        <div class="container">
            <h1 id="h1">Login Embarques</h1>
        </div>
    </header>
    <main>
        <form method='POST' action='embarques.php'>
            <div class="lb">
                <label >Nombre de Usuario:</label>
                <input type="text" name="user" id="User" required>
            </div>

            <div>
                <label>Contraseña:</label>
                <input type="password" name="pass" maxlength="16" required>
            </div>

            <input id="btn" value="Entrar" type='submit'>
        </form>
    </main>
</body>
<footer>
    <span>
        <p>
            Fleximatic S.A. de C.V. Modificado por: ISC. Luis Esparza Septiembre 2015 Basado en: Free CSS Templates, © Moderna 2014 All right reserved. By Bootstraptaste
        </p>
    </span>
</footer>
<script>
    $(()=> {
        document.getElementById("User").focus();

    });
    
</script>
</html>
