const URL_API = "https://proyecto-production-7568.up.railway.app/api";

document.addEventListener('DOMContentLoaded', () => {

    // funcion de cabecera
    const contenedorDerecho = document.querySelector('.cabecera-derecha');
    const menuNavegacion = document.querySelector('.menu-cabecera') || document.querySelector('nav');
    const datosUsuario = localStorage.getItem('usuario');
    const token = localStorage.getItem("token");

    if (datosUsuario && token && contenedorDerecho) {
        const usuario = JSON.parse(datosUsuario);
        const inicial = usuario.nombre ? usuario.nombre.charAt(0).toUpperCase() : '?';
        const esAdmin = usuario.tipoUsuario === 'admin';

        if (esAdmin && menuNavegacion) {
            if (!document.querySelector('.admin-link')) {
                const linkAdmin = document.createElement('a');
                linkAdmin.href = "panel-admin.html";
                linkAdmin.textContent = "Panel admin";
                linkAdmin.classList.add('admin-link');
                menuNavegacion.appendChild(linkAdmin);
            }
        }

        contenedorDerecho.innerHTML = `
            <div id="perfil-usuario" class="avatar-circular">${inicial}</div>
            <div id="popup-usuario" class="popup-perfil d-none">
                <div class="popup-header">
                    <strong>${usuario.nombre}</strong>
                    <span>${usuario.email}</span>
                    ${esAdmin ? '<span class="badge-admin" style="background: red; color: white; padding: 2px 5px; border-radius: 4px; font-size: 10px;">ADMIN</span>' : ''}
                </div>
                <ul class="popup-opciones">
                    <li><a href="cuenta.html">Mi Cuenta</a></li>
                    <li><a href="carrito.html">Carrito</a></li>
                    <li><a href="facturas.html">Facturas</a></li>
                </ul>
                <hr>
                <button id="btn-logout-click" class="btn-cerrar-sesion">Cerrar Sesión</button>
            </div>
        `;

        const avatar = document.getElementById('perfil-usuario');
        const popup = document.getElementById('popup-usuario');

        if (avatar && popup) {
            avatar.addEventListener('click', (e) => {
                e.stopPropagation();
                popup.classList.toggle('d-none');
            });
            document.addEventListener('click', () => popup.classList.add('d-none'));
        }

        document.getElementById('btn-logout-click').addEventListener('click', cerrarSesion);

    } else if (contenedorDerecho) {
        const icono = contenedorDerecho.querySelector('.cuenta-cabecera');
        if (icono) {
            icono.style.cursor = 'pointer';
            icono.onclick = () => window.location.href = 'login.html';
        }
    }

    // Lógica para la página de Mi Cuenta
    if (window.location.pathname.includes("cuenta.html")) {
        cargarDatosPerfil();

        const formCuenta = document.getElementById('formCuenta');
        if (formCuenta) {
            formCuenta.addEventListener('submit', async (e) => {
                e.preventDefault();
                await guardarDatosPerfil();
            });
        }
    }

    // funcion de login    
    const formLogin = document.getElementById('formularioLogin');
    if (formLogin) {
        formLogin.addEventListener('submit', async (e) => {
            e.preventDefault();
            const credenciales = {
                email: document.getElementById('correo_login').value,
                password: document.getElementById('contrasena_login').value
            };
            try {
                const res = await axios.post(`${URL_API}/login`, credenciales);
                localStorage.setItem('token', res.data.token);
                localStorage.setItem('usuario', JSON.stringify(res.data.usuario));
                window.location.href = "index.html";
            } catch (e) {
                console.error(e);
            }
        });
    }

    // funcion para enviar registro
    const formRegistro = document.getElementById('formularioRegistro');
    if (formRegistro) {
        formRegistro.addEventListener('submit', async (e) => {
            e.preventDefault();
            const telefono = document.getElementById('telefono').value;
            const email = document.getElementById('correo').value;
            const ps1 = document.getElementById('contrasena').value;
            const ps2 = document.getElementById('confirmar_contrasena').value;

            const validacionTelf = /^[0-9]{9}$/;
            if (!validacionTelf.test(telefono)) {
                alert("El teléfono debe contener exactamente 9 números.");
                return;
            }

            if (ps1 !== ps2) {
                alert("las contraseñas no coinciden.");
                return;
            }

            const datos = {
                nombre: document.getElementById('nombre_completo').value,
                email: email,
                telefono: telefono,
                ps1: ps1,
                ps2: ps2
            };

            try {
                await axios.post(`${URL_API}/registro`, datos);
                alert("¡Registro exitoso! Ahora puedes iniciar sesión.");
                window.location.href = "login.html";
            } catch (e) {
                console.error(e);
                // Manejo de errores de validación para verificar si exste el email
                if (e.response && e.response.status === 422) {
                    const errores = e.response.data.errors;
                    }
                    if (errores.email) {
                        alert(errores.email[0]);
                    }
            }
        });
    }

    // funcion para enviar cita
        const btnEnviarCita = document.getElementById('btnEnviarCita');
        if (btnEnviarCita) {
            btnEnviarCita.addEventListener('click', async () => {
                let idCliente = null;
                if (datosUsuario) {
                    idCliente = JSON.parse(datosUsuario).id;
                }

                const telefono = document.getElementById('telefono').value;
                const correo = document.getElementById('correo').value;
                const informacionAdicional = document.getElementById('informacionAdicional')?.value || "";

                const validacionTelf = /^[0-9]{9}$/;
                if (!validacionTelf.test(telefono)) {
                    alert("El teléfono debe contener exactamente 9 números.");
                    return;
                }

                const validacionEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!validacionEmail.test(correo)) {
                    alert("El formato del correo no es válido.");
                    return;
                }

                if (informacionAdicional === "") {
                    alert("La información adicional no puede estar vacía.");
                    return;
                }

                const data = {
                    nombre: document.getElementById('nombre').value,
                    correo: correo,
                    telefono: telefono,
                    numMatricula: document.getElementById('numMatricula').value,
                    numIdC: document.getElementById('numIdC').value,
                    tipoConsulta: document.getElementById('tipoConsulta').value,
                    informacionAdicional: informacionAdicional,
                    idCliente: idCliente
                };

                try {
                    await axios.post(`${URL_API}/citas`, data);
                    window.location.reload();
                } catch (e) {
                    console.error(e);
                }
            });
        }

    if (window.location.pathname.includes("carrito.html")) {
        renderizarCarrito();
    }

    if (window.location.pathname.includes("facturas.html")) {
        cargarFacturas();
    }

    cargarProductos();
});

    //funcion cargar datos del perfil
    async function cargarDatosPerfil() {
        try {
            const token = localStorage.getItem('token');
            const res = await axios.get(`${URL_API}/user`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            
            const usuario = res.data;

            if(document.getElementById('nombre')) document.getElementById('nombre').value = usuario.nombre || '';
            if(document.getElementById('email_perfil')) document.getElementById('email_perfil').value = usuario.email || '';
            if(document.getElementById('telefono')) document.getElementById('telefono').value = usuario.telefono || '';
            if(document.getElementById('display-id')) document.getElementById('display-id').innerText = `#${usuario.id}`;

        } catch (e) {
            console.error("Error al cargar perfil:", e);
        }
    }

    //funcion para guardar datos del perfil
    async function cargarDatosPerfil() {
        const localUser = JSON.parse(localStorage.getItem('usuario'));
        if (!localUser) return;

        document.getElementById('nombre').value = localUser.nombre || '';
        document.getElementById('email').value = localUser.email || '';
        document.getElementById('telefono').value = localUser.telefono || '';
        document.getElementById('display-id').textContent = `#${localUser.id}`;

        const puntos = localUser.puntosRacha || 0;
        const contenedor = document.getElementById('contenedor-racha');
        
        if (contenedor) {
            const circulos = contenedor.querySelectorAll('i');
            circulos.forEach((circulo, index) => {
                if (index < puntos) {
                    circulo.classList.remove('bi-circle');
                    circulo.classList.add('bi-circle-fill');
                } else {
                    circulo.classList.remove('bi-circle-fill');
                    circulo.classList.add('bi-circle');
                }
            });
        }

        const statusCaja = document.getElementById('status-descuento');
        const descTexto = document.getElementById('descuento-texto');
        const descIcono = document.getElementById('descuento-icono');

        if (localUser.descuentoActivo == 1) {
            if (statusCaja) statusCaja.classList.add('activo');
            if (descTexto) descTexto.textContent = "DESCUENTO DISPONIBLE";
            if (descIcono) descIcono.textContent = "redeem";
        } else {
            if (statusCaja) statusCaja.classList.remove('activo');
            if (descTexto) descTexto.textContent = "SIN DESCUENTO ACTIVO";
            if (descIcono) descIcono.textContent = "lock";
        }
    }

    async function guardarDatosPerfil(event) {


    if (event) event.preventDefault();

    const localUser = JSON.parse(localStorage.getItem('usuario'));

    const token = localStorage.getItem('token');

    const datosParaEnviar = {
        nombre: document.getElementById('nombre').value,
        email: document.getElementById('email').value,
        telefono: document.getElementById('telefono').value,
        tipoUsuario: localUser.tipoUsuario
    };

    try {

        const res = await axios.put(
            `${URL_API}/usuario/${localUser.id}`,
            datosParaEnviar,
            {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            }
        );

        localStorage.setItem(
            'usuario',
            JSON.stringify(res.data)
        );

        window.location.reload();

    } catch (e) {

        console.error(e);
    }

    }


    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('formCuenta');
        if (form) {
            form.addEventListener('submit', guardarDatosPerfil);
        }
        cargarDatosPerfil(); 
    });

    // funcion para cargar productos
    async function cargarProductos() {
        const ruta = window.location.pathname;
        let tipo = null;
        let contenedor = null;

        if (ruta.includes("tienda.html")) {
            tipo = "compra";
            contenedor = document.getElementById("contenedor-tienda");
        } else if (ruta.includes("alquiler.html")) {
            tipo = "alquiler";
            contenedor = document.getElementById("contenedor-alquiler");
        }

        if (!tipo || !contenedor) return;

        try {
            const res = await axios.get(`${URL_API}/objetos`, {
                headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
            });

            const productos = res.data.filter(p => p.tipo.toLowerCase() === tipo);
            contenedor.innerHTML = "";
            const grid = document.createElement("div");
            grid.classList.add("grid-productos");

            productos.forEach(producto => {
                const tarjeta = document.createElement("div");
                tarjeta.classList.add("card-producto");

                let claseEstado = "estado-disponible";
                const estadoTexto = producto.estado ? producto.estado.toLowerCase() : 'disponible';

                if (tipo === "alquiler") {
                    if (estadoTexto === "mantenimiento") claseEstado = "estado-mantenimiento";
                    if (estadoTexto === "ocupado") claseEstado = "estado-ocupado";
                }

                let imagen = producto.imagen ? `http://localhost:8000/storage/${producto.imagen}` : "img/sinimagen.png";
                const badgeDescuento = (producto.descuento && producto.descuento > 0) ? `<span class="badge-descuento">-${producto.descuento}%</span>` : "";

                tarjeta.style.position = "relative";
                tarjeta.innerHTML = `
                    ${badgeDescuento}
                    <img src="${imagen}" alt="${producto.nombre}" class="imagen-producto" onerror="this.src='img/sinimagen.png'">
                    <div class="contenido-producto">
                        <h2>${producto.nombre}</h2>
                        <p class="precio">${producto.precio}€</p>
                        <p class="status-badge ${claseEstado}">${producto.estado ?? 'Disponible'}</p>
                        <p class="calificacion">★ ${producto.calificacion ?? '0'}/5</p>
                    </div>
                    <button class="btn-producto" ${estadoTexto !== 'disponible' && tipo === 'alquiler' ? 'disabled style="background: #ccc; cursor: not-allowed;"' : ''}>
                        ${estadoTexto !== 'disponible' && tipo === 'alquiler' ? 'No disponible' : 'Añadir al carrito'}
                    </button>
                `;

                const btnAccion = tarjeta.querySelector('.btn-producto');
                if (btnAccion && !btnAccion.disabled) {
                    btnAccion.onclick = () => añadirAlCarrito(producto, tipo);
                }

                grid.appendChild(tarjeta);
            });
            contenedor.appendChild(grid);
        } catch (e) {
            console.error(e);
        }
    }

