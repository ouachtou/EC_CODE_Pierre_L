document.querySelector("#book_read_form").addEventListener('submit', (e) => { 
    e.preventDefault();

    // Récupération des données du formulaire
    let form = new FormData(document.querySelector("#book_read_form"));
    form = Object.fromEntries(form);

    // Envoi des données au serveur
    fetch('/book/add', {
        method: 'POST',
        body: JSON.stringify(form),
    })
    // Traitement de la réponse du serveur
    .then(response => {
        if (!response.ok) {
            throw new Error(response.statusText);
        }
        return response.json();
    })
    .then(data => {
        console.log(data); 
    })
    .catch(error => {
        console.error('Error:', error);
    });

    // Fermeture de la modal
    let test = document.querySelector("#book_modal");
    var modal = KTModal.getInstance(test);
    if (modal) {
        modal.toggle(test);
    }

    return false;
})