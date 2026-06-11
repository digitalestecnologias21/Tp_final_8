const boton = document.getElementById("btnEventos");

boton.addEventListener("click", () => {
    alert("Próximamente podrás ver todos los eventos.");
});

const botones = document.querySelectorAll(".btn-info");

botones.forEach(boton => {

    boton.addEventListener("click", () => {

        const detalle = boton.nextElementSibling;

        detalle.classList.toggle("visible");

        if(detalle.classList.contains("visible")){
            boton.textContent = "Ver menos";
        }else{
            boton.textContent = "Más información";
        }

    });

});