// funcion para añadir al carrito (localStorage)
function añadirAlCarrito(producto, tipo) {

    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];

    const itemIndex = carrito.findIndex(
        i => i.idObjeto === producto.id && i.tipo === tipo
    );

    let precioOriginal = parseFloat(producto.precio);

    let porcentajeDescuento = producto.descuento || 0;

    let precioConDescuento = precioOriginal;

    if (porcentajeDescuento > 0) {

        precioConDescuento = precioOriginal * (1 - (porcentajeDescuento / 100));
    }

    if (itemIndex > -1) {

        carrito[itemIndex].cantidad += 1;

        if (tipo === 'alquiler') {

            const fInicio = new Date();

            const fFin = new Date();

            fFin.setMonth(
                fInicio.getMonth() + carrito[itemIndex].cantidad
            );

            carrito[itemIndex].fechaInicio =fInicio.toISOString().split('T')[0];

            carrito[itemIndex].fechaFin =fFin.toISOString().split('T')[0];
        }

    } else {

        let objetoCarrito = {
            idObjeto: producto.id,
            nombre: producto.nombre,
            precio: precioConDescuento,
            precioOriginal: precioOriginal,
            descuento: porcentajeDescuento,
            tipo: tipo,
            cantidad: 1
        };

        if (tipo === 'alquiler') {

            const fInicio = new Date();

            const fFin = new Date();

            fFin.setMonth(fInicio.getMonth() + 1);

            objetoCarrito.fechaInicio =
                fInicio.toISOString().split('T')[0];

            objetoCarrito.fechaFin =
                fFin.toISOString().split('T')[0];
        }

        carrito.push(objetoCarrito);
    }

    localStorage.setItem(
        'carrito',
        JSON.stringify(carrito)
    );
}

