document.getElementById('search_input').addEventListener('input', function() {
    const query = this.value; // Récupération de la valeur de recherche
    fetch(`/search?query=${encodeURIComponent(query)}`) // Envoi de la requête de recherche
        .then(response => response.json())
        .then(data => {
            const searchResultsContainer = document.getElementById('search_results');
            searchResultsContainer.innerHTML = ''; // Réinitialisation des résultats

            if (data.length > 0) {
                data.forEach(searchBook => {
                    let ratingHtml = ''; // Construction de l'affichage des étoiles

                    for(let i=0; i<5; i++) {
                        if(i < searchBook.rating) {
                            ratingHtml += `<i class="rating-on ki-solid ki-star text-base leading-none"></i>`; // Étoile remplie
                        } else {
                            ratingHtml += `<i class="rating-on ki-outline ki-star text-base leading-none"></i>`; // Étoile vide
                        }
                    }

                    const bookElement = ` <!-- Élément de livre -->
                    <div class="scrollable-y-auto" data-scrollable="true" data-scrollable-max-height="auto" data-scrollable-offset="300px">
                        <div class="menu menu-default p-0 flex-col">
                            <div class="grid gap-1">
                                <div class="menu-item">
                                    <div class="menu-link flex justify-between gap-2">
                                        <div class="flex items-center gap-2.5">
                                            <img alt="Cover" class="rounded-full size-9 shrink-0" src="${searchBook.cover}" /> <!-- Couverture du livre -->
                                            <div class="flex flex-col">
                                                <a class="text-sm font-semibold text-gray-900 hover:text-primary-active mb-px" href="#">
                                                    ${searchBook.name} <!-- Nom du livre -->
                                                </a>
                                                <span class="text-2sm font-normal text-gray-500">
                                                    ${searchBook.description} <!-- Description du livre -->
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <div class="rating">
                                                ${ratingHtml} <!-- Affichage des étoiles -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                    searchResultsContainer.innerHTML += bookElement; // Ajout de l'élément au conteneur
                });
            } else {
                searchResultsContainer.innerHTML = '<div class="text-center">Aucun résultat trouvé.</div>'; // Message si aucun résultat
            }
        })
        .catch(error => {
            console.error('Erreur lors de la recherche:', error); // Gestion des erreurs
            const searchResultsContainer = document.getElementById('search_results');
            searchResultsContainer.innerHTML = '<div class="text-center">Une erreur est survenue lors de la recherche.</div>'; // Message d'erreur
        });
});