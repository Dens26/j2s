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
        // Sélectionne tous les groupes avec le contrôleur
        const checkboxGroups = document.querySelectorAll('[data-controller="checkbox-group"]');
    
        checkboxGroups.forEach(group => {
            // Trouve la case principale
            const mainCheckbox = group.querySelector('input[type="checkbox"]');
    
            // Trouve toutes les sous-cases dans ce groupe (excluant la case principale)
            const dependentCheckboxes = group.querySelectorAll('input[type="checkbox"]:not([id="' + mainCheckbox.id + '"])');
    
            if (mainCheckbox) {
                // Vérifie l'état initial (par exemple, si la case mère est déjà cochée)
                updateDependentCheckboxes(mainCheckbox.checked, dependentCheckboxes);
    
                // Ajoute un événement pour activer/désactiver les sous-cases
                mainCheckbox.addEventListener('change', () => {
                    updateDependentCheckboxes(mainCheckbox.checked, dependentCheckboxes);
                });
            }
        });
    }
    
    function updateDependentCheckboxes(isChecked, dependentCheckboxes) {
        dependentCheckboxes.forEach(checkbox => {
            checkbox.disabled = !isChecked;
            if (!isChecked) {
                checkbox.checked = false;
            }
        });
    }
    
    // Appeler la fonction après le chargement du DOM
    document.addEventListener("DOMContentLoaded", handleCheckboxGroups);
    

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
