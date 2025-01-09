import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

// Ajout du comportement du spinner
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
});

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
