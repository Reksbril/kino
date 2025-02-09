let selectedTitles = new Set();
let selectedLocations = new Set();

document.addEventListener("DOMContentLoaded", () => {
    populateDropdown("titleDropdown", availableTitles, selectedTitles);
    populateDropdown("locationDropdown", availableLocations, selectedLocations);
});

function populateDropdown(dropdownId, items, selectedSet) {
    let dropdown = document.getElementById(dropdownId);
    items.forEach(item => {
        let option = document.createElement("div");
        option.classList.add("dropdown_item");

        checkbox_field = document.createElement("label");
        checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.value = item;

        checkbox.addEventListener("change", (event) => {
            checkbox = event.target
            toggleSelection(checkbox, selectedSet, filterRepertoire);
        });
        checkbox_field.appendChild(checkbox);

        var text = document.createTextNode(item);
        checkbox_field.appendChild(text);

        option.appendChild(checkbox_field);

        dropdown.appendChild(option);
    });
}

function toggleSelection(checkbox, selectedSet, filterFunction) {
    console.log(checkbox.value);
    if (checkbox.checked) {
        selectedSet.add(checkbox.value);
    } else {
        selectedSet.delete(checkbox.value);
    }
    filterFunction();
}

function toggleDropdown(id) {
    if (!document.getElementById(id).classList.contains("show")) {
        hideDropdowns();
        document.getElementById(id).classList.toggle("show");
    } else {
        hideDropdowns();
    }
}

function hideDropdowns() {
    document.querySelectorAll(".dropdown_content").forEach(dropdown => {
        dropdown.classList.remove("show");
    });
}

window.onclick = function (event) {
    if (!event.target.matches('.dropdown_button') && !event.target.matches('.dropdown_item') && !event.target.parentNode.matches('.dropdown_item') && !event.target.parentNode.parentNode.matches('.dropdown_item')) {
        hideDropdowns();
    }
}

function filterRepertoire() {
    let elements = document.querySelectorAll(".repertoire_element_container");

    elements.forEach(element => {
        let title = element.getAttribute("data-title");
        let location = element.getAttribute("data-location");

        let matchTitle = selectedTitles.size === 0 || selectedTitles.has(title);
        let matchLocation = selectedLocations.size === 0 || selectedLocations.has(location);

        element.style.display = matchTitle && matchLocation ? "grid" : "none";
    });
}