// funcion para renderizar la lista del carrito
function renderizarCarrito() {

    const lista = document.getElementById('lista-productos-carrito');

    const subtotalElem = document.getElementById('subtotal-precio');

    const totalElem = document.getElementById('total-final');

    if (!lista) return;

    let carrito =
        JSON.parse(localStorage.getItem('carrito')) || [];

    lista.innerHTML = "";

    let subtotal = 0;

    carrito.forEach((item, index) => {

        subtotal += item.precio * item.cantidad;

        const div = document.createElement('div');

        div.className =
            "item-carrito d-flex justify-content-between align-items-center mb-2 p-2 border-bottom";

        let detalleAlquiler = "";

        if (item.tipo === 'alquiler') {

            detalleAlquiler = `
                <div class="small text-muted">
                    Meses:
                    <input
                        type="number"
                        value="${item.cantidad}"
                        min="1"
                        style="width:50px"
                        onchange="actualizarMeses(${index}, this.value)"
                    >

                    <br>

                    Inicio: ${item.fechaInicio}

                    <br>

                    Fin: ${item.fechaFin}
                </div>
            `;
        }

        div.innerHTML = `
            <div>

                <strong>${item.nombre}</strong>

                (${item.tipo})

                <br>

                <span>
                    ${item.precio}€
                    ${item.tipo === 'alquiler'
                        ? 'al mes'
                        : 'x ' + item.cantidad}
                </span>

                ${detalleAlquiler}

            </div>

            <button
                class="btn btn-sm btn-danger"
                onclick="eliminarDelCarrito(${index})">

                Eliminar

            </button>
        `;

        lista.appendChild(div);
    });

    if (subtotalElem) {
        subtotalElem.innerText = subtotal.toFixed(2) + "€";
    }

    if (totalElem) {
        totalElem.innerText = subtotal.toFixed(2) + "€";
    }

    const btnVaciar = document.getElementById('btnVaciarCarrito');

    if (btnVaciar) {

        btnVaciar.onclick = () => {

            localStorage.removeItem('carrito');

            renderizarCarrito();
        };
    }

    const btnFinalizar = document.getElementById('btnFinalizarCompra');

    if (btnFinalizar) {

        btnFinalizar.onclick = finalizarCompra;
    }
}

