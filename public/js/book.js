document.querySelector("#book_read_form").addEventListener('submit', (e) => { 
    e.preventDefault(); // Empêche le rechargement de la page lors de la soumission du formulaire

    // Récupération des données du formulaire
    let form = new FormData(document.querySelector("#book_read_form"));
    form = Object.fromEntries(form); // Convertit les données du formulaire en objet

    // Envoi des données au serveur
    if(form['id'] === null) { // Vérifie si c'est une nouvelle lecture
        fetch('/book/add', {
            method: 'POST',
            body: JSON.stringify(form), // Envoie les données sous forme JSON
        })
        // Traitement de la réponse du serveur
        .then(response => {
            if (!response.ok) {
                throw new Error(response.statusText); // Gère les erreurs de réponse
            }
            return response.json(); // Convertit la réponse en JSON
        })
        .then(data => {
            console.log(data); 
            alert('Il faut rafraichir la page, la lecture a été ajoutée'); // Alerte utilisateur
        })
        .catch(error => {
            console.error('Error:', error); // Affiche l'erreur dans la console
            alert('Une erreur est survenue lors de l\'envoi des données'); // Alerte utilisateur
        });
    } else { // Si c'est une mise à jour
        fetch('/book/update', {
            method: 'PUT',
            body: JSON.stringify(form), // Envoie les données sous forme JSON
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(response.statusText); // Gère les erreurs de réponse
            }
            return response.json(); // Convertit la réponse en JSON
        })
        .then(data => {
            console.log(data); 
            alert('Il faut rafraichir la page, la lecture a été modifiée'); // Alerte utilisateur
        })
        .catch(error => {
            console.error('Error:', error); // Affiche l'erreur dans la console
            alert('Une erreur est survenue lors de l\'envoi des données'); // Alerte utilisateur
        });
    }

    // Fermeture de la modal
    let test = document.querySelector("#book_modal");
    var modal = KTModal.getInstance(test); // Récupère l'instance de la modal
    if (modal) {
        modal.toggle(test); // Ferme la modal
    }

    return false; // Empêche le comportement par défaut
})