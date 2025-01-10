document.getElementById('search_input').addEventListener('input', function() {
    const query = this.value;
    fetch(`/search?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            const searchResultsContainer = document.getElementById('search_results');
            searchResultsContainer.innerHTML = '';

            if (data.length > 0) {
                data.forEach(searchBook => {
                    let ratingHtml = '';

                    for(let i=0; i<5; i++) {
                        if(i < searchBook.rating) {
                            ratingHtml += `<i class="rating-on ki-solid ki-star text-base leading-none"></i>`;
                        } else {
                            ratingHtml += `<i class="rating-on ki-outline ki-star text-base leading-none"></i>`;
                        }
                    }

                    const bookElement = `
                    <div class="scrollable-y-auto" data-scrollable="true" data-scrollable-max-height="auto" data-scrollable-offset="300px">
                        <div class="menu menu-default p-0 flex-col">
                            <div class="grid gap-1">
                                <div class="menu-item">
                                    <div class="menu-link flex justify-between gap-2">
                                        <div class="flex items-center gap-2.5">
                                            <img alt="Cover" class="rounded-full size-9 shrink-0" src="${searchBook.cover}" />
                                            <div class="flex flex-col">
                                                <a class="text-sm font-semibold text-gray-900 hover:text-primary-active mb-px" href="#">
                                                    ${searchBook.name}
                                                </a>
                                                <span class="text-2sm font-normal text-gray-500">
                                                    ${searchBook.description}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <div class="rating">
                                                ${ratingHtml}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                    searchResultsContainer.innerHTML += bookElement;
                });
            } else {
                searchResultsContainer.innerHTML = '<div class="text-center">Aucun résultat trouvé.</div>';
            }
        })
        .catch(error => {
            console.error('Erreur lors de la recherche:', error);
            const searchResultsContainer = document.getElementById('search_results');
            searchResultsContainer.innerHTML = '<div class="text-center">Une erreur est survenue lors de la recherche.</div>';
        });
});