// Función para actualizar meses de alquiler
window.actualizarMeses = function(index, valor) {

    let carrito =JSON.parse(localStorage.getItem('carrito')) || [];

    let meses = parseInt(valor);

    if (meses < 1) meses = 1;

    if (carrito[index].tipo === 'alquiler') {

        carrito[index].cantidad = meses;

        const fInicio = new Date();

        const fFin = new Date();

        fFin.setMonth(fInicio.getMonth() + meses);

        carrito[index].fechaInicio = fInicio.toISOString().split('T')[0];

        carrito[index].fechaFin = fFin.toISOString().split('T')[0];
    }

    localStorage.setItem('carrito',JSON.stringify(carrito));

    renderizarCarrito();
}

// funcion para eliminar un producto del carrito
window.eliminarDelCarrito = function(index) {

    let carrito =JSON.parse(localStorage.getItem('carrito')) || [];

    carrito.splice(index, 1);

    localStorage.setItem('carrito',JSON.stringify(carrito));  

    renderizarCarrito();
}

// funcion para finalizar compra enviando al backend

async function finalizarCompra() {

    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];

    if (carrito.length === 0) {
        alert("El carrito está vacío");
        return;
    }

    const token = localStorage.getItem('token');

    if (!token) {
        alert("Debes iniciar sesión");
        return;
    }

    let totalNum = 0;

    carrito.forEach(item => {
        totalNum += Number(item.precio) * Number(item.cantidad);
    });

    totalNum = Number(totalNum.toFixed(2));

    const payload = {
        carrito: carrito,
        precioTotal: totalNum,
        metodoPago: 'efectivo'
    };

    console.log("PAYLOAD ENVIADO:", payload);

    try {

        const res = await axios.post(
            `${URL_API}/carrito/finalizar`,
            payload,
            {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            }
        );

        console.log("RESPUESTA:", res.data);

        localStorage.removeItem('carrito');

        window.location.href = "index.html";

    } catch (e) {

        console.error("ERROR COMPLETO:", e);

        if (e.response) {
            console.error("RESPUESTA BACKEND:", e.response.data);
            alert("Error backend: " + JSON.stringify(e.response.data));
        } else {
            alert("Error de conexión");
        }
    }
}


