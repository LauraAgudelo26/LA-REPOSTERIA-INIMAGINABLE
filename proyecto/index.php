<?php
/**
 * Punto de entrada principal del sistema
 * index.php
 */

// Redirigir al archivo principal de la aplicación
header('Location: public/views/index.html');
exit();
?>
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
         .title {
            font-family: 'Comic Sans MS', cursive;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            font-size: 3.5rem;
            align-items: center;
            justify-content: center;
        }
        .subtitle {
            color: #5a3e36;
            font-style: italic;
        }
        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
            background-color: white;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            color: #FF0000;
            font-weight: bold;
        }
        .card-text {
            color: #7f8c8d;
        }
        .price {
            font-size: 1.5rem;
            color: #27ae60;
            font-weight: bold;
        }
        .carousel-item img {
            height: 500px;
            object-fit: cover;
        }
        .carousel-caption {
            background-color: rgba(0,0,0,0.5);
            border-radius: 15px;
            padding: 20px;
        }
        .section-title {
            color: #FF0000;
            border-bottom: 2px dashed #FF0000;
            padding-bottom: 10px;
            margin: 40px 0 30px;
            text-align: center;
        }
        .btn-fruty {
            background-color: #FF0000;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            transition: all 0.3s;
        }
        .btn-fruty:hover {
            background-color: #FF0000;
            transform: scale(1.05);
        }
        /* Efecto de brillo animado */
        @keyframes brillo {
        0% { box-shadow: 0 0 5px rgba(255,255,255,0.3); }
        50% { box-shadow: 0 0 15px rgba(255,255,255,0.7); }
        100% { box-shadow: 0 0 5px rgba(255,255,255,0.3); }
        }

        /* Botón Registrarse */
        .btn-registrarse {
            background: linear-gradient(135deg, #ff6b6b, #ffcc70);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 1rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-registrarse:hover {
            background: linear-gradient(135deg, #ff4d4d, #ffb84d);
            transform: scale(1.05);
            animation: brillo 1s infinite;
        }

        /* Botón Iniciar Sesión */
        .btn-iniciar {
            background: linear-gradient(135deg, #4facfe, #43e97b);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 1rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-iniciar:hover {
            background: linear-gradient(135deg, #3b82f6, #2ecc71);
            transform: scale(1.05);
            animation: brillo 1s infinite;
        }


    </style>
</head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Header con título -->
    <div class="header">
        <div class="container d-flex justify-content-between align-items-center flex-wrap">
            <div>
                    <h1 class="title">La Reposteria De Lo Inimaginable</h1>
                    <p class="subtitle">Delicias naturales llenas de sabor y fantasía</p>
            </div>
            <img src="img/logo.crdownload" alt="Logo Fruty Fantasy" style="position: absolute; top: 10px; left: 30px; width: 160px; height: auto;">
            <!-- Botón grande (solo en pantallas md en adelante) -->
            <div class="ms-auto d-none d-md-block">


        <!-- Ícono pequeño (solo en pantallas pequeñas) -->
        <!-- Botón Iniciar Sesión -->
        <a href="iniciar_sesion.html" class="btn btn-iniciar">
             <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
        </a>

        <!-- Botón Registrarse -->
        <a href="registrarse.html" class="btn btn-registrarse">
            <i class="fas fa-user-plus"></i> Registrarse
        </a>

        </div>
    </div>
    <!-- Carrusel -->
    <div class="container mb-5">
        <div id="frutyCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#frutyCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#frutyCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#frutyCarousel" data-bs-slide-to="2"></button>
                <button type="button" data-bs-target="#frutyCarousel" data-bs-slide-to="3"></button>
                <button type="button" data-bs-target="#frutyCarousel" data-bs-slide-to="4"></button>
                <button type="button" data-bs-target="#frutyCarousel" data-bs-slide-to="5"></button>
            </div>
            <div class="carousel-inner rounded-3">
                <div class="carousel-item active">
                    <img src="img/banano.jpg" class="d-block w-100" alt="Frutas tropicales">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Banano</h5>
                        <p>El banano es una fruta tropical muy popular y nutritiva.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="img/manzana.jpeg" class="d-block w-100" alt="Batidos naturales">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Manzana</h5>
                        <p>Las manzanas son una de las frutas más populares y consumidas a nivel mundial.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="img/fresas con masmelo.jpeg" class="d-block w-100" alt="Postres frutales">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Fresa con masmelo</h5>
                        <p>La combinación de fresa con masmelo es un deleite que combina lo mejor de dos mundos.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="img/pichos.jpeg" class="d-block w-100" alt="Postres frutales">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Pinchos</h5>
                        <p>Los pinchos de frutas son muy nutritivos para la salud.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="img/uvas.jpeg" class="d-block w-100" alt="Postres frutales">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Uvas</h5>
                        <p>Las uvas son frutas versátiles y nutritivas con una larga historia de consumo y cultivo.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="img/cereza.jpeg" class="d-block w-100" alt="Postres frutales">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Cereza</h5>
                        <p>Las cerezas son unas frutas deliciosas y nutritivas.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="img/bebidas refrescantes.png" class="d-block w-100" alt="Postres frutales">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Bebidas Refrescantes</h5>
                        <p>Deliciosas bebidas refrescantes a base de frutas.</p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#frutyCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#frutyCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

    <!-- Tarjetas de productos -->
    <div class="container">
        <h2 class="section-title">Nuestros productos</h2>
        
        <div class="row">
            <!-- Tarjeta 1 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/banano.jpg" class="card-img-top" alt="Banano">
                    <div class="card-body">
                        <h5 class="card-title">Banano</h5>
                        <p class="card-text">El banano es una fruta tropical muy popular y nutritiva.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$2.000</span>
                            <button class="btn btn-fruty btn-ordenar" data-producto="Banano" data-precio="2000">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta 2 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/manzana.jpeg" class="card-img-top" alt="Manzana">
                    <div class="card-body">
                        <h5 class="card-title">Manzana</h5>
                        <p class="card-text">La manzana es una fruta crujiente y dulce, perfecta para cualquier ocasión.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$3.000</span>
                            <button class="btn btn-fruty btn-ordenar" data-producto="Manzana" data-precio="3000">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta 3 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/fresas con masmelo.jpeg" class="card-img-top" alt="Fresa con masmelo">
                    <div class="card-body">
                        <h5 class="card-title">Fresa con masmelo</h5>
                        <p class="card-text">La combinación de fresa con masmelo es un deleite que combina lo mejor de dos mundos.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$2.500</span>

                            <button class="btn btn-fruty btn-ordenar"  data-producto="Fresa con masmelo" data-precio="2500">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta 4 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/pichos.jpeg" class="card-img-top" alt="Pinchos">
                    <div class="card-body">
                        <h5 class="card-title">Pinchos</h5>
                        <p class="card-text">Los pinchos de frutas son una opción divertida y colorida para disfrutar de una variedad de sabores.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$3.000</span>
                            <button class="btn btn-fruty btn-ordenar" data-producto="Pinchos" data-precio="3000">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta 5 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/uvas.jpeg" class="card-img-top" alt="Uvas">
                    <div class="card-body">
                        <h5 class="card-title">Uvas</h5>
                        <p class="card-text">Las uvas son frutas versátiles y nutritivas con una larga historia de consumo y cultivo.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$5.700</span>
                            <button class="btn btn-fruty btn-ordenar" data-producto="Uvas" data-precio="5700">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta 6 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/cereza.jpeg" class="card-img-top" alt="Cerezas">
                    <div class="card-body">
                        <h5 class="card-title">Cereza</h5>
                        <p class="card-text">Las cerezas son unas frutas deliciosas y nutritivas.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$4.200</span>
                           <button class="btn btn-fruty btn-ordenar" onclick="ordenarProducto('Cereza', 4200)" data-producto="Cereza" data-precio="4200">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 7 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/Cappuccino .jpg" class="card-img-top" alt="Cappuccino">
                    <div class="card-body">
                        <h5 class="card-title">Cappuccino</h5>
                        <p class="card-text">Una bebida caliente y cremosa que combina espresso intenso con leche vaporizada y espuma suave, creando un equilibrio perfecto de sabores y texturas.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$4.200</span>
                           <button class="btn btn-fruty btn-ordenar" onclick="ordenarProducto('Cappuccino', 4200)" data-producto="Cappuccino" data-precio="4200">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 8 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/bebidas refrescantes.png" class="card-img-top" alt="bebidas refrescantes">
                    <div class="card-body">
                        <h5 class="card-title">Bebidas de frutas</h5>
                        <p class="card-text">Bebidas frescas y revitalizantes hechas con frutas naturales, perfectas para calmar la sed y disfrutar de sabores intensos y saludables.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$5.000</span>
                           <button class="btn btn-fruty btn-ordenar" onclick="ordenarProducto('Bebidas de frutas', 5000)" data-producto="Bebidas de frutas" data-precio="5000">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 9 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/galletas con chips de chocolate.jpg" class="card-img-top" alt="galletas con chips de chocolate">
                    <div class="card-body">
                        <h5 class="card-title">Galletas de chip de chocolate</h5>
                        <p class="card-text">Galletas crujientes por fuera y suaves por dentro, con trozos de chocolate derretido que aportan un toque dulce y delicioso.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$1.000</span>
                           <button class="btn btn-fruty btn-ordenar" onclick="ordenarProducto('Galletas de chip de chocolate', 1000)" data-producto="Galletas de chip de chocolate" data-precio="1000">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 10 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/rollos de canela.jpg" class="card-img-top" alt="rollos e canela">
                    <div class="card-body">
                        <h5 class="card-title">Rollos de canela</h5>
                        <p class="card-text">Rollos de canela suaves y esponjosos, rellenos de canela y azúcar, cubiertos con un delicioso glaseado de turrón que añade un toque dulce y cremoso.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$2.000</span>
                           <button class="btn btn-fruty btn-ordenar" onclick="ordenarProducto('Banano', 2000)" data-producto="Banano" data-precio="2000">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 11 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/Cheesecake de Fresa Delicioso.jpg" class="card-img-top" alt="Cheesecake de fresa">
                    <div class="card-body">
                        <h5 class="card-title">Cheesecake de fresa</h5>
                        <p class="card-text">Cheesecake de fresa: un postre cremoso que combina queso crema con fresas frescas, sobre una base crujiente de galletas.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$10.000</span>
                           <button class="btn btn-fruty btn-ordenar" onclick="ordenarProducto('Banano', 2000)" data-producto="Banano" data-precio="2000">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Tarjeta 12 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/donas.jpg" class="card-img-top" alt="donas">
                    <div class="card-body">
                        <h5 class="card-title">Donas</h5>
                        <p class="card-text">cubiertos con glaseado, azúcar o toppings decorativos. Su textura suave y esponjosa las hace irresistibles, y vienen en una variedad de sabores como chocolate, vainilla, fresa y más</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$5.000</span>
                           <button class="btn btn-fruty btn-ordenar" onclick="ordenarProducto('Donas', 2000)" data-producto="Donas" data-precio="2000">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Tarjeta 13 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/postre de limon.jpg" class="card-img-top" alt="postre de limon">
                    <div class="card-body">
                        <h5 class="card-title">Postre de limon</h5>
                        <p class="card-text">Delicioso y refrescante dulce que combina la acidez del limón con la dulzura de azúcares y cremas</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$5.000</span>
                           <button class="btn btn-fruty btn-ordenar" onclick="ordenarProducto('Postre de limon', 2000)" data-producto="Postre de limon" data-precio="2000">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Tarjeta 14 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/fresas con crema.jpg" class="card-img-top" alt="fresas con crema">
                    <div class="card-body">
                        <h5 class="card-title">Fresas con crema</h5>
                        <p class="card-text"> Un postre clásico y delicioso que combina fresas frescas y dulces con crema para untar, generalmente azucarada y batida para darle una textura suave y esponjosa.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$5.000</span>
                           <button class="btn btn-fruty btn-ordenar" onclick="ordenarProducto('Fresas con crema', 2000)" data-producto="Fresas con crema" data-precio="2000">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Tarjeta 15 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/postre de gelatina.jpg" class="card-img-top" alt="postre de gelatina">
                    <div class="card-body">
                        <h5 class="card-title">Postre de gelatina</h5>
                        <p class="card-text"> Hecho con gelatina disuelta en líquido, azúcar y saborizantes. Puede tener frutas, colores vibrantes y texturas divertidas, lo que lo hace ideal para cualquier ocasión.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$5.000</span>
                           <button class="btn btn-fruty btn-ordenar" onclick="ordenarProducto('Postre de gelatina', 2000)" data-producto="Postre de gelatina" data-precio="2000">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Tarjeta 16 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/mini tarta de frutas.jpg" class="card-img-top" alt="mini tarta de frutas">
                    <div class="card-body">
                        <h5 class="card-title">Mini tarta de frutas</h5>
                        <p class="card-text"> Las minis tartas de frutas son pequeñas obras maestras dulces, con una base crujiente de masa y un relleno de frutas frescas y jugosas. Son perfectas para reuniones y eventos.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$2.000</span>
                           <button class="btn btn-fruty btn-ordenar" onclick="ordenarProducto('Mini tarta de frutas', 2000)" data-producto="Mini tarta de frutas" data-precio="2000">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Tarjeta 17 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/churros.jpg" class="card-img-top" alt="churros">
                    <div class="card-body">
                        <h5 class="card-title">Churros</h5>
                        <p class="card-text"> "Descubre el sabor irresistible de nuestros churros: crujientes por fuera, suaves por dentro y bañados en azúcar. ¡Perfectos para acompañar con chocolate o caramelo!"</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$2.000</span>
                           <button class="btn btn-fruty btn-ordenar" onclick="ordenarProducto('Churros', 2000)" data-producto="Churros" data-precio="2000">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Tarjeta 18 -->
            <div class="col-md-4">
                <div class="card">
                    <img src="img/postre de oreo.jfif" class="card-img-top" alt="postre de oreo">
                    <div class="card-body">
                        <h5 class="card-title">Postre de Oreo</h5>
                        <p class="card-text"> "Disfruta de nuestro delicioso postre de Oreo, con capas de galleta y crema que se derriten en la boca. ¡Un placer para los amantes del chocolate!"</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$3.500</span>
                           <button class="btn btn-fruty btn-ordenar" onclick="ordenarProducto('Postre de Oreo', 3500)" data-producto="Postre de Oreo" data-precio="3500">Ordenar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
        
        <body>
            <div class="text-center my-4">
                <p>Los Placeres Ocultos</p> <style></style>
                <a href="placeres_ocultos.html" class="btn btn-primary">Ver Productos</a>
            </div>
            <!-- Bootstrap JS Bundle -->
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>

    </div>
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <div class="social-icons">
                <a href="https://www.facebook.com/tuperfil" target="_blank"><i class="fab fa-facebook"></i></a>
                <a href="https://twitter.com/tuusuario" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="https://www.instagram.com/tuusuario/" target="_blank"><i class="fab fa-instagram"></i></a>
            </div>
            <p class="mb-0">© 2024 Reposteria De Lo Inimaginable - Todos los derechos reservados</p>
            <p class="mb-0">Diseñado con ❤️ para los amantes de la reposteria</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
    document.querySelectorAll('.btn-ordenar').forEach(boton => {
        boton.addEventListener('click', () => {
            const producto = boton.getAttribute('data-producto');
            const precio = boton.getAttribute('data-precio');
    
            // Aquí podrías mostrar un SweetAlert o enviar datos a PHP
            Swal.fire({
                title: `¿Deseas ordenar ${producto}?`,
                text: `Precio: $${precio}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, ordenar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aquí se podría enviar la orden a PHP (por POST, por ejemplo)
                    location.href = ("../../Formulario_de_registrar_producto.html")
                    fetch('ordenar.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `producto=${producto}&precio=${precio}`
                    })
                    .then(res => res.text())
                    .then(respuesta => {
                        Swal.fire('¡Orden enviada!', respuesta, 'success');
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Hubo un problema al enviar la orden', 'error');
                    });
                }
            });
        });
    });
    <section id="productos" class="mt-5">
        <h2>Sección de Productos</h2>
             <!-- Contenido aquí -->
    </section>

    </script>    
    <script src="./Script.js"></script>

    
</html>
