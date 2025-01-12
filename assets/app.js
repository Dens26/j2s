// app.js

import './bootstrap.js';
import './styles/app.css';

// Ajout du comportement du spinner et description toggle
document.addEventListener("DOMContentLoaded", function () {
    const links = document.querySelectorAll(".pagination-link");
    const overlay = document.getElementById("loading-overlay");
    const searchForm = document.querySelector("form[role='search']"); // Cible le formulaire de recherche

    // Gestion de la pagination
    links.forEach(link => {
        link.addEventListener("click", function () {
            overlay.style.display = "flex"; // Active le spinner pour la pagination
        });
    });

    // Gestion de la recherche principale
    if (searchForm) {
        searchForm.addEventListener("submit", function () {
            overlay.style.display = "flex"; // Active le spinner lors de la première recherche
        });
    }

    // Gestion du toggle de description en utilisant un sélecteur universel
    document.querySelectorAll('.toggle-description-button').forEach(button => {
        button.addEventListener('click', function () {
            const shortDescription = this.previousElementSibling.querySelector('.short-description');
            const fullDescription = this.previousElementSibling.querySelector('.full-description');

            if (shortDescription.style.display === "none") {
                shortDescription.style.display = "block";
                fullDescription.style.display = "none";
                this.textContent = "Afficher plus";
            } else {
                shortDescription.style.display = "none";
                fullDescription.style.display = "block";
                this.textContent = "Afficher moins";
            }
        });
    });
});

console.log('This log comes from assets/app.js - welcome to Symfony 7 AssetMapper! 🎉');