// funcion para cargar facturas
async function cargarFacturas() {
    const contenedor = document.getElementById('lista-facturas');
    if (!contenedor) return;
    try {
        const res = await axios.get(`${URL_API}/facturas/mis-facturas`, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        });
        const facturas = res.data;
        if (facturas.length === 0) {
            contenedor.innerHTML = '<div class="caja-dato">No tienes facturas registradas.</div>';
            return;
        }
        contenedor.innerHTML = "";
        facturas.forEach(f => {
            const tipo = f.idCompra ? "COMPRA" : "ALQUILER";
            const div = document.createElement('div');
            div.className = "caja-dato d-flex justify-content-between align-items-center mb-3 p-3";
            div.innerHTML = `
                <div>
                    <span class="fw-bold text-uppercase" style="color: #666; font-size: 0.8rem;">${tipo} - ${f.fechaCreacion}</span>
                    <h3 class="mb-0 h5" style="font-family: 'Bebas Neue';">${f.listaObjetos}</h3>
                    <span class="text-success fw-bold">${f.precioTotal}€</span>
                </div>
                <button class="btn-negro btn-sm py-1 px-3" onclick="verDetallesFactura(${f.idFactura})">VER DETALLES</button>
            `;
            contenedor.appendChild(div);
        });
    } catch (e) {
        console.error(e);
    }
}

