import './bootstrap.js';
import './styles/app.css';
import canvasConfetti from 'canvas-confetti'

document.addEventListener("DOMContentLoaded", () => {
    // Suppression du localStorage lors de la création d'un nouveau jeu mystère
    const resetLocalStorageButton = document.getElementById("resetLocalStorageButton");
    if (resetLocalStorageButton) {
        resetLocalStorageButton.addEventListener("click", () => {
            console.log("Nouvelle partie détectée : suppression du localStorage.");
            localStorage.clear(); // Efface tout le localStorage
        });
    }

    // Masquer le nom du Jeu mystère en attente
    document.querySelectorAll(".hideTheName").forEach((button) => {
        button.addEventListener("click", () => {
            const parentContainer = button.closest(".d-flex"); // Trouve le bon parent
            const mysteryGameName = parentContainer.querySelector(".mysteryGameName");
            const mysteryGameNameHidden = parentContainer.querySelector(".mysteryGameNameHidden");
            const hideIcon = button.querySelector("img");

            console.log("Cache/Montre le nom du jeu mystère");

            if (mysteryGameName.style.display === "none") {
                mysteryGameName.style.display = "inline";
                mysteryGameNameHidden.style.display = "none";
            } else {
                mysteryGameName.style.display = "none";
                mysteryGameNameHidden.style.display = "inline";
            }
        });
    });



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
        const checkboxGroups = document.querySelectorAll('[data-controller="checkbox-group"]');

        checkboxGroups.forEach(group => {
            const mainCheckbox = group.querySelector('input[type="checkbox"]');
            const dependentCheckboxes = group.querySelectorAll('input[type="checkbox"]:not([id="' + mainCheckbox.id + '"])');

            if (mainCheckbox) {
                updateDependentCheckboxes(mainCheckbox.checked, dependentCheckboxes);
                mainCheckbox.addEventListener('change', () => {
                    updateDependentCheckboxes(mainCheckbox.checked, dependentCheckboxes);
                });
            }
        });
    }

    function updateDependentCheckboxes(isChecked, dependentCheckboxes) {
        dependentCheckboxes.forEach(checkbox => {
            checkbox.disabled = !isChecked;
            checkbox.checked = true;
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

    // Gestion des bouton indices
    function handleCategoryToggle() {
        const categoryContainers = document.querySelectorAll(".category-container");

        categoryContainers.forEach(container => {
            const placeholder = container.querySelector(".category-placeholder");
            const list = container.querySelector(".category-list");
            const categoryName = container.dataset.category; // Identifiant unique

            // Restauration de l'état au chargement
            if (localStorage.getItem(`category_${categoryName}`) === "open") {
                list.style.display = "block";
                placeholder.style.display = "none";
                container.classList.add("active");
            }

            container.addEventListener("click", () => {
                const isActive = container.classList.toggle("active");

                if (isActive) {
                    list.style.display = "block";
                    placeholder.style.display = "none";
                    localStorage.setItem(`category_${categoryName}`, "open");
                } else {
                    list.style.display = "none";
                    placeholder.style.display = "inline";
                    localStorage.setItem(`category_${categoryName}`, "closed");
                }
            });
        });
    }

    // Empêcher les boutons indices d'affecter l'état des catégories
    function preventHintButtonPropagation() {
        document.querySelectorAll(".btn-hint").forEach(button => {
            button.addEventListener("click", function (event) {
                event.stopPropagation(); // Empêche le clic de remonter au parent
            });
        });
    }

    // Canvas confetti
    function win() {
        // Effet de confettis animé
        var duration = 1 * 1000; // Durée en millisecondes (3 sec)
        var end = Date.now() + duration;

        (function frame() {
            canvasConfetti({
                particleCount: 5, // Nombre de particules par explosion
                angle: 60,
                spread: 55,
                origin: { x: 0 } // Côté gauche
            });

            canvasConfetti({
                particleCount: 5,
                angle: 120,
                spread: 55,
                origin: { x: 1 } // Côté droit
            });

            if (Date.now() < end) {
                requestAnimationFrame(frame);
            }
        })();
    }

    // Initialisation
    function init() {
        handlePaginationLinks();
        handleSearchForm();
        handleCheckboxGroups();
        handleDescriptionToggle();
        handleContentToggle();
        handleFooterShadow();
        handleCategoryToggle();
        preventHintButtonPropagation();
    }
    if (document.querySelector(".win-message")) {
        win();
    }

    init();

    console.log('This log comes from assets/app.js - welcome to Symfony 7 AssetMapper! 🎉');
});
