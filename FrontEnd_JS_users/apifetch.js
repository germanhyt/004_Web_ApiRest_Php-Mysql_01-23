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
        "http://localhost/PROYECTOS/16_Api_Rest_Productos_Front_Back/Backend_PHP_users/users?page=1"
      ),
      json = await res.json();
    // console.log(json[0]);
    if (!res.ok)
      throw {
        status: res.status,
        statusText: res.statusText,
      };

    json.forEach((el) => {
      console.log(el.image);
      $template.querySelector(".id").textContent = el.id;
      $template.querySelector(".name").textContent = el.name;
      $template.querySelector(".lastname").textContent = el.lastname;
      $template.querySelector(".email").textContent = el.email;
      $template.querySelector(".phone").textContent = el.phone;
      $template.querySelector(".state").textContent = el.state;
      // $template.querySelector(
      //   ".image_view"
      // ).src = `./308d795c3cac0f8f16610f53df4e1005.jpg`;
      $template.querySelector(".edit").dataset.id = el.id;
      $template.querySelector(".edit").dataset.email = el.email;
      $template.querySelector(".edit").dataset.phone = el.phone;
      $template.querySelector(".edit").dataset.state = el.state;
      $template.querySelector(".delete").dataset.id = el.id;

      let $clone = d.importNode($template, true);
      $fragment.appendChild($clone);
    });

    $table.querySelector("tbody").appendChild($fragment);
  } catch (err) {
    console.error("Error:", err);
  }
};
d.addEventListener("DOMContentLoaded", getAll());

//Escuchador de envío de formulario
d.addEventListener("submit", async (e) => {
  if (e.target === $form) {
    e.preventDefault();
    if (!e.target.id.value) {
      //Create - POST
      let img_input = document.getElementById("image_file");
      const file = img_input.files[0];
      const reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = function () {
        fetch(
          "http://localhost/PROYECTOS/16_Api_Rest_Productos_Front_Back/Backend_PHP_users/users",
          {
            method: "POST",
            body: JSON.stringify({
              name: e.target.name.value,
              lastname: e.target.lastname.value,
              email: e.target.email.value,
              phone: e.target.phone.value,
              password: e.target.password.value,
              created_at: e.target.created_at.value,
              updated_at: e.target.updated_at.value,
              image: reader.result,
              state: e.target.state.value,
              token: "1cfb91799f090a2fe18fd4c23347d9fd",
            }),
            headers: {
              "Content-Type": "application/json",
            },
          }
        )
          .then((response) => response.json())
          .then((data) => {
            console.log("Succes:", data);

            location.reload(); //reacargamos
          })
          .catch((error) => {
            console.error("Error:", error);
          });
      };
    } else {
      //Update - PUT
      try {
        console.log(e.target.id.value);
        let options = {
            method: "PUT",
            headers: {
              "Content-type": "application/json; charset=utf-8",
            },  
            body: JSON.stringify({
              id: e.target.id.value,
              email: e.target.email.value,
              phone: e.target.phone.value,
              state: e.target.state.value,
              token: "1cfb91799f090a2fe18fd4c23347d9fd",
            }),
          },
          res = await fetch(
            `http://localhost/PROYECTOS/16_Api_Rest_Productos_Front_Back/Backend_PHP_users/users`,
            options
          ),
          json = await res.json();
        console.log(json);
        console.log(res);

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


d.addEventListener("click", async (e) => {
  if (e.target.matches(".edit")) {
    $form.id.value = e.target.dataset.id;
    $form.email.value = e.target.dataset.email;
    $form.phone.value = e.target.dataset.phone;
    $form.state.value = e.target.dataset.state;
  }

  if (e.target.matches(".delete")) {
    let isDelete = confirm(
      `¿Estás seguro de eliminar el id ${e.target.dataset.id}?`
    );

    if (isDelete) {
      //Delete - DELETE
      try {
        let options = {
            method: "DELETE",
            headers: {
              "Content-type": "application/json; charset=utf-8",
            },
            body: JSON.stringify({
              id: e.target.dataset.id,
              token: "1cfb91799f090a2fe18fd4c23347d9fd",
            }),
          },
          res = await fetch(
            `http://localhost/PROYECTOS/16_Api_Rest_Productos_Front_Back/Backend_PHP_users/users`,
            options
          ),
          json = await res.json();

        if (!res.ok) throw { status: res.status, statusText: res.statusText };

        location.reload();
      } catch (err) {
        let message = err.statusText || "Ocurrió un error";
        alert(`Error ${err.status}: ${message}`);
      }
    }
  }
});
