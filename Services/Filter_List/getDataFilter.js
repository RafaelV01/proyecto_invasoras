// Obtener referencias a los elementos
const modal = document.getElementById("filtroModal");
const btn = document.getElementById("filtroBtn");
const span = document.getElementsByClassName("close")[0];
const form = document.getElementById("filtroForm");
const searchFormText = document.getElementById("searcherInfo");
const resultado = document.getElementById('list');

btn.onclick = function () {
    modal.style.display = "block";
}

span.onclick = function () {
    modal.style.display = "none";
}


window.onclick = function (event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
}

// Función para manejar el envío de formularios
function handleFormSubmit(formElement) {
    console.log("recibiendo")
    const formData = new FormData(formElement);
    fetch('../templates/php/get/getDataFiltered.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            resultado.innerHTML = data;
            if (formElement === form) {
                modal.style.display = "none"; // Cerrar el modal solo si es el filtro
            }
        })
        .catch(error => console.error('Error:', error));
}

// formulario de filtro
form.addEventListener('submit', function (event) {
    event.preventDefault();
    handleFormSubmit(form);
});

// formulario de búsqueda
searchFormText.addEventListener('submit', function (event) {
    event.preventDefault();
    handleFormSubmit(searchFormText);
});

