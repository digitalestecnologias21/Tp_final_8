// busqueda.js
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector(".search-bar");
    const rows = document.querySelectorAll(".event-table tr");

    searchInput.addEventListener("keyup", function() {
        const filter = this.value.toLowerCase();
        rows.forEach((row, index) => {
            if(index === 0) return; // salta la fila de encabezados
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });
});
