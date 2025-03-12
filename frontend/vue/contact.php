<?php
require_once 'session/session.php';
require_once 'session/session_timeout.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact - Club de Basket Labège</title>
    <style>
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #333;
            color: #1a8f3b;
            padding: 10px 0;
            display: flex;
            align-items: center;
            text-align: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        header img {
            height: 60px;
            margin-right: 20px;
        }

        .header-content {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }

        header h1 {
            color: white;
            text-align: center;
            flex-grow: 1;
            font-size: 1.8rem;
        }

        .home-icon {
            height: 30px;
            margin-left: 20px;
        }

        nav {
            background-color: #444;
            position: fixed;
            top: 80px;
            width: 100%;
            z-index: 999;
        }

        nav ul {
            display: flex;
            justify-content: center;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        nav ul li {
            position: relative;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 15px 25px;
            display: block;
            font-size: 1.1rem;
        }

        nav ul li:hover > a {
            background-color: #666;
        }

        nav ul li ul {
            display: none;
            position: absolute;
            background-color: white;
            width: 200px;
            box-shadow: var(--shadow);
        }

        nav ul li:hover ul {
            display: block;
        }

        nav ul li ul li {
            width: 100%;
        }

        nav ul li ul li a {
            color: var(--nav-color);
            padding: 12px 20px;
        }

        .container {
            display: flex;
            flex-direction: column;
            padding: 20px;
            text-align: center;
            margin-top: 120px;
        }

        .contact-info {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }

        .contact-info h2 {
            margin-bottom: 10px;
        }

        .contact-info p {
            margin: 5px 0;
        }

        .map {
            width: 100%;
            height: 400px;
            border: 0;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: auto;
            width: 100%;
        }

        @media (max-width: 768px) {
            header {
                height: auto;
                padding: 10px 0;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            header img {
                margin: 0 0 10px 0;
            }

            header h1 {
                font-size: 1.4rem;
            }

            nav {
                top: auto;
                position: relative;
            }

            nav ul {
                flex-direction: column;
            }

            nav ul li ul {
                position: static;
                width: 100%;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="header-content">
        <img src="public/logoClub.png" alt="Club de Basket Labège Logo">
        <h1>Contact - Club de Basket de Labège</h1>
        <a href="index.php">
            <img src="public/home.png" alt="Home" class="home-icon">
        </a>
    </div>
</header>

<nav>
    <ul>
        <li>
            <a href="#">Joueurs</a>
            <ul>
                <li><a href="saisie_joueur.php">Ajouter un Joueur</a></li>
                <li><a href="saisie_recherche_joueur.php">Rechercher un Joueur</a></li>
            </ul>
        </li>
        <li>
            <a href="#">Matchs</a>
            <ul>
                <li><a href="saisie_match.php">Ajouter un Match</a></li>
                <li><a href="saisie_recherche_match.php">Rechercher un Match</a></li>
            </ul>
        </li>
        <li><a href="feuilleStatistique.php">Statistiques</a></li>
        <li><a href="contact.php">Contact</a></li>
    </ul>
</nav>

<div class="container">
    <div class="contact-info">
        <h2>Contactez-nous</h2>
        <p><strong>Adresse:</strong> Rue des Ecoles, 31670 Labège, France</p>
        <p><strong>Téléphone:</strong> +33 1 23 45 67 89</p>
        <p><strong>Email:</strong> contact@basketlabege.fr</p>
    </div>
    <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2888.123456789!2d1.123456789!3d43.123456789!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1234567890abcdef%3A0x1234567890abcdef!2sRue%20des%20Ecoles%2C%2031670%20Lab%C3%A8ge%2C%20France!5e0!3m2!1sen!2sfr!4v1234567890" allowfullscreen="" loading="lazy"></iframe>
</div>

<footer>
    <p>&copy; 2024 Club de Basket Labège. Tous droits réservés.</p>
    <a href="logout.php" class="logout-button">Se déconnecter</a>
</footer>

</body>
</html>