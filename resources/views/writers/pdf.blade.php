<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Írók listája</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; text-align: center; line-height: 35px; border-bottom: 1px solid #ddd; }
        footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; text-align: center; border-top: 1px solid #ddd; padding-top: 10px; }
        .logo { font-size: 20px; font-weight: bold; color: #4a5568; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <header>
        <div class="logo">KÖNYVEK API KLIENS</div>
    </header>

    <footer>
        Készült: {{ date('Y.m.d H:i') }} | Oldal: <span class="pagenum"></span>
    </footer>

    <main>
        <h2 style="text-align: center; margin-top: 20px;">Nyilvántartott Írók</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Név</th>
                    <th>Bio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($writers as $writer)
                <tr>
                    <td>{{ $writer['id'] }}</td>
                    <td>{{ $writer['name'] }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($writer['bio'] ?? '', 100) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>