// funcion para ver detalles de una factura
window.verDetallesFactura = async function(id) {

    try {

        const res = await axios.get(
            `${URL_API}/facturas/detalles/${id}`,
            {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            }
        );

        console.log("DETALLE FACTURA:", res.data);

        const factura = res.data.factura || res.data;
        const items = res.data.items || [];

        if (!factura) {
            alert("No se encontró la factura");
            return;
        }

        let htmlItems = "";
        let totalCalculado = 0;

        items.forEach(item => {

        console.log("ITEM:", item);

        const esAlquiler = item.tipo === 'alquiler';

        const precio =
            Number(item.precioPagado) ||
            Number(item.precio) ||
            Number(item.precioUnitario) ||
            Number(item.precioObjeto) ||
            Number(item.coste) ||
            0;

        const cantidad =
            Number(item.cantidad) || 1;

        const subtotal = precio * cantidad;

        totalCalculado += subtotal;

        let detalleExtra = "";

        if (esAlquiler) {

            detalleExtra = `
                <div class="small text-muted">
                    ${cantidad} mes(es)
                    <br>
                    Inicio: ${item.fechaInicio}
                    <br>
                    Fin: ${item.fechaFin}
                </div>
            `;
        }

        htmlItems += `
            <div class="d-flex justify-content-between border-bottom py-2">

                <div>
                    <strong>${item.nombre}</strong>

                    ${!esAlquiler
                        ? `<div class="small text-muted">
                            Cantidad: ${cantidad}
                        </div>`
                        : detalleExtra
                    }
                </div>

                <span>
                    ${subtotal.toFixed(2)}€
                </span>

            </div>
        `;


        });


        const modalDetalle = document.createElement('div');

        modalDetalle.style = `
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.8);
            z-index:9999;
            display:flex;
            align-items:center;
            justify-content:center;
        `;

        modalDetalle.id = "temp-modal-factura";

        const titulo = factura.idCompra
            ? 'DETALLES DE COMPRA'
            : 'DETALLES DE ALQUILER';

        const totalFinal =
            Number(factura.precioTotal) ||
            totalCalculado;

        modalDetalle.innerHTML = `
            <div
                class="bg-white p-4 rounded shadow-lg"
                style="width:90%; max-width:500px; color:black;"
            >

                <h2 style="font-family:'Bebas Neue'">
                    ${titulo}
                </h2>

                <hr>

                <div class="mb-3">
                    ${htmlItems || '<p>No hay productos.</p>'}
                </div>

                <div class="d-flex justify-content-between fw-bold h5">
                    <span>TOTAL</span>
                    <span>${totalFinal.toFixed(2)}€</span>
                </div>

                <p class="small text-muted mt-2">
                    Método de pago:
                    ${factura.metodoPago || 'No especificado'}
                </p>

                <button
                    class="btn-negro w-100 mt-3"
                    onclick="document.getElementById('temp-modal-factura').remove()"
                >
                    CERRAR
                </button>

            </div>
        `;

        document.body.appendChild(modalDetalle);

    } catch (e) {

        console.error("ERROR DETALLES FACTURA:", e);

        if (e.response) {
            console.error(e.response.data);
        }

        alert("Error al cargar detalles de factura");
    }
}

// funcion para cerrar sesion
async function cerrarSesion() {
    try {
        const token = localStorage.getItem('token');
        if (token) {
            await axios.post(`${URL_API}/salir`, {}, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
        }
    } catch (e) {
        console.error(e);
    } finally {
        localStorage.clear();
        window.location.href = "login.html";
    }
}
