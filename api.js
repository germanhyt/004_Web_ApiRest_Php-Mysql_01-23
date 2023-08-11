const d = document,
  $table = d.querySelector(".crud-table"),
  $form = d.querySelector(".crud-form"),
  $title = d.querySelector("crud-title"),
  $template = d.getElementById("crud-template").content,
  $fragment = d.createDocumentFragment();

//Obtnemos los datos del JSON
const getAll = async () => {
  try {
    let res = await fetch(
        "https://ecommerce-navidev.000webhostapp.com/Api_Productos/api_view.php"
      ),
      json = await res.json();

    if (!res.ok) throw { status: res.status, statusText: res.statusText };

    json.items.forEach((el) => {
      console.log(el);
      $template.querySelector(".id").textContent = el.id;
      $template.querySelector(".name").textContent = el.name;
      $template.querySelector(".image").textContent = el.image;
      $template.querySelector(
        ".image_view"
      ).src = `https://ecommerce-navidev.000webhostapp.com/Api_Productos/assets/images/${el.image}`;
      $template.querySelector(".edit").dataset.id = el.id;
      $template.querySelector(".edit").dataset.name = el.name;
      $template.querySelector(".edit").dataset.image = el.image;
      $template.querySelector(".delete").dataset.id = el.id;

      let $clone = d.importNode($template, true);
      $fragment.appendChild($clone);
    });

    $table.querySelector("tbody").appendChild($fragment);
  } catch (err) {
    let message = err.statusText || "Ocurrió un error";
    $table.insertAdjacentHTML(
      "afterend",
      `<p><b>Error ${err.status}: ${message}</b></p>`
    );
  }
};
d.addEventListener("DOMContentLoaded", getAll());

//Escuchador de envío de formulario
d.addEventListener("submit", async (e) => {
  if (e.target === $form) {
    e.preventDefault();

    if (!e.target.id.value) {
      //Si nuestro tag tipo "Hidden" no tiene id entonces realizamos un POST-INSERT
      //Create - POST
      try {
        let options = {
            method: "POST",
            headers: {
              //En la cabecera, colocamos los siguientes objetos
              "Content-type": "application/json; charset=utf-8",
            },
            body: JSON.stringify({
              //Convertimos en forma de  sintaxis JSON pero tipo cadena
              nombre: e.target.nombre.value,
              constelacion: e.target.image.value,
            }),
          },
          res = await fetch(
            "https://ecommerce-navidev.000webhostapp.com/Api_Productos",
            options
          ), //Esperamos a realizar la petición con las "options" definidas
          json = await res.json(); //Esperamos a convertir la respuesta a tipo json()

        if (!res.ok) throw { status: res.status, statusText: res.statusText };

        location.reload(); //reacargamos
      } catch (err) {
        let message = err.statusText || "Ocurrió un error";
        $form.insertAdjacentHTML(
          "afterend",
          `<p><b>Error ${err.status}: ${message}</b></p>`
        );
      }
    } else {
      //Si el id tiene valor entonces se relaiza un PUT-UPDATE
      //Update - PUT
      try {
        let options = {
            method: "PUT",
            headers: {
              "Content-type": "application/json; charset=utf-8",
            },
            body: JSON.stringify({
              nombre: e.target.nombre.value,
              constelacion: e.target.constelacion.value,
            }),
          },
          res = await fetch(
            `https://ecommerce-navidev.000webhostapp.com/Api_Productos?id=${e.target.id.value}`,
            options
          ), //La URl es la que varía en el método PUT con respecto al POST
          json = await res.json();

        if (!res.ok) throw { status: res.status, statusText: res.statusText };

        location.reload();
      } catch (err) {
        let message = err.statusText || "Ocurrió un error";
        $form.insertAdjacentHTML(
          "afterend",
          `<p><b>Error ${err.status}: ${message}</b></p>`
        );
      }
    }
  }
});

// d.addEventListener("click", async e => {
//     if (e.target.matches(".edit")) {

//     //   $title.content = "Editar producto";
//       $form.id.value = e.target.dataset.id;
//       $form.nombre.value = e.target.dataset.name;
//       $form.imagen.value = e.target.dataset.image;
//     }

//     if (e.target.matches(".delete")) {  //Método de Eliminar
//       let isDelete = confirm(`¿Estás seguro de eliminar el id ${e.target.dataset.id}?`);

//       if (isDelete) {
//         //Delete - DELETE
//         try {
//           let options = {
//             method: "DELETE",
//             headers: {
//               "Content-type": "application/json; charset=utf-8"
//             }
//           },
//             res = await fetch(`https://ecommerce-navidev.000webhostapp.com/Api_Productos?delete=${e.target.dataset.id}`, options),
//             json = await res.json();

//           if (!res.ok) throw { status: res.status, statusText: res.statusText };

//           location.reload();
//         } catch (err) {
//           let message = err.statusText || "Ocurrió un error";
//           alert(`Error ${err.status}: ${message}`);
//         }
//       }
//     }
//   })
