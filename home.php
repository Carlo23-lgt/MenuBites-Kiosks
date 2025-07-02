<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MenuBites - D'Breaker's Resto Bar 2</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #f8f8f8;
        }
        .hero {
            position: relative;
            width: 100%;
            height: 100vh;
            background: url('assets/DBreakers\ Background.png') no-repeat center center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
        }
        .content {
            position: relative;
            z-index: 2;
        }
        .content h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .content h2 {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .btn {
            background-color: white;
            border: 2px solid #000;
            border-radius: 20px; /* Added border-radius for rounded edges */
            box-sizing: border-box;
            color: black;
            cursor: pointer;
            display: inline-block;
            font-weight: 700;
            letter-spacing: 0.05em;
            margin: 0;
            outline: none;
            overflow: visible;
            padding: 1.25em 2em;
            position: relative;
            text-align: center;
            text-decoration: none;
            text-transform: none;
            transition: all 0.3s ease-in-out;
            user-select: none;
            font-size: 13px;
        }
        .btn::before {
            content: none; /* Removed the line */
            width: 0; /* Set width to 0 to ensure no line appears */
        }
        .btn .text {
            font-size: 1.125em;
            line-height: 1.33333em;
            padding-left: 0; /* Removed padding left since there's no line */
            display: block;
            text-align: center; /* Changed to center alignment */
            transition: all 0.3s ease-in-out;
            text-transform: uppercase;
            text-decoration: none;
            color: black;
        }
        .btn .top-key {
            display: none; /* Hide the top key line */
        }
        .btn .bottom-key-1 {
            display: none; /* Hide the bottom key line 1 */
        }
        .btn .bottom-key-2 {
            display: none; /* Hide the bottom key line 2 */
        }
        .btn:hover {
            color: white;
            background: black;
        }
        .btn:hover::before {
            width: 0; /* Ensure no line appears on hover */
            background: transparent;
        }
        .btn:hover .text {
            color: white;
            padding-left: 0; /* Keep text centered on hover */
        }
        .navbar {
            position: absolute;
            top: 20px;
            left: 20px;
            display: flex;
            align-items: center;
        }
        .navbar h2 {
            font-size: 1.5rem;
            color: white;
        }
        .navbar h2 .abril-fatface {
            font-family: 'Abril Fatface', sans-serif;
        }
        .nav-links {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="hero">
        <div class="overlay"></div>
        <div class="navbar">
            <h2>
                <img src="assets/logo.png" alt="MenuBites Logo" style="height: 40px; vertical-align: middle;">
                <b style="color: #f5f5dc">Menu<span style="color: black;">Bites</span></b>
            </h2>
        </div>
        <div class="content">
            <h2>Welcome to</h2>
            <h1>D'Breaker's Resto Bar 2</h1>
            <a href="customer/menu.php" class="btn">
                <span class="top-key"></span>
                <span class="text">Order now</span>
                <span class="bottom-key-1"></span>
                <span class="bottom-key-2"></span>
            </a>
        </div>
    </div>
</body>
</html>