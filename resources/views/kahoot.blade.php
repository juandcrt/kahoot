<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Kahoot Clone - Test</title>
    <style>
        body { background-color: #f2f2f2; font-family: Arial, sans-serif; text-align: center; padding-top: 100px; }
        .box { background: white; padding: 40px; border-radius: 10px; width: 300px; margin: auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h1 { color: #46178f; font-weight: 800; font-size: 35px; margin-top: 0; }
        input { width: 90%; padding: 15px; margin: 10px 0; font-size: 16px; border: 2px solid #ccc; border-radius: 4px; text-align: center; font-weight: bold; }
        button { background: #333; color: white; border: none; padding: 15px 20px; font-size: 18px; font-weight: bold; border-radius: 4px; cursor: pointer; width: 100%; margin-top: 10px; }
        button:hover { background: #222; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Kahoot!</h1>
        <form action="#" method="GET">
            <input type="text" placeholder="PIN de juego" name="pin" required>
            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>