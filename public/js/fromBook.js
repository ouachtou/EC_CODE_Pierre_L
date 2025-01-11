function openBookModal(bookId, description, rating, isRead, id) {
    console.log(bookId, description, rating, isRead, id);
    // Mise à jour des champs du formulaire dans le modal avec les données passées
    document.querySelector('[name="book_read_form[id]"]').value = id;
    document.querySelector('[name="book_read_form[book_id]"]').value = bookId;
    document.querySelector('[name="book_read_form[description]"]').value = description;
    document.querySelector('[name="book_read_form[rating]"]').value = rating;
    document.querySelector('[name="book_read_form[is_read]"]').checked = isRead === 'checked';

    // Ouvrir le modal en changeant son style pour le rendre visible
    const modal = document.getElementById('book_modal');
    modal.style.display = 'block';
}
