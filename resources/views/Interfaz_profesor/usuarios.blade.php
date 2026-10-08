<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios — Kahoot 2.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        :root { --kahoot-purple-deep: #2a0b5c; --kahoot-gold: #ffa602; --ivory: #ffffff; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Jost', sans-serif; background: linear-gradient(135deg, var(--kahoot-purple-deep), #4a148c); color: var(--ivory); min-height: 100vh; padding: 40px; }
        
        .container { max-width: 1100px; margin: 0 auto; }
        .glass-card { background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 20px; padding: 30px; margin-bottom: 30px; }
        
        .form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; }
        label { font-size: 14px; font-weight: 700; color: var(--kahoot-gold); margin-bottom: 5px; }
        input, select { background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 12px; border-radius: 10px; outline: none; font-family: 'Jost', sans-serif; }
        input::placeholder { color: rgba(255,255,255,0.4); }
        
        .btn-submit { background: var(--kahoot-gold); color: black; border: none; padding: 12px 25px; border-radius: 10px; font-weight: 800; font-size: 16px; cursor: pointer; transition: 0.2s; }
        .btn-submit:hover { transform: scale(1.02); }
        .btn-delete { background: #e21b3c; color: white; border: none; padding: 8px 15px; border-radius: 8px; font-weight: 700; cursor: pointer; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
        th { color: var(--kahoot-gold); text-transform: uppercase; font-size: 14px; }
        
        .alert { padding: 15px; background: rgba(38, 137, 12, 0.2); border-left: 4px solid #26890c; margin-bottom: 20px; border-radius: 5px; }
        .alert-error { background: rgba(226, 27, 60, 0.2); border-left: 4px solid #e21b3c; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('dashboard.profesor') }}" style="color: white; text-decoration: none; font-weight: bold; font-size: 18px;">← Volver al Panel</a>
        <div style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0;">
            <h1 style="font-size: 36px; font-weight: 800; color: var(--kahoot-gold);">Gestión de Usuarios</h1>
            <div style="background: rgba(255,255,255,0.1); padding: 10px 20px; border-radius: 12px; border: 1px solid var(--kahoot-gold);">
                Periodo Actual: <strong>{{ $bimestreActual }}</strong>
            </div>
        </div>

        @if(session('success')) <div class="alert">{{ session('success') }}</div> @endif
        @if($errors->any()) <div class="alert alert-error">Revisa los datos ingresados. DNI o Email ya existen.</div> @endif

        <div class="glass-card">
            <h2 style="font-size: 22px; margin-bottom: 20px;">Registrar Nuevo Usuario</h2>
            <form action="{{ route('profesor.usuarios.crear') }}" method="POST">
                @csrf
                <div class="form-grid">
                    <div class="form-group"><label>DNI</label><input type="text" name="dni" required placeholder="Ej. 71357154"></div>
                    <div class="form-group"><label>Nombres</label><input type="text" name="name" required placeholder="Ej. Juan David"></div>
                    <div class="form-group"><label>Apellidos</label><input type="text" name="apellidos" required placeholder="Ej. Chuchón Reyes"></div>
                    
                    <div class="form-group"><label>Grado y Sección / Aula</label><input type="text" name="grado_seccion" placeholder="Ej. 4to B / Aula 3G"></div>
                    <div class="form-group">
                        <label>Rol del Usuario</label>
                        <select name="role" required style="color: black;">
                            <option value="estudiante">Estudiante</option>
                            <option value="docente">Profesor / Docente</option>
                        </select>
                    </div>

                    <div class="form-group"><label>Correo (Login)</label><input type="email" name="email" required placeholder="usuario@escuela.edu"></div>
                    <div class="form-group"><label>Contraseña</label><input type="password" name="password" required placeholder="Mínimo 6 caracteres"></div>
                    
                    <div class="form-group" style="justify-content: flex-end;">
                        <button type="submit" class="btn-submit">+ Guardar Usuario</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="glass-card">
            <h2 style="font-size: 22px; margin-bottom: 20px;">Directorio de Usuarios</h2>
            <table>
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Apellidos y Nombres</th>
                        <th>Rol</th>
                        <th>Grado/Aula</th>
                        <th>Bimestre Actual</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $u)
                    <tr>
                        <td>{{ $u->dni ?? '-' }}</td>
                        <td>{{ $u->apellidos }} {{ $u->name }}</td>
                        <td><span style="background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 12px; font-size: 13px;">{{ strtoupper($u->role) }}</span></td>
                        <td>{{ $u->grado_seccion ?? '-' }}</td>
                        <td>{{ $bimestreActual }}</td>
                        <td>
                            @if(Auth::id() !== $u->id)
                            <form action="{{ route('profesor.usuarios.eliminar', $u->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario permanentemente?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete">Eliminar</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>