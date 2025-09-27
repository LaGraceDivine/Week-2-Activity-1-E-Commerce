document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("addCategoryForm");
    const nameInput = document.getElementById("categoryName");
    const tableBody = document.querySelector("#categoryTable tbody");

    //Fetch
    function loadCategories() {
        fetch("../actions/fetch_category_action.php")
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = "";
                data.forEach(cat => {
                    let row = `
                        <tr>
                            <td>${cat.id}</td>
                            <td><input type="text" value="${cat.name}" data-id="${cat.id}" class="editName"></td>
                            <td>
                                <button class="updateBtn" data-id="${cat.id}">Update</button>
                                <button class="deleteBtn" data-id="${cat.id}">Delete</button>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            });
    }

    loadCategories();

    //Add
    form.addEventListener("submit", e => {
        e.preventDefault();
        let name = nameInput.value.trim();
        if (!name) return alert("Category name required!");
        fetch("../actions/add_category_action.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "name=" + encodeURIComponent(name)
        })
        .then(res => res.text())
        .then(msg => {
            alert(msg);
            nameInput.value = "";
            loadCategories();
        });
    });

    //update & delete
    tableBody.addEventListener("click", e => {
        if (e.target.classList.contains("updateBtn")) {
            let id = e.target.dataset.id;
            let newName = document.querySelector(`.editName[data-id='${id}']`).value;
            fetch("../actions/update_category_action.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "id=" + id + "&name=" + encodeURIComponent(newName)
            })
            .then(res => res.text())
            .then(msg => {
                alert(msg);
                loadCategories();
            });
        }

        if (e.target.classList.contains("deleteBtn")) {
            let id = e.target.dataset.id;
            if (confirm("Delete this category?")) {
                fetch("../actions/delete_category_action.php", {
                    method: "POST",
                    headers: {"Content-Type": "application/x-www-form-urlencoded"},
                    body: "id=" + id
                })
                .then(res => res.text())
                .then(msg => {
                    alert(msg);
                    loadCategories();
                });
            }
        }
    });
});
