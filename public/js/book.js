document.querySelector("#book_read_form").addEventListener('submit', (e) => { 
    e.preventDefault();

    // Récupération des données du formulaire
    let form = new FormData(document.querySelector("#book_read_form"));
    form = Object.fromEntries(form);

    // Envoi des données au serveur
    if(form['id'] === null) {
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
            alert('Il faut rafraichir la page, la lecture a été ajoutée');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de l\'envoi des données');
        });
    }else{
        fetch('/book/update', {
            method: 'PUT',
            body: JSON.stringify(form),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log(data); 
            alert('Il faut rafraichir la page, la lecture a été modifiée');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de l\'envoi des données');
        });
    }

    // Fermeture de la modal
    let test = document.querySelector("#book_modal");
    var modal = KTModal.getInstance(test);
    if (modal) {
        modal.toggle(test);
    }

    return false;
})