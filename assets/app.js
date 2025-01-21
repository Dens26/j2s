import './bootstrap.js';
import './styles/app.css';

document.addEventListener("DOMContentLoaded", () => {
    const overlay = document.getElementById("loading-overlay");

    // Gestion de la pagination
    function handlePaginationLinks() {
        const links = document.querySelectorAll(".pagination-link");
        links.forEach(link => {
            link.addEventListener("click", () => {
                if (overlay) overlay.style.display = "flex";
            });
        });
    }

    // Gestion de la recherche principale
    function handleSearchForm() {
        const searchForm = document.querySelector("form[role='search']");
        if (searchForm) {
            searchForm.addEventListener("submit", () => {
                if (overlay) overlay.style.display = "flex";
            });
        }
    }

    // Gestion des groupes de cases à cocher
    function handleCheckboxGroups() {
        // Sélectionne tous les groupes de cases à cocher avec le contrôleur
        const checkboxGroups = document.querySelectorAll('[data-controller="checkbox-group"]');
    
        checkboxGroups.forEach(group => {
            // Trouve la case principale
            const mainCheckbox = group.querySelector('input[type="checkbox"]:first-child');
    
            // Trouve toutes les sous-cases dans ce groupe (excluant la première case)
            const dependentCheckboxes = group.querySelectorAll('input[type="checkbox"]:not([id="artists"])');
    
            if (mainCheckbox) {
                // Ajoute un événement pour activer/désactiver les sous-cases
                mainCheckbox.addEventListener('change', () => {
                    const isChecked = mainCheckbox.checked;
                    dependentCheckboxes.forEach(checkbox => {
                        checkbox.disabled = !isChecked; // Active ou désactive les cases
                        if (!isChecked) {
                            checkbox.checked = false; // Décoche les cases si désactivées
                        }
                    });
                });
            }
        });
    }

    // Gestion du toggle des descriptions
    function handleDescriptionToggle() {
        document.querySelectorAll('.toggle-description-button').forEach(button => {
            button.addEventListener('click', function () {
                const shortDescription = this.previousElementSibling.querySelector('.short-description');
                const fullDescription = this.previousElementSibling.querySelector('.full-description');

                if (shortDescription.style.display === "none") {
                    shortDescription.style.display = "block";
                    fullDescription.style.display = "none";
                    this.textContent = "Afficher plus ...";
                } else {
                    shortDescription.style.display = "none";
                    fullDescription.style.display = "block";
                    this.textContent = "... Afficher moins";
                }
            });
        });
    }

    // Gestion du toggle des contenus supplémentaires
    function handleContentToggle() {
        document.querySelectorAll('.toggle-content-button').forEach(button => {
            button.addEventListener('click', function () {
                const extraContent = this.previousElementSibling;
                if (extraContent.style.display === "none") {
                    extraContent.style.display = "block";
                    this.textContent = "... Afficher moins";
                } else {
                    extraContent.style.display = "none";
                    this.textContent = "Afficher plus ...";
                }
            });
        });
    }

    // Gestion de l'ombrage du footer
    function handleFooterShadow() {
        const footerFixed = document.querySelector(".footer-fixed");
        if (!footerFixed) return;

        const updateFooterShadow = () => {
            const scrollableHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrolledPosition = window.scrollY;

            if (scrolledPosition >= scrollableHeight - 100 || scrollableHeight <= 0) {
                footerFixed.classList.add("no-shadow");
            } else {
                footerFixed.classList.remove("no-shadow");
            }
        };

        document.addEventListener("scroll", updateFooterShadow);
        updateFooterShadow(); // Appel initial
    }

    // Initialisation
    function init() {
        handlePaginationLinks();
        handleSearchForm();
        handleCheckboxGroups();
        handleDescriptionToggle();
        handleContentToggle();
        handleFooterShadow();
    }

    init();

    console.log('This log comes from assets/app.js - welcome to Symfony 7 AssetMapper! 🎉');
});
