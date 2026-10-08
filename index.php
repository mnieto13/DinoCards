<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DinoCards - TCG Oficial</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles/styles.css">
</head>
<body>

  <!-- ==================== VISTA 1: PORTADA ==================== -->
  <section id="vista-portada" class="vista vista-activa">
    <div class="portada-contenedor">
      <div class="logo-gigante-caja">
        <h1 class="logo-texto-principal">DINOCARDS</h1>
        <p class="logo-subtexto">COLECCIONADOR DE CARTAS JURÁSICAS</p>
      </div>

      <button type="button" id="btn-entrar" class="btn-tactico">
        <span class="fa-solid fa-crosshairs"></span> ENTRAR
      </button>
    </div>
  </section>

  <!-- ==================== VISTA 2: ACCESO (LOGIN / REGISTRO) ==================== -->
  <section id="vista-splash" class="vista vista-oculta">
    <header class="splash-header">
      <div class="header-logo">
        <span class="fa-solid fa-dragon logo-icono"></span>
        <h2>DINOCARDS</h2>
      </div>

      <div class="header-user-status">
        <span class="badge-status-dot" id="user-status-dot"></span>
        <span>Genetista:</span>
        <strong id="user-badge" class="badge-name">Invitado</strong>
      </div>
    </header>

    <main class="splash-main">
      <div class="hero-banner">
        <h2 class="hero-titulo">¡Inicia sesión para reclamar tu sobre diario!</h2>
      </div>

      <div class="auth-box">
        <div class="pestanas-panel">
          <button id="tab-login" class="tab-btn activo">INICIAR SESIÓN</button>
          <button id="tab-registro" class="tab-btn">REGISTRO</button>
        </div>

        <div id="alerta-box" class="alerta-box oculto"></div>

        <!-- LOGIN -->
        <form id="form-login" class="auth-form activo">
          <div class="campo-grupo">
            <label for="login-identificador"><span class="fa-solid fa-id-card-clip"></span> USUARIO O EMAIL</label>
            <input type="text" id="login-identificador" placeholder="rex01 o user@dino.com" required>
          </div>

          <div class="campo-grupo">
            <label for="login-password"><span class="fa-solid fa-key"></span> CONTRASEÑA</label>
            <input type="password" id="login-password" placeholder="••••••••" required>
          </div>

          <button type="submit" class="btn-enviar">ENTRAR AL SISTEMA</button>
        </form>

        <!-- REGISTRO -->
        <form id="form-registro" class="auth-form oculto">
          <div class="campo-grupo">
            <label for="registro-username"><span class="fa-solid fa-user-ninja"></span> NICKNAME</label>
            <input type="text" id="registro-username" placeholder="RaptorAlpha" required>
          </div>

          <div class="campo-grupo">
            <label for="registro-email"><span class="fa-solid fa-envelope"></span> CORREO ELECTRÓNICO</label>
            <input type="email" id="registro-email" placeholder="usuario@dino.com" required>
          </div>

          <div class="campo-grupo">
            <label for="registro-password"><span class="fa-solid fa-lock"></span> CONTRASEÑA</label>
            <input type="password" id="registro-password" placeholder="••••••••" required>
          </div>

          <div class="campo-grupo">
            <label for="registro-confirm-password"><span class="fa-solid fa-shield-halved"></span> CONFIRMAR CONTRASEÑA</label>
            <input type="password" id="registro-confirm-password" placeholder="••••••••" required>
          </div>

          <button type="submit" class="btn-enviar">REGISTRAR</button>
        </form>
      </div>
    </main>
  </section>

  <!-- ==================== VISTA 3: EL JUEGO (SPA HUD + TIENDA / ÁLBUM) ==================== -->
  <section id="vista-juego" class="vista-juego oculto">
    
    <header class="hud">
      <div class="hud-datos">
        <span>INVESTIGADOR: <strong id="hud-user">Invitado</strong></span>
        <span>ESPECÍMENES DESCUBIERTOS: <strong id="hud-cuenta">0 / 100</strong></span>
        <span>PODER MEDIO: <strong id="hud-poder">0.0</strong></span>
      </div>
      <button class="btn-salir" id="btn-logout" title="Cerrar sesión">SALIR</button>
    </header>

    <nav class="nav-central">
      <button class="btn-nav-central activo" id="tab-tienda">
        <i class="fa-solid fa-boxes-stacked"></i> EXTRACCIÓN (TIENDA)
      </button>
      <button class="btn-nav-central" id="tab-album">
        <i class="fa-solid fa-book-open"></i> MI ÁLBUM (<span id="cuenta-tab">0</span>)
      </button>
    </nav>

    <!-- TIENDA -->
    <main id="seccion-tienda">
      <section class="bahia-extraccion">
        <div class="bahia-header">
          <div class="punto-led"></div>
          <span>MÓDULO DE EXTRACCIÓN GENÉTICA // SECUENCIADOR TCG</span>
        </div>

        <div class="zona-packs-flex">
          <div class="pack-pedestal diario" id="sobre-diario">
            <div class="pack-halo-fondo"></div>
            <img src="res/sobre_diario.png" class="pack-img" alt="Sobre Diario T-Rex">
            <div class="pedestal-base"></div>
            <div class="pedestal-suelo"></div>
            <div class="pack-boton-accion">
              <span class="titulo-btn" id="txt-diario">SOBRE DIARIO</span>
              <span class="sub-btn">5 CARTAS DEL DÍA</span>
            </div>
          </div>

          <div class="pack-pedestal infinito" id="sobre-infinito">
            <div class="pack-halo-fondo"></div>
            <img src="res/sobre_infinito.png" class="pack-img" alt="Sobre Infinito Raptor">
            <div class="pedestal-base"></div>
            <div class="pedestal-suelo"></div>
            <div class="pack-boton-accion">
              <span class="titulo-btn">SIMULADOR INFINITO</span>
              <span class="sub-btn">EXTRACCIÓN ILIMITADA</span>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- ÁLBUM -->
    <main id="seccion-album" class="oculto">
      <section class="seccion-album">
        <div class="album-header">
          <div>
            <h2 style="color: var(--color-amber); font-size: 20px;">ÁLBUM DE ESPECÍMENES CLONADOS</h2>
            <p style="font-size: 12px; color: #839788; font-family: var(--font-mono);">Toca una carta para inspeccionar su ficha técnica completa.</p>
          </div>

          <div class="filtros">
            <button class="btn-filtro activo" data-periodo="TODOS">TODOS</button>
            <button class="btn-filtro" data-periodo="Triásico">TRIÁSICO</button>
            <button class="btn-filtro" data-periodo="Jurásico">JURÁSICO</button>
            <button class="btn-filtro" data-periodo="Cretácico">CRETÁCICO</button>
          </div>
        </div>

        <div class="grid-coleccion" id="grid-coleccion"></div>
      </section>

      <section id="panel-detalle" class="oculto">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
          <h2 id="det-titulo" style="color: var(--color-amber); font-size: 18px;">FICHA TÉCNICA</h2>
          <button class="btn-salir" id="btn-cerrar-detalle">CERRAR</button>
        </div>
        <div class="detalle-contenedor" id="det-contenido"></div>
      </section>
    </main>
  </section>

  <!-- ==================== VISTA 4: MESA DE APERTURA ==================== -->
  <section id="vista-apertura" class="oculto">
    <div>
      <h2 style="color: var(--color-amber); font-size: 24px; letter-spacing: 2px;">PAQUETE SECUENCIADO</h2>
      <p style="color: #839788; font-size: 13px; font-family: var(--font-mono); margin-top: 4px;">Toca cada carta para romper el precinto de ADN y descubrirla.</p>
    </div>

    <div class="mesa-sobres-fila" id="mesa-cartas-apertura"></div>

    <button id="btn-volver-tienda" class="btn-filtro activo oculto" style="padding: 14px 32px; font-size: 14px; font-weight: 700;">
      <i class="fa-solid fa-boxes-stacked"></i> GUARDAR Y VOLVER A LA TIENDA
    </button>
  </section>

  <!-- Solo main.js: models.js ya no es necesario -->
  <script src="js/main.js"></script>
</body>
</html>