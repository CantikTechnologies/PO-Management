

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        /* Header and Logo Styles */ 
 header {
    text-align: center;
    margin-bottom: 40px;
    padding: 0 0;
    background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
    color: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    z-index: 1;
} 

 .logo {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
    position: relative;
    z-index: 2;
} 

 .logo-triangles {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    width: 100px;
    height: 80px;
}

.triangle {
    position: absolute;
    width: 0;
    height: 0;
    border-left: 20px solid transparent;
    border-right: 20px solid transparent;
    border-bottom: 35px solid #667eea;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
}

.triangle-1 {
    bottom: 0;
    left: 15px;
    animation: float 3s ease-in-out infinite;
}

.triangle-2 {
    bottom: 0;
    right: 15px;
    animation: float 3s ease-in-out infinite 0.5s;
}

.triangle-3 {
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    border-bottom: 35px solid #667eea;
    border-top: 35px solid transparent;
    border-left: 20px solid transparent;
    border-right: 20px solid transparent;
    animation: float 3s ease-in-out infinite 1s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.logo h1 {
    font-size: 3rem;
    font-weight: 800;
    letter-spacing: 3px;
    margin: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.tagline {
    font-size: 1.2rem;
    font-style: italic;
    opacity: 0.9;
    margin: 0;
    color: #e2e8f0;
}

    </style>

</head>
<body>
    <header>
            <div class="logo">
                <div class="logo-triangles">
                    <div class="triangle triangle-1"></div>
                    <div class="triangle triangle-2"></div>
                    <div class="triangle triangle-3"></div>
                </div>
                <h1>CANTIK TECHNOLOGY</h1>
                <p class="tagline">We Delight..</p>
            </div>
        </header>
</body>
</html>
