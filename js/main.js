document.addEventListener("DOMContentLoaded", () => {
  let usuarioActual = null;
  let miColeccion = [];
  let sobreActual = [];
  let reveladas = 0;
  let filtroActual = "TODOS";

  // Vistas
  const vistaPortada  = document.getElementById("vista-portada");
  const vistaSplash   = document.getElementById("vista-splash");
  const vistaJuego    = document.getElementById("vista-juego");
  const vistaApertura = document.getElementById("vista-apertura");

  // Auth DOM
  const btnEntrar    = document.getElementById("btn-entrar");
  const tabLogin     = document.getElementById("tab-login");
  const tabRegistro  = document.getElementById("tab-registro");
  const formLogin    = document.getElementById("form-login");
  const formRegistro = document.getElementById("form-registro");
  const alertaBox    = document.getElementById("alerta-box");

  // HUD DOM
  const hudUser   = document.getElementById("hud-user");
  const hudCuenta = document.getElementById("hud-cuenta");
  const hudPoder  = document.getElementById("hud-poder");
  const cuentaTab = document.getElementById("cuenta-tab");
  const btnLogout = document.getElementById("btn-logout");

  // Navegación Juego
  const tabTienda     = document.getElementById("tab-tienda");
  const tabAlbum      = document.getElementById("tab-album");
  const seccionTienda = document.getElementById("seccion-tienda");
  const seccionAlbum  = document.getElementById("seccion-album");

  // Sobres
  const sobreDiario   = document.getElementById("sobre-diario");
  const sobreInfinito = document.getElementById("sobre-infinito");
  const txtDiario     = document.getElementById("txt-diario");

  // Apertura y Álbum
  const mesaApertura     = document.getElementById("mesa-cartas-apertura");
  const btnVolverTienda  = document.getElementById("btn-volver-tienda");
  const gridColeccion    = document.getElementById("grid-coleccion");
  const panelDetalle     = document.getElementById("panel-detalle");
  const btnCerrarDetalle = document.getElementById("btn-cerrar-detalle");

  // Helper matemático de combate directo
  const calcPoder = (d) => {
    const hp  = Number(d.hp || 0);
    const vig = Number(d.vigor || 0);
    const atq = Number(d.ataque || 0);
    const def = Number(d.defensa || 0);
    const agi = Number(d.agilidad || 0);
    return Number(((hp + vig + atq + def + agi) / 5).toFixed(1));
  };

  const calcRareza = (p) => p >= 75 ? "oro" : (p >= 55 ? "plata" : "bronce");
  const colorSemaforo = (v) => v >= 75 ? "stat-verde" : (v >= 50 ? "stat-amarillo" : "stat-rojo");

  function mostrarAlerta(mensaje, tipo = "error") {
    alertaBox.textContent = mensaje;
    alertaBox.className = `alerta-box ${tipo}`;
    alertaBox.classList.remove("oculto");
    setTimeout(() => alertaBox.classList.add("oculto"), 3500);
  }

  function cambiarSeccion(seccion) {
    seccionTienda.classList.toggle("oculto", seccion !== "tienda");
    seccionAlbum.classList.toggle("oculto", seccion !== "album");
    tabTienda.classList.toggle("activo", seccion === "tienda");
    tabAlbum.classList.toggle("activo", seccion === "album");
    if (seccion === "album") renderizarColeccion();
  }

  function crearHtmlCarta(dino, repetidas = 0) {
    const pod = calcPoder(dino);
    const renderBarra = (n, v) => `
      <div class="stat-fila">
        <span class="stat-nombre">${n}</span>
        <div class="stat-track"><div class="stat-fill ${colorSemaforo(v)}" style="width:${v}%"></div></div>
        <span class="stat-val">${v}</span>
      </div>`;

    return `
      ${repetidas > 1 ? `<span class="badge-x">x${repetidas}</span>` : ""}
      <div class="carta-col-izq">
        <span style="font-family:'Share Tech Mono'; font-size:10px; color:#839788;">#0${dino.id}</span>
        <div class="emblema-radar">
          <i class="fa-solid fa-dragon"></i>
          <span>${dino.periodo}</span>
        </div>
        <span class="carta-poder-num">${pod}</span>
      </div>
      <div class="carta-col-der">
        <div>
          <div class="carta-nombre">${dino.nombre}</div>
          <div class="carta-sub">${dino.especie}</div>
        </div>
        <div class="carta-stats-barras">
          ${renderBarra("HP", dino.hp)}
          ${renderBarra("ATQ", dino.ataque)}
          ${renderBarra("DEF", dino.defensa)}
          ${renderBarra("AGI", dino.agilidad)}
        </div>
      </div>`;
  }

  function cargarColeccionBD() {
    fetch("api/obtener_coleccion.php")
      .then((res) => res.json())
      .then((data) => {
        if (data.exito) {
          miColeccion = data.coleccion;
          actualizarContadores();
        }
      })
      .catch((err) => console.error("Error al cargar la colección:", err));
  }

  function actualizarContadores() {
    const unicas = new Set(miColeccion.map((d) => d.id)).size;
    hudCuenta.textContent = `${unicas} / 100`;
    cuentaTab.textContent = unicas;
    if (miColeccion.length) {
      const media = (
        miColeccion.reduce((acc, d) => acc + calcPoder(d), 0) /
        miColeccion.length
      ).toFixed(1);
      hudPoder.textContent = media;
    } else {
      hudPoder.textContent = "0.0";
    }
  }

  function comprobarEstadoSobreDiario() {
    if (usuarioActual && usuarioActual.ultima_obtencion) {
      const ultimaVez = new Date(usuarioActual.ultima_obtencion).getTime();
      const ahora = new Date().getTime();
      const horasPasadas = (ahora - ultimaVez) / (1000 * 60 * 60);

      if (horasPasadas < 24) {
        sobreDiario.style.opacity = "0.4";
        sobreDiario.style.cursor = "not-allowed";
        txtDiario.textContent = "RECLAMADO HOY";
        return;
      }
    }
    sobreDiario.style.opacity = "1";
    sobreDiario.style.cursor = "pointer";
    txtDiario.textContent = "SOBRE DIARIO";
  }

  function ejecutarApertura(tipo) {
    fetch("api/abrir_sobre.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ tipo })
    })
      .then((res) => res.json())
      .then((data) => {
        if (!data.exito) {
          alert(data.mensaje);
          return;
        }

        sobreActual = data.cartas;
        reveladas = 0;

        if (tipo === "diario") {
          usuarioActual.ultima_obtencion = new Date().toISOString().split("T")[0];
          comprobarEstadoSobreDiario();
        }

        mesaApertura.innerHTML = "";
        btnVolverTienda.classList.add("oculto");

        sobreActual.forEach((dino) => {
          const card = document.createElement("div");
          card.className = `carta oculta ${calcRareza(calcPoder(dino))}`;
          card.innerHTML = crearHtmlCarta(dino);

          card.onclick = () => {
            if (card.classList.contains("oculta")) {
              card.classList.remove("oculta");
              if (++reveladas === 5) btnVolverTienda.classList.remove("oculto");
            }
          };
          mesaApertura.appendChild(card);
        });

        vistaJuego.classList.add("oculto");
        vistaApertura.classList.remove("oculto");
      })
      .catch((err) => alert("No se pudo conectar con el secuenciador: " + err.message));
  }

  btnVolverTienda.addEventListener("click", () => {
    miColeccion.push(...sobreActual);
    vistaApertura.classList.add("oculto");
    vistaJuego.classList.remove("oculto");
    actualizarContadores();
    cambiarSeccion("tienda");
  });

  function renderizarColeccion() {
    gridColeccion.innerHTML = "";

    if (!miColeccion.length) {
      gridColeccion.innerHTML = `<p style="grid-column:1/-1; text-align:center; color:#839788; padding:40px;">Tu colección está vacía. Abre un sobre en la Tienda.</p>`;
      return;
    }

    const conteo = miColeccion.reduce((acc, d) => {
      acc[d.id] = (acc[d.id] || 0) + 1;
      return acc;
    }, {});

    const mapaUnicos = new Map();
    miColeccion.forEach((d) => {
      if (!mapaUnicos.has(d.id)) mapaUnicos.set(d.id, d);
    });

    const unicas = Array.from(mapaUnicos.values()).filter((d) => {
      if (filtroActual === "TODOS") return true;
      if (!d.periodo) return false;
      return d.periodo.toLowerCase().includes(filtroActual.toLowerCase());
    });

    unicas.forEach((dino) => {
      const card = document.createElement("div");
      card.className = `carta ${calcRareza(calcPoder(dino))}`;
      card.innerHTML = crearHtmlCarta(dino, conteo[dino.id]);
      card.onclick = () => mostrarDetalleGrande(dino);
      gridColeccion.appendChild(card);
    });
  }

  function mostrarDetalleGrande(d) {
    const pod = calcPoder(d);
    const row = (n, v) => `
      <div class="stat-fila-lg">
        <span style="width:90px; color:#839788;">${n}</span>
        <div class="stat-track-lg"><div class="stat-fill ${colorSemaforo(v)}" style="width:${v}%"></div></div>
        <strong style="width:25px; text-align:right;">${v}</strong>
      </div>`;

    document.getElementById("det-titulo").textContent = `ESPÉCIMEN #${d.id} · ${d.nombre.toUpperCase()}`;
    document.getElementById("det-contenido").innerHTML = `
      <div style="background:rgba(0,0,0,0.4); padding:14px; border:1px solid #1a2e20; border-radius:4px;">
        <h4 style="color:var(--color-amber); margin-bottom:6px;">${d.especie}</h4>
        <p style="font-size:12px; color:#839788; margin-bottom:10px;">Época: <strong>${d.periodo}</strong></p>
        <div style="font-family:var(--font-mono); font-size:11px; color:#839788; border-top:1px solid #1a2e20; padding-top:6px;">
          <div>Altura: <strong>${d.altura} m</strong> | Peso: <strong>${d.peso} kg</strong></div>
          <div style="margin-top:2px;">Poder Medio: <strong style="color:var(--color-amber);">${pod}</strong></div>
        </div>
      </div>
      <div style="background:rgba(0,0,0,0.4); padding:14px; border:1px solid #1a2e20; border-radius:4px;">
        ${row("SALUD (HP)", d.hp)} ${row("VIGOR", d.vigor)} ${row("ATAQUE", d.ataque)} ${row("DEFENSA", d.defensa)} ${row("AGILIDAD", d.agilidad)}
      </div>`;

    panelDetalle.classList.remove("oculto");
    panelDetalle.scrollIntoView({ behavior: "smooth" });
  }

  btnCerrarDetalle.addEventListener("click", () => panelDetalle.classList.add("oculto"));

  // Eventos Navegación
  btnEntrar.addEventListener("click", () => {
    vistaPortada.classList.replace("vista-activa", "vista-oculta");
    vistaSplash.classList.replace("vista-oculta", "vista-activa");
  });

  tabLogin.addEventListener("click", () => {
    tabLogin.classList.add("activo");
    tabRegistro.classList.remove("activo");
    formLogin.classList.remove("oculto");
    formRegistro.classList.add("oculto");
  });

  tabRegistro.addEventListener("click", () => {
    tabRegistro.classList.add("activo");
    tabLogin.classList.remove("activo");
    formRegistro.classList.remove("oculto");
    formLogin.classList.add("oculto");
  });

  tabTienda.addEventListener("click", () => cambiarSeccion("tienda"));
  tabAlbum.addEventListener("click", () => cambiarSeccion("album"));

  sobreDiario.addEventListener("click", () => ejecutarApertura("diario"));
  sobreInfinito.addEventListener("click", () => ejecutarApertura("infinito"));

  document.querySelectorAll(".btn-filtro").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      document.querySelectorAll(".btn-filtro").forEach((b) => b.classList.remove("activo"));
      e.target.classList.add("activo");
      filtroActual = e.target.dataset.periodo;
      renderizarColeccion();
    });
  });

  // Login
  formLogin.addEventListener("submit", (e) => {
    e.preventDefault();
    const identificador = document.getElementById("login-identificador").value.trim();
    const password = document.getElementById("login-password").value;

    fetch("auth/login.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ identificador, password })
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.exito) {
          usuarioActual = data.usuario;
          hudUser.textContent = usuarioActual.username;
          comprobarEstadoSobreDiario();
          cargarColeccionBD();

          vistaSplash.classList.replace("vista-activa", "vista-oculta");
          vistaJuego.classList.remove("oculto");
          cambiarSeccion("tienda");
        } else {
          mostrarAlerta(data.mensaje, "error");
        }
      })
      .catch(() => mostrarAlerta("Error al conectar con el servidor."));
  });

  // Registro
  formRegistro.addEventListener("submit", (e) => {
    e.preventDefault();
    const username = document.getElementById("registro-username").value.trim();
    const email = document.getElementById("registro-email").value.trim();
    const password = document.getElementById("registro-password").value;
    const confirmPassword = document.getElementById("registro-confirm-password").value;

    fetch("auth/registro.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ username, email, password, confirmPassword })
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.exito) {
          mostrarAlerta(data.mensaje, "success");
          formRegistro.reset();
          document.getElementById("login-identificador").value = username;
          tabLogin.click();
        } else {
          mostrarAlerta(data.mensaje, "error");
        }
      })
      .catch(() => mostrarAlerta("Error al conectar con el servidor."));
  });

  // Logout
  btnLogout.addEventListener("click", () => {
    fetch("auth/logout.php")
      .then(() => {
        usuarioActual = null;
        miColeccion = [];
        vistaJuego.classList.add("oculto");
        vistaApertura.classList.add("oculto");
        vistaSplash.classList.replace("vista-oculta", "vista-activa");
        formLogin.reset();
      });
  });
});