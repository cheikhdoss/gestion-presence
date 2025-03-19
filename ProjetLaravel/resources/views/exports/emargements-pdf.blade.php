<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des émargements</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 20px;
        }
        .present { color: green; }
        .absent { color: red; }
        .retard { color: orange; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Liste des émargements</h1>
        @if($cours)
            <h2>{{ $cours->matiere }}</h2>
            <p>Date du cours : {{ $cours->date_cours->format('d/m/Y H:i') }}</p>
        @endif
        <p>Généré le {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @if(!$cours)
                    <th>Cours</th>
                    <th>Date du cours</th>
                @endif
                <th>Étudiant</th>
                <th>Statut</th>
                <th>Date de signature</th>
                <th>Commentaire</th>
            </tr>
        </thead>
        <tbody>
            @foreach($emargements as $emargement)
                <tr>
                    @if(!$cours)
                        <td>{{ $emargement->cours->matiere }}</td>
                        <td>{{ $emargement->cours->date_cours->format('d/m/Y H:i') }}</td>
                    @endif
                    <td>{{ $emargement->user->nom }} {{ $emargement->user->prenom }}</td>
                    <td class="{{ $emargement->statut }}">{{ ucfirst($emargement->statut) }}</td>
                    <td>{{ $emargement->date_signature ? $emargement->date_signature->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $emargement->commentaire ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Document généré automatiquement - Page {{ $loop->iteration }} sur {{ $loop->count }}</p>
    </div>
</body>
</